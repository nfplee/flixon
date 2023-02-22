<?php

namespace Flixon\Mvc\Annotations;

use Attribute;

#[Attribute]
class Layout {
    public string $layout;

    public function __construct(string $layout) {
        $this->layout = $layout;
    }
}