<?php

namespace Flixon\Routing;

use Flixon\Common\Traits\PropertyAccessor;
use Symfony\Component\Routing\Generator\UrlGenerator as UrlGeneratorBase;

class UrlGenerator extends UrlGeneratorBase {
	use PropertyAccessor;

	private array $defaults = [];

	public function addDefault(string $name, string $value, callable $callback): UrlGenerator {
		$this->defaults[] = compact('name', 'value', 'callback');

		return $this;
	}

	public function generate(string $name, array $parameters = [], int $referenceType = self::ABSOLUTE_PATH): string {
		// Merge the defaults.
		foreach ($this->defaults as $default) {
			if ($default['callback']($parameters[$default['name']] ?? null)) {
				$parameters[$default['name']] = $default['value'];
			}
		}

		// Generate the url.
		return parent::generate($name, $parameters, $referenceType);
	}
}