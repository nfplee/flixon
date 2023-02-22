<?php

namespace App\Controllers;

use Flixon\Http\Annotations\ResponseCache;
use Flixon\Http\Response;
use Flixon\Logging\Logger;
use Flixon\Mvc\Controller;

class WidgetsController extends Controller {
    private Logger $logger;

    public function __construct(Logger $logger) {
        $this->logger = $logger;
    }

    #[ResponseCache]
    public function canonical(): Response {
        $this->logger->info('Canonical Widget');

        return $this->content('Canonical');
    }
}