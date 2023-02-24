<?php

namespace Flixon\Mvc\Middleware;

use Flixon\Common\Collections\Enumerable;
use Flixon\DependencyInjection\Container;
use Flixon\Foundation\Middleware;
use Flixon\Http\Request;
use Flixon\Http\Response;
use Flixon\Mvc\ControllerResolver;
use Flixon\Mvc\Annotations\Layout;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use ReflectionMethod;

class ControllerMiddleware extends Middleware {
    private Container $container;

    public function __construct(Container $container) {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, callable $next = null) {
        // Create the controller and argument resolver.
        $controllerResolver = new ControllerResolver($this->container);
        $argumentResolver = new ArgumentResolver();

        // Get the controller and the arguments.
        $controller = $controllerResolver->getController($request);
        $arguments = $argumentResolver->getArguments($request, $controller);

        // Set the response against the controller.
        $controller[0]->response = $response;

        // Set the layout (make sure it uses the last annotation (against the method and not the class) if multiple found).
        if ($request->attributes->has('_annotations') && $annotation = Enumerable::from($request->attributes->get('_annotations'))->first(fn($a) => $a instanceof Layout)) {
            $controller[0]->view->layout = $annotation->layout;
        }

        // Set the url parameters against the node (if applicable).
        if ($request->node) {
            // Get the parameter keys.
            $keys = array_map(fn($p) => $p->getName(), (new ReflectionMethod($request->attributes->get('_controller')))->getParameters());

            // Set the url parameters (including the keys). This can then be fed into the UrlGenerator.
            $request->node->urlParameters = array_combine($keys, $arguments);
        }

        // Call the method against the controller.
        $response = call_user_func_array($controller, $arguments);

        return $next($request, $response, $next);
    }
}