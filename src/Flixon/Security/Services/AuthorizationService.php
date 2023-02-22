<?php

namespace Flixon\Security\Services;

use Flixon\Security\User;

class AuthorizationService {
    public function isAllowed(User $user, int $role): bool {
        return $user->roleId == $role;
    }
}