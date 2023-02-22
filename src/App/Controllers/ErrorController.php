<?php

namespace App\Controllers;

use Flixon\Http\Response;
use Flixon\Mvc\Controller;
use Flixon\Routing\Annotations\Route;
use Throwable;

#[Route('/error', name: 'error_')]
class ErrorController extends Controller {
    #[Route('/', name: 'home')]
    public function index(Throwable $exception) {
        return $this->render('error/index', [
            'exception'    => $exception
        ], false);
    }

    #[Route('/access-denied', name: 'access_denied')]
    public function accessDenied() {
        return $this->render('error/access-denied', [], false);
    }

    #[Route('/page-not-found', name: 'page_not_found')]
    public function pageNotFound() {
        return $this->render('error/page-not-found', [], false);
    }
}