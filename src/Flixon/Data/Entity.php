<?php

namespace Flixon\Data;

use Closure;
use Exception;
use Flixon\Common\Collections\Enumerable;
use Flixon\Common\Utilities;
use Flixon\DependencyInjection\Container;

#[\AllowDynamicProperties]
abstract class Entity {
    private array $lazy = [], $dirtyProperties = [];
    protected array $properties = [];

    public function __get(string $name) {
        if (method_exists($this, 'get' . ucfirst($name))) {
            return $this->{'get' . ucfirst($name)}();
        } else if (array_key_exists($name, $this->properties)) {
            return $this->properties[$name];
        } else {
            throw new Exception('Undefined property: ' . get_class($this) . '::$' . $name);
        }
    }

    public function __set(string $name, mixed $value) {
        if (method_exists($this, 'set' . ucfirst($name))) {
            $this->{'set' . ucfirst($name)}($value);
        } else if (!method_exists($this, 'get' . ucfirst($name))) {
            $this->properties[$name] = $value;
        } else {
            throw new Exception('Property ' . get_class($this) . '::$' . $name . ' is read-only');
        }

        // Mark the property as dirty (if not an array/object).
        if (!is_array($value) && !is_object($value)) {
            $this->dirtyProperties[$name] = $value;
        }
    }

    public static function __set_state(array $array): Entity {
        return (new static())->init($array['properties']);
    }

    public function __isset(string $name): bool {
        // Making sure the method exists allows you to say empty($entity->property) where the property is a function getProperty() e.g. Product.getFlag().
        return method_exists($this, 'get' . ucfirst($name)) || array_key_exists($name, $this->properties);
    }

    public function __unset(string $name) {
        unset($this->properties[$name]);
    }

    private function getCallingProperty(): string {
        return lcfirst(substr(debug_backtrace()[2]['function'], 3));
    }

    public function getDirtyProperties(): array {
        return $this->dirtyProperties;
    }

    public function get(array $properties): array {
        return array_map(function($name) { return $this->$name; }, $properties);
    }

    protected function hasOne(string $class, ?string $foreignKey = null) {
        // Get the calling property.
        $property = $this->getCallingProperty();

        return $this->lazy(function() use ($class, $foreignKey, $property) {
            // If no foreign key is provided then get it from convention.
            if ($foreignKey === null) {
                $foreignKey = Container::$current->get('db')->structure->getForeignKey($property, false);
            }

            // Make sure the foreign key's value is not null.
            if ($this->$foreignKey === null) {
                return null;
            }

            // If the property already exists then use that.
            if (array_key_exists($property, $this->properties)) {
                return (new $class())->init($this->properties[$property]);
            }
            
            // TODO: Should this support multiple primary keys by allowing the foreignKey to be an array aswell?
            return Query::from($class, $this->$foreignKey)->fetch();
        }, $property);
    }

    protected function hasMany(string $class, ?string $foreignKey = null, ?string $primaryKey = null): Enumerable {
        // Get the calling property.
        $property = $this->getCallingProperty();

        return $this->lazy(function() use ($class, $foreignKey, $primaryKey) {
            // If no foreign key is provided then get it from convention.
            if ($foreignKey === null) {
                $foreignKey = Container::$current->get('db')->structure->getForeignKey(Utilities::stripNamespaceFromClass(get_class($this)), false);
            }

            // If no primary key is provided then get it from convention.
            if ($primaryKey === null) {
                $primaryKey = Container::$current->get('db')->structure->getPrimaryKey(Query::getTable(get_class($this)))[0];
            }

            // TODO: Should this support multiple primary keys by allowing the foreignKey to be an array aswell?
            return Query::from($class)->where($foreignKey, $this->$primaryKey)->fetchAll();
        }, $property);
    }

    public function init(array $properties): Entity {
        // This removes any properties which are marked dirty when the object is created (from the constructor).
        $this->reset();

        // Set the properties.
        foreach ($properties as $name => $value) {
            $property = &$this->properties;

            foreach (explode('_', $name) as $index) {
                $property = &$property[$index];
            }

            $property = $value;
        }
        
        return $this;
    }

    protected function lazy(Closure $callback, ?string $key = null): mixed {
        // If no key is set then use the property name.
        if ($key === null) {
            $key = $this->getCallingProperty();
        }

        // If the key doesn't exist then call the callback function and add the data to the lazy property to make sure we don't call it again.
        if (!array_key_exists($key, $this->lazy)) {
            $this->lazy[$key] = $callback();
        }

        return $this->lazy[$key];
    }

    /**
     * Refresh a lazy loaded object.
     */
    public function refresh(?string $key = null, mixed $value = null): Entity {
        // If a key is provided then just update/remove that else refresh the whole entity.
        if ($key !== null) {
            if ($value !== null) {
                $this->lazy[$key] = $value;
            } else {
                unset($this->lazy[$key]);
            }
        } else {
            $this->lazy = [];
        }

        return $this;
    }

    /**
     * Removes any properties which are marked dirty.
     */
    public function reset(): Entity {
        $this->dirtyProperties = [];
        
        return $this;
    }

    /**
     * Set an array of properties.
     */
    public function set(array $properties): Entity {
        foreach ($properties as $name => $value) {
            $this->$name = $value;
        }
        
        return $this;
    }
}