<?php

namespace DeepSearch\Tests\MocksAndStubs;

use Brain\Monkey\Functions;

function commonWordPressMocks(): void
{
    // Translation functions - return first argument
    Functions\when('__')->returnArg();
    Functions\when('_e')->returnArg();
    Functions\when('esc_html__')->returnArg();
    Functions\when('esc_html_e')->returnArg();
    Functions\when('esc_attr__')->returnArg();
    Functions\when('esc_attr_e')->returnArg();
    Functions\when('_n')->returnArg();
    Functions\when('_x')->returnArg();

    // Escaping functions - return first argument
    Functions\when('esc_html')->returnArg();
    Functions\when('esc_attr')->returnArg();
    Functions\when('esc_url')->returnArg();
    Functions\when('esc_js')->returnArg();
    Functions\when('esc_textarea')->returnArg();
    Functions\when('sanitize_text_field')->returnArg();
    Functions\when('sanitize_title')->returnArg();
    Functions\when('wp_kses_post')->returnArg();

    // Nonce functions
    Functions\when('wp_create_nonce')->justReturn('test_nonce');
    Functions\when('wp_verify_nonce')->justReturn(true);
    Functions\when('check_ajax_referer')->justReturn(true);

    // URL/Path functions
    $baseUrl = 'http://example.com';
    Functions\when('plugin_dir_url')->justReturn($baseUrl . '/wp-content/plugins/deep-search/');
    Functions\when('plugin_dir_path')->justReturn(DS_PLUGIN_DIR);
    Functions\when('plugins_url')->justReturn($baseUrl . '/wp-content/plugins/');
    Functions\when('admin_url')->alias(fn($path = '') => $baseUrl . '/wp-admin/' . $path);
    Functions\when('home_url')->alias(fn($path = '') => $baseUrl . '/' . $path);
    Functions\when('site_url')->alias(fn($path = '') => $baseUrl . '/' . $path);
    Functions\when('get_site_url')->justReturn($baseUrl);

    // Options functions
    Functions\when('get_option')->justReturn(false);
    Functions\when('update_option')->justReturn(true);
    Functions\when('delete_option')->justReturn(true);

    // Hooks - do nothing by default
    Functions\when('do_action')->justReturn(null);
    Functions\when('apply_filters')->returnArg(2);
    Functions\when('did_action')->justReturn(0);

    // Misc common functions
    Functions\when('wp_json_encode')->alias('json_encode');
    Functions\when('absint')->alias('intval');
    Functions\when('wp_unslash')->returnArg();
    Functions\when('wp_parse_args')->alias(fn($args, $defaults) => array_merge($defaults, (array) $args));

    // AJAX response functions - throw exception to simulate die()
    Functions\when('wp_send_json_error')->alias(function ($data, $status = 400) {
        throw new WpDieException(json_encode(['success' => false, 'data' => $data]), $status);
    });
    Functions\when('wp_send_json_success')->alias(function ($data, $status = 200) {
        throw new WpDieException(json_encode(['success' => true, 'data' => $data]), $status);
    });
    Functions\when('wp_send_json')->alias(function ($data, $status = 200) {
        throw new WpDieException(json_encode($data), $status);
    });

    Functions\when('wp_reset_postdata')->justReturn(null);
}
