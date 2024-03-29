<?php

namespace Flixon\Localization\Middleware;

use Flixon\Foundation\Middleware;
use Flixon\Http\Request;
use Flixon\Http\Response;
use Flixon\Localization\Services\LanguageService;

class LanguageLoaderMiddleware extends Middleware {
    private LanguageService $languageService;

    public function __construct(LanguageService $languageService) {
        $this->languageService = $languageService;
    }

    public function __invoke(Request $request, Response $response, callable $next = null) {
        // Load the default resources.
        $this->languageService->load('default');

        // Load the locale resources (if applicable).
        if ($request->locale) {
            $this->languageService->load($request->locale->format);
        }

        return $next($request, $response, $next);
    }
}