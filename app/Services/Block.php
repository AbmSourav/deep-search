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
        // if minimum PHP version is not met, do not register block and return early
        if (phpversion() < DS_MIN_PHP_VERSION) {
            return;
        }

        // register block
        add_action('init', [$this, 'registerBlocks']);

        // REST API and AJAX handlers
        add_action('rest_api_init', [$this, 'restApi']);
        add_action('wp_ajax_search', [$this, 'search']);
        add_action('wp_ajax_nopriv_search', [$this, 'search']);

        // Cache invalidation
        add_action('save_post', [$this, 'invalidateSearchCache']);
        add_action('delete_post', [$this, 'invalidateSearchCache']);
        add_action('created_term', [$this, 'invalidateTaxonomyCache']);
        add_action('edited_term', [$this, 'invalidateTaxonomyCache']);
        add_action('delete_term', [$this, 'invalidateTaxonomyCache']);
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
        $cached = get_transient('ds_categories');
        if ($cached !== false) {
            return $cached;
        }

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

        // store cache data
        set_transient('ds_categories', $list, 3600);

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
        $cached = get_transient('ds_tags');
        if ($cached !== false) {
            return $cached;
        }

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

        // store cache data
        set_transient('ds_tags', $list, 3600);

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
        $cacheEnabled = $searchConfigs['cache_enabled'] ?? 1;

        $queryParams['currentPage'] = isset($queryParams['currentPage']) ? absint($queryParams['currentPage']) : 1;
        $args = [
            'post_status'    => 'publish',
            'posts_per_page' => $searchConfigs['posts_per_page'] ?? 5,
            'paged'          => $queryParams['currentPage'],
        ];

        if (!empty($queryParams['postTypes'])) {
            $args['post_type'] = explode(',', $queryParams['postTypes']);
        }

        if (!empty($queryParams['s'])) {
            $args['s'] = $queryParams['s'];
        }

        if (!empty($queryParams['cats'])) {
            $args['category__in'] = explode(',', $queryParams['cats']);
        }

        if (!empty($queryParams['tags'])) {
            $args['tag__in'] = explode(',', $queryParams['tags']);
        }

        // Check transient cache
        $cacheKey = null;
        if ($cacheEnabled) {
            $cacheKey = 'ds_q_' . md5(wp_json_encode($args));
            $cached = get_transient($cacheKey);
            if ($cached !== false) {
                return $cached;
            }
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

        $result = [
            'posts'      => $posts,
            'totalPosts' => $query->found_posts,
            ...$this->pagination($searchConfigs, $query, $queryParams),
        ];

        // Store in transient cache
        if ($cacheEnabled && $cacheKey) {
            $ttl = ($searchConfigs['cache_ttl'] ?? 15) * 60;
            set_transient($cacheKey, $result, $ttl);
        }

        return $result;
    }

    public function invalidateSearchCache($postId = null)
    {
        if ($postId) {
            if (wp_is_post_revision($postId) || wp_is_post_autosave($postId)) {
                return;
            }
        }
        $this->deleteSearchTransients();
    }

    protected function deleteSearchTransients()
    {
        global $wpdb;
        $wpdb->query($wpdb->prepare(
            "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s OR option_name LIKE %s",
            $wpdb->esc_like('_transient_ds_q_') . '%',
            $wpdb->esc_like('_transient_timeout_ds_q_') . '%'
        ));
    }

    public function invalidateTaxonomyCache()
    {
        delete_transient('ds_categories');
        delete_transient('ds_tags');
        $this->invalidateSearchCache();
    }

    protected function pagination($searchConfigs, $query, $params)
    {
        $nextPage = 0;
        $prevPage = 0;
        if (! empty($searchConfigs['show_pagination'])) {
            if ($params['currentPage'] < $query->max_num_pages) {
                $nextPage = $params['currentPage'] + 1;
            }
            $prevPage = $params['currentPage'] - 1;
        }
        if (empty($searchConfigs)) {
            if ($params['currentPage'] < $query->max_num_pages) {
                $nextPage = $params['currentPage'] + 1;
            }
            $prevPage = $params['currentPage'] - 1;
        }

        return [
            'totalPage' => $query->max_num_pages,
            'nextPage'  => $nextPage,
            'prevPage'  => $prevPage,
        ];
    }
}
