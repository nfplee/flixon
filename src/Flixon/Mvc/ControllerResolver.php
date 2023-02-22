<?php

namespace Flixon\Mvc;

use Flixon\DependencyInjection\Container;
use Symfony\Component\HttpKernel\Controller\ControllerResolver as ControllerResolverBase;

class ControllerResolver extends ControllerResolverBase {
	private Container $container;

	public function __construct(Container $container) {
        parent::__construct();

        $this->container = $container;
    }

    protected function instantiateController(string $class): object {
        return $this->container->get($class);
    }
}