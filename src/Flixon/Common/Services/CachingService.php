<?php

namespace Flixon\Common\Services;

interface CachingService {
	/**
	 * Get item from cache.
	 * 
	 * @param  string  $key   		item to look for in the cache.
	 * @param  int 	   $duration  	the cache duration.
	 * @return mixed             	returns the cache.
	 */
	public function get(string $key, int $duration = 300): mixed;

	/**
	 * Get or add an item from the cache.
	 * 
	 * @param  string  	$key       	item to look for in the cache.
	 * @param  callable $callback  	the callback function.
	 * @param  int	 	$duration  	the cache duration.
	 * @param  string 	$serialize 	serializing the data is slower but if you don't then the cached data must implement __set_state.
	 * @return mixed            	returns the cache.
	 */
	public function getOrAdd(string $key, callable $callback, int $duration = 300, bool $serialize = true): mixed;
	
	/**
	 * Remove item(s) from the cache.
	 * 
	 * @param  string  	$prefix     prefix to look for in the cache.
	 * @param  int  	$duration 	the cache duration to keep.
	 */
	public function remove(string $prefix, ?int $duration = null): void;

	/**
	 * Add value to a cache.
	 *
	 * @param string $key  		name the data to save.
	 * @param string $value 	the data to save.
	 * @param bool	 $serialize serializing the data is slower but if you don't then the cached data must implement __set_state.
	 */
	public function set(string $key, $value, bool $serialize = true): void;
}