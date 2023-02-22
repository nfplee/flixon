<?php

namespace App;

use Flixon\Foundation\Application;
use Flixon\Testing\TestCase as TestCaseBase;

abstract class TestCase extends TestCaseBase {
    protected string $rootPath = __DIR__ . '/../..';

    public function createApplication(): Application {
        // Create the application.
        $app = parent::createApplication();

        // Add any extra modules.
        $app->modules->add(AppModule::class);

        return $app;
    }
}