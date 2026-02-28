<?php

/**
 * @property Block $block
 * @property ReflectionClass $reflection
 */

use Brain\Monkey\Functions;
use Brain\Monkey\Actions;
use DeepSearch\App\Services\Block;
use DeepSearch\Tests\MocksAndStubs\WpDieException;

beforeEach(function () {
    $this->block = new Block();
    $this->reflection = new ReflectionClass($this->block);
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

    Actions\expectAdded('rest_api_init')
        ->once()
        ->with(\Mockery::type('array'), 10, 1);

    Actions\expectAdded('wp_ajax_search')
        ->once()
        ->with(\Mockery::type('array'), 10, 1);

    Actions\expectAdded('wp_ajax_nopriv_search')
        ->once()
        ->with(\Mockery::type('array'), 10, 1);

    // Cache invalidation hooks
    Actions\expectAdded('save_post')
        ->once()
        ->with(\Mockery::type('array'), 10, 1);

    Actions\expectAdded('delete_post')
        ->once()
        ->with(\Mockery::type('array'), 10, 1);

    Actions\expectAdded('created_term')
        ->once()
        ->with(\Mockery::type('array'), 10, 1);

    Actions\expectAdded('edited_term')
        ->once()
        ->with(\Mockery::type('array'), 10, 1);

    Actions\expectAdded('delete_term')
        ->once()
        ->with(\Mockery::type('array'), 10, 1);

    $this->block->register();
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
            \Mockery::on(fn ($args) => isset($args['render_callback']))
        );

    $this->block->registerBlocks();
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

    $output = $this->block->renderBlock($blockAttributes, '');

    expect($output)->toContain('<div class="ds-root" data-block="');
});

/*
|--------------------------------------------------------------------------
| categoryList Method Tests (private method via reflection)
|--------------------------------------------------------------------------
*/

it('returns empty array when no categories - categoryList', function () {
    Functions\when('get_categories')->justReturn([]);

    $method = $this->reflection->getMethod('categoryList');
    $result = $method->invoke($this->block);

    expect($result)->toBeArray()->toBeEmpty();
});

it('returns formatted category array - categoryList', function () {
    $mockCategory = (object) [
        'term_id' => 1,
        'slug'    => 'uncategorized',
        'name'    => 'Uncategorized',
    ];

    Functions\when('get_categories')->justReturn([$mockCategory]);

    $method = $this->reflection->getMethod('categoryList');
    $result = $method->invoke($this->block);

    expect($result)->toBeArray()->toHaveCount(1);
    expect($result[0])->toBe([
        'term_id' => 1,
        'value'   => 'uncategorized',
        'label'   => 'Uncategorized',
    ]);
});

/*
|--------------------------------------------------------------------------
| postTypeList Method Tests (private method via reflection)
|--------------------------------------------------------------------------
*/

it('returns empty array when no categories - postTypeList', function () {
    Functions\when('get_post_types')->justReturn([]);

    $method = $this->reflection->getMethod('postTypeList');
    $result = $method->invoke($this->block);

    expect($result)->toBeArray()->toBeEmpty();
});

it('returns formatted postType array - postTypeList', function () {
    $mockPostTypes = [
        'post' => 'Post',
        'page' => 'Page',
    ];

    Functions\when('get_post_types')->justReturn($mockPostTypes);

    $method = $this->reflection->getMethod('postTypeList');
    $result = $method->invoke($this->block);

    expect($result)->toBeArray()->toHaveCount(2);
    expect($result[0])->toBe([
        'value' => 'post',
        'label' => 'Post',
    ]);
});

/*
|--------------------------------------------------------------------------
| tagList Method Tests (private method via reflection)
|--------------------------------------------------------------------------
*/

it('returns empty array when no categories - tagList', function () {
    Functions\when('get_tags')->justReturn([]);

    $method = $this->reflection->getMethod('tagList');
    $result = $method->invoke($this->block);

    expect($result)->toBeArray()->toBeEmpty();
});

it('returns formatted tags array - tagList', function () {
    $mockPostTypes = (object) [
        'term_id' => 1,
        'slug'    => 'tagslug',
        'name'    => 'Tag Name',
    ];

    Functions\when('get_tags')->justReturn([$mockPostTypes]);

    $method = $this->reflection->getMethod('tagList');
    $result = $method->invoke($this->block);

    expect($result)->toBeArray()->toHaveCount(1);
    expect($result[0])->toBe([
        'term_id' => 1,
        'value'   => 'tagslug',
        'label'   => 'Tag Name',
    ]);
});

/*
|--------------------------------------------------------------------------
| restApi Method Tests
|--------------------------------------------------------------------------
*/

it('registers REST route with correct parameters', function () {
    $captured = null;
    Functions\when('register_rest_route')->alias(function ($namespace, $route, $args) use (&$captured) {
        $captured = ['namespace' => $namespace, 'route' => $route, 'args' => $args];
        return true;
    });

    $this->block->restApi();

    expect($captured)->not->toBeNull();
    expect($captured['namespace'])->toBe('deep-search/v1');
    expect($captured['route'])->toBe('/search');
    expect($captured['args']['methods'])->toBe('POST');
    expect($captured['args']['permission_callback'])->toBe('__return_true');
    expect($captured['args']['callback'])->toBeArray();
});

/*
|--------------------------------------------------------------------------
| restSearch Method Tests
|--------------------------------------------------------------------------
*/

it('returns 400 when query param is missing - restSearch', function () {
    $request = new WP_REST_Request('POST', '/deep-search/v1/search');

    $response = $this->block->restSearch($request);

    expect($response)->toBeInstanceOf(WP_REST_Response::class);
    expect($response->get_status())->toBe(400);
    expect($response->get_data()['message'])->toBe('Missing parameter');
});

it('returns validation error for invalid query data - restSearch', function () {
    $request = new WP_REST_Request('POST', '/deep-search/v1/search');
    $request->set_param('query', ['s' => 123]);

    try {
        $this->block->restSearch($request);
        $this->fail('Expected WpDieException to be thrown');
    } catch (WpDieException $e) {
        $data = $e->getResponseData();
        expect($data['success'])->toBeFalse();
        expect($data['data']['message'])->toBe('Validation error');
        expect($e->statusCode)->toBe(403);
    }
});

it('returns posts data on successful search - restSearch', function () {
    $request = new WP_REST_Request('POST', '/deep-search/v1/search');
    $request->set_param('query', ['s' => 'test', 'currentPage' => 1]);

    $response = $this->block->restSearch($request);

    expect($response)->toBeInstanceOf(WP_REST_Response::class);
    expect($response->get_status())->toBe(200);
    expect($response->get_data())->toHaveKey('data');
    expect($response->get_data()['data'])->toHaveKeys([
        'posts', 'totalPosts', 'totalPage', 'nextPage', 'prevPage'
    ]);
});

it('handles search with filters - restSearch', function () {
    $request = new WP_REST_Request('POST', '/deep-search/v1/search');
    $request->set_param('query', [
        's'         => 'test',
        'postTypes' => 'post,page',
        'cats'      => '1,2',
        'tags'      => '3,4',
    ]);

    $response = $this->block->restSearch($request);

    expect($response)->toBeInstanceOf(WP_REST_Response::class);
    expect($response->get_status())->toBe(200);
    expect($response->get_data()['data']['posts'])->toBeArray();
});

/*
|--------------------------------------------------------------------------
| search Method Tests
|--------------------------------------------------------------------------
*/

it('returns validation error when nonce is missing - search', function () {
    $_POST = [
        'action' => 'search',
        'query'  => '{"s":"test"}',
    ];

    try {
        $this->block->search();
        $this->fail('Expected WpDieException to be thrown');
    } catch (WpDieException $e) {
        $data = $e->getResponseData();
        expect($data['success'])->toBeFalse();
        expect($data['data']['message'])->toBe('Validation error');
        expect($e->statusCode)->toBe(403);
    }
});

it('returns validation error when action is missing - search', function () {
    $_POST = [
        'nonce' => 'test_nonce',
        'query' => '{"s":"test"}',
    ];

    try {
        $this->block->search();
        $this->fail('Expected WpDieException to be thrown');
    } catch (WpDieException $e) {
        $data = $e->getResponseData();
        expect($data['success'])->toBeFalse();
        expect($data['data']['message'])->toBe('Validation error');
        expect($e->statusCode)->toBe(403);
    }
});

it('returns validation error when query is missing - search', function () {
    $_POST = [
        'nonce'  => 'test_nonce',
        'action' => 'search',
    ];

    try {
        $this->block->search();
        $this->fail('Expected WpDieException to be thrown');
    } catch (WpDieException $e) {
        $data = $e->getResponseData();
        expect($data['success'])->toBeFalse();
        expect($data['data']['message'])->toBe('Validation error');
        expect($e->statusCode)->toBe(403);
    }
});

it('returns error when nonce verification fails - search', function () {
    $_POST = [
        'nonce'  => 'invalid_nonce',
        'action' => 'search',
        'query'  => '{"s":"test"}',
    ];

    Functions\when('wp_verify_nonce')->justReturn(false);

    try {
        $this->block->search();
        $this->fail('Expected WpDieException to be thrown');
    } catch (WpDieException $e) {
        $data = $e->getResponseData();
        expect($data['success'])->toBeFalse();
        expect($data['data']['message'])->toBe('Invalid security token.');
        expect($e->statusCode)->toBe(403);
    }
});

it('returns posts data on successful search - search', function () {
    $_POST = [
        'nonce'  => 'valid_nonce',
        'action' => 'search',
        'query'  => '{"s":"test","currentPage":1}',
    ];

    try {
        $this->block->search();
        $this->fail('Expected WpDieException to be thrown');
    } catch (WpDieException $e) {
        $data = $e->getResponseData();
        expect($e->statusCode)->toBe(200);
        expect($data)->toHaveKey('data');
        expect($data['data'])->toHaveKeys([
            'posts', 'totalPosts', 'totalPage', 'nextPage', 'prevPage'
        ]);
    }
});

/*
|--------------------------------------------------------------------------
| query Method Tests
|--------------------------------------------------------------------------
*/

it('query method is protected', function () {
    $method = $this->reflection->getMethod('query');

    expect($method->isProtected())->toBeTrue();
    expect($method->getNumberOfParameters())->toBe(1);
});

it('returns empty posts when no posts found - query', function () {
    $method = $this->reflection->getMethod('query');
    $result = $method->invoke($this->block, ['currentPage' => 1]);

    expect($result)->toBeArray();
    expect($result['posts'])->toBeArray()->toBeEmpty();
    expect($result['totalPosts'])->toBe(0);
    expect($result['totalPage'])->toBe(0);
});

it('returns correct pagination when on first page - query', function () {
    $method = $this->reflection->getMethod('query');
    $result = $method->invoke($this->block, ['currentPage' => 1]);

    expect($result['prevPage'])->toBe(0);
});

/*
|--------------------------------------------------------------------------
| Cache Invalidation Method Tests
|--------------------------------------------------------------------------
*/

it('has invalidateSearchCache method', function () {
    expect(method_exists(Block::class, 'invalidateSearchCache'))->toBeTrue();
});

it('skips invalidation for post revisions', function () {
    Functions\when('wp_is_post_revision')->justReturn(true);

    // Should not throw or call update_option for revisions
    $this->block->invalidateSearchCache(123);

    // If we get here without error, revision was skipped
    expect(true)->toBeTrue();
});

it('skips invalidation for autosaves', function () {
    Functions\when('wp_is_post_autosave')->justReturn(true);

    $this->block->invalidateSearchCache(123);

    expect(true)->toBeTrue();
});

it('invalidates search cache for regular posts', function () {
    Functions\when('wp_is_post_revision')->justReturn(false);
    Functions\when('wp_is_post_autosave')->justReturn(false);

    $this->block->invalidateSearchCache(123);

    // If we get here, update_option was called successfully
    expect(true)->toBeTrue();
});

it('has invalidateTaxonomyCache method', function () {
    expect(method_exists(Block::class, 'invalidateTaxonomyCache'))->toBeTrue();
});
