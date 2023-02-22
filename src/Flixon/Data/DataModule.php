<?php

namespace Flixon\Data;

use Flixon\Foundation\Application;
use Flixon\Foundation\Module;

class DataModule extends Module {
    public function register(Application $app): void {
        // Register the database class.
		$app->container->mapSingleton(Database::class)->map('db', Database::class);
    }
}