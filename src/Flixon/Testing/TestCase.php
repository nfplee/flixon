<?php

namespace Flixon\Testing;

use Flixon\Foundation\Application;
use Flixon\Foundation\Environment;
use Flixon\Http\Request;
use Flixon\Security\User;
use Flixon\Security\Services\UsersService;
use PHPUnit\Framework\TestCase as TestCaseBase;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

abstract class TestCase extends TestCaseBase {
    use \Flixon\Foundation\Traits\Application;

    protected string $rootPath = __DIR__ . '/../../..';

    public function createApplication(): Application {
        // Create the application.
        $app = new Application(Environment::TESTING, $this->rootPath);

        // Add the modules.
        $app->modules
            ->add(\Flixon\Common\CommonModule::class) // Add this module first so we can use the caching service when registering the other modules.
            ->add(\Flixon\Config\ConfigModule::class)
            ->add(\Flixon\Data\DataModule::class)
            ->add(\Flixon\Events\EventsModule::class)
            ->add(\Flixon\Exceptions\ExceptionsModule::class)
            ->add(\Flixon\Http\HttpModule::class)
            ->add(\Flixon\Localization\LocalizationModule::class)
            ->add(\Flixon\Logging\LoggingModule::class)
            ->add(\Flixon\Mvc\MvcModule::class)
            ->add(\Flixon\Routing\RoutingModule::class)
            ->add(\Flixon\Scheduling\SchedulingModule::class)
            ->add(\Flixon\Security\SecurityModule::class)
            ->add(\Flixon\SiteMap\SiteMapModule::class);

        return $app;
    }

    public function createRequest(string $uri, string $method = 'GET', array $parameters = [], array $cookies = [], array $files = [], array $server = [], ?string $content = null): Request {
        // Create the request.
        $request = Request::create($uri, $method, $parameters, $cookies, $files, $server, $content);

        // Set the session storage.
        $request->session = new Session(new MockArraySessionStorage());

        return $request;
    }
    
    public function setUp(): void {
        // Override the memory limit (this prevents the fatal error "Allowed memory size of 134217728 bytes exhausted" from throwing).
        ini_set('memory_limit', '-1');

        // Create the application and register the modules.
        // Make sure you set the app property as the one injected in the Application trait is set before the application is created.
        $this->app = $this->createApplication()->register();
    }
}