<?php

declare(strict_types=1);

namespace DeepSearch\Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Brain\Monkey;
use Brain\Monkey\Functions;
use function DeepSearch\Tests\MocksAndStubs\commonWordPressMocks;

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
        commonWordPressMocks();
    }

    protected function tearDown(): void
    {
        Monkey\tearDown();
        parent::tearDown();
    }
}
