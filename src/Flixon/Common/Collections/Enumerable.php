<?php

namespace Flixon\Common\Collections;

use ArrayAccess;
use ArrayIterator;
use Countable;
use Iterator;
use IteratorAggregate;

class Enumerable implements ArrayAccess, Countable, IteratorAggregate {
	protected array $enumerable;

    public function __construct(array $enumerable) {
        $this->enumerable = $enumerable;
    }

    public static function __set_state(array $array): Enumerable {
        return new self($array['enumerable']);
    }

    public function add(mixed ...$values): Enumerable {
        return new static(array_merge($this->enumerable, $values));
    }

    public function all(callable $callback = null, int $flag = 0): bool {
        return $this->count($callback, $flag) == $this->count();
    }

    public function any(callable $callback = null, int $flag = 0): bool {
        return $this->count($callback, $flag) > 0;
    }

    public function contains(mixed $item): bool {
        return in_array($item, $this->enumerable);
    }

    public function containsKey(string $key): bool {
        return array_key_exists($key, $this->enumerable);
    }

    public function count(callable $callback = null, int $flag = 0): int {
        return count($callback != null ? $this->filter($callback, $flag)->toArray() : $this->enumerable);
    }

    public function distinct(): Enumerable {
        return new static(array_unique($this->enumerable));
    }

    public function each(callable $callback): Enumerable {
        return new static(array_walk($this->enumerable, $callback));
    }

    public function filter(callable $callback, int $flag = 0): Enumerable {
        return new static(array_filter($this->enumerable, $callback, $flag));
    }

    public function first(callable $callback = null, int $flag = 0) {
        $enumerable = $callback != null ? $this->filter($callback, $flag)->toArray() : $this->enumerable;
        
        return array_shift($enumerable);
    }

    public static function from(array $enumerable): Enumerable {
        return new static($enumerable);
    }

	public function getIterator(): Iterator {
        return new ArrayIterator($this->enumerable);
    }

    public function group(callable $callback = null): Enumerable {
        $enumerable = [];

        foreach ($this->enumerable as $value) {
            $key = $callback != null ? $callback($value) : $value;

            if (!array_key_exists($key, $enumerable)) {
                $enumerable[$key] = Enumerable::from([]);
            }

            $enumerable[$key] = $enumerable[$key]->add($value);
        }

        return new static($enumerable);
    }

    public function insert(mixed ...$values): Enumerable {
        $enumerable = $this->enumerable;
        array_unshift($enumerable, ...$values);

        return new static($enumerable);
    }

    public function keys(?string $searchValue = null): Enumerable {
        return new static($searchValue != null ? array_keys($this->enumerable, $searchValue) : array_keys($this->enumerable));
    }

    public function last(callable $callback = null, int $flag = 0) {
        return $this->reverse()->first($callback, $flag);
    }

    public function map(callable $callback): Enumerable {
        return new static(array_map($callback, $this->enumerable));
    }

    public function mapCollection(callable $callback): Enumerable {
        return new static(array_merge(...$this->map($callback)->toArray()));
    }

    public function max(): float {
        return max($this->enumerable);
    }

    public function offsetGet(mixed $offset): mixed {
        return $this->enumerable[$offset];
    }

    public function offsetSet(mixed $offset, $value): void {
        $this->enumerable[] = $value;
    }

    public function offsetExists(mixed $offset): bool {
        return array_key_exists($offset, $this->enumerable);
    }

    public function offsetUnset(mixed $offset): void {
        unset($this->enumerable[$offset]);
    }

    public function random(): mixed {
        return array_rand($this->enumerable);
    }

    public function reverse(): Enumerable {
        return new static(array_reverse($this->enumerable));
    }

    public function slice(int $offset, ?int $length = null): Enumerable {
        return new static(array_slice($this->enumerable, $offset, $length));
    }

    public function sort(callable $callback = null, bool $maintainIndexAssociation = false): Enumerable {
        $enumerable = $this->enumerable;

        if ($callback != null) {
            if ($maintainIndexAssociation) {
                uasort($enumerable, $callback);
            } else {
                usort($enumerable, $callback);
            }
        } else {
            if ($maintainIndexAssociation) {
                asort($enumerable);
            } else {
                sort($enumerable);
            }
        }
        
        return new static($enumerable);
    }

    public function sum(callable $callback = null): float {
        return array_sum($callback != null ? $this->map($callback)->toArray() : $this->enumerable);
    }

    public function toArray(): array {
        return $this->enumerable;
    }

    public function toJson(): string {
        return json_encode($this->enumerable, JSON_NUMERIC_CHECK);
    }

    public function toString(string $glue = ', '): string {
        return implode($glue, $this->enumerable);
    }

    public function values(): Enumerable {
        return new static(array_values($this->enumerable));
    }
}