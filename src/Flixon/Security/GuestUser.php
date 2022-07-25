<?php

namespace Flixon\Security;

class GuestUser implements User {
	public $id, $roleId;

	public function __construct() {
        $this->id = null;
		$this->roleId = Roles::GUEST;
	}
}