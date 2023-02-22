<?php
    
namespace Flixon\Routing;

use ReflectionClass;
use ReflectionMethod;
use Symfony\Component\Routing\Loader\AnnotationClassLoader;
use Symfony\Component\Routing\Route;
 
class AnnotatedRouteControllerLoader extends AnnotationClassLoader {
    protected function configureRoute(Route $route, ReflectionClass $class, ReflectionMethod $method, object $routeAnnotation) {
        // Set the controller and annotations for the route.
        $route->setDefault('_controller', $class->getName() . '::' . $method->getName());
        $route->setDefault('_annotations', array_merge(array_map(fn($attribute) => $attribute->newInstance(), $class->getAttributes()), array_map(fn($attribute) => $attribute->newInstance(), $method->getAttributes())));
    }
}