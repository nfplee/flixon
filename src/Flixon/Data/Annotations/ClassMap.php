<?php

namespace Flixon\Data\Annotations;

use Attribute;

#[Attribute]
class ClassMap {
    public string $table;

    public function __construct(string $table) {
        $this->table = $table;
    }
}