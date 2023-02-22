<?php

namespace Flixon\Localization;

use Flixon\Foundation\Application;
use Flixon\Foundation\Module;
use Flixon\Localization\Middleware\LanguageLoaderMiddleware;
use Flixon\Localization\Middleware\LocalizationMiddleware;
use Flixon\Localization\Middleware\LocalizedUrlsMiddleware;
use Flixon\Localization\Services\LanguageService;

class LocalizationModule extends Module {
    public function register(Application $app): void {
		// Add the middleware.
    	$app->middleware->add(LanguageLoaderMiddleware::class, 400);
    	$app->middleware->add(LocalizationMiddleware::class, 1000);
    	$app->middleware->add(LocalizedUrlsMiddleware::class, 500);

        // Register the language service.
        $app->container->mapSingleton(LanguageService::class)->map('lang', LanguageService::class);
    }

	public function registered(Application $app): void {
    	// Get the config and routes.
    	$config = $app->container->get('config');
		$routes = $app->container->get('routes');

		// Add the locale prefix to the routes.
    	foreach ($routes->all() as $name => $route) {
            $routes->remove($name);
        
            foreach ($config->localization->locales as $locale => $localePrefix) {
                $localizedRoute = clone $route;
                $localizedRoute->setDefault('_locale', $locale);
                $localizedRoute->setRequirement('_locale', preg_quote($locale));
                $localizedRoute->setDefault('_canonical_route', $name);
                $localizedRoute->setPath($localePrefix . ($route->getPath() === '/' ? '' : $route->getPath()));
                $routes->add($name . ($localePrefix != '' ? '.' . $locale : ''), $localizedRoute);
            }
        }
    }
}