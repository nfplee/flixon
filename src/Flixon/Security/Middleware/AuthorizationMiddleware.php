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
            $isAllowed = count(array_filter($request->attributes->get('_annotations'), function($annotation) use ($request) {
                if ($annotation instanceof Authorize) {
                    // Get the matching roles.
                    $roles = Enumerable::from($annotation->roles)->filter(function($role) use ($request) {
                        return $this->authorizationService->isAllowed($request->user, $role);
                    });

                    return (count($roles) > 0 && $annotation->negate) || (count($roles) == 0 && !$annotation->negate);
                } else {
                    return false;
                }
            })) == 0;

            // If not allowed then throw an access denied exception.
            if (!$isAllowed) {
                throw new AccessDeniedException();
            }
        }

        return $next($request, $response, $next);
    }
}