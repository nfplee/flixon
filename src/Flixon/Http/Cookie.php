<?php

namespace Flixon\Http;

use Flixon\Common\Traits\PropertyAccessor;
use Symfony\Component\HttpFoundation\Cookie as CookieBase;

class Cookie extends CookieBase {
    use PropertyAccessor;
}