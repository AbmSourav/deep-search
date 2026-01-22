<?php

namespace DeepSearch\App\Services;

use DeepSearch\App\Lib\BaseService;
use DeepSearch\App\Lib\SingleTon;

if (! defined('ABSPATH')) exit;

class AssetsManager implements BaseService
{
    use SingleTon;

    public function register()
    {
        add_action('admin_enqueue_scripts', [$this, 'enqueueAdminAssets']);
    }

    public function enqueueAdminAssets($route)
    {
        // Only load for plugin's admin page
        $allowed_pages = [
            'toplevel_page_deep-search',
        ];

        if (!in_array($route, $allowed_pages)) {
            return;
        }

        $asset_file = DS_PLUGIN_DIR . 'resources/admin/build/app.asset.php';
        if (!file_exists($asset_file)) {
            return;
        }
        $asset_data = require $asset_file;

        wp_enqueue_script(
            'ds-admin',
            DS_PLUGIN_URL . 'resources/admin/build/app.js',
            $asset_data['dependencies'],
            DS_VERSION,
            true
        );

        wp_enqueue_style(
            'ds-admin',
            DS_PLUGIN_URL . 'resources/admin/build/style-app.css',
            [],
            DS_VERSION
        );

        wp_localize_script(
            'ds-admin',
            'dsAdmin',
            [
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'nonce'   => wp_create_nonce('ds_admin_nonce'),
            ]
        );
    }
}
