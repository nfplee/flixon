<?php

namespace Flixon\Scheduling;

use Flixon\Foundation\Application;
use Flixon\Foundation\Module;
use Flixon\Scheduling\Middleware\TaskRunnerMiddleware;

class SchedulingModule extends Module {
    public function register(Application $app): void {
        // Add the middleware.
        $app->middleware->add(TaskRunnerMiddleware::class, 200);

        // Register the task runner.
        $app->container->mapSingleton(TaskRunner::class);
    }
}