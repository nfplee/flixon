<?php
    
namespace Flixon\Routing;

use Symfony\Component\Routing\Loader\AnnotationDirectoryLoader as BaseAnnotationDirectoryLoader;

class AnnotationDirectoryLoader extends BaseAnnotationDirectoryLoader {
	public function load($path, $type = null) {
        // Call the parent method to get the collection of routes.
        $routes = parent::load($path, $type)->all();

        // Sort the routes by the priority.
        uasort($routes, function(Route $a, Route $b) {
            if ($a->priority != $b->priority) {
                return $a->priority < $b->priority;
            } else {
                return strcmp($a->path, $b->path);
            }
        });

        // Create a new collection.
        $collection = new RouteCollection();

        // Add the sorted routes to the collection.
        foreach ($routes as $name => $route) {
            $collection->add($name, $route);
        }

        return $collection;
    }
}