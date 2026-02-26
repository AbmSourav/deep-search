<?php

namespace DeepSearch\App\Services;

use Codesvault\Validator\Validator;
use DeepSearch\App\Lib\BaseService;

if (! defined('ABSPATH')) exit;

class SearchConfigs implements BaseService
{
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
                'nonce'          => 'required|string',
                'action'         => 'required|stringOnly',
                'postPerPage'    => 'integer',
                'showPagination' => 'bool',
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

        $postPerPage = $data['postPerPage'] ?? 5;
        $showPagination = $data['showPagination'] ?? 1;
        update_option('ds_configs', [
            'posts_per_page'  => absint($postPerPage),
            'show_pagination' => wp_validate_boolean($showPagination)
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
        if (! $configs || empty($configs)) {
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
