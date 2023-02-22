<?php

namespace Flixon\Data;

use Doctrine\Common\Annotations\Reader as AnnotationReader;
use Exception;
use Flixon\Common\Collections\Enumerable;
use Flixon\Common\Inflector;
use Flixon\Common\Utilities;
use Flixon\Data\Annotations\ClassMap;
use Flixon\Data\Queries\Select;
use Flixon\DependencyInjection\Container;
use ReflectionClass;

class Query {
    /**
     * Create a select query from a class.
     *
     * @param     string     $class         The entity class to select from.
     * @param     mixed     $primaryKey Optional primary key value, if multiple primary keys then specify as an array.
     *
     * @return Select The select query.
     */
    public static function from(string $class, mixed $primaryKey = null): Select {
        return Container::$current->get('db')->from(self::getTable($class), $primaryKey)
            ->disableSmartJoin() // Fixes an issue when doing sub queries and it tries to join the sub query's table.
            ->asEntity($class);
    }
    
    /**
     * Insert an entity.
     *
     * @param     Entity     $entity The entity to insert.
     *
     * @return mixed The result of the query -- it will return false if an error is thrown.
     */
    public static function insert(Entity $entity): mixed {
        // Get the database.
        $db = Container::$current->get('db');

        // Get the table.
        $table = self::getTable(get_class($entity));

        // Get the primary key (do this before the insert otherwise lastInsertId won't work when there is no cache).
        $primaryKey = $db->structure->getPrimaryKey($table);

        // Create and execute the insert query.
        $result = $db->insertInto($table, self::handleReservedWords($entity->dirtyProperties))->execute();

        // If the primary key not been set then we need to set it.
        if (!isset($entity->{$primaryKey[0]})) {
            $entity->{$primaryKey[0]} = $db->pdo->lastInsertId($primaryKey[0]);
        }
        
        // Reset the dirty properties (this makes sure future updates don't try to update the properties which have not changed since they were inserted).
        $entity->reset();

        return $result;
    }

    /**
     * Update an entity.
     *
     * @param Entity $entity The entity to update.
     *
     * @return mixed The result of the query -- it will return false if an error is thrown.
     */
    public static function update(Entity $entity): mixed {
        // First make sure the entity has changed.
        if (count($entity->dirtyProperties) == 0) {
            return true;
        }

        // Get the database.
        $db = Container::$current->get('db');

        // Get the table.
        $table = self::getTable(get_class($entity));

        // Get the primary key.
        $primaryKey = $db->structure->getPrimaryKey($table);

        // Update the entity.
        $result = $db->update($table, self::handleReservedWords($entity->dirtyProperties), $entity->get($primaryKey))->execute();
        
        // Reset the dirty properties (this makes sure future updates don't try to update the properties which have not changed since they were updated).
        $entity->reset();
        
        return $result;
    }

    /**
     * Wraps the property keys with ` to allow reserved words. e.g. default
     *
     * @param array properties The properties to wrap.
     *
     * @return array The original array with their keys wrapped in `.
     */
    private static function handleReservedWords(array $properties): array {
        foreach ($properties as $name => $value) {
            $properties['`' . $name . '`'] = $value;
            unset($properties[$name]);
        }

        return $properties;
    }

    /**
     * Delete an entity.
     *
     * @param Entity $entity The entity to delete.
     *
     * @return mixed The result of the query -- it will return false if an error is thrown.
     */
    public static function delete(Entity $entity): mixed {
        // Get the database.
        $db = Container::$current->get('db');

        // Get the table.
        $table = self::getTable(get_class($entity));

        // Get the primary key.
        $primaryKey = $db->structure->getPrimaryKey($table);

		if (count($primaryKey) > 1) {
			throw new Exception('Fluent PDO does not support deleting from tables with multiple primary keys.');
		}

        // Delete the entity.
        return $db->deleteFrom($table, $entity->get($primaryKey)[0])->execute();
    }

    /**
     * Get the table name for a class.
     *
     * @param string $class The class to get the table name from.
     *
     * @return string The table name.
     */
    public static function getTable(string $class): string {
        $reflectionClass = new ReflectionClass($class);

        // If a mapping exists then use that else format using convention.
        if (($attribute = Enumerable::from($reflectionClass->getAttributes(ClassMap::class))->first()) != null) {
            return $attribute->newInstance()->table;
        } else {
            return strtolower(Utilities::splitUpperCase(Inflector::pluralize(Utilities::stripNamespaceFromClass($class)), '_'));
        }
    }
}