<?php

namespace Flixon\Security\Middleware;

use Flixon\Common\Collections\Enumerable;
use Flixon\Exceptions\AccessDeniedException;
use Flixon\Foundation\Middleware;
use Flixon\Http\Request;
use Flixon\Http\Response;
use Flixon\Security\Annotations\Authorize;
use Flixon\Security\Services\AuthorizationService;

class AuthorizationMiddleware extends Middleware {
    private $authorizationService;

    public function __construct(AuthorizationService $authorizationService) {
        $this->authorizationService = $authorizationService;
    }

    public function __invoke(Request $request, Response $response, callable $next = null) {
        // Make sure the user is allowed to view the page (if applicable).
        if ($request->attributes->has('_annotations')) {
            // Work out whether the user is allowed to view the page.
            $isAllowed = !Enumerable::from($request->attributes->get('_annotations'))->any(function($annotation) use ($request) {
                if ($annotation instanceof Authorize) {
                    $isAllowed = Enumerable::from($annotation->roles)->any(function($role) use ($request) {
                        return $this->authorizationService->isAllowed($request->user, $role);
                    });

                    return ($isAllowed && $annotation->negate) || (!$isAllowed && !$annotation->negate);
                } else {
                    return false;
                }
            });

            // If not allowed then throw an access denied exception.
            if (!$isAllowed) {
                throw new AccessDeniedException();
            }
        }

        return $next($request, $response, $next);
    }
}