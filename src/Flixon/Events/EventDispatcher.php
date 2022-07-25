<?php

namespace Flixon\Events;

use Symfony\Component\EventDispatcher\EventDispatcher as BaseEventDispatcher;

class EventDispatcher extends BaseEventDispatcher {
    public function on(string $event, $callback) {
        $this->addListener($event, $callback);
    }

    public function trigger(string $name, $event) {
        $this->dispatch($name, $event);
    }
}