<?php

declare(strict_types=1);

namespace DeepSearch\Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;
use wpdb;

/**
 * Base Test Case for Howdy QB Tests
 */
abstract class TestCase extends BaseTestCase
{
    /**
     * Current driver being used for testing
     * @var string
     */
    protected $currentDriver = 'pdo';

    /**
     * Mock wpdb object for testing
     * @var wpdb|\PHPUnit\Framework\MockObject\MockObject|null
     */
    protected $mockWpdb = null;

    protected function setUp(): void
    {
        parent::setUp();


    }

    protected function tearDown(): void
    {
        parent::tearDown();
        // Add any cleanup logic here
    }
}
