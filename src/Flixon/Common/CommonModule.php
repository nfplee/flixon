<?php

namespace Flixon\Common;

use Flixon\Common\Services\CachingService;
use Flixon\Common\Services\FileCachingService;
use Flixon\Foundation\Application;
use Flixon\Foundation\Module;

class CommonModule extends Module {
    public function register(Application $app) {
		// Register the cache service.
        $app->container->mapSingleton(CachingService::class, FileCachingService::class)->map('cache', CachingService::class);
    }
}