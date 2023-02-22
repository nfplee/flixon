<?php

namespace Flixon\Foundation\Traits;

use Flixon\DependencyInjection\Annotations\Inject;

trait Application {
	#[Inject(\Flixon\Foundation\Application::class)]
	protected $app;

	public function __get(string $name) {
    	if (property_exists($this->app, $name)) {
			return $this->app->$name;
    	} else {
    		return $this->app->container->get($name);
    	}
    }
}