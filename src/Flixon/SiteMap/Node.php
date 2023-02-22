<?php

namespace Flixon\SiteMap;

interface Node {
	function getPath(): array;
	function getRoot(): Node;
	function isAdmin(): bool;
}