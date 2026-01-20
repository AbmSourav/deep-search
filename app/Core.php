<?php

namespace DeepSearch\App;

use DeepSearch\App\Lib\SingleTon;
use DeepSearch\App\Services\AdminMenu;
use DeepSearch\App\Services\AssetsManager;
use DeepSearch\App\Services\Block;

if (! defined('ABSPATH')) exit;

final class Core
{
    use SingleTon;

    public function __construct()
    {
        $this->boot();
    }

    public function boot()
    {
        foreach ($this->services() as $service) {
            $service::getInstance()->register();
        }
    }

    private function services(): array
    {
        return [
            AssetsManager::class,
            AdminMenu::class,
            Block::class,
        ];
    }
}
