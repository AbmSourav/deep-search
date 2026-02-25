<?php

namespace DeepSearch\App\Services;

use Codesvault\Validator\Validator;
use DeepSearch\App\Lib\BaseService;
use WP_Query;
use WP_REST_Request;

if (! defined('ABSPATH')) exit;

class Block implements BaseService
{
    public function register()
    {
        add_action('init', [$this, 'registerBlocks']);
        add_action('rest_api_init', [$this, 'restApi']);
        add_action('wp_ajax_search', [$this, 'search']);
        add_action('wp_ajax_nopriv_search', [$this, 'search']);
    }

    public function registerBlocks()
    {
        if (! function_exists('register_block_type')) {
            return;
        }

        $block = register_block_type(DS_PLUGIN_DIR . 'resources/block', [
            'render_callback' => [$this, 'renderBlock']
        ]);

        if ($block && isset($block->editor_script_handles[0])) {
            wp_localize_script(
                $block->editor_script_handles[0],
                'dsBlock',
                [
                    'ajaxUrl' => admin_url('admin-ajax.php'),
                    'nonce'   => wp_create_nonce('deep_search_nonce'),
                ]
            );
        }
    }

    public function renderBlock(array $blockAttributes, string $content)
    {
        $postTypes = get_post_types(
            ['public' => true, 'show_ui' => true],
            'names'
        );

        $postTypes = array_diff($postTypes, ['attachment']);

        $block_data = [
            'ajaxUrl'    => admin_url('admin-ajax.php'),
            'nonce'      => wp_create_nonce('deep_search_nonce'),
            'attibutes'  => $blockAttributes,
            'postTypes'  => $blockAttributes['showPostType'] ? $this->postTypeList() : [],
            'categories' => $blockAttributes['showCat'] ? $this->categoryList() : [],
            'tags'       => $blockAttributes['showTag'] ? $this->tagList() : [],
        ];

        ob_start();
        include DS_PLUGIN_DIR . 'resources/block/view.php';
        return ob_get_clean();
    }

    protected function categoryList(): array
    {
        $categories = get_categories([
            'orderby'    => 'name',
            'hide_empty' => 1
        ]);

        if (empty($categories)) {
            return [];
        }

        $list = [];
        foreach ($categories as $category) {
            $list[] = [
                'term_id' => $category->term_id,
                'value'   => $category->slug,
                'label'   => $category->name
            ];
        }

        return $list;
    }

    protected function postTypeList(): array
    {
        $postTypes = get_post_types(
            ['public' => true, 'show_ui' => true],
            'names'
        );
        $postTypes = array_diff($postTypes, ['attachment']);

        if (empty($postTypes)) {
            return [];
        }

        $list = [];
        foreach ($postTypes as $slug => $postType) {
            $list[] = ['value' => $slug, 'label' => $postType];
        }

        return $list;
    }

    protected function tagList(): array
    {
        $tags = get_tags([
            'orderby'    => 'name',
            'hide_empty' => 1
        ]);

        if (empty($tags)) {
            return [];
        }

        $list = [];
        foreach ($tags as $tag) {
            $list[] = [
                'term_id' => $tag->term_id,
                'value'   => $tag->slug,
                'label'   => $tag->name
            ];
        }

        return $list;
    }

    public function restApi()
    {
        register_rest_route('deep-search/v1', '/search', [
            'methods'             => 'POST',
            'callback'            => [$this, 'restSearch'],
            'permission_callback' => '__return_true',
        ]);
    }

    public function restSearch(WP_REST_Request $request)
    {
        $query = $request->get_param('query');
        if (! $query) {
            return new \WP_REST_Response([
                'message' => 'Missing parameter',
            ], 400);
        }

        // validate data type and strip html tags
        $validator = Validator::validate(
            [
                's'           => 'string',
                'postTypes'   => 'string',
                'cats'        => 'string',
                'tags'        => 'string',
                'currentPage' => 'integer'
            ],
            $query
        );

        if ($validator->error()) {
            wp_send_json_error([
                'message' => 'Validation error',
                'errors'  => $validator->error()
            ], 403);
        }

        $posts = $this->query($validator->getData());

        return new \WP_REST_Response(['data' => $posts], 200);
    }

    public function search()
    {
        // validate data type and strip html tags
        $validator = Validator::validate(
            [
                'nonce'  => 'required|string',
                'action' => 'required|stringOnly',
                'query'  => 'required|string',
            ],
            $_POST
        );

        $errors = $validator->error();
        if ($errors) {
            wp_send_json_error([
                'message' => 'Validation error',
                'errors'  => $errors
            ], 403);
        }

        $data = $validator->getData();

        // Verify nonce
        if (
            !isset($data['nonce']) ||
            !wp_verify_nonce($data['nonce'], 'deep_search_nonce')
        ) {
            wp_send_json_error([
                'message' => 'Invalid security token.'
            ], 403);
        }

        $queryParams = json_decode(wp_unslash($data['query']), true);
        $posts = $this->query($queryParams);

        wp_send_json([
            'data' => $posts,
        ], 200);
    }

    protected function query($queryParams)
    {
        $searchConfigs = get_option('ds_configs');
        $args = [
            'post_status'    => 'publish',
            'posts_per_page' => $searchConfigs['posts_per_page'] ?? 5,
            'paged'          => isset($queryParams['currentPage']) ? absint($queryParams['currentPage']) : 1,
        ];

        if (!empty($queryParams['postTypes'])) {
            $args['post_type'] = explode(',', $queryParams['postTypes']);
        }

        if (!empty($queryParams['s'])) {
            $args['s'] = sanitize_text_field($queryParams['s']);
        }

        if (!empty($queryParams['cats'])) {
            $args['category__in'] = explode(',', $queryParams['cats']);
        }

        if (!empty($queryParams['tags'])) {
            $args['tag__in'] = explode(',', $queryParams['tags']);
        }

        $query = new WP_Query($args);

        $posts = [];
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $posts[] = [
                    'id'        => get_the_ID(),
                    'title'     => get_the_title(),
                    'permalink' => get_permalink(),
                    'date'      => get_the_date(),
                ];
            }
            wp_reset_postdata();
        }

        $nextPage = 0;
        $prevPage = 0;
        // default configs
        if (! $searchConfigs) {
            if ($queryParams['currentPage'] < $query->max_num_pages) {
                $nextPage = $queryParams['currentPage'] + 1;
            }
            $prevPage = $queryParams['currentPage'] - 1;
        }
        // after config is set by user
        if ($searchConfigs && isset($searchConfigs['show_pagination']) && $searchConfigs['show_pagination']) {
            if ($queryParams['currentPage'] < $query->max_num_pages) {
                $nextPage = $queryParams['currentPage'] + 1;
            }
            $prevPage = $queryParams['currentPage'] - 1;
        }

        return [
            'posts'      => $posts,
            'totalPosts' => $query->found_posts,
            'totalPage'  => $query->max_num_pages,
            'nextPage'   => $nextPage,
            'prevPage'   => $prevPage,
        ];
    }
}
