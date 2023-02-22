<?php

namespace Flixon\Security\Services;

use Flixon\Security\User;

interface AuthenticationService {
	public function authenticate(User $user, string $password): bool;
	public function getUser(): User;
	public function login(User $user, bool $remember = false): void;
	public function logout(): void;
}