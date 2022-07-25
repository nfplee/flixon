<?php

namespace Flixon\Data;

use Flixon\Common\Collections\Enumerable;
use Flixon\Common\Inflector;
use Flixon\Common\Services\CachingService;
use Flixon\Common\Utilities;
use Flixon\Foundation\Application;
use FluentStructure;
use PDO;

class DatabaseStructure extends FluentStructure {
	private $app, $cachingService, $db;
	
	function __construct(Application $app, CachingService $cachingService, Database $db) {
		$this->app = $app;
		$this->cachingService = $cachingService;
		$this->db = $db;
	}
	
	public function getColumns(string $table): array {
		return $this->cachingService->getOrAdd('columns-' . $table, function() use ($table) {
			return $this->db->execute('SHOW COLUMNS FROM `' . $table . '`')->fetchAll(PDO::FETCH_ASSOC);
		}, $this->app->environment == Application::PRODUCTION ? 60 * 60 * 24 : 60, false);
	}

	public function getForeignKey($table, bool $singularize = true): string {
        return ($singularize ? Inflector::singularize($table) : $table) . 'Id';
    }

	public function getPrimaryKey($table, string $prefix = ''): array {
		return Enumerable::from($this->getColumns($table))->filter(function($column) { return $column['Key'] == 'PRI'; })->map(function($column) use ($prefix) { return $prefix . $column['Field']; })->toArray();
	}

	public function getTable(string $foreignKey): string {
		return strtolower(Utilities::splitUpperCase(Inflector::pluralize(substr($foreignKey, 0, strlen($foreignKey) - 2)), '_'));
	}

	public function getTables(): array {
		return $this->cachingService->getOrAdd('tables', function() {
			return $this->db->execute('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
		}, $this->app->environment == Application::PRODUCTION ? 60 * 60 * 24 : 60, false);
	}
}