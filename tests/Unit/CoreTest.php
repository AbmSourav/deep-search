<?php

use DeepSearch\App\Core;
use DeepSearch\App\Lib\BaseService;
use DeepSearch\App\Services\AdminMenu;
use DeepSearch\App\Services\AssetsManager;
use DeepSearch\App\Services\Block;
use DeepSearch\App\Services\SearchConfigs;

/*
|--------------------------------------------------------------------------
| Plugin File Structure Tests
|--------------------------------------------------------------------------
*/

it('has main plugin file', function () {
    expect(DS_PLUGIN_DIR . 'search.php')->toBeFile();
});

it('has autoload file', function () {
    expect(DS_PLUGIN_DIR . 'vendor/autoload.php')->toBeFile();
});

it('has composer.json file', function () {
    expect(DS_PLUGIN_DIR . 'composer.json')->toBeFile();
});

/*
|--------------------------------------------------------------------------
| Core Class Tests
|--------------------------------------------------------------------------
*/

it('has Core class', function () {
    expect(Core::class)->toBeString();
    expect(class_exists(Core::class))->toBeTrue();
});

it('Core class is final', function () {
    $reflection = new ReflectionClass(Core::class);

    expect($reflection->isFinal())->toBeTrue();
});

it('Core class uses SingleTon trait', function () {
    $traits = class_uses(Core::class);

    expect($traits)->toContain('DeepSearch\App\Lib\SingleTon');
});

/*
|--------------------------------------------------------------------------
| Base Library Classes Tests
|--------------------------------------------------------------------------
*/

it('has BaseService interface', function () {
    expect(interface_exists(BaseService::class))->toBeTrue();
});

it('BaseService interface has register method', function () {
    $reflection = new ReflectionClass(BaseService::class);

    expect($reflection->hasMethod('register'))->toBeTrue();
});

/*
|--------------------------------------------------------------------------
| Service Classes Tests
|--------------------------------------------------------------------------
*/

it('has AdminMenu service class', function () {
    expect(class_exists(AdminMenu::class))->toBeTrue();
});

it('has AssetsManager service class', function () {
    expect(class_exists(AssetsManager::class))->toBeTrue();
});

it('has Block service class', function () {
    expect(class_exists(Block::class))->toBeTrue();
});

it('has SearchConfigs service class', function () {
    expect(class_exists(SearchConfigs::class))->toBeTrue();
});
