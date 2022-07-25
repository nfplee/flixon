<?php

namespace Flixon\Common\Traits;

use Exception;

trait PropertyAccessor {
	public function __get(string $name) {
        if (method_exists($this, 'get' . ucfirst($name))) {
            return $this->{'get' . ucfirst($name)}();
        } else {
            throw new Exception('Undefined property: ' . get_class($this) . '::$' . $name);
        }
    }

    public function __set(string $name, $value) {
        if (method_exists($this, 'set' . ucfirst($name))) {
            $this->{'set' . ucfirst($name)}($value);
        } else if (!method_exists($this, 'get' . ucfirst($name))) {
            throw new Exception('Undefined property: ' . get_class($this) . '::$' . $name);
        } else {
            throw new Exception('Property ' . get_class($this) . '::$' . $name . ' is read-only');
        }
    }
}