<?php

namespace App\Controllers;

use Flixon\Http\Response;
use Flixon\Mvc\Controller;
use Flixon\Routing\Annotations\Route;

#[Route('/blog', name: 'blog_')]
class BlogController extends Controller {
    #[Route('/{slug}', name: 'home')]
    public function index(string $slug = null): Response {
        return $this->content('Blog | Slug: ' . ($slug  ?? 'None') . ' | Locale: ' . $this->request->locale->format);
    }
}