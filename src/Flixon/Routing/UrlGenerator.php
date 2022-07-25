<?php

namespace Flixon\Routing;

use Symfony\Component\Routing\Generator\UrlGenerator as BaseUrlGenerator;

class UrlGenerator extends BaseUrlGenerator {
	use \Flixon\Common\Traits\PropertyAccessor;

	private $defaults = [];

	protected $routes;

	public function __construct(RouteCollection $routes) {
		$this->routes = $routes;
    }

	public function addDefault(string $name, string $value, callable $callback): UrlGenerator {
		$this->defaults[] = compact('name', 'value', 'callback');

		return $this;
	}

	public function generate($name, $parameters = [], $referenceType = self::ABSOLUTE_PATH): string {
		// Merge the defaults.
		foreach ($this->defaults as $default) {
			if ($default['callback'](array_key_exists($default['name'], $parameters) ? $parameters[$default['name']] : null)) {
				$parameters[$default['name']] = $default['value'];
			}
		}

		// Generate the url.
		$url = parent::generate($name, $parameters, $referenceType);

        // Fix for when the default parameter is blank (replace multiple forward slashes with a single one). Make sure the previous character is not a colon (otherwise http(s):// becomes http(s):/).
        return preg_replace('/(?<!:)\/+/', '/', str_ireplace('%3A', ':', $url));
	}
}