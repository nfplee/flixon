<?php

namespace Flixon\Mvc\Middleware;

use Flixon\DependencyInjection\Container;
use Flixon\Foundation\Middleware;
use Flixon\Http\Request;
use Flixon\Http\Response;
use Flixon\Mvc\Annotations\Layout;
use Flixon\Mvc\ControllerResolver;

class ControllerMiddleware extends Middleware {
    private $container;

    public function __construct(Container $container) {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, callable $next = null) {
        // Create the controller resolver.
        $resolver = new ControllerResolver($this->container);

        // Get the controller and the arguments.
        $controller = $resolver->getController($request);
        $arguments = $resolver->getArguments($request, $controller);

        // Set the url parameters against the node (if applicable).
        if ($request->node) {
            $request->node->urlParameters = $arguments;
        }

        // Set the response against the controller.
        $controller[0]->response = $response;

        // Set the layout (make sure it uses the last annotation (against the method and not the class) if multiple found).
        if ($request->attributes->has('_annotations')) {
            foreach ($request->attributes->get('_annotations') as $annotation) {
                if ($annotation instanceof Layout) {
                    $controller[0]->view->layout = $annotation->layout;
                }
            }
        }

        // Call the method against the controller.
        $response = call_user_func_array($controller, $arguments);

        return $next($request, $response, $next);
    }
}