<?php

namespace Flixon\Foundation;

use Flixon\DependencyInjection\Container;
use Flixon\Foundation\Stopwatch;
use Flixon\Http\Pipeline;
use Flixon\Http\Request;
use Flixon\Http\Response;

class Application {
    const PRODUCTION = 'production';
    const TESTING = 'testing';
    const DEVELOPMENT = 'development';

    /**
     * The dependency injection container.
     *
     * @var \Flixon\DependencyInjection\Container
     */
    public $container;

    /**
     * The application environment.
     *
     * @var const
     */
    public $environment;

    /**
     * The http middleware.
     *
     * @var \Flixon\Foundation\MiddlewareCollection
     */
    public $middleware;

    /**
     * The application modules.
     *
     * @var \Flixon\Foundation\ModuleCollection
     */
    public $modules;

    /**
     * The root path.
     *
     * @var const
     */
    public $path;

    /**
     * The application stopwatch.
     *
     * @var \Flixon\Foundation\Stopwatch
     */
    public $stopwatch;

    /**
     * Whether the application's modules have been registered.
     *
     * @var bool
     */
    protected $registered = false;

    public function __construct(string $environment = Application::PRODUCTION, string $path = __DIR__ . '/../../..') {
        // Create and start the stopwatch.
        $this->stopwatch = (new Stopwatch())->start();

        // Create the container.
        $this->container = new Container($environment, $path);

        // Add the current instance (including an alias) and the container itself to the container.
        $this->container
            ->add(Application::class, $this)->map('app', Application::class)
            ->add(Container::class, $this->container);

        // Set the environment and root path.
        $this->environment = $environment;
        $this->path = $path;

        // Create the middleware collection.
        $this->middleware = new MiddlewareCollection();

        // Create the modules collection.
        $this->modules = new ModuleCollection();
    }

    public function handle(Request $request, Request $parent = null, $catch = true): Response {
        // Set the parent and whether we catch exceptions against the request.
        $request->parent = $parent;
        $request->catch = $catch;

        // Add the request to the container and an alias.
        $this->container->add(Request::class, $request)->map('request', Request::class);

        // Handle the request.
        return $this->container->get(Pipeline::class)->pipe($this->middleware)->handle($request);
    }

    public function register(): Application {
        // Register the modules.
        foreach ($this->modules as $module) {
            $module->register($this);
        }

        // Mark the application as registered.
        $this->registered = true;

        // Call the registered method against the modules.
        foreach ($this->modules as $module) {
            $module->registered($this);
        }

        return $this;
    }
    
    public function run(Request $request = null): Application {
        // If the request is null then create it from the global variables.
        if ($request === null) {
            $request = Request::createFromGlobals();
        }

        // Register the modules (if not done so already).
        if (!$this->registered) {
            $this->register();
        }

        // Handle the request.
        $response = $this->handle($request);

        // Send the response.
        $response->send();

        return $this;
    }

    public function terminate(): Application {
        // Terminate the modules.
        foreach ($this->modules as $module) {
            $module->terminate($this);
        }

        return $this;
    }
}