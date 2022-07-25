<?php
    
namespace Flixon\Routing\Middleware;

use Doctrine\Common\Annotations\Reader as AnnotationReader;
use Flixon\Foundation\Middleware;
use Flixon\Http\Request;
use Flixon\Http\Response;
use Flixon\Routing\RouteCollection;
use ReflectionMethod;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;

class UrlMatcherMiddleware extends Middleware {
    private $annotationReader, $routes;

    public function __construct(AnnotationReader $annotationReader, RouteCollection $routes) {
        $this->annotationReader = $annotationReader;
        $this->routes = $routes;
    }

    public function __invoke(Request $request, Response $response, callable $next = null) {
        // Make sure the controller hasn't already been set (this happens for child requests).
        if (!$request->attributes->has('_controller')) {
            // Create a context using the current request.
            $context = new RequestContext();
            $context->fromRequest($request);

            // Create the url matcher.
            $matcher = new UrlMatcher($this->routes, $context);

            // Try to get a matching route for the request and add the annotations (this will fire a ResourceNotFoundException if no route matched).
            $request->attributes->add($matcher->match($request->pathInfo));
        } else {
            // Get the class and method for the child request.
            list($class, $method) = explode('::', $request->attributes->get('_controller'));
            
            // Add the annotations for the child request (shouldn't need the class annotations).
            $request->attributes->set('_annotations', $this->annotationReader->getMethodAnnotations(new ReflectionMethod($class, $method)));
        }

        return $next($request, $response, $next);
    }
}