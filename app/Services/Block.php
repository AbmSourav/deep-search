<?php

namespace DeepSearch\App\Services;

use Codesvault\Validator\Validator;
use DeepSearch\App\Lib\BaseService;
use DeepSearch\App\Lib\SingleTon;
use WP_Query;

if (! defined('ABSPATH')) exit;

class Block implements BaseService
{
    use SingleTon;

    public function register()
    {
        add_action('init', [$this, 'registerBlocks']);
        add_action('wp_ajax_search', [$this, 'search']);
        add_action('wp_ajax_nopriv_search', [$this, 'search']);
    }

    public function registerBlocks()
    {
        if (! function_exists('register_block_type')) {
            return;
        }

        register_block_type(DS_PLUGIN_DIR . 'resources/block/src', [
            'render_callback' => [$this, 'renderBlock']
        ]);
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
            'postTypes'  => $this->postTypeList(),
            'categories' => $this->categoryList(),
            'tags'       => $this->tagList(),
        ];

        ob_start();
        include DS_PLUGIN_DIR . 'resources/block/view.php';
        return ob_get_clean();
    }

    private function categoryList(): array
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

    private function postTypeList(): array
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

    private function tagList(): array
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

    public function search()
    {
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
                'errors' => $errors
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

    private function query($queryParams)
    {
        $args = [
            'post_status'    => 'publish',
            'posts_per_page' => 5,
            'paged'          => $queryParams['currentPage'] ?? 1,
        ];

        if (!empty($queryParams['postTypes'])) {
            $args['post_type'] = $queryParams['postTypes'];
        }

        if (!empty($queryParams['s'])) {
            $args['s'] = sanitize_text_field($queryParams['s']);
        }

        if (!empty($queryParams['cats']) && is_array($queryParams['cats'])) {
            $args['category__in'] = $queryParams['cats'];
        }

        if (!empty($queryParams['tags']) && is_array($queryParams['tags'])) {
            $args['tag__in'] = $queryParams['tags'];
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
        if ($queryParams['currentPage'] < $query->max_num_pages) {
            $nextPage = $queryParams['currentPage'] + 1;
        }
        $prevPage = $queryParams['currentPage'] - 1;

        return [
            'posts'      => $posts,
            'totalPosts' => $query->found_posts,
            'totalPage'  => $query->max_num_pages,
            'nextPage'   => $nextPage,
            'prevPage'   => $prevPage,
        ];
    }
}
