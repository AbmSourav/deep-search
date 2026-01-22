<?php

/**
 * @version 1.0.0
 * @package deep-search
 *
 * Plugin Name: Deep Search
 * Plugin URI:
 * Description: Search plugin for WordPress
 * Author: Keramot UL Islam
 * Author URI: https://abmsourav.com
 * Version: 1.0.0
 * Requires at least: 6.8
 * Requires PHP: 8.1
 * License URI:  https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * License: GPL v2 or later
 * Text Domain: deep-search
 */

use DeepSearch\App\Core;

if (! defined('ABSPATH')) exit;

define('DS_VERSION', '1.0.0');
define('DS_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('DS_PLUGIN_URL', plugins_url('/', __FILE__));

require __DIR__ . '/vendor/autoload.php';

Core::getInstance();
