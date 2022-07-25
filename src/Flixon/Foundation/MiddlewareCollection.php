<?php

namespace Flixon\Foundation;

use ArrayIterator;
use Iterator;
use IteratorAggregate;

class MiddlewareCollection implements IteratorAggregate {
	private $middleware = [];
    
    public function add(string $class, int $priority = 1, array $parameters = []): MiddlewareCollection {
        $this->middleware[] = compact('class', 'parameters', 'priority');

        return $this;
    }
    
    public function getIterator(): Iterator {
        return new ArrayIterator($this->middleware);
    }
}