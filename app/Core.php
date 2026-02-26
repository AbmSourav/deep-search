<?php

namespace DeepSearch\App;

use DeepSearch\App\Services\AdminMenu;
use DeepSearch\App\Services\AssetsManager;
use DeepSearch\App\Services\Block;
use DeepSearch\App\Services\SearchConfigs;

if (! defined('ABSPATH')) exit;

final class Core
{
    public function __construct()
    {
        $this->boot();
    }

    public function boot()
    {
        foreach ($this->services() as $service) {
            (new $service())->register();
        }
    }

    protected function services(): array
    {
        return [
            AssetsManager::class,
            AdminMenu::class,
            SearchConfigs::class,
            Block::class,
        ];
    }
}
