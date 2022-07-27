<?php

namespace Flixon\Common\Services;

use Flixon\Foundation\Application;

class FileCachingService implements CachingService {
	private $path;

    public function __construct(Application $app) {
		$this->path = $app->rootPath . '/resources/cache/';
    }

	/**
	 * Get item from cache.
	 * 
	 * @param  string  $key   		item to look for in the cache.
	 * @param  string  $duration  	the cache duration.
	 * @return string             	returns the cache.
	 */
	public function get(string $key, int $duration = 300) {
		$filename = $this->path . '/' . $key;

		if ($duration > 0 && is_readable($filename) && filemtime($filename) >= time() - $duration) {
			return include $filename;
		}

		return null;
	}

	/**
	 * Get or add an item from the cache.
	 * 
	 * @param  string  	$key       	item to look for in the cache.
	 * @param  callable $callback  	the callback function.
	 * @param  string 	$duration  	the cache duration.
	 * @param  string 	$serialize 	serializing the data is slower but if you don't then the cached data must implement __set_state.
	 * @return string            	returns the cache.
	 */
	public function getOrAdd(string $key, callable $callback, int $duration = 300, bool $serialize = true) {
		$value = $this->get($key, $duration);

		if ($value === null) {
			$value = $callback();

			if ($duration > 0) {
				$this->set($key, $value, $serialize);
			}
		}

		return $value;
	}
	
	/**
	 * Remove item(s) from the cache.
	 * 
	 * @param  string  $prefix      prefix to look for in the cache.
	 * @param  int  $duration 		the cache duration to keep.
	 */
	public function remove(string $prefix, ?int $duration = null) {
		foreach (scandir($this->path) as $file) {
			$filename = $this->path . '/' . $file;

			if (strpos($file, $prefix) === 0 && ($duration == null || filemtime($filename) < time() - $duration)) {
				unlink($filename);
			}
		}
	}

	/**
	 * Add value to a cache.
	 *
	 * @param string $key   	name the data to save.
	 * @param string $value 	the data to save.
	 * @param string $serialize serializing the data is slower but if you don't then the cached data must implement __set_state.
	 */
	public function set(string $key, $value, bool $serialize = true) {
		$filename = $this->path . '/' . $key;
		$tempFilename = $filename . uniqid('', true) . '.tmp';

		if ($serialize) {
			$value = 'unserialize(' . $this->export(serialize($value), true) . ')';
		} else {
			$value = $this->export($value, true);
			$value = str_replace('stdClass::__set_state', '(object)', $value);
		}

		file_put_contents($tempFilename, '<?php return ' . $value . ';');
		rename($tempFilename, $filename);
	}

	/**
	 * Does a standard variable export but removes white spaces.
	 */
	private function export($expression, $return = false): string {
		// Export the variables.
		$export = var_export($expression, $return);

		// Remove any whitespace.
		//$export = preg_replace('/\s+/', ' ', $export);
		
		return $export;
	}
}