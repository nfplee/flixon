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
        // Get/set the user for child requests.
        if (!$request->isChildRequest()) {
            $user = $this->authenticationService->getUser();
            $request->session->set('_user', $user);
        } else {
            $user = $request->session->get('_user');
        }

        // Store the user against the request.
        $request->user = $user;

        return $next($request, $response, $next);
    }
}