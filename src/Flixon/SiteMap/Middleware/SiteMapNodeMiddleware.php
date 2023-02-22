<?php

namespace Flixon\SiteMap\Middleware;

use Flixon\Foundation\Middleware;
use Flixon\Http\Request;
use Flixon\Http\Response;
use Flixon\SiteMap\Services\SiteMapService;

class SiteMapNodeMiddleware extends Middleware {
    private SiteMapService $siteMapService;

    public function __construct(SiteMapService $siteMapService) {
        $this->siteMapService = $siteMapService;
    }

    public function __invoke(Request $request, Response $response, callable $next = null) {
        // Make sure the controller attribute exists and it is not a child request (unless serving the error page).
        if ($request->attributes->has('_controller') && (!$request->isChildRequest() || $request->attributes->has('exception'))) {
            // Set the site map node.
            $request->node = $this->siteMapService->getNodeByController($request->attributes->get('_controller'));
        }

        return $next($request, $response, $next);
    }
}