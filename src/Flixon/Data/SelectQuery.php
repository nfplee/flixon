<?php

namespace Flixon\Data;

use ArrayIterator;
use Flixon\Common\Collections\Enumerable;
use Flixon\Common\Collections\PagedEnumerable;
use Flixon\Common\Utilities;
use Iterator;
use SelectQuery as BaseSelectQuery;

class SelectQuery extends BaseSelectQuery {
	use \Flixon\Common\Traits\PropertyAccessor;

	private $class = null, $with = [];

	public function asEntity($class = null): SelectQuery {
		$this->class = $class;

		return $this;
	}

	public function fetch($column = '') {
		// Call the parent method.
		$row = parent::fetch($column);

		// Make sure a row is returned.
		if (!$row) {
			return null;
		}

		// Return the mapped data (if applicable).
		return $this->map($row);
	}

	public function fetchAll($index = '', $selectOnly = ''): Enumerable {
		// Call the parent method.
		$data = parent::fetchAll($index, $selectOnly);

		// Return an enumerable of the mapped data (if applicable).
		return Enumerable::from($data)->map([$this, 'map']);
	}

	public function fetchPaged(int $page, int $pageSize): PagedEnumerable {
		// Get the number of records (must do this before we filter the data).
		$count = $this->count();
	
		// Apply the filter to the current query.
		$this->limit($pageSize)->offset(($page - 1) * $pageSize);

		// Fetch the data.
		$data = parent::fetchAll();
		
		// Return a paged enumerable of the mapped data (if applicable).
		return PagedEnumerable::from($data)->map([$this, 'map'])->page($page, $pageSize, $count);
	}

	public function getIterator(): Iterator {
        return new ArrayIterator($this->fetchAll()->toArray());
    }

	public function map($row) {
		return $this->class !== null ? (new $this->class())->init($row) : $row;
	}

	// TODO: Support one-to-many.
	//  - Maybe detect if the name is plural/singular on whether to do a withOne or a withMany.
	//	- Possibly when the query is executed (e.g. fetchAll) do an additional query with a WHERE x IN (...) - where the ... is the id's of the data returned from the fetchAll.
	public function with(string $name, string $table = null, string $foreignKey = null): SelectQuery {
		// Get the path as an array.
		$path = explode('.', $name);

		// Add each part.
		$prefix = '';

		for ($i = 0; $i < count($path); $i++) {
			// Set the full name.
			$name = $prefix . $path[$i];

			// Make sure the with clause doesn't already exist.
			if (!in_array($name, $this->with)) {
				// If the last part of the path then pass the overrides.
				if ($i == count($path) - 1) {
					$this->withOne($name, $table, $foreignKey);
				} else {
					$this->withOne($name);
				}
		
				// Add the with clause.
				$this->with[] = $name;
			}

			// Set the prefix.
			$prefix .= $path[$i] . '.';
		}

		return $this;
	}

	private function withOne(string $name, string $table = null, string $foreignKey = null): SelectQuery {
		// If no foreign key is provided then get it from convention.
		if ($foreignKey === null) {
			$foreignKey = $this->structure->getForeignKey(substr($name, (strrpos($name, '.') ?: -1) + 1), false);
		}

		// If no table is provided then get it from convention.
		if ($table === null) {
			$table = $this->structure->getTable($foreignKey);
		}

		// Work out the alias (replace any period characters with underscores as period characters are not supported as an alias).
		$alias = str_replace('.', '_', $name);

		// Work out the from alias (fallback to the from alias if no parent).
		$fromAlias = Utilities::contains($name, '.') ? str_replace('.', '_', substr($name, 0, strrpos($name, '.'))) : $this->fromAlias;

		// Get the join string.
		// TODO: Should this support multiple primary keys by allowing the foreignKey to be an array aswell?
		$join = $table . ' AS `' . $alias . '` ON ' . $this->structure->getPrimaryKey($table, $alias . '.')[0] . ' = ' . $fromAlias . '.' . $foreignKey;

		// Get the fields to select with the alias as a prefix.
		$select = Enumerable::from($this->structure->getColumns($table))->map(function($column) use ($alias) {
			return $alias . '.' . $column['Field'] . ' AS ' . $alias . '_' . $column['Field'];
		})->toString(', ');

		// Add the join and select the fields.
		return $this->leftJoin($join)->select($select);
	}
}