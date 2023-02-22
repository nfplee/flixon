<?php

namespace App\Controllers;

use App\TestCase;

class JobsControllerTest extends TestCase {
    public function testHome() {
        // Arrange: Create the request.
        $request = $this->createRequest('/jobs');

        // Act
        $response = $this->app->handle($request);

        // Assert
        $this->assertEquals(200, $response->statusCode);
        $this->assertStringContainsString('Jobs | Slug: None | Locale: en', $response->content);
    }

    public function testLocaleHome() {
        // Arrange: Create the request.
        $request = $this->createRequest('/de/jobs');

        // Act
        $response = $this->app->handle($request);

        // Assert
        $this->assertEquals(200, $response->statusCode);
        $this->assertStringContainsString('Jobs | Slug: None | Locale: de', $response->content);
    }

    public function testHomeWithSlug() {
        // Arrange: Create the request.
        $request = $this->createRequest('/jobs/foo');

        // Act
        $response = $this->app->handle($request);

        // Assert
        $this->assertEquals(200, $response->statusCode);
        $this->assertStringContainsString('Jobs | Slug: foo | Locale: en', $response->content);
    }

    public function testLocaleHomeWithSlug() {
        // Arrange: Create the request.
        $request = $this->createRequest('/de/jobs/foo');

        // Act
        $response = $this->app->handle($request);

        // Assert
        $this->assertEquals(200, $response->statusCode);
        $this->assertStringContainsString('Jobs | Slug: foo | Locale: de', $response->content);
    }
}