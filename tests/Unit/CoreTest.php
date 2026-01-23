<?php

use Brain\Monkey\Functions;

beforeEach(function () {
    // Stub the exit function that Core.php uses via ABSPATH check
});

it('can use brain monkey to mock wordpress functions', function () {
    Functions\when('esc_html')->returnArg();

    $result = esc_html('<script>alert("xss")</script>');

    expect($result)->toBe('<script>alert("xss")</script>');
});

it('can mock add_action', function () {
    Functions\expect('add_action')
        ->once()
        ->with('init', \Mockery::type('callable'));

    add_action('init', function () {
        return 'test';
    });
});
