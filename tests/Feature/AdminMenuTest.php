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

/*
|--------------------------------------------------------------------------
| AdminMenu Class Tests
|--------------------------------------------------------------------------
*/

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

/*
|--------------------------------------------------------------------------
| Rendered view.php Tests
|--------------------------------------------------------------------------
*/

it('has admin view file', function () {
    $viewFile = DS_PLUGIN_DIR . 'resources/admin/view.php';

    expect($viewFile)->toBeFile();
});

it('renders admin page by requiring the view file', function () {
    $adminMenu = AdminMenu::getInstance();

    // Start output buffering to capture output from the view
    ob_start();
    $adminMenu->renderAdminPage();
    $output = ob_get_clean();

    // The view file should be included and produce the expected HTML structure
    expect($output)->toContain('<div id="ds-container"></div>');
});
