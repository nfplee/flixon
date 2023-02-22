<?php

namespace Flixon\SiteMap;

use Flixon\Foundation\Application;
use Flixon\Foundation\Module;
use Flixon\SiteMap\Middleware\SiteMapNodeMiddleware;

class SiteMapModule extends Module {
    public function register(Application $app): void {
        // Add the middleware.
        $app->middleware->add(SiteMapNodeMiddleware::class, 600);
    }
}