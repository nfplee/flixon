<?php

namespace App\Tasks;

use Flixon\Common\Services\CachingService;
use Flixon\Scheduling\Task;

class TestTask extends Task {
    public function run(): void {
        echo 'Test | ';
    }
}