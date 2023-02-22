<?php

namespace App\Controllers;

use Flixon\Http\Response;
use Flixon\Mvc\Annotations\Layout;
use Flixon\Mvc\Controller;
use Flixon\Routing\Annotations\Route;

#[Route('/jobs', name: 'jobs_')]
class JobsController extends Controller {
    #[Route('/{slug}', name: 'home')]
    public function index(string $slug = null): Response {
        return $this->content('Jobs | Slug: ' . ($slug  ?? 'None') . ' | Locale: ' . $this->request->locale->format);
    }
}