<?php

namespace Flixon\Exceptions;

use Flixon\Exceptions\Middleware\ExceptionMiddleware;
use Flixon\Foundation\Application;
use Flixon\Foundation\Environment;
use Flixon\Foundation\Module;
use Flixon\Http\Response;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Throwable;

class ExceptionsModule extends Module {
	public function register(Application $app): void {
		// Add the middleware.
        $app->middleware->add(ExceptionMiddleware::class, 1300, [
            'errorHandlers' => [
                [
                    'class'         => AccessDeniedException::class,
                    'handler'       => 'App\Controllers\ErrorController::accessDenied',
                    'statusCode'    => Response::HTTP_FORBIDDEN
                ],
                [
                    'class'         => MethodNotAllowedException::class,
                    'handler'       => 'App\Controllers\ErrorController::pageNotFound',
                    'statusCode'    => Response::HTTP_NOT_FOUND
                ],
                [
                    'class'         => ResourceNotFoundException::class,
                    'handler'       => 'App\Controllers\ErrorController::pageNotFound',
                    'statusCode'    => Response::HTTP_NOT_FOUND
                ],
                [
                    'class'         => Throwable::class,
                    'handler'       => 'App\Controllers\ErrorController::index',
                    'statusCode'    => Response::HTTP_INTERNAL_SERVER_ERROR
                ]
            ]
        ]);

        // Set the debug flag.
        switch ($app->environment) {
            case Environment::DEVELOPMENT:
            case Environment::TESTING:
                error_reporting(E_ALL);
                break;
            case Environment::PRODUCTION:
                error_reporting(0);
                break;
        }
    }
}