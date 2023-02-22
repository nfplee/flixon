<?php

namespace Flixon\SiteMap\Services;

use Flixon\SiteMap\Node;

interface SiteMapService {
	public function getNodeByController(string $controller): ?Node;
}