<?php

namespace Flixon\Routing\Annotations;

use Symfony\Component\Routing\Annotation\Route as BaseRoute;

/**
 * @Annotation
 */
class Route extends BaseRoute {
    use \Flixon\Common\Traits\PropertyAccessor;

    // This property is made private and is accessible via the Symfony get/set standards so that the value is set by Symfony when configurating the route annotation.
    private $priority = 1;

    public static function __set_state(array $array): Route {
        return new Route($array);
    }

    public function getPriority() {
        return $this->priority;
    }

    public function setPriority($value) {
        $this->priority = $value;
    }
}