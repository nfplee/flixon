<?php

namespace App\Controllers;

use Flixon\Http\Response;
use Flixon\Mvc\Controller;
use Flixon\Routing\Annotations\Route;

#[Route('/', priority: -1)]
class HomeController extends Controller {
    #[Route('/{slug}', name: 'home')]
    public function index(string $slug = null): Response {
        return $this->content('Home | Slug: ' . ($slug  ?? 'None') . ' | Locale: ' . $this->request->locale->format);
    }

    #[Route('/{slug}.htm', name: 'details', priority: 1)]
    public function details(string $slug): Response {
        return $this->render('home/details', [
            'slug' => $slug
        ]);
    }
}