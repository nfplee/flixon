<?php

namespace Flixon\Security;

enum Roles: int {
    const ADMIN = 1;
    const USER = 2;
    const GUEST = 3;
}