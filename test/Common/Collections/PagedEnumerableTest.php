<?php

namespace Flixon\Tests\Common\Collections;

use Flixon\Common\Collections\PagedEnumerable;
use Flixon\Testing\TestCase;

class PagedEnumerableTest extends TestCase {
    public function testSkip() {
        // Arrange
        $enumable = PagedEnumerable::from(['foo', 'bar', 'baz', 'qux', 'quux']);

        // Act
        $result = $enumable->skip(2);

        // Assert
        $this->assertCount(5, $enumable);
        $this->assertCount(3, $result);
        $this->assertEquals('baz', $result[0]);
    }

    public function testTake() {
        // Arrange
        $enumable = PagedEnumerable::from(['foo', 'bar', 'baz', 'qux', 'quux']);

        // Act
        $result = $enumable->take(2);

        // Assert
        $this->assertCount(5, $enumable);
        $this->assertCount(2, $result);
        $this->assertEquals('foo', $result[0]);
    }
}