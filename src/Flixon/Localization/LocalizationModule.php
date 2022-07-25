<?php

namespace Flixon\Localization;

use Flixon\Foundation\Application;
use Flixon\Foundation\Module;
use Flixon\Localization\Middleware\LanguageLoaderMiddleware;
use Flixon\Localization\Middleware\LocalizationMiddleware;
use Flixon\Localization\Middleware\LocalizedUrlsMiddleware;
use Flixon\Localization\Services\LanguageService;

class LocalizationModule extends Module {
	public function register(Application $app) {
		// Add the middleware.
    	$app->middleware->add(LanguageLoaderMiddleware::class, 400);
    	$app->middleware->add(LocalizationMiddleware::class, 1000);
    	$app->middleware->add(LocalizedUrlsMiddleware::class, 500);

        // Register the language service.
        $app->container->mapSingleton(LanguageService::class)->map('lang', LanguageService::class);
    }

    public function registered(Application $app) {
    	// Get the config.
    	$config = $app->container->get('config');

    	// Add the locale prefix to the routes.
    	$app->container->get('routes')->addPrefix('/{_locale}', ['_locale' => ''], ['_locale' => $config->localization->locales . (!empty($config->localization->defaultLocale) ? '|' : '')]);
    }
}