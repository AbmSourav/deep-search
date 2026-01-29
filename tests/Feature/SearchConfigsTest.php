<?php

/**
 * @property SearchConfigs $searchConfigs
 * @property ReflectionClass $reflection
 */

use Brain\Monkey\Functions;
use Brain\Monkey\Actions;
use DeepSearch\App\Services\SearchConfigs;
use DeepSearch\Tests\MocksAndStubs\WpDieException;

beforeEach(function () {
    // Reset singleton instance between tests
    $reflection = new ReflectionClass(SearchConfigs::class);
    $instance = $reflection->getProperty('instance');
    $instance->setValue(null, null);

    // Set up shared test context
    $this->searchConfigs = SearchConfigs::getInstance();
});

/*
|--------------------------------------------------------------------------
| SearchConfigs Class Tests
|--------------------------------------------------------------------------
*/

it('has SearchConfigs class', function () {
    expect(SearchConfigs::class)->toBeString();
    expect(class_exists(SearchConfigs::class))->toBeTrue();
});

it('has register method', function () {
    expect(method_exists(SearchConfigs::class, 'register'))->toBeTrue();
});

it('registers ajax actions when in admin', function () {
    Functions\when('is_admin')->justReturn(true);

    Actions\expectAdded('wp_ajax_setConfigurations')
        ->once()
        ->with(\Mockery::type('array'), 10, 1);

    Actions\expectAdded('wp_ajax_getConfigurations')
        ->once()
        ->with(\Mockery::type('array'), 10, 1);

    $this->searchConfigs->register();
});

it('does not register ajax actions when not in admin', function () {
    Functions\when('is_admin')->justReturn(false);

    Actions\expectAdded('wp_ajax_setConfigurations')->never();
    Actions\expectAdded('wp_ajax_getConfigurations')->never();

    $this->searchConfigs->register();
});

/*
|--------------------------------------------------------------------------
| setConfigurations Method Tests
|--------------------------------------------------------------------------
*/

it('returns validation error when nonce is missing - setConfigurations', function () {
    $_POST = [
        'action'  => 'setConfigurations',
        'configs' => '{"postPerPage":10}',
    ];

    try {
        $this->searchConfigs->setConfigurations();
        $this->fail('Expected WpDieException to be thrown');
    } catch (WpDieException $e) {
        $data = $e->getResponseData();
        expect($data['success'])->toBeFalse();
        expect($data['data']['message'])->toBe('Validation error');
        expect($e->statusCode)->toBe(403);
    }
});

it('returns validation error when configs is missing - setConfigurations', function () {
    $_POST = [
        'nonce'  => 'test_nonce',
        'action' => 'setConfigurations',
    ];

    try {
        $this->searchConfigs->setConfigurations();
        $this->fail('Expected WpDieException to be thrown');
    } catch (WpDieException $e) {
        $data = $e->getResponseData();
        expect($data['success'])->toBeFalse();
        expect($data['data']['message'])->toBe('Validation error');
        expect($e->statusCode)->toBe(403);
    }
});

it('returns error when nonce verification fails - setConfigurations', function () {
    $_POST = [
        'nonce'   => 'invalid_nonce',
        'action'  => 'setConfigurations',
        'configs' => '{"postPerPage":10}',
    ];

    Functions\when('wp_verify_nonce')->justReturn(false);

    try {
        $this->searchConfigs->setConfigurations();
        $this->fail('Expected WpDieException to be thrown');
    } catch (WpDieException $e) {
        $data = $e->getResponseData();
        expect($data['success'])->toBeFalse();
        expect($data['data']['message'])->toBe('Invalid security token.');
        expect($e->statusCode)->toBe(403);
    }
});

it('saves configs successfully - setConfigurations', function () {
    $_POST = [
        'nonce'   => 'valid_nonce',
        'action'  => 'setConfigurations',
        'configs' => '{"postPerPage":10,"showPagination":true}',
    ];

    try {
        $this->searchConfigs->setConfigurations();
        $this->fail('Expected WpDieException to be thrown');
    } catch (WpDieException $e) {
        $data = $e->getResponseData();
        expect($data['success'])->toBeTrue();
        expect($data['data']['message'])->toBe('Configs stored');
        expect($e->statusCode)->toBe(200);
    }
});

/*
|--------------------------------------------------------------------------
| getConfigurations Method Tests
|--------------------------------------------------------------------------
*/

it('returns validation error when nonce is missing - getConfigurations', function () {
    $_POST = [
        'action' => 'getConfigurations',
    ];

    try {
        $this->searchConfigs->getConfigurations();
        $this->fail('Expected WpDieException to be thrown');
    } catch (WpDieException $e) {
        $data = $e->getResponseData();
        expect($data['success'])->toBeFalse();
        expect($data['data']['message'])->toBe('Validation error');
        expect($e->statusCode)->toBe(403);
    }
});

it('returns error when nonce verification fails - getConfigurations', function () {
    $_POST = [
        'nonce'  => 'invalid_nonce',
        'action' => 'getConfigurations',
    ];

    Functions\when('wp_verify_nonce')->justReturn(false);

    try {
        $this->searchConfigs->getConfigurations();
        $this->fail('Expected WpDieException to be thrown');
    } catch (WpDieException $e) {
        $data = $e->getResponseData();
        expect($data['success'])->toBeFalse();
        expect($data['data']['message'])->toBe('Invalid security token.');
        expect($e->statusCode)->toBe(403);
    }
});

it('returns default configs when no configs saved - getConfigurations', function () {
    $_POST = [
        'nonce'  => 'valid_nonce',
        'action' => 'getConfigurations',
    ];

    Functions\when('get_option')->justReturn(false);

    try {
        $this->searchConfigs->getConfigurations();
        $this->fail('Expected WpDieException to be thrown');
    } catch (WpDieException $e) {
        $data = $e->getResponseData();
        expect($data['success'])->toBeTrue();
        expect($data['data']['configs'])->toBe([
            'posts_per_page'  => 5,
            'show_pagination' => 1,
        ]);
        expect($e->statusCode)->toBe(200);
    }
});

it('returns saved configs - getConfigurations', function () {
    $_POST = [
        'nonce'  => 'valid_nonce',
        'action' => 'getConfigurations',
    ];

    $savedConfigs = [
        'posts_per_page'  => 10,
        'show_pagination' => true,
    ];

    Functions\when('get_option')->justReturn($savedConfigs);

    try {
        $this->searchConfigs->getConfigurations();
        $this->fail('Expected WpDieException to be thrown');
    } catch (WpDieException $e) {
        $data = $e->getResponseData();
        expect($data['success'])->toBeTrue();
        expect($data['data']['configs'])->toBe($savedConfigs);
        expect($e->statusCode)->toBe(200);
    }
});
