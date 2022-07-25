<?php

namespace Flixon\Mvc;

use Flixon\Foundation\Application;
use Flixon\Foundation\Module;
use Flixon\Mvc\Middleware\ControllerMiddleware;

class MvcModule extends Module {
	public function register(Application $app) {
		// Add the middleware.
    	$app->middleware->add(ControllerMiddleware::class, 100);
    }
}