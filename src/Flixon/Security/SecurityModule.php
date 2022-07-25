<?php

namespace Flixon\Security;

use Flixon\Foundation\Application;
use Flixon\Foundation\Module;
use Flixon\Security\Middleware\AuthenticationMiddleware;
use Flixon\Security\Middleware\AuthorizationMiddleware;
use Flixon\Security\Services\AuthenticationService;
use Flixon\Security\Services\AuthorizationService;
use Flixon\Security\Services\CookieAuthenticationService;

class SecurityModule extends Module {
    public function register(Application $app) {
    	// Add the middleware.
    	$app->middleware->add(AuthenticationMiddleware::class, 1200);
    	$app->middleware->add(AuthorizationMiddleware::class, 800);

        // Map the services and add aliases.
		$app->container->map(AuthenticationService::class, CookieAuthenticationService::class)->map('auth', AuthenticationService::class);
    	$app->container->map('authorize', AuthorizationService::class);
    }
}