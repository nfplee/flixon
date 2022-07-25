<?php

namespace Flixon\DependencyInjection;

use ArrayAccess;
use Closure;
use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\FileCacheReader;
use Exception;
use Flixon\Common\Collections\Enumerable;
use Flixon\DependencyInjection\Annotations\Inject;
use Flixon\Foundation\Application;
use ReflectionClass;

class Container implements ArrayAccess {
    /**
     * The current globally available container.
     *
     * @var static
     */
    public static $current;

    protected $annotationReader, $instances = [], $map = [];

    public function __construct(string $environment, string $path = __DIR__ . '/../../..') {
        // Store the current instance in a static variable so we can call this class statically.
        static::$current = $this;

        // Add container dependencies.
        $this->annotationReader = $this->add(Reader::class, new FileCacheReader(new AnnotationReader(), $path . '/resources/cache', $debug = $environment != Application::PRODUCTION))->get(Reader::class);
    }

    /**
     * Add a shared instance or a closure. Closures allow you to lazy load an instance aswell as not make it a singleton.
     */
    public function add(string $class, $instance): Container {
        $this->instances[$class] = $instance;

        return $this;
    }

    /**
     * Get an instance of a class.
     */
    public function get(string $name) {
        // Try to get a mapping.
        $map = array_key_exists($name, $this->map) ? $this->map[$name] : null;

        // If a mapping doesn't exist then use the name.
        $class = $map != null ? $map['class'] : $name;

        // If an instance has already been defined then return it.
        if (array_key_exists($class, $this->instances)) {
            // If the instance is a closure then call it else return the instance.
            if ($this->instances[$class] instanceof Closure) {
                return $this->instances[$class]($this);
            } else {
                return $this->instances[$class];
            }
        }

        // Make sure the class exists.
	    if (!class_exists($class)) {
	        throw new Exception('No class with the name "' . $class . '" exists');
	    }

	    // Initialize the ReflectionClass.
	    $reflectionClass = new ReflectionClass($class);

        // Get the constructor arguments.
        $arguments = [];

        // Try to get the constructor.
        $constructor = $reflectionClass->getConstructor();

        // If there is a constructor then set the arguments for each parameter.
        if ($constructor !== null) {
            foreach ($constructor->getParameters() as $parameter) {
                // Only set if there is not a default value.
                if (!$parameter->isDefaultValueAvailable()) {
                    $arguments[$parameter->name] = $this->get($parameter->getClass()->name);
                }
            }
        }

	    // Create an instance of the class.
	    $instance = $reflectionClass->newInstanceArgs($arguments);

        // Inject the properties
        foreach ($reflectionClass->getProperties() as $property) {
            // Try to get an inject annotation.
            $annotation = $this->annotationReader->getPropertyAnnotation($property, Inject::class);

            if ($annotation !== null) {
                $property->setAccessible(true);
                $property->setValue($instance, $this->get($annotation->class));
            }
        }

        // If a mapping exists and it is a shared mapping then add it to the instances so that it is returned next time.
        if ($map != null && $map['shared']) {
            $this->add($class, $instance);
        }

        return $instance;
    }

    /**
     * Has an instance been defined with the name given.
     */
    public function has(string $name): bool {
        // Try to get a mapped class.
        $class = array_key_exists($name, $this->map) ? $this->map[$name]['class'] : $name;

        return array_key_exists($class, $this->instances);
    }

    /**
     * Add a class mapping.
     */
    public function map(string $name, string $class = null, bool $shared = false): Container {
        if ($class === null) {
            $class = $name;
        }

		// If the mapped class has already been mapped then use the existing mapping.
        $this->map[$name] = array_key_exists($class, $this->map) ? $this->map[$class] : compact('class', 'shared');

        return $this;
    }

    /**
     * Add a class mapping as a singleton.
     */
    public function mapSingleton(string $name, string $class = null): Container {
        $this->map($name, $class, true);

        return $this;
    }

    public function offsetGet($offset) {
        return $this->get($offset);
    }

    public function offsetSet($offset, $value) {
        $this->add($offset, $value);
    }

    public function offsetExists($offset) {
        return $this->has($offset);
    }

    public function offsetUnset($offset) {
        unset($this->instances[$offset]);
    }
}