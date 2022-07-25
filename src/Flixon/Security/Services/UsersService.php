<?php

namespace Flixon\Security\Services;

interface UsersService {
	public function getUserByUsername(string $username);
}