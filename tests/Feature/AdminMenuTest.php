<?php

use Brain\Monkey\Functions;
use Brain\Monkey\Actions;
use DeepSearch\App\Services\AdminMenu;

beforeEach(function () {
    $this->adminMenu = new AdminMenu();
});

/*
|--------------------------------------------------------------------------
| AdminMenu Class Tests
|--------------------------------------------------------------------------
*/

it('has AdminMenu class', function () {
    expect(AdminMenu::class)->toBeString();
    expect(class_exists(AdminMenu::class))->toBeTrue();
});

it('registers admin_menu action when in admin', function () {
    Functions\when('is_admin')->justReturn(true);

    Actions\expectAdded('admin_menu')
        ->once()
        ->with(\Mockery::type('array'), 10, 1);

    $this->adminMenu->register();
});

it('does not register admin_menu action when not in admin', function () {
    Functions\when('is_admin')->justReturn(false);

    Actions\expectAdded('admin_menu')->never();

    $this->adminMenu->register();
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

    $this->adminMenu->addMenu();
});

/*
|--------------------------------------------------------------------------
| Rendered view.php Tests
|--------------------------------------------------------------------------
*/

it('has admin view file', function () {
    expect(DS_PLUGIN_DIR . 'resources/admin/view.php')->toBeFile();
});

it('renders admin page by requiring the view file', function () {
    // Start output buffering to capture output from the view
    ob_start();
    $this->adminMenu->renderAdminPage();
    $output = ob_get_clean();

    // The view file should be included and produce the expected HTML structure
    expect($output)->toContain('<div id="ds-container"></div>');
});
