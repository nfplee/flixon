<?php

namespace Flixon\Testing\Traits;

use Flixon\DependencyInjection\Container;

trait Database {
    protected $queries;

    public function setUp() {
        // Call the parent.
        parent::setUp();

        // Set the debugger.
        $this->db->debug = function($query) {
            $this->queries[] = ['query' => $query->getQuery(true), 'parameters' => $query->getParameters()];
        };
    }

	public function tearDown() {
        // Call the parent.
        parent::tearDown();

        // Get the tables.
        $tables = $this->db->structure->getTables();

        foreach ($tables as $table) {
	        $this->db->execute('TRUNCATE TABLE `' . $table . '`');
        }
    }
}