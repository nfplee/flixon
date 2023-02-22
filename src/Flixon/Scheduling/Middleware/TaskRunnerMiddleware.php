<?php

namespace Flixon\Scheduling\Middleware;

use Flixon\DependencyInjection\Container;
use Flixon\Foundation\Middleware;
use Flixon\Http\Request;
use Flixon\Http\Response;
use Flixon\Localization\Services\LanguageService;
use Flixon\Scheduling\TaskRunner;

class TaskRunnerMiddleware extends Middleware {
    private Container $container;
    private LanguageService $lang;

    public function __construct(Container $container, LanguageService $lang) {
        $this->container = $container;
        $this->lang = $lang;
    }

	public function __invoke(Request $request, Response $response, callable $next = null) {
        // Only execute if the url matches.
        if (strtolower($request->pathInfo) == '/task-runner') {
            // Execute the task runner.
            $this->container->get(TaskRunner::class)->execute();

            // Make sure the request is not cached.
            $request->attributes->remove('_cache');

            // Set the response.
            $response->content = $this->lang->get('Task runner successfully run.');

            return $response;
        }

        return $next($request, $response, $next);
    }
}