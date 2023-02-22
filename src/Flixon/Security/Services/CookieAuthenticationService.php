<?php

namespace Flixon\Security\Services;

use Flixon\Http\Cookie;
use Flixon\Http\CookieCollection;
use Flixon\Http\Request;
use Flixon\Security\GuestUser;
use Flixon\Security\User;

class CookieAuthenticationService implements AuthenticationService {
	private CookieCollection $cookies;
	private Request $request;
	private UsersService $usersService;

    public function __construct(CookieCollection $cookies, Request $request, UsersService $usersService) {
    	$this->cookies = $cookies;
        $this->request = $request;
        $this->usersService = $usersService;
    }

	public function authenticate(User $user, string $password): bool {
		return $user->password == hash('sha256', $password . $user->salt);
	}

	public function getUser(): User {
		// Try to get the user (first try to get it from the session and then user's cookies).
		// Note: If the session exists then we know they have already been authenticated.
		if ($this->request->session->has('username')) {
			$user = $this->usersService->getUserByUsername($this->request->session->get('username'));
		} else if ($this->request->cookies->has('username') && $this->request->cookies->has('token')) {
			$user = $this->usersService->getUserByUsername($this->request->cookies->get('username'));

			// If a matching user found and the token matches then re-issue the cookie else set the user to null.
			if ($user && $this->request->cookies->get('token') == hash('sha256', $user->username . $user->salt)) {
				$this->login($user, true);
			} else {
				$user = null;
			}
		}

		// Return the user.
		return $user ?? new GuestUser();
	}

	public function login(User $user, bool $remember = false): void {
		// Store the logged in username in the session.
		$this->request->session->set('username', $user->username);
		
		// Add a cookie if remember is true.
		if ($remember) {
			$this->cookies->add(new Cookie('username', $user->username, time() + 60 * 60 * 24 * 14));
			$this->cookies->add(new Cookie('token', hash('sha256', $user->username . $user->salt), time() + 60 * 60 * 24 * 14));
		}
	}
	
	public function logout(): void {
		// Log the user out.
		$this->request->session->remove('username');

		// Delete the cookies.
		$this->cookies->add(new Cookie('username', null, time() - 60 * 60));
		$this->cookies->add(new Cookie('token', null, time() - 60 * 60));
	}
}