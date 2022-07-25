<?php

namespace Flixon\Tests\Common;

use Flixon\Common\Utilities;
use Flixon\Testing\TestCase;

class UtilitiesTest extends TestCase {
    public function testStartsWith() {
        // Act
        $result = Utilities::startsWith('Foobar', 'Foo');

        // Assert
        $this->assertTrue($result);
    }

    public function testStartsWithFails() {
        // Act
        $result = Utilities::startsWith('Foobar', 'bar');

        // Assert
        $this->assertFalse($result);
    }
}