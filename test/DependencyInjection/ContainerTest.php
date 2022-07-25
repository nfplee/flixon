<?php

namespace Flixon\Tests\DependencyInjection\Utilities;

use Flixon\Common\Utilities;
use Flixon\DependencyInjection\Container;
use Flixon\Foundation\Application;
use Flixon\Testing\TestCase;

class UtilitiesTest extends TestCase {
    public function testAdd() {
        // Arrange
        $container = new Container(Application::TESTING);
        $foo = new Foo();
        $container->add('foo', $foo);

        // Act
        $result = $container->get('foo')->bar;

        // Assert: Make sure the get returns the same instance.
        $this->assertEquals($foo->bar, $result);
    }

    public function testAddMap() {
        // Arrange
        $container = new Container(Application::TESTING);
        $foo = new Foo();
        $container->add(Foo::class, $foo)->map('foo', Foo::class);

        // Act
        $result = $container->get('foo')->bar;
        $result2 = $container->get(Foo::class)->bar;

        // Assert: Make sure the get returns the same instance.
        $this->assertEquals($foo->bar, $result);

        // Assert: Make sure the get returns the same instance.
        $this->assertEquals($foo->bar, $result2);
    }

    public function testMapExtended() {
        // Arrange
        $container = new Container(Application::TESTING);
        $container->map(Foo::class, FooExtended::class);

        // Act
        $result = $container->get(Foo::class)->getBar();
        $result2 = $container->get(Foo::class)->getBar();

        // Assert: Make sure the overridden class was used.
        $this->assertTrue(Utilities::endsWith($result, 'Extended'));

        // Assert: Make sure the instance is not shared.
        $this->assertNotEquals($result, $result2);
    }

    public function testMapExtendedMap() {
        // Arrange
        $container = new Container(Application::TESTING);
        $container->map(Foo::class, FooExtended::class)->map('foo', Foo::class);

        // Act
        $result = $container->get('foo')->getBar();
        $result2 = $container->get('foo')->getBar();

        // Assert: Make sure the overridden class was used.
        $this->assertTrue(Utilities::endsWith($result, 'Extended'));

        // Assert: Make sure the instance is not shared.
        $this->assertNotEquals($result, $result2);
    }

    public function testMapSingleton() {
        // Arrange
        $container = new Container(Application::TESTING);
        $container->mapSingleton(Foo::class);

        // Act
        $result = $container->get(Foo::class)->getBar();
        $result2 = $container->get(Foo::class)->getBar();

        // Assert: Make sure the get returns the same instance.
        $this->assertEquals($result, $result2);
    }

    public function testMapSingletonMap() {
        // Arrange
        $container = new Container(Application::TESTING);
        $container->mapSingleton(Foo::class)->map('foo', Foo::class);

        // Act
        $result = $container->get('foo')->bar;
        $result2 = $container->get('foo')->bar;
        $result3 = $container->get(Foo::class)->bar;
        $result4 = $container->get(Foo::class)->bar;

        // Assert: Make sure the get returns the same instance.
        $this->assertEquals($result, $result2);
        $this->assertEquals($result, $result3);
        $this->assertEquals($result, $result4);
    }

    public function testMapSingletonExtended() {
        // Arrange
        $container = new Container(Application::TESTING);
        $container->mapSingleton(Foo::class, FooExtended::class);

        // Act
        $result = $container->get(Foo::class)->getBar();
        $result2 = $container->get(Foo::class)->getBar();

        // Assert: Make sure the overridden class was used.
        $this->assertTrue(Utilities::endsWith($result, 'Extended'));

        // Assert: Make sure the get returns the same instance.
        $this->assertEquals($result, $result2);
    }

    public function testMapSingletonExtendedMap() {
        // Arrange
        $container = new Container(Application::TESTING);
        $container->mapSingleton(Foo::class, FooExtended::class)->map('foo', Foo::class);

        // Act
        $result = $container->get('foo')->getBar();
        $result2 = $container->get('foo')->getBar();

        // Assert: Make sure the overridden class was used.
        $this->assertTrue(Utilities::endsWith($result, 'Extended'));

        // Assert: Make sure the get returns the same instance.
        $this->assertEquals($result, $result2);
    }

    public function testMapDoesNotExist() {
        // Arrange
        $container = new Container(Application::TESTING);

        // Act
        $result = $container->get(Foo::class)->bar;

        // Assert: Make sure a result is returned.
        $this->assertNotFalse($result);
    }

    public function testMapDoesNotExistAndClassDoesNotExist() {
        // Arrange
        $container = new Container(Application::TESTING);

        // Expect: Expect an exception to be thrown (must come before the exception occurs).
        $this->expectExceptionMessage('No class with the name "foo" exists');

        // Act
        $result = $container->get('foo');
    }
}

class Foo {
    public $bar;

    public function __construct() {
        $this->bar = microtime();
    }

    public function getBar() {
        return $this->bar;
    }
}

class FooExtended extends Foo {
    public function getBar() {
        return parent::getBar() . 'Extended';
    }
}