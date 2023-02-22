<?php

namespace Flixon\Logging;

use Flixon\Config\Config;
use Flixon\Foundation\Application;

class OutputLogger implements Logger {
	private Application $app;
    private Config $config;
    private array $logs = [];

	public function __construct(Application $app, Config $config) {
		$this->app = $app;
        $this->config = $config;
	}

	public function info(string $message): OutputLogger {
		return $this->log('info', $message);
	}

	public function log(string $level, string $message): OutputLogger {
        $time = $this->app->stopwatch->elapsed();
		
        $this->logs[] = (object)[
            'level'     => $level,
            'message'   => $message,
            'time'      => $time,
			'elapsed'	=> $time - (count($this->logs) > 0 ? $this->logs[count($this->logs) - 1]->time : 0)
        ];

        return $this;
	}

	public function write(): OutputLogger {
		// Make sure logging is enabled.
        if ($this->config->logging->enabled) {
            foreach ($this->logs as $log) {
				echo $log->message . ' - ' . $log->time . ' - ' . $log->elapsed;
			}
        }

        return $this;
	}
}