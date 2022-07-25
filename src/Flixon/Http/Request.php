<?php

namespace Flixon\Http;

use Exception;
use Symfony\Component\HttpFoundation\Request as BaseRequest;
use Symfony\Component\HttpFoundation\Session\Session;

class Request extends BaseRequest {
    use \Flixon\Common\Traits\PropertyAccessor;

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

    // This fixes an issue where the request uri is appending /index.php to the uri.
    public function getRequestUri() {
        return str_replace('/index.php', '/', $this->server->get('REQUEST_URI'));
    }

    public function getRoot(): Request {
        return $this->parent ? $this->parent->root : $this;
    }

    public function getSession() {
        // Make sure the session exists.
        if (!$this->hasSession()) {
            // Create the session (use the parent session if it exists).
            $session = $this->parent != null ? $this->parent->session : new Session();

            // Start the session (if it hasn't been already).
            //if (!$session->isStarted()) {
            //    $session->start();
            //}

            // Set the session.
            $this->setSession($session);
        }

        return parent::getSession();
    }
}