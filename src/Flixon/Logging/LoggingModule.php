<?php

namespace Flixon\Logging;

use Flixon\Foundation\Application;
use Flixon\Foundation\Module;

class LoggingModule extends Module {
    public function register(Application $app): void {
        // Register the default logger.
        $app->container->mapSingleton(Logger::class, FileLogger::class)->map('log', Logger::class);
    }

    public function terminate(Application $app): void {
        // Write the logs.
        $app->container->get(Logger::class)->write();
    }
}