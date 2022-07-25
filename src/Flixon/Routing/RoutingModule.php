<?php

namespace Flixon\Routing;

use Doctrine\Common\Annotations\Reader as AnnotationReader;
use Flixon\Foundation\Application;
use Flixon\Foundation\Module;
use Flixon\Routing\Middleware\UrlGeneratorMiddleware;
use Flixon\Routing\Middleware\UrlMatcherMiddleware;
use Symfony\Component\Config\FileLocator;

class RoutingModule extends Module {
    public function register(Application $app) {
    	// Add the middleware.
    	$app->middleware->add(UrlGeneratorMiddleware::class, 900);
    	$app->middleware->add(UrlMatcherMiddleware::class, 1100);

        // Get the routes.
    	$routes = $app->container->get('cache')->getOrAdd('routes', function() use ($app) {
            // Create the route collection.
            $routes = new RouteCollection();

            // Initialize the annotation loader.
            $loader = new AnnotationDirectoryLoader(new FileLocator(), new AnnotatedRouteControllerLoader($app->container->get(AnnotationReader::class)));

            // Add the routes.
            if (file_exists(__DIR__ . '/../../../app/Controllers')) {
                $routes->addCollection($loader->load(__DIR__ . '/../../../app/Controllers')); // Note: Remove /Controllers to make this more modular.
            }

            return $routes;
        }, $app->environment == Application::PRODUCTION ? 60 * 60 : 60, false);

        // Register the routes and add an alias.
		$app->container->add(RouteCollection::class, $routes)->map('routes', RouteCollection::class);

        // Register the url generator and add an alias.
		$app->container->mapSingleton(UrlGenerator::class)->map('url', UrlGenerator::class);
    }
}