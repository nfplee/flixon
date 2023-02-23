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
        // Set the locale.
        if (!$locale = $request->attributes->get('_locale')) {
            $locale = $this->config->localization->defaultLocale;
        }

        // Get/set the locale for child requests.
        if (!$request->isChildRequest()) {
            $request->session->set('_locale', $locale);
        } else {
            // Fall back to the default locale if no session exists and within a child request (for example a page not found or an access denied request). This will prevent a blank page being displayed since a page not found exception is thrown and it gets stuck in an infinite loop.
            $locale = $request->session->get('_locale', $locale);
        }

        // Store the locale against the request.
        $request->locale = $this->localizationService->getLocaleByFormat($locale);

        return $next($request, $response, $next);
    }
}