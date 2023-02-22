<?php

namespace App\Services;

use App\Models\User;
use Flixon\Security\Services\UsersService as UsersServiceInterface;
use Flixon\Security\User as UserInterface;

class UsersService implements UsersServiceInterface {
    public function getUserByUsername(string $username): ?UserInterface {
		return new User($username);
	}
}