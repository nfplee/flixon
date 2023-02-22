<?php

namespace Flixon\Security\Middleware;

use Flixon\Foundation\Middleware;
use Flixon\Http\Request;
use Flixon\Http\Response;
use Flixon\Security\Services\AuthenticationService;

class AuthenticationMiddleware extends Middleware {
    private AuthenticationService $authenticationService;

    public function __construct(AuthenticationService $authenticationService) {
        $this->authenticationService = $authenticationService;
    }

    public function __invoke(Request $request, Response $response, callable $next = null) {
        // Store the user against the request.
        $request->user = $request->parent?->user ?? $this->authenticationService->getUser();

        return $next($request, $response, $next);
    }
}