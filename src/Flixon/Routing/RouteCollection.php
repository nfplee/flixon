<?php
    
namespace Flixon\Routing;

use Symfony\Component\Routing\RouteCollection as BaseRouteCollection;

class RouteCollection extends BaseRouteCollection {
    public function __construct(array $routes = []) {
        foreach ($routes as $key => $value) {
            $this->add($key, $value);
        }
    }

    public static function __set_state(array $array): RouteCollection {
        return new RouteCollection($array['routes']);
    }

	/**
     * This overrides the default to make sure the route doesn't end with a forward slash (this happens for the home page).
     * Investigate why this is needed. Without it the checkout login step doesn't work.
     */
    public function addPrefix($prefix, array $defaults = [], array $requirements = []): RouteCollection {
        foreach ($this->all() as $route) {
            $route->path = $prefix . rtrim($route->path, '/');
            $route->addDefaults($defaults);
            $route->addRequirements($requirements);
        }
 
        return $this;
    }
}