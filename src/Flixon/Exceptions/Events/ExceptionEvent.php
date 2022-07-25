<?php

namespace Flixon\Exceptions\Events;

use Flixon\Events\Event;
use Throwable;

class ExceptionEvent extends Event {
    public $exception;

    public function __construct(Throwable $exception) {
        $this->exception = $exception;
    }
}