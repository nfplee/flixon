<?php

namespace Flixon\Security;

class GuestUser implements User {
	public ?int $id;
	public int $roleId;

	public function __construct() {
        $this->id = null;
		$this->roleId = Roles::GUEST;
	}
}