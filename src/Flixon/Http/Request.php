<?php

namespace Flixon\Http;

use Exception;
use Flixon\Common\Traits\PropertyAccessor;
use Symfony\Component\HttpFoundation\Request as RequestBase;
use Symfony\Component\HttpFoundation\Session\Session;

class Request extends RequestBase {
    use PropertyAccessor;

    public $catch = true, $locale = null, $node = null, $parent = null, $user = null;

    public function isAjax(): bool {
        return strtolower($this->server->get('HTTP_X_REQUESTED_WITH')) == 'xmlhttprequest';
    }

    public function isBot(): bool {
        return preg_match('/bot|crawl|slurp|spider/i', $this->server->get('HTTP_USER_AGENT'));
    }

    public function isChildRequest(): bool {
        return isset($this->parent);
    }

    public function getRoot(): Request {
        return $this->parent ? $this->parent->root : $this;
    }

    public function getSession(): Session {
        // Make sure the session exists.
        if (!$this->hasSession()) {
            // Create the session (use the parent session if it exists).
            $session = $this->parent?->session ?? new Session();

            // Set the session.
            $this->setSession($session);
        }

        return parent::getSession();
    }
}