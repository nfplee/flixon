<?php

namespace App\Models;

use Flixon\SiteMap\Node as NodeInterface;

class Node implements NodeInterface {
    public string $controller;
    public array $urlParameters = [];

    public function __construct(string $controller) {
        $this->controller = $controller;
    }

    public function getPath(): array {
        return [];
    }

	public function getRoot(): NodeInterface {
        return $this;
    }

	public function isAdmin(): bool {
        return str_contains($this->controller, 'AdminController');
    }
}