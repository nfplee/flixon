<?php

namespace Flixon\Data;

use CommonQuery;
use DeleteQuery;
use FluentPDO;
use Flixon\Common\Services\CachingService;
use Flixon\Config\Config;
use Flixon\Foundation\Application;
use PDO;
use UpdateQuery;

class Database extends FluentPDO {
	use \Flixon\Common\Traits\PropertyAccessor;

	public function __construct(Application $app, CachingService $cachingService, Config $config) {
		// Connect to the database.
		parent::__construct(new PDO($config->database->type . ':host=' . $config->database->host . ';dbname=' . $config->database->name, $config->database->username, $config->database->password), new DatabaseStructure($app, $cachingService, $this));

		// Set the error mode to throw exceptions.
		$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		//$this->debug = function($context) {
		//	echo $context->getQuery(true);
		//	print_r($context->getParameters());
		//};
    }
	
	public function execute(string $query, array $parameters = []) {
		// Prepare the query.
		$stmt = $this->pdo->prepare($query);
		
		// Execute the query.
		$stmt->execute($parameters);

		return $stmt;
	}

	public function from($table, $primaryKey = null): SelectQuery {
		$query = new SelectQuery($this, $table);

        if ($primaryKey !== null) {
			$query = $this->filterByPrimaryKey($query, $query->fromTable, $primaryKey, $query->fromAlias . '.');
        }

		return $query;
	}

	// This overrides the base and allows filtering by multiple primary keys.
	public function update($table, $set = [], $primaryKey = null): UpdateQuery {
		$query = parent::update($table, $set);

        if ($primaryKey !== null) {
			$query = $this->filterByPrimaryKey($query, $table, $primaryKey);
        }

        return $query;
    }

	// This overrides the base and allows filtering by multiple primary keys.
	public function delete($table, $primaryKey = null): DeleteQuery {
        $query = parent::delete($table);

        if ($primaryKey !== null) {
			$query = $this->filterByPrimaryKey($query, $table, $primaryKey);
        }
        
        return $query;
    }

	private function filterByPrimaryKey(CommonQuery $query, string $table, $primaryKey, string $prefix = ''): CommonQuery {
		// Construct the where clause.
		$where = array_combine($this->structure->getPrimaryKey($table, $prefix), !is_array($primaryKey) ? [$primaryKey] : $primaryKey);

		// Apply the filter.
		return $query->where($where);
	}
}