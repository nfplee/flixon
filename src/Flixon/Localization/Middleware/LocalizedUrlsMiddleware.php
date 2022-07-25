<?php

namespace Flixon\Localization\Middleware;

use Flixon\Config\Config;
use Flixon\Foundation\Middleware;
use Flixon\Http\Request;
use Flixon\Http\Response;
use Flixon\Routing\UrlGenerator;

class LocalizedUrlsMiddleware extends Middleware {
	private $config, $url;

	public function __construct(Config $config, UrlGenerator $url) {
		$this->config = $config;
        $this->url = $url;
	}

    public function __invoke(Request $request, Response $response, callable $next = null) {
        // Only do this for the root request otherwise the defaults get added multiple times. This leads to an issue where child actions produce the wrong default urls e.g. canonical url widget.
        if (!$request->isChildRequest()) {
            // Add the default locale (if one doesn't exist).
            $this->url->addDefault('_locale', $request->locale->format, function($value) {
                return $value == null;
            });

            // If the locale equals the default locale then set it to blank.
            $this->url->addDefault('_locale', '', function($value) {
                return $value == $this->config->localization->defaultLocale;
            });
        }

        return $next($request, $response, $next);
    }
}