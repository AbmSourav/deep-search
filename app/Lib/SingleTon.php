<?php

namespace DeepSearch\App\Lib;

if (! defined('ABSPATH')) exit;

trait SingleTon
{
    protected static $instance = null;

    public static function getInstance()
    {
        if (! self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }
}
