<?php

namespace Flixon\DependencyInjection\Annotations;

use Attribute;

#[Attribute]
class Inject {
    public string $class;

    public function __construct(string $class) {
        $this->class = $class;
    }
}