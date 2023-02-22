<?php

namespace Flixon\Localization\Middleware;

use Flixon\Config\Config;
use Flixon\Foundation\Middleware;
use Flixon\Http\Request;
use Flixon\Http\Response;
use Flixon\Localization\Services\LocalizationService;

class LocalizationMiddleware extends Middleware {
	private Config $config;
    private LocalizationService $localizationService;

	public function __construct(Config $config, LocalizationService $localizationService) {
		$this->config = $config;
        $this->localizationService = $localizationService;
	}

    public function __invoke(Request $request, Response $response, callable $next = null) {
        // Store the locale against the request.
        if ($request->isChildRequest()) {
            $request->locale = $request->parent->locale;
        } else {
            // Set the locale.
            if (!$locale = $request->attributes->get('_locale')) {
                $locale = $this->config->localization->defaultLocale;
            }

            $request->locale = $this->localizationService->getLocaleByFormat($locale);
        }

        return $next($request, $response, $next);
    }
}