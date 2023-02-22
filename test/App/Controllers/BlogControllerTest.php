<?php

namespace App\Controllers;

use App\TestCase;

class BlogControllerTest extends TestCase {
    public function testHome() {
        // Arrange: Create the request.
        $request = $this->createRequest('/blog');

        // Act
        $response = $this->app->handle($request);

        // Assert
        $this->assertEquals(200, $response->statusCode);
        $this->assertStringContainsString('Blog | Slug: None | Locale: en', $response->content);
    }

    public function testLocaleHome() {
        // Arrange: Create the request.
        $request = $this->createRequest('/de/blog');

        // Act
        $response = $this->app->handle($request);

        // Assert
        $this->assertEquals(200, $response->statusCode);
        $this->assertStringContainsString('Blog | Slug: None | Locale: de', $response->content);
    }

    public function testHomeWithSlug() {
        // Arrange: Create the request.
        $request = $this->createRequest('/blog/foo');

        // Act
        $response = $this->app->handle($request);

        // Assert
        $this->assertEquals(200, $response->statusCode);
        $this->assertStringContainsString('Blog | Slug: foo | Locale: en', $response->content);
    }

    public function testLocaleHomeWithSlug() {
        // Arrange: Create the request.
        $request = $this->createRequest('/de/blog/foo');

        // Act
        $response = $this->app->handle($request);

        // Assert
        $this->assertEquals(200, $response->statusCode);
        $this->assertStringContainsString('Blog | Slug: foo | Locale: de', $response->content);
    }
}