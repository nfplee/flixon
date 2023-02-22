<?php

namespace Flixon\Http;

use ArrayIterator;
use Iterator;
use IteratorAggregate;

class CookieCollection implements IteratorAggregate {
    private array $cookies = [];

    public function add(Cookie $cookie): CookieCollection {
        $this->cookies[] = $cookie;

        return $this;
    }
    
    public function getIterator(): Iterator {
        return new ArrayIterator($this->cookies);
    }
}