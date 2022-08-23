<?php

namespace Flixon\Http\Middleware;

use Exception;
use Flixon\Common\Collections\Enumerable;
use Flixon\Common\Services\CachingService;
use Flixon\Config\Config;
use Flixon\Foundation\Application;
use Flixon\Foundation\Middleware;
use Flixon\Http\Annotations\ResponseCache;
use Flixon\Http\Request;
use Flixon\Http\Response;
use Flixon\Http\ResponseCacheVaryByCollection;

class ResponseCacheMiddleware extends Middleware {
    private $app, $cachingService, $config, $varyBys;

    public function __construct(Application $app, CachingService $cachingService, Config $config, ResponseCacheVaryByCollection $varyBys) {
        $this->app = $app;
        $this->cachingService = $cachingService;
        $this->config = $config;
        $this->varyBys = $varyBys;
    }

    public function __invoke(Request $request, Response $response, callable $next = null) {
        // Try to get a response cache attribute (if response caching is enabled).
        $responseCache = $this->config->http->responseCacheEnabled && $request->attributes->has('_annotations') ? Enumerable::from($request->attributes->get('_annotations'))->filter(function($annotation) {
            return $annotation instanceof ResponseCache;
        })->first() : null;

        if ($responseCache != null) {
            // Get the cache key.
            $key = 'response-cache-' . str_replace(':', '-', str_replace('\\', '-', $request->attributes->get('_controller'))) . '-' . Enumerable::from(explode(';', $responseCache->varyBy))->map(function($key) use ($request) {
                switch ($key) {
                    case 'locale':
                        return 'locale=' . $request->locale->id;
                    case 'role':
                        return 'role=' . $request->root->user->roleId;
                    case 'url':
                        // Get the allowed query string.
                        $query = Enumerable::from($request->root->query->keys())->filter(function($key) {
                            return in_array($key, $this->config->http->allowedQueryParameters);
                        })->map(function($key) use ($request) {
                            return $key . '=' . $request->root->query->get($key);
                        })->toString('&');

                        return 'url=' . md5($request->root->pathInfo . '?' . $query);
                    case 'user':
                        return 'user=' . $request->root->user->id;
                    default:
                        // Make sure a custom vary by exists.
                        if (!isset($this->varyBys[$key])) {
                            throw new Exception('No custom vary by exists with the name "' . $key . '"');
                        }
                        
                        return $key . '=' . $this->varyBys[$key]($request);
                }
            })->toString('_');

            // Set the cache key against the request.
            $request->attributes->set('_cache', $key);

            // Return the cached response (if applicable).
            if (($response->content = $this->cachingService->get($key, $responseCache->duration)) != null) {
                // If it's the root cached request then replace the cache wrapper else add the wrapper so that the parent cache includes the placeholder.
                if (!$request->isChildRequest() || !$request->parent->attributes->has('_cache')) {
                    // Look for all the cached child requests within the response content.
                    preg_match_all('/<cache controller="(.*?)">.*?<\/cache>/s', $response->content, $matches);

                    // Replace the child requests.
                    for ($i = 0; $i < count($matches[0]); $i++) {
                        // Create a child request.
                        $childRequest = new Request();
                        
                        // Set the controller.
                        $childRequest->attributes->set('_controller', $matches[1][$i]);

                        // Get the child response.
                        $childResponse = $this->app->handle($childRequest, $request, false);

                        // Replace the content.
                        $response->content = str_replace($matches[0][$i], $childResponse->content, $response->content);
                    }
                } else {
                    $response->content = '<cache controller="' . $request->attributes->get('_controller') . '">' . $response->content . '</cache>';
                }

                return $response;
            }
        }

        $next = $next($request, $response, $next);
        
        // Cache the response (if applicable).
        if ($request->attributes->has('_cache')) {
            // Cache the response.
            $this->cachingService->set($request->attributes->get('_cache'), $response->content, false);

            // If it's the root cached request then remove the cache wrapper else add the wrapper so that the parent cache includes the placeholder.
            if (!$request->isChildRequest() || !$request->parent->attributes->has('_cache')) {
				$response->content = preg_replace('/<cache.*?>(.*?)<\/cache>/s', '$1', $response->content);
			} else {
                $response->content = '<cache controller="' . $request->attributes->get('_controller') . '">' . $response->content . '</cache>';
			}
        }

        return $next;
    }
}