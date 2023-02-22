<?php

namespace App\Controllers;

use App\Models\User;
use App\TestCase;
use Flixon\Security\Roles;

class AdminControllerTest extends TestCase {
    public function testAuthenticated() {
        // Arrange: Create the request.
        $request = $this->createRequest('/admin');
        $request->session->set('username', 'admin');

        // Act
        $response = $this->app->handle($request);

        // Assert
        $this->assertEquals(200, $response->statusCode);
        $this->assertStringContainsString('Admin | admin', $response->content);
    }

    public function testNotAuthenticated() {
        // Arrange: Create the request.
        $request = $this->createRequest('/admin');

        // Fix annoying warning phpunit throws.
        ob_start();

        // Act
        $response = $this->app->handle($request);

        // Assert
        $this->assertEquals(403, $response->statusCode);
        $this->assertStringContainsString('<a href="/">Home</a> | Access Denied', $response->content);
    }
}