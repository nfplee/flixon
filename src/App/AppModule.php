<?php

namespace App;

use App\Services\LocalizationService;
use App\Services\SiteMapService;
use App\Services\UsersService;
use App\Tasks\TestTask;
use Flixon\Foundation\Application;
use Flixon\Foundation\Module;
use Flixon\Localization\Services\LocalizationService as LocalizationServiceInterface;
use Flixon\Scheduling\TaskRunner;
use Flixon\Security\Services\UsersService as UsersServiceInterface;
use Flixon\SiteMap\Services\SiteMapService as SiteMapServiceInterface;

class AppModule extends Module {
    public function register(Application $app) : void {
        // Register the services.
        $app->container->map(LocalizationServiceInterface::class, LocalizationService::class);
        $app->container->map(SiteMapServiceInterface::class, SiteMapService::class);
        $app->container->map(UsersServiceInterface::class, UsersService::class);
    }

    public function registered(Application $app): void {
        $app->container->get(TaskRunner::class)->add(TestTask::class, '* * * * *'); // Note: The task runner only executes every 10 minutes.
    }
}