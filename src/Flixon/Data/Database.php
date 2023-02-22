<?php

namespace Flixon\Data;

use Envms\FluentPDO\Queries\{Common, Update, Delete};
use Flixon\Common\Services\CachingService;
use Flixon\Config\Config;
use Flixon\Data\Queries\Select;
use Flixon\Foundation\Application;
use PDO;

class Database extends \Envms\FluentPDO\Query {
    use \Flixon\Common\Traits\PropertyAccessor;

    public function __construct(Application $app, CachingService $cachingService, Config $config) {
        // Connect to the database.
        parent::__construct(new PDO($config->database->type . ':host=' . $config->database->host . ';dbname=' . $config->database->name, $config->database->username, $config->database->password), new DatabaseStructure($app, $cachingService, $this));

        // Set the error mode to throw exceptions.
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        //$this->debug = function($context) {
        //    echo $context->getQuery(true);
        //    print_r($context->getParameters());
        //};
    }
    
    public function execute(string $query, array $parameters = []): mixed {
        // Prepare the query.
        $stmt = $this->pdo->prepare($query);
        
        // Execute the query.
        $stmt->execute($parameters);

        return $stmt;
    }

    public function from(?string $table = null, mixed $primaryKey = null): Select {
        $query = new Select($this, $table);

        if ($primaryKey !== null) {
            $query = $this->filterByPrimaryKey($query, $query->fromTable, $primaryKey, $query->fromAlias . '.');
        }

        return $query;
    }

    // This overrides the base and allows filtering by multiple primary keys.
    public function update(?string $table = null, mixed $set = [], mixed $primaryKey = null): Update {
        $query = parent::update($table, $set);

        if ($primaryKey !== null) {
            $query = $this->filterByPrimaryKey($query, $table, $primaryKey);
        }

        return $query;
    }

    // This overrides the base and allows filtering by multiple primary keys.
    public function delete(?string $table = null, mixed $primaryKey = null): Delete {
        $query = parent::delete($table);

        if ($primaryKey !== null) {
            $query = $this->filterByPrimaryKey($query, $table, $primaryKey);
        }
        
        return $query;
    }

    private function filterByPrimaryKey(Common $query, ?string $table, $primaryKey, string $prefix = ''): Common {
        // Construct the where clause.
        $where = array_combine($this->structure->getPrimaryKey($table, $prefix), !is_array($primaryKey) ? [$primaryKey] : $primaryKey);

        // Apply the filter.
        return $query->where($where);
    }
}