<?php

namespace Flixon\Http;

use Flixon\Common\Traits\PropertyAccessor;
use Symfony\Component\HttpFoundation\Response as ResponseBase;

class Response extends ResponseBase {
    use PropertyAccessor;

    public $callback;

    public function sendContent(): static {
        parent::sendContent();

        // Call the callback (if applicable).
        if (isset($this->callback)) {
            ($this->callback)();
        }

        return $this;
    }
}