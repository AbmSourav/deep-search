<?php

use DeepSearch\Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Bootstrap Files
|--------------------------------------------------------------------------
| Require mock and stub functionality before running tests
*/

require_once __DIR__ . '/MocksAndStubs/WpDieException.php';
require_once __DIR__ . '/MocksAndStubs/CommonMocks.php';
require_once __DIR__ . '/MocksAndStubs/WPQueryStub.php';
require_once __DIR__ . '/MocksAndStubs/WPRestStub.php';

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
*/

uses(TestCase::class)->in('Unit', 'Feature');
