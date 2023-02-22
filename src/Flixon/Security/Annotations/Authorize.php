<?php

namespace Flixon\Security\Annotations;

use Attribute;

#[Attribute]
class Authorize {
    public bool $negate;
    public array $roles;

    public function __construct(int|array $roles, bool $negate = false) {
        $this->roles = is_array($roles) ? $roles : [$roles];
        $this->negate = $negate;
    }
}