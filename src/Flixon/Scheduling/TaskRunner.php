<?php

namespace Flixon\Scheduling;

use Cron\CronExpression;
use Flixon\DependencyInjection\Container;

class TaskRunner {
    private Container $container;
    private array $tasks = [];

    public function __construct(Container $container) {
        $this->container = $container;
    }

    public function add(string $class, string $schedule): TaskRunner {
        $this->tasks[] = compact('class', 'schedule');

        return $this;
    }

    public function execute(): void {
        foreach ($this->tasks as $task) {
            if (CronExpression::factory($task['schedule'])->isDue()) {
                $this->container->get($task['class'])->run();
            }
        }
    }
}