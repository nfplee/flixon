<?php

namespace Flixon\Events;

use Flixon\Foundation\Application;
use Flixon\Foundation\Module;

class EventsModule extends Module {
    public function register(Application $app): void {
        $app->container->mapSingleton(EventDispatcher::class);
    }
}