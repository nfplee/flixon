<?php

namespace Flixon\Config;

use DirectoryIterator;
use stdClass;

#[\AllowDynamicProperties]
class Config {
    /**
     * The current globally available config.
     */
    public static Config $current;
    
    public function __construct(array $data = []) {
        // Store the current instance in a static variable so we can call this class statically.
        static::$current = $this;

        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }

    public static function __set_state(array $array): Config {
        return new Config($array);
    }

	public function load(string $path): void {
		// Get the config files.
        $files = new DirectoryIterator($path);
        
        // Set the config properties.
        foreach ($files as $file) {
            if ($file->isFile()) {
                // Split the file name by the period character.
                $parts = explode('.', $file->getFilename());

                // Get the name of the file and the environment (if applicable).
                $name = $parts[0];
                $environment = count($parts) > 2 ? $parts[1] : null;

                // Work out whether there is a environment specific version of this config file.
                $hasSpecific = file_exists($path . '/' . $name . '.' . $this->environment . '.php');

                // If the environment matches or has not been supplied and there is not an environment specific version.
                if (($environment == null && !$hasSpecific) || $environment == $this->environment) {
                    $this->$name = (object)(require $file->getPathname());
                }
            }
        }
	}
}