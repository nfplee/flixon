<?php

namespace Flixon\Config;

use Flixon\Foundation\Application;
use Flixon\Foundation\Module;

class ConfigModule extends Module {
    public function register(Application $app) {
    	$config = $app->container->get('cache')->getOrAdd('config-' . $app->environment, function() use ($app) {
            // Create the config instance.
			$config = new Config(['environment' => $app->environment]);

	        // Load the config.
	        $config->load($app->rootPath . '/config');

            return $config;
        }, $app->environment == Application::PRODUCTION ? 60 * 60 : 60, false);

        // Add the config instance and an alias.
        $app->container->add(Config::class, $config)->map('config', Config::class);
    }
}