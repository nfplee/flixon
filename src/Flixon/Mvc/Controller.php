<?php

namespace Flixon\Mvc;

use Flixon\DependencyInjection\Annotations\Inject;
use Flixon\Foundation\Traits\Application;
use Flixon\Http\Response;
use Flixon\Mvc\View;
use GUMP as Gump;

abstract class Controller {
	use Application;

    #[Inject(ModelState::class)]
    protected ModelState $modelState;

    public Response $response;

	#[Inject(View::class)]
    public View $view;

	public function alert(string $type, string $title, string $message, ?string $url = null): Response {
		return $this->render('shared/alert', [
			'type'		=> $type,
			'title'		=> $title,
			'message'	=> $message,
			'url'		=> $url
		]);
	}

    public function content(string $content): Response {
        // Set the response content.
        $this->response->content = $content;

		return $this->response;
    }

    public function csv(string $fileName, array $data, string $delimiter = ','): Response {
        return $this->file($fileName, 'text/csv', function() use ($data, $delimiter) {
            CsvHelpers::write('php://output', $data, $delimiter);
        });
    }

    public function file(string $fileName, string $contentType, callable $callback): Response {
        // Set the headers.
        $this->response->headers->set('Cache-Control', 'must-revalidate, post-check=0, pre-check=0');
        $this->response->headers->set('Content-Type', $contentType . '; charset=utf-8');
        $this->response->headers->set('Content-Disposition', 'attachment; filename="' . $fileName . '";');

        // Set the callback function.
        $this->response->callback = $callback;

        return $this->response;
    }

    public function json(mixed $data): Response {
        // Set the json header and content.
        $this->response->headers->set('Content-Type', 'application/json');

        // Return the content.
        return $this->content(json_encode($data, JSON_NUMERIC_CHECK));
    }

	public function redirect(string $url, int $statusCode = Response::HTTP_FOUND): Response {
		// Set the location header and status code.
		$this->response->headers->set('Location', $url);
		$this->response->statusCode = $statusCode;

		return $this->response;
	}

	public function render(string $view, array $model = [], bool $layout = true): Response {
		// Add the model state to the model.
		$model['modelState'] = $this->modelState;

        // Return the content.
		return $this->content($this->view->render($view, $model, $layout));
	}

	public function validate(array $data, array $validators, string $prefix = ''): bool {
		// Call the gump is_valid method (this returns an array of the errors if invalid or true if valid).
		$isValid = Gump::is_valid($data, $validators);

		// Set the errors.
		$this->modelState->setErrors($isValid !== true ? $isValid : [], $prefix);

		return $this->modelState->isValid($prefix);
	}
}