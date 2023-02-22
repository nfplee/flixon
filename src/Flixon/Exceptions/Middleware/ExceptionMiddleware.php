<?php

namespace Flixon\Exceptions\Middleware;

use Flixon\Common\Collections\Enumerable;
use Flixon\Events\EventDispatcher;
use Flixon\Exceptions\Events\ExceptionEvent;
use Flixon\Foundation\Application;
use Flixon\Foundation\Middleware;
use Flixon\Http\Request;
use Flixon\Http\Response;
use Throwable;

class ExceptionMiddleware extends Middleware {
	private Application $app;
    private EventDispatcher $event;

	/**
     * The error handlers.
     */
    public array $errorHandlers = [];

	public function __construct(Application $app, EventDispatcher $event) {
		$this->app = $app;
		$this->event = $event;
	}

    public function __invoke(Request $request, Response $response, callable $next = null) {
	    try {
            return $next($request, $response, $next);
        } catch (Throwable $ex) {
            // Trigger the exception event (do this before serving the child request).
            $this->event->trigger('Exception', new ExceptionEvent($ex));

            // Clear the current output buffer (this makes sure that if an error is thrown within the view the contents of the view are removed).
            if (ob_get_level()) {
                ob_end_clean();
            }

            // Filter for valid error handler.
            $errorHandler = Enumerable::from($this->errorHandlers)->first(function($handler) use ($ex) {
                return $ex instanceof $handler['class'];
            });

            // Rethrow the exception if no matching error handler or not catching exceptions.
            if (!$errorHandler || !$request->catch) {
                throw $ex;
            }

            // Create a child request.
            $childRequest = new Request();

            // Set the controller and exception against the request.
            $childRequest->attributes->set('_controller', $errorHandler['handler']);
            $childRequest->attributes->set('exception', $ex);

            // Handle the request.
            $response = $this->app->handle($childRequest, $request, false);

            // Set the status code.
            $response->statusCode = $errorHandler['statusCode'];

            return $response;
        }
    }
}