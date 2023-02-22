<?php

namespace App\Services;

use App\Models\Node;
use Flixon\SiteMap\Services\SiteMapService as SiteMapServiceInterface;
use Flixon\SiteMap\Node as NodeInterface;

class SiteMapService implements SiteMapServiceInterface {
    public function getNodeByController(string $controller): ?NodeInterface {
        return new Node($controller);
    }
}