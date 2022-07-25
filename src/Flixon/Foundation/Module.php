<?php

namespace Flixon\Foundation;

abstract class Module {
    /**
     * Register container services and middleware.
     */
    public function register(Application $app) { }

    /**
     * You should only use this if you need to guarantee the container services have been registered.
     */
    public function registered(Application $app) { }

    public function terminate(Application $app) { }
}