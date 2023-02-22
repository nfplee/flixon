<?php

namespace App\Controllers;

use App\TestCase;

class HomeControllerTest extends TestCase {
    public function testHome() {
        // Arrange: Create the request.
        $request = $this->createRequest('/');

        // Act
        $response = $this->app->handle($request);

        // Assert
        $this->assertEquals(200, $response->statusCode);
        $this->assertStringContainsString('Home | Slug: None | Locale: en', $response->content);
    }

    public function testLocaleHome() {
        // Arrange: Create the request.
        $request = $this->createRequest('/de');

        // Act
        $response = $this->app->handle($request);

        // Assert
        $this->assertEquals(200, $response->statusCode);
        $this->assertStringContainsString('Home | Slug: None | Locale: de', $response->content);
    }

    public function testHomeWithSlug() {
        // Arrange: Create the request.
        $request = $this->createRequest('/foo');

        // Act
        $response = $this->app->handle($request);

        // Assert
        $this->assertEquals(200, $response->statusCode);
        $this->assertStringContainsString('Home | Slug: foo | Locale: en', $response->content);
    }

    public function testLocaleHomeWithSlug() {
        // Arrange: Create the request.
        $request = $this->createRequest('/de/foo');

        // Act
        $response = $this->app->handle($request);

        // Assert
        $this->assertEquals(200, $response->statusCode);
        $this->assertStringContainsString('Home | Slug: foo | Locale: de', $response->content);
    }

    public function testDetails() {
        // Arrange: Create the request.
        $request = $this->createRequest('/foo.htm');

        // Act
        $response = $this->app->handle($request);

        // Assert
        $this->assertEquals(200, $response->statusCode);
        $this->assertStringContainsString('Theme | Details View | Slug: foo | Locale: en', $response->content);
    }

    public function testLocaleDetails() {
        // Arrange: Create the request.
        $request = $this->createRequest('/de/foo.htm');

        // Act
        $response = $this->app->handle($request);

        // Assert
        $this->assertEquals(200, $response->statusCode);
        $this->assertStringContainsString('Theme | Details View | Slug: foo | Locale: de', $response->content);
    }
}