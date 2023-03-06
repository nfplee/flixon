<?php

namespace Flixon\Common\Collections;

use Flixon\Common\Collections\Enumerable;
use Flixon\Testing\TestCase;

class EnumerableTest extends TestCase {
    public function testAdd() {
        // Arrange
        $enumable = Enumerable::from(['foo', 'bar']);

        // Act
        $result = $enumable->add('Baz', 'Qux');

        // Assert
        $this->assertCount(2, $enumable);
        $this->assertCount(4, $result);
        $this->assertEquals('Qux', $result[3]);
    }

    public function testAny() {
        // Arrange
        $enumable = Enumerable::from(['foo', 'bar']);

        // Act
        $result = $enumable->any(fn($i) => $i === 'foo');

        // Assert
        $this->assertCount(2, $enumable);
        $this->assertTrue($result);
    }

    public function testAnyFails() {
        // Arrange
        $enumable = Enumerable::from(['foo', 'bar']);

        // Act
        $result = $enumable->any(fn($i) => $i === 'baz');

        // Assert
        $this->assertFalse($result);
    }

    public function testAll() {
        // Arrange
        $enumable = Enumerable::from(['foo', 'foo']);

        // Act
        $result = $enumable->all(fn($i) => $i === 'foo');

        // Assert
        $this->assertCount(2, $enumable);
        $this->assertTrue($result);
    }

    public function testAllFails() {
        // Arrange
        $enumable = Enumerable::from(['foo', 'bar']);

        // Act
        $result = $enumable->all(fn($i) => $i === 'foo');

        // Assert
        $this->assertFalse($result);
    }

    public function testContains() {
        // Arrange
        $enumable = Enumerable::from(['foo', 'bar']);

        // Act
        $result = $enumable->contains('foo');

        // Assert
        $this->assertCount(2, $enumable);
        $this->assertTrue($result);
    }

    public function testContainsFails() {
        // Arrange
        $enumable = Enumerable::from(['foo', 'bar']);

        // Act
        $result = $enumable->contains('baz');

        // Assert
        $this->assertFalse($result);
    }

    public function testContainsKey() {
        // Arrange
        $enumable = Enumerable::from(['foo' => 'Foo', 'bar' => 'Bar']);

        // Act
        $result = $enumable->containsKey('foo');

        // Assert
        $this->assertCount(2, $enumable);
        $this->assertTrue($result);
    }

    public function testContainsKeyFails() {
        // Arrange
        $enumable = Enumerable::from(['foo' => 'Foo', 'bar' => 'Bar']);

        // Act
        $result = $enumable->containsKey('baz');

        // Assert
        $this->assertFalse($result);
    }

    public function testCount() {
        // Arrange
        $enumable = Enumerable::from(['foo', 'bar']);

        // Act
        $result = $enumable->count();

        // Assert
        $this->assertEquals(2, $result);
    }

    public function testCountCallable() {
        // Arrange
        $enumable = Enumerable::from(['foo', 'bar']);

        // Act
        $result = $enumable->count(fn($i) => $i === 'foo');

        // Assert
        $this->assertCount(2, $enumable);
        $this->assertEquals(1, $result);
    }

    public function testDistinct() {
        // Arrange
        $enumable = Enumerable::from(['foo', 'bar', 'bar', 'bar', 'baz', 'baz', 'qux', 'qux']);

        // Act
        $result = $enumable->distinct();

        // Assert
        $this->assertCount(8, $enumable);
        $this->assertCount(4, $result);
    }

    public function testFilter() {
        // Arrange
        $enumable = Enumerable::from(['foo', 'bar']);

        // Act
        $result = $enumable->filter(fn($i) => $i === 'foo');

        // Assert
        $this->assertCount(2, $enumable);
        $this->assertCount(1, $result);
    }

    public function testFilterFails() {
        // Arrange
        $enumable = Enumerable::from(['foo', 'bar']);

        // Act
        $result = $enumable->filter(fn($i) => $i === 'baz');

        // Assert
        $this->assertCount(0, $result);
    }

    public function testFirst() {
        // Arrange
        $enumable = Enumerable::from(['foo', 'bar']);

        // Act
        $result = $enumable->first();

        // Assert
        $this->assertCount(2, $enumable);
        $this->assertEquals('foo', $result);
    }

    public function testFirstCallable() {
        // Arrange
        $enumable = Enumerable::from(['foo', 'bar']);

        // Act
        $result = $enumable->first(fn($i) => $i === 'bar');

        // Assert
        $this->assertEquals('bar', $result);
    }

    public function testFirstCallableFails() {
        // Arrange
        $enumable = Enumerable::from(['foo', 'bar']);

        // Act
        $result = $enumable->first(fn($i) => $i === 'baz');

        // Assert
        $this->assertNull($result);
    }

    public function testGroup() {
        // Arrange
        $enumable = Enumerable::from(['foo', 'foo', 'bar', 'baz', 'baz']);

        // Act
        $result = $enumable->group();

        // Assert
        $this->assertCount(5, $enumable);
        $this->assertCount(3, $result);
    }

    public function testGroupKey() {
        // Arrange
        $enumable = Enumerable::from([
            (object)['foo' => 'Foo'],
            (object)['foo' => 'Foo'],
            (object)['foo' => 'Bar'],
            (object)['foo' => 'Baz'],
            (object)['foo' => 'Baz']
        ]);

        // Act
        $result = $enumable->group(fn($i) => $i->foo);

        // Assert
        $this->assertCount(5, $enumable);
        $this->assertCount(3, $result);
    }

    public function testGroupComplexKey() {
        // Arrange
        $enumable = Enumerable::from([
            (object)['foo' => (object)['bar' => 'Foo']],
            (object)['foo' => (object)['bar' => 'Foo']],
            (object)['foo' => (object)['bar' => 'Bar']],
            (object)['foo' => (object)['bar' => 'Baz']],
            (object)['foo' => (object)['bar' => 'Baz']]
        ]);

        // Act
        $result = $enumable->group(fn($i) => $i->foo->bar);

        // Assert
        $this->assertCount(5, $enumable);
        $this->assertCount(3, $result);
    }

    public function testInsert() {
        // Arrange
        $enumable = Enumerable::from(['foo', 'bar']);

        // Act
        $result = $enumable->insert('Baz', 'Qux');

        // Assert
        $this->assertCount(2, $enumable);
        $this->assertCount(4, $result);
        $this->assertEquals('Qux', $result[1]);
    }

    public function testKeys() {
        // Arrange
        $enumable = Enumerable::from(['foo' => 'Foo']);

        // Act
        $result = $enumable->keys();

        // Assert
        $this->assertCount(1, $enumable);
        $this->assertEquals('foo', $result[0]);
    }

    public function testLast() {
        // Arrange
        $enumable = Enumerable::from(['foo', 'bar']);

        // Act
        $result = $enumable->last();

        // Assert
        $this->assertCount(2, $enumable);
        $this->assertEquals('bar', $result);
    }

    public function testLastCallable() {
        // Arrange
        $enumable = Enumerable::from(['foo', 'bar']);

        // Act
        $result = $enumable->last(fn($i) => $i === 'foo');

        // Assert
        $this->assertEquals('foo', $result);
    }

    public function testLastCallableFails() {
        // Arrange
        $enumable = Enumerable::from(['foo', 'bar']);

        // Act
        $result = $enumable->last(fn($i) => $i === 'baz');

        // Assert
        $this->assertNull($result);
    }

    public function testMap() {
        // Arrange
        $enumable = Enumerable::from([
            (object)['foo' => 'Foo'],
            (object)['foo' => 'Bar'],
            (object)['foo' => 'Baz']
        ]);

        // Act
        $result = $enumable->map(fn($i) => $i->foo);

        // Assert
        $this->assertNotEquals('Foo', $enumable[0]);
        $this->assertEquals('Foo', $result[0]);
    }

    public function testMapCollection() {
        // Arrange
        $enumable = Enumerable::from([
            (object)['foo' => 'Foo', 'bar' => [1, 2, 3]],
            (object)['foo' => 'Bar', 'bar' => [4, 5, 6]],
            (object)['foo' => 'Baz', 'bar' => [7, 8]]
        ]);

        // Act
        $result = $enumable->mapCollection(fn($i) => $i->bar);

        // Assert
        $this->assertCount(3, $enumable);
        $this->assertCount(8, $result);
        $this->assertEquals(8, $result[7]);
    }

    public function testMax() {
        // Arrange
        $enumable = Enumerable::from([1, 2, 3]);

        // Act
        $result = $enumable->max();

        // Assert
        $this->assertEquals(3, $result);
    }

    public function testReverse() {
        // Arrange
        $enumable = Enumerable::from([1, 2, 3]);

        // Act
        $result = $enumable->reverse();

        // Assert
        $this->assertEquals(1, $enumable[0]);
        $this->assertEquals(3, $result[0]);
    }

    public function testSort() {
        // Arrange
        $enumable = Enumerable::from(['foo', 'bar', 'baz', 'qux', 'quux']);

        // Act
        $result = $enumable->sort();

        // Assert
        $this->assertEquals('foo', $enumable[0]);
        $this->assertEquals('bar', $result[0]);
    }

    public function testSortCallable() {
        // Arrange
        $enumable = Enumerable::from([
            (object)['foo' => 'Foo'],
            (object)['foo' => 'Foo'],
            (object)['foo' => 'Bar'],
            (object)['foo' => 'Baz'],
            (object)['foo' => 'Baz']
        ]);

        // Act
        $result = $enumable->sort(function($item1, $item2) {
            return $item1->foo <=> $item2->foo;
        });

        // Assert
        $this->assertEquals('Foo', $enumable[0]->foo);
        $this->assertEquals('Bar', $result[0]->foo);
    }

    public function testSum() {
        // Arrange
        $enumable = Enumerable::from([1, 2, 3]);

        // Act
        $result = $enumable->sum();

        // Assert
        $this->assertEquals(6, $result);
    }

    public function testValues() {
        // Arrange
        $enumable = Enumerable::from(['foo' => 'Foo']);

        // Act
        $result = $enumable->values();

        // Assert
        $this->assertCount(1, $enumable);
        $this->assertEquals('Foo', $result[0]);
    }
}