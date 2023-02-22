<?php

namespace Flixon\Http;

use Flixon\Common\Collections\PriorityQueue;
use Flixon\DependencyInjection\Container;
use Flixon\Foundation\MiddlewareCollection;

/**
 * Reference: https://github.com/phapi/pipeline/blob/master/src/Phapi/Middleware/Pipeline.php
 */
class Pipeline {
    private Container $container;
    private PriorityQueue $queue;

    public function __construct(Container $container) {
        $this->container = $container;
        $this->queue = new PriorityQueue();
    }

    public function __invoke(Request $request, Response $response, callable $next = null) {
        // Check if we are at the end of the queue.
        if (!$this->queue->isEmpty()) {
            // Get the next middleware from the queue.
            $middleware = $this->queue->extract();

            // Resolve the middleware (injecting the dependencies).
            $next = $this->container->get($middleware['class']);

            // Set the parameters.
            foreach ($middleware['parameters'] as $key => $value) {
                // Make sure the property exists.
                if (property_exists($next, $key)) {
                    $next->$key = $value;
                }
            }

            // Call the middleware.
            $next = $next($request, $response, $this);

            return $next;
        }

        // Return the response as we are at the end of the queue.
        return $response;
    }

    public function handle(Request $request): Response {
        return $this($request, new Response(), $this);
    }

    public function pipe(MiddlewareCollection $middleware): Pipeline {
        foreach ($middleware as $middleware) {
            $this->queue->insert($middleware, $middleware['priority']);
        }

        return $this;
    }
}