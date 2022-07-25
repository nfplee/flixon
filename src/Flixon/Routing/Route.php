<?php

namespace Flixon\Routing;

use ReflectionProperty;
use Symfony\Component\Routing\CompiledRoute;
use Symfony\Component\Routing\Route as BaseRoute;

class Route extends BaseRoute {
    use \Flixon\Common\Traits\PropertyAccessor;
    
    public $priority;

    public function __construct(string $path, array $defaults = [], array $requirements = [], array $options = [], string $host = '', $schemes = [], $methods = [], string $condition = '', int $priority = 1) {
        parent::__construct($path, $defaults, $requirements, $options, $host, $schemes, $methods, $condition);

        $this->priority = $priority;
    }

    public static function __set_state(array $array): Route {
        return new Route($array['path'], $array['defaults'], $array['requirements'], $array['options'], $array['host'], $array['schemes'], $array['methods'], $array['condition'], $array['priority']);
    }

    /**
     * This allows you to match /{foo}/{bar} where the url = /bar ({foo}'s requirements doesn't match, therefore it falls back to {bar}).
	 * This is not needed once the following issue is solved https://github.com/symfony/symfony/issues/5424.
     */
	public function compile(): CompiledRoute {
		// Call the parent method to get the compiled route.
        $compiledRoute = parent::compile();

        // Override the regex to make slashes optional. This is a hack as the properties are read only. This saves you from having to override alot more classes.
        $property = new ReflectionProperty($compiledRoute, 'regex');
		$property->setAccessible(true);
		$property->setValue($compiledRoute, str_replace('/??', '/?', str_replace('/', '/?', $compiledRoute->getRegex())));

        // This makes the / after the locale required only if it is not the end of the expression. This allows /de (/ after locale not required as at the end of expression) and /deals (/ after locale is required as not at the end of expression). If you didn't have it then /deals would be detected as a localized url since it starts with /de and the / after the locale would not be required even though we're not at the end of the expression.
        if ($this->getPath() == '/{_locale}/{slug}') {
            $property->setValue($compiledRoute, str_replace('|)?', '|)(?:/(?!$)|$)', $compiledRoute->getRegex()));
        }

    	return $compiledRoute;
    }
}