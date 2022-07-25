<?php

namespace Flixon\Http;

use Symfony\Component\HttpFoundation\Cookie as BaseCookie;

class Cookie extends BaseCookie {
	use \Flixon\Common\Traits\PropertyAccessor;
}