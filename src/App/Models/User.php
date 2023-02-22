<?php

namespace App\Models;

use Flixon\Security\Roles;
use Flixon\Security\User as UserInterface;

class User implements UserInterface {
    public ?int $id;
	public int $roleId;
    public string $username;

    public function __construct(string $username, ?int $roleId = null) {
        $this->id = 1;
		$this->roleId = $roleId ?? ($username == 'admin' ? Roles::ADMIN : Roles::USER);
        $this->username = $username;
    }
}