<?php
    
namespace Flixon\Routing\Middleware;

use Flixon\Foundation\Middleware;
use Flixon\Http\Request;
use Flixon\Http\Response;
use Flixon\Routing\UrlGenerator;
use Symfony\Component\Routing\RequestContext;

class UrlGeneratorMiddleware extends Middleware {
    private $url;

	public function __construct(UrlGenerator $url) {
        $this->url = $url;
	}

    public function __invoke(Request $request, Response $response, callable $next = null) {
        // Create the request context.
        $context = new RequestContext();
        $context->fromRequest($request->root); // Note: It must be the root otherwise the exception pages will generate http/https links incorrectly.

        // Set the context against the url generator.
        $this->url->context = $context;

        return $next($request, $response, $next);
    }
}