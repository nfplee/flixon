<?php

namespace App\Controllers;

use Flixon\Http\Response;
use Flixon\Mvc\Controller;
use Flixon\Routing\Annotations\Route;
use Flixon\Security\Annotations\Authorize;
use Flixon\Security\Roles;

#[Authorize(Roles::ADMIN)]
#[Route('/admin', name: 'admin_')]
class AdminController extends Controller {
    #[Route(name: 'home')]
    public function index(): Response {
        return $this->content('Admin | ' . $this->request->user->username);
    }
}