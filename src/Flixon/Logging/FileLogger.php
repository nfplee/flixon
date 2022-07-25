<?php

namespace Flixon\Logging;

use Flixon\Common\Collections\Enumerable;
use Flixon\Config\Config;
use Flixon\Foundation\Application;

class FileLogger implements Logger {
    private $app, $config, $logs = [];

    public function __construct(Application $app, Config $config) {
        $this->app = $app;
        $this->config = $config;
    }

    public function info(string $message): Logger {
        return $this->log('info', $message);
    }

    public function log(string $level, string $message): Logger {
		$time = $this->app->stopwatch->elapsed();
		
        $this->logs[] = (object)[
            'level'     => $level,
            'message'   => $message,
            'time'      => $time,
			'elapsed'	=> $time - (count($this->logs) > 0 ? $this->logs[count($this->logs) - 1]->time : 0)
        ];

        return $this;
    }

    public function write(): Logger {
        // Make sure logging is enabled.
        if ($this->config->logging->enabled && count($this->logs) > 0) {
            // Get the current url.
            $url = str_replace('/', '-', substr($this->app->container->get('request')->root->pathInfo, 1));

            // Create a unique name for the log file.
            $name = (strlen($url) > 1 ? $url . '-' : '') . round(microtime(true) * 1000) . '.txt';
            
            // Write the log file.
            file_put_contents($this->app->path . '/resources/logs/' . $name, Enumerable::from($this->logs)->map(function($log) {
                return $log->message . ' - ' . $log->time . ' - ' . $log->elapsed . ($log->elapsed > 0.01 ? ' WARNING' : '');
            })->toString(PHP_EOL));
        }

        return $this;
    }
}