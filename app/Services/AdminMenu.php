<?php

namespace DeepSearch\App\Services;

use DeepSearch\App\Lib\BaseService;
use DeepSearch\App\Lib\SingleTon;

class AdminMenu implements BaseService
{
    use SingleTon;

    public function register()
    {
        if (! is_admin()) {
            return;
        }

        add_action('admin_menu', [$this, 'addMenu']);
    }

    public function addMenu()
    {
        add_menu_page(
            __('Deep Search', 'deep-search'),
            __('Deep Search', 'deep-search'),
            'manage_options',
            'deep-search',
            [$this, 'renderAdminPage'],
            'dashicons-search',
            30
        );
    }

    public function renderAdminPage()
    {
        require_once DS_PLUGIN_DIR . 'resources/admin/view.php';
    }
}
