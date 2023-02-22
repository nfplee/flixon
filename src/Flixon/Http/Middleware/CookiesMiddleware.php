<?php

namespace Flixon\Http\Middleware;

use Flixon\Config\Config;
use Flixon\Foundation\Middleware;
use Flixon\Http\Cookie;
use Flixon\Http\CookieCollection;
use Flixon\Http\Request;
use Flixon\Http\Response;

class CookiesMiddleware extends Middleware {
    private Config $config;
    private CookieCollection $cookies;

    public function __construct(Config $config, CookieCollection $cookies) {
        $this->config = $config;
        $this->cookies = $cookies;
    }

    public function __invoke(Request $request, Response $response, callable $next = null) {
        $next = $next($request, $response, $next);

        // Make sure it is not a child request.
        if (!$request->isChildRequest()) {
            // Add the cookies in the cookie collection.
            foreach ($this->cookies as $cookie) {
                $response->headers->setCookie($cookie);
            }

            // Set the cookie domain and path.
            foreach ($response->headers->getCookies() as $cookie) {
                $response->headers->removeCookie($cookie->name);
                $response->headers->setCookie(new Cookie($cookie->name, $cookie->value, $cookie->expiresTime, $this->config->cookies->path, $this->config->cookies->domain));
            }
        }

        return $next;
    }
}