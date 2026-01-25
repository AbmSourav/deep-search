<?php

use Brain\Monkey\Functions;
use Brain\Monkey\Actions;
use DeepSearch\App\Services\Block;

beforeEach(function () {
    // Reset singleton instance between tests
    $reflection = new ReflectionClass(Block::class);
    $instance = $reflection->getProperty('instance');
    $instance->setValue(null, null);
});

/*
|--------------------------------------------------------------------------
| Block Class Tests
|--------------------------------------------------------------------------
*/

it('has Block class', function () {
    expect(Block::class)->toBeString();
    expect(class_exists(Block::class))->toBeTrue();
});

it('has register method', function () {
    expect(method_exists(Block::class, 'register'))->toBeTrue();
});

it('registers actions', function () {
    Actions\expectAdded('init')
        ->once()
        ->with(\Mockery::type('array'), 10, 1);

    Actions\expectAdded('wp_ajax_search')
        ->once()
        ->with(\Mockery::type('array'), 10, 1);

    Actions\expectAdded('wp_ajax_nopriv_search')
        ->once()
        ->with(\Mockery::type('array'), 10, 1);

    Block::getInstance()->register();
});

/*
|--------------------------------------------------------------------------
| registerBlocks Method Tests
|--------------------------------------------------------------------------
*/

it('has block directory', function () {
    expect(DS_PLUGIN_DIR . 'resources/block')->toBeDirectory();
});

it('has block.json file', function () {
    expect(DS_PLUGIN_DIR . 'resources/block/block.json')->toBeFile();
});

it('registers deep-search block', function () {
    Functions\expect('register_block_type')
        ->once()
        ->with(
            DS_PLUGIN_DIR . 'resources/block',
            \Mockery::on(fn($args) => isset($args['render_callback']))
        );

    Block::getInstance()->registerBlocks();
});

/*
|--------------------------------------------------------------------------
| renderBlock Method Tests
|--------------------------------------------------------------------------
*/

it('has view.php file', function () {
    expect(DS_PLUGIN_DIR . 'resources/block/view.php')->toBeFile();
});

it('renders the block view file', function () {
    Functions\when('get_post_types')->justReturn([]);

    $blockAttributes = [
        'showPostType' => false,
        'showCat'      => false,
        'showTag'      => false,
    ];

    $output = Block::getInstance()->renderBlock($blockAttributes, '');

    expect($output)->toContain('<div class="ds-root" data-block="');
});

/*
|--------------------------------------------------------------------------
| categoryList Method Tests (private method via reflection)
|--------------------------------------------------------------------------
*/

it('categoryList returns empty array when no categories', function () {
    Functions\when('get_categories')->justReturn([]);

    $block = Block::getInstance();
    $reflection = new ReflectionClass($block);
    $method = $reflection->getMethod('categoryList');

    $result = $method->invoke($block);

    expect($result)->toBeArray()->toBeEmpty();
});

it('categoryList returns formatted category array', function () {
    $mockCategory = (object) [
        'term_id' => 1,
        'slug'    => 'uncategorized',
        'name'    => 'Uncategorized',
    ];

    Functions\when('get_categories')->justReturn([$mockCategory]);

    $block = Block::getInstance();
    $reflection = new ReflectionClass($block);
    $method = $reflection->getMethod('categoryList');

    $result = $method->invoke($block);

    expect($result)->toBeArray()->toHaveCount(1);
    expect($result[0])->toBe([
        'term_id' => 1,
        'value'   => 'uncategorized',
        'label'   => 'Uncategorized',
    ]);
});
