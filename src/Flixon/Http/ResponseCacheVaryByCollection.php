<?php

namespace Flixon\Http;

use ArrayAccess;
use ArrayIterator;
use Iterator;
use IteratorAggregate;

class ResponseCacheVaryByCollection implements ArrayAccess, IteratorAggregate {
	private $varyBys = [];

	public function add(string $key, callable $varyBy): ResponseCacheVaryByCollection {
		$this->varyBys[$key] = $varyBy;

		return $this;
	}
    
    public function getIterator(): Iterator {
        return new ArrayIterator($this->varyBys);
	}
	
	public function offsetGet($offset) {
        return $this->varyBys[$offset];
    }

    public function offsetSet($offset, $value) {
        $this->varyBys[$offset] = $value;
    }

    public function offsetExists($offset) {
        return array_key_exists($offset, $this->varyBys);
    }

    public function offsetUnset($offset) {
        unset($this->varyBys[$offset]);
    }
}