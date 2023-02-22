<?php

namespace Flixon\Security\Services;

use Flixon\Security\User;

interface UsersService {
	public function getUserByUsername(string $username): ?User;
}