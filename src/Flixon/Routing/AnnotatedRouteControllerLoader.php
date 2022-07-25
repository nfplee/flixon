<?php
    
namespace Flixon\Routing;

use Flixon\Routing\Route;
use ReflectionClass;
use ReflectionMethod;
use Symfony\Component\Routing\Loader\AnnotationClassLoader;
use Symfony\Component\Routing\Route as BaseRoute;
 
/**
 * Reference: https://github.com/sensiolabs/SensioFrameworkExtraBundle/blob/master/Routing/AnnotatedRouteControllerLoader.php
 */
class AnnotatedRouteControllerLoader extends AnnotationClassLoader {
	protected $routeAnnotationClass = \Flixon\Routing\Annotations\Route::class;

    protected function configureRoute(BaseRoute $route, ReflectionClass $class, ReflectionMethod $method, $routeAnnotation) {
        // Set the route priority.
        $route->priority = $routeAnnotation->priority;

    	// Set the controller and annotations for the route.
        $route->setDefault('_controller', $class->getName() . '::' . $method->getName());
        $route->setDefault('_annotations', array_merge($this->reader->getClassAnnotations($class), $this->reader->getMethodAnnotations($method)));
    }

    protected function createRoute($path, $defaults, $requirements, $options, $host, $schemes, $methods, $condition) {
    	return new Route($path, $defaults, $requirements, $options, $host, $schemes, $methods, $condition);
    }
}