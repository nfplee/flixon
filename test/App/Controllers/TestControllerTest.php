<?php

namespace App\Controllers;

use App\TestCase;

class TestControllerTest extends TestCase {
    public function testChildController() {
        // Arrange: Create the request.
        $request = $this->createRequest('/test/child-controller');

        // Act
        $response = $this->app->handle($request);

        // Assert
        $this->assertEquals(200, $response->statusCode);
        $this->assertStringContainsString('Canonical', $response->content);

        // Arrange: Create the same request again.
        $request = $this->createRequest('/test/child-controller');

        // Act
        $response = $this->app->handle($request);

        // Assert: Make sure the cache is returned.
        $this->assertEquals(200, $response->statusCode);
        $this->assertStringContainsString('Canonical', $response->content);
    }

    public function error() {
        // Arrange: Create the request.
        $request = $this->createRequest('/test/error');

        // Act
        $response = $this->app->handle($request);

        // Assert
        $this->assertEquals(500, $response->statusCode);
        $this->assertStringContainsString('<a href="/">Home</a> | Division by zero | Locale: en', $response->content);
    }

    public function testLangText() {
        // Arrange: Create the request.
        $request = $this->createRequest('/test/lang-text');

        // Act
        $response = $this->app->handle($request);

        // Assert
        $this->assertEquals(200, $response->statusCode);
        $this->assertStringContainsString('Overridden Foo | Bar', $response->content);
    }

    public function testLayoutAnnotation() {
        // Arrange: Create the request.
        $request = $this->createRequest('/test/layout-annotation');

        // Act
        $response = $this->app->handle($request);

        // Assert
        $this->assertEquals(200, $response->statusCode);
        $this->assertStringContainsString('Theme2 | View', $response->content);
    }

    public function testPageNotFound() {
        // Arrange: Create the request.
        $request = $this->createRequest('/test/page-not-found');

        // Fix annoying warning phpunit throws.
        ob_start();

        // Act
        $response = $this->app->handle($request);

        // Assert
        $this->assertEquals(404, $response->statusCode);
        $this->assertStringContainsString('<a href="/">Home</a> | Page Not Found | Locale: en', $response->content);
    }

    public function testResponseCache() {
        // Arrange: Create the request.
        $request = $this->createRequest('/test/response-cache');

        // Act
        $response = $this->app->handle($request);

        // Assert
        $this->assertEquals(200, $response->statusCode);
        $this->assertStringContainsString('View', $response->content);

        // Arrange: Create the same request again.
        $request = $this->createRequest('/test/response-cache');

        // Act
        $response = $this->app->handle($request);

        // Assert: Make sure the cache is returned.
        $this->assertEquals(200, $response->statusCode);
        $this->assertStringContainsString('View', $response->content);
    }

    public function testResponseCacheWithChildController() {
        // Arrange: Create the request.
        $request = $this->createRequest('/test/response-cache-with-child-controller');

        // Act
        $response = $this->app->handle($request);

        // Assert
        $this->assertEquals(200, $response->statusCode);
        $this->assertStringContainsString('Canonical', $response->content);

        // Arrange: Create the same request again.
        $request = $this->createRequest('/test/response-cache-with-child-controller');

        // Act
        $response = $this->app->handle($request);

        // Assert: Make sure the cache is returned.
        $this->assertEquals(200, $response->statusCode);
        $this->assertStringContainsString('Canonical', $response->content);
    }

    public function testUrlGenerator() {
        // Arrange: Create the request.
        $request = $this->createRequest('/test/url-generator');

        // Act
        $response = $this->app->handle($request);

        // Assert
        $this->assertEquals(200, $response->statusCode);
        $this->assertStringContainsString('<ul>
    <li>/ | / | /de</li>
    <li>/foo | /foo | /de/foo</li>
    <li>/foo.htm | /foo.htm | /de/foo.htm</li>
    <li>/blog | /blog | /de/blog</li>
    <li>/blog/foo | /blog/foo | /de/blog/foo</li>
    <li>/jobs | /jobs | /de/jobs</li>
    <li>/jobs/foo | /jobs/foo | /de/jobs/foo</li>
</ul>', $response->content);
    }

    public function testLocaleUrlGenerator() {
        // Arrange: Create the request.
        $request = $this->createRequest('/de/test/url-generator');

        // Act
        $response = $this->app->handle($request);

        // Assert
        $this->assertEquals(200, $response->statusCode);
        $this->assertStringContainsString('<ul>
    <li>/de | / | /de</li>
    <li>/de/foo | /foo | /de/foo</li>
    <li>/de/foo.htm | /foo.htm | /de/foo.htm</li>
    <li>/de/blog | /blog | /de/blog</li>
    <li>/de/blog/foo | /blog/foo | /de/blog/foo</li>
    <li>/de/jobs | /jobs | /de/jobs</li>
    <li>/de/jobs/foo | /jobs/foo | /de/jobs/foo</li>
</ul>', $response->content);
    }

    public function testView() {
        // Arrange: Create the request.
        $request = $this->createRequest('/test/view');

        // Act
        $response = $this->app->handle($request);

        // Assert
        $this->assertEquals(200, $response->statusCode);
        $this->assertStringContainsString('View', $response->content);
    }

    public function testViewWithModel() {
        // Arrange: Create the request.
        $request = $this->createRequest('/test/view-with-model');

        // Act
        $response = $this->app->handle($request);

        // Assert
        $this->assertEquals(200, $response->statusCode);
        $this->assertStringContainsString('View | Foo', $response->content);
    }

    public function testViewWithLayout() {
        // Arrange: Create the request.
        $request = $this->createRequest('/test/view-with-layout');

        // Act
        $response = $this->app->handle($request);

        // Assert
        $this->assertEquals(200, $response->statusCode);
        $this->assertStringContainsString('Theme | View', $response->content);
    }
}