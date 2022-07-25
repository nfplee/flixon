<?php

namespace Flixon\Foundation;

use ArrayIterator;
use Iterator;
use IteratorAggregate;

class ModuleCollection implements IteratorAggregate {
	private $modules = [];

	public function add(string $class): ModuleCollection {
		$this->modules[] = new $class();

		return $this;
	}

	public function getIterator(): Iterator {
        return new ArrayIterator($this->modules);
    }
}