<?php

namespace Flixon\Http;

use Flixon\Foundation\Application;
use Flixon\Foundation\Module;
use Flixon\Http\Middleware\CookiesMiddleware;
use Flixon\Http\Middleware\ResponseCacheMiddleware;

class HttpModule extends Module {
    public function register(Application $app): void {
        // Add the middleware.
        $app->middleware->add(CookiesMiddleware::class, 300);
        $app->middleware->add(ResponseCacheMiddleware::class, 700);

        // Add the cookie and response cache vary by collections.
        $app->container->mapSingleton(CookieCollection::class);
        $app->container->mapSingleton(ResponseCacheVaryByCollection::class);
    }
}