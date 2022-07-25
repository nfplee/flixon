<?php

namespace Flixon\Mvc;

use Closure;
use Flixon\DependencyInjection\Container;
use ReflectionMethod;
use ReflectionObject;
use ReflectionFunction;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ControllerResolver as BaseControllerResolver;

class ControllerResolver extends BaseControllerResolver {
	private $container;

	public function __construct(Container $container, LoggerInterface $logger = null) {
        parent::__construct($logger);

        $this->container = $container;
    }

    /**
     * This overrides the base function to return a key/value array instead of an array of just the values.
     */
    public function getArguments(Request $request, $controller) {
        if (is_array($controller)) {
            $r = new ReflectionMethod($controller[0], $controller[1]);
        } elseif (is_object($controller) && !$controller instanceof Closure) {
            $r = new ReflectionObject($controller);
            $r = $r->getMethod('__invoke');
        } else {
            $r = new ReflectionFunction($controller);
        }

        return array_combine(array_map(function($p) { return $p->name; }, $r->getParameters()), $this->doGetArguments($request, $controller, $r->getParameters()));
    }

    protected function instantiateController($classname) {
        return $this->container->get($classname);
    }
}