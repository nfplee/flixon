<?php

namespace Flixon\Http;

use ArrayAccess;
use ArrayIterator;
use Iterator;
use IteratorAggregate;

class ResponseCacheVaryByCollection implements ArrayAccess, IteratorAggregate {
	private array $varyBys = [];

	public function add(string $key, callable $varyBy): ResponseCacheVaryByCollection {
		$this->varyBys[$key] = $varyBy;

		return $this;
	}
    
    public function getIterator(): Iterator {
        return new ArrayIterator($this->varyBys);
	}
	
    public function offsetGet(mixed $offset): mixed {
        return $this->varyBys[$offset];
    }

    public function offsetSet(mixed $offset, mixed $value): void {
        $this->varyBys[$offset] = $value;
    }

    public function offsetExists(mixed $offset): bool {
        return array_key_exists($offset, $this->varyBys);
    }

    public function offsetUnset(mixed $offset): void {
        unset($this->varyBys[$offset]);
    }
}