<?php

namespace Flixon\Events;

use Symfony\Contracts\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcher as EventDispatcherBase;

class EventDispatcher extends EventDispatcherBase {
    public function on(string $eventName, callable $callback, int $priority = 0): void {
        $this->addListener($eventName, $callback, $priority);
    }

    public function trigger(string $eventName, Event $event): object {
        return $this->dispatch($event, $eventName);
    }
}