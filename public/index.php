<?php

require_once __DIR__.'/../vendor/autoload.php';

use Flixon\Foundation\Application;
use Flixon\Foundation\Environment;

// Create the application.
$app = new Application(Environment::DEVELOPMENT);

// Add the modules.
$app->modules
    ->add(Flixon\Common\CommonModule::class) // Add this module first so we can use the caching service when registering the other modules.
    ->add(Flixon\Config\ConfigModule::class)
    ->add(Flixon\Data\DataModule::class)
    ->add(Flixon\Events\EventsModule::class)
    ->add(Flixon\Exceptions\ExceptionsModule::class)
    ->add(Flixon\Http\HttpModule::class)
    ->add(Flixon\Localization\LocalizationModule::class)
    ->add(Flixon\Logging\LoggingModule::class)
    ->add(Flixon\Mvc\MvcModule::class)
    ->add(Flixon\Routing\RoutingModule::class)
    ->add(Flixon\Scheduling\SchedulingModule::class)
    ->add(Flixon\Security\SecurityModule::class)
    ->add(Flixon\SiteMap\SiteMapModule::class)
    ->add(App\AppModule::class);

$app->run()->terminate();