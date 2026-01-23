<?php

use Brain\Monkey\Functions;
use Brain\Monkey\Actions;
use DeepSearch\App\Services\AdminMenu;

beforeEach(function () {
    // Reset singleton instance between tests
    $reflection = new ReflectionClass(AdminMenu::class);
    $instance = $reflection->getProperty('instance');
    $instance->setAccessible(true);
    $instance->setValue(null, null);
});

it('registers admin_menu action when in admin', function () {
    Functions\when('is_admin')->justReturn(true);

    Actions\expectAdded('admin_menu')
        ->once()
        ->with(\Mockery::type('array'), 10, 1);

    $adminMenu = AdminMenu::getInstance();
    $adminMenu->register();
});

it('does not register admin_menu action when not in admin', function () {
    Functions\when('is_admin')->justReturn(false);

    Actions\expectAdded('admin_menu')->never();

    $adminMenu = AdminMenu::getInstance();
    $adminMenu->register();
});

it('adds menu page with correct parameters', function () {
    Functions\when('__')->returnArg();

    Functions\expect('add_menu_page')
        ->once()
        ->with(
            'Deep Search',
            'Deep Search',
            'manage_options',
            'deep-search',
            \Mockery::type('array'),
            'dashicons-search',
            30
        );

    $adminMenu = AdminMenu::getInstance();
    $adminMenu->addMenu();
});
