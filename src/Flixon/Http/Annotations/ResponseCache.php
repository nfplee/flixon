<?php

namespace Flixon\Http\Annotations;

/**
 * @Annotation
 */
class ResponseCache {
    public $duration, $varyBy;

    public function __construct(array $values) {
        $this->duration = $values['duration'] ?? 300;
        $this->varyBy = $values['varyBy'];
    }

    public static function __set_state(array $array): ResponseCache {
        return new ResponseCache($array);
    }
}