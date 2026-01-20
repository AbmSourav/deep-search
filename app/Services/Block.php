<?php

namespace DeepSearch\App\Services;

use DeepSearch\App\Lib\BaseService;
use DeepSearch\App\Lib\SingleTon;

if (! defined('ABSPATH')) exit;

class Block implements BaseService
{
    use SingleTon;

    public function register()
    {
        add_action('init', [$this, 'registerBlocks']);
        // add_action('wp_enqueue_scripts', [$this, 'localizeBlockScript']);
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
        ob_start();
        include DS_PLUGIN_DIR . 'resources/block/view.php';
        return ob_get_clean();
    }

    // public function localizeBlockScript()
    // {
    //     if (! has_block('assistant-interface/chat-block')) {
    //         return;
    //     }

    //     wp_localize_script(
    //         'assistant-interface-chat-block-view-script',
    //         'aiAssistantData',
    //         [
    //             'ajaxUrl'   => admin_url('admin-ajax.php'),
    //             'nonce'     => wp_create_nonce('ai_assistant_nonce'),
    //             'siteUrl'   => get_site_url(),
    //             'pluginUrl' => DS_PLUGIN_URL,
    //         ]
    //     );
    // }
}
