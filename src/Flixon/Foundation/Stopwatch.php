<?php

namespace Flixon\Foundation;

class Stopwatch {
    /**
	 * @var $timers array
	 */
    private $timers = [];
    
	/**
	 * Start the timer.
	 *
	 * @param $name string
	 * @return Flixon\Foundation\Stopwatch
	 */
	public function start($name = 'default'): Stopwatch {
        $this->timers[$name] = microtime(true);
        
        return $this;
    }
    
	/**
	 * Get the elapsed time in seconds.
	 *
	 * @param $name string
	 * @return float The elapsed time since start() was called.
	 */
	public function elapsed($name = 'default'): float {
		return microtime(true) - $this->timers[$name];
	}
}