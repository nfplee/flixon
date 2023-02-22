<?php

namespace Flixon\Routing;

use Flixon\Foundation\Application;
use Flixon\Foundation\Environment;
use Flixon\Foundation\Module;
use Flixon\Routing\Middleware\UrlGeneratorMiddleware;
use Flixon\Routing\Middleware\UrlMatcherMiddleware;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Loader\AnnotationClassLoader;
use Symfony\Component\Routing\Loader\Psr4DirectoryLoader;
use Symfony\Component\Routing\RouteCollection;

class RoutingModule extends Module {
    public function register(Application $app): void {
    	// Add the middleware.
    	$app->middleware->add(UrlGeneratorMiddleware::class, 900);
    	$app->middleware->add(UrlMatcherMiddleware::class, 1100);

        // Get the routes.
    	$routes = $app->container->get('cache')->getOrAdd('routes', function() use ($app) {
            // Create the route collection.
            $routes = new RouteCollection();

            if (file_exists(__DIR__ . '/../../App/Controllers')) {
                $loader = new DelegatingLoader(
                    new LoaderResolver([
                        new Psr4DirectoryLoader(
                            new FileLocator()
                        ),
                        new AnnotatedRouteControllerLoader()
                    ])
                );
            
                $routes->addCollection($loader->load(['path' => __DIR__ . '/../../App/Controllers', 'namespace' => 'App\Controllers'], 'attribute'));
            }

            return $routes;
        }, $app->environment == Environment::PRODUCTION ? 60 * 60 : 60);

        // Register the routes and add an alias.
		$app->container->add(RouteCollection::class, $routes)->map('routes', RouteCollection::class);

        // Register the url generator and add an alias.
		$app->container->mapSingleton(UrlGenerator::class)->map('url', UrlGenerator::class);
    }
}