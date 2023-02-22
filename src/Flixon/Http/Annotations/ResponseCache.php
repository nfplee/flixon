<?php

namespace Flixon\Http\Annotations;

use Attribute;

#[Attribute]
class ResponseCache {
    public int $duration;
    public ?string $varyBy;

    public function __construct(?string $varyBy = null, int $duration = 300) {
        $this->varyBy = $varyBy;
        $this->duration = $duration;
    }
}