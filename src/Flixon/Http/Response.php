<?php

namespace Flixon\Http;

use Symfony\Component\HttpFoundation\Response as BaseResponse;

class Response extends BaseResponse {
	use \Flixon\Common\Traits\PropertyAccessor;

    public $callback;

    public function sendContent() {
        parent::sendContent();

        // Call the callback (if applicable).
        if (isset($this->callback)) {
            ($this->callback)();
        }

        return $this;
    }
}