<?php

namespace App\Controllers;

use Flixon\Http\Annotations\ResponseCache;
use Flixon\Http\Response;
use Flixon\Logging\Logger;
use Flixon\Mvc\Annotations\Layout;
use Flixon\Mvc\Controller;
use Flixon\Routing\Annotations\Route;

#[Route('/test')]
class TestController extends Controller {
    private Logger $logger;

    public function __construct(Logger $logger) {
        $this->logger = $logger;
    }

    #[Route('/child-controller')]
    public function childController(): Response {
        $this->logger->info('Child Controller');

        return $this->render('test/child-controller', [], false);
    }

    #[Route('/error')]
    public function error(): Response {
        return $this->render('test/error', [], false);
    }

    #[Route('/lang-text')]
    public function langText(): Response {
        return $this->render('test/lang-text', [], false);
    }

    #[Route('/layout-annotation')]
    #[Layout('themes/theme2/views/shared/layout')]
    public function layoutAnnotation(): Response {
        return $this->render('test/view');
    }

    #[Route('/response-cache')]
    #[ResponseCache]
    public function responseCache(): Response {
        $this->logger->info('Response Cache');

        return $this->render('test/view', [], false);
    }

    #[Route('/response-cache-with-child-controller')]
    #[ResponseCache]
    public function responseCacheWithChildController(): Response {
        $this->logger->info('Response Cache With Child Controller');

        return $this->render('test/child-controller', [], false);
    }

    #[Route('/route-with-parameters/{param1}-{param2}', name: 'route_with_parameters')]
    public function routeWithParameters(string $param1, string $param2): Response {
        return $this->render('test/route-with-parameters', [
            'param1' => $param1,
            'param2' => $param2
        ], false);
    }

    #[Route('/route-with-parameters-reverse/{param2}-{param1}', name: 'route_with_parameters_reverse')]
    public function routeWithParametersReverse(string $param1, string $param2): Response {
        return $this->render('test/route-with-parameters-reverse', [
            'param1' => $param1,
            'param2' => $param2
        ], false);
    }

    #[Route('/url-generator')]
    public function urlGenerator(): Response {
        return $this->render('test/url-generator', [], false);
    }

    #[Route('/view')]
    public function view(): Response {
        return $this->render('test/view', [], false);
    }

    #[Route('/view-with-model')]
    public function viewWithModel(): Response {
        return $this->render('test/view-with-model', [
            'foo' => 'Foo'
        ], false);
    }

    #[Route('/view-with-layout')]
    public function viewWithLayout(): Response {
        return $this->render('test/view');
    }
}