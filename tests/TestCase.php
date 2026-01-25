<?php

declare(strict_types=1);

namespace DeepSearch\Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Brain\Monkey;
use Brain\Monkey\Functions;

abstract class TestCase extends BaseTestCase
{
    use MockeryPHPUnitIntegration;

    protected function setUp(): void
    {
        parent::setUp();
        Monkey\setUp();

        // Define ABSPATH if not defined
        if (!defined('ABSPATH')) {
            define('ABSPATH', '/var/www/html/');
        }

        // Define plugin constants if not defined (mirroring search.php)
        if (!defined('DS_VERSION')) {
            define('DS_VERSION', '1.0.0');
        }
        if (!defined('DS_PLUGIN_DIR')) {
            define('DS_PLUGIN_DIR', dirname(__DIR__) . '/');
        }
        if (!defined('DS_PLUGIN_URL')) {
            define('DS_PLUGIN_URL', 'http://example.com/wp-content/plugins/deep-search/');
        }

        // commonly used WordPress function mocks
        $this->setupCommonWordPressMocks();
    }

    protected function setupCommonWordPressMocks(): void
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
    }

    protected function tearDown(): void
    {
        Monkey\tearDown();
        parent::tearDown();
    }
}
