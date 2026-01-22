<?php

namespace DeepSearch\App\Services;

use Codesvault\Validator\Validator;
use DeepSearch\App\Lib\BaseService;
use DeepSearch\App\Lib\SingleTon;

if (! defined('ABSPATH')) exit;

class SearchConfigs implements BaseService
{
    use SingleTon;

    public function register()
    {
        if (! is_admin()) {
            return;
        }

        add_action('wp_ajax_setConfigurations', [$this, 'setConfigurations']);
        add_action('wp_ajax_getConfigurations', [$this, 'getConfigurations']);
    }

    public function setConfigurations()
    {
        $validator = Validator::validate(
            [
                'nonce'   => 'required|string',
                'action'  => 'required|stringOnly',
                'configs' => 'required|string',
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

        if (
            !isset($data['nonce']) ||
            !wp_verify_nonce($data['nonce'], 'ds_admin_nonce')
        ) {
            wp_send_json_error([
                'message' => 'Invalid security token.'
            ], 403);
        }

        $configs = json_decode(wp_unslash($data['configs']), true);
        $postPerPage = $configs['postPerPage'] ?? 5;
        $showPagination = $configs['showPagination'] ?? true;
        update_option('ds_configs', [
            'posts_per_page'  => sanitize_text_field($postPerPage),
            'show_pagination' => sanitize_text_field($showPagination)
        ]);

        wp_send_json_success([
           'message' => 'Configs stored',
        ]);
    }

    public function getConfigurations()
    {
        $validator = Validator::validate(
            [
                'nonce'  => 'required|string',
                'action' => 'required|stringOnly',
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

        if (
            !isset($data['nonce']) ||
            !wp_verify_nonce($data['nonce'], 'ds_admin_nonce')
        ) {
            wp_send_json_error([
                'message' => 'Invalid security token.'
            ], 403);
        }

        $configs = get_option('ds_configs');
        if (! $configs) {
            $configs = [
                'posts_per_page'  => 5,
                'show_pagination' => 1
            ];
        }

        wp_send_json_success([
            'configs' => $configs,
        ]);
    }
}
