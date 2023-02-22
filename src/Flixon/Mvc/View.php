<?php

namespace Flixon\Mvc;

use Flixon\Config\Config;
use Flixon\Foundation\Application;
use Flixon\Http\Request;
use Flixon\Templating\Template;

/**
 * This class must extend Template rather that take a dependency on it. This allows you to say $this within the view/template and it will point to an instance of this class.
 */
class View extends Template {
    protected string $body, $rootPath;
    protected array $section = [];
    public string $layout;

    /**
     * This is merged with the model when rendering the view. It also allows you to access the model in derived classes.
     */
    protected array $model = [];

    /**
     * If you don't set the request in the constructor here then when you say $this->request it would be overridden by any child requests.
     */
    protected Request $request;

    public function __construct(Application $app, Config $config, Request $request) {
        $this->rootPath = $app->rootPath;
        $this->layout = 'themes/' . $config->theming->defaultTheme . '/views/shared/layout';
        $this->request = $request;
    }

    public function controller(string $controller, array $attributes = []): string {
        // Create a child request.
        $request = new Request();

        // Set the controller add the attributes.
        $request->attributes->set('_controller', $controller);
        $request->attributes->add($attributes);

        // Handle the request.
        return $this->app->handle($request, $this->request, false)->content;
    }

    public function render(string $view, array $model = [], bool $layout = true): string {
        // Merge the model.
        $this->model = array_merge($this->model, $model);

        // Get the body contents.
        $this->body = parent::render($this->rootPath . '/resources/views/' . $view . '.php', $this->model);

        // Return the content.
        if (!empty($this->layout) && $layout) {
            return parent::render($this->rootPath . '/resources/' . $this->layout . '.php', $this->model);
        } else {
            return $this->body;
        }
    }

    public function section(string $section): string {
        if (array_key_exists($section, $this->section)) {
            return $this->section[$section];
        } else {
            return '';
        }
    }

    public function template(string $template, array $model = []) {
        return parent::render($this->rootPath . '/resources/views/shared/templates/' . $template . '.php', $model);
    }
}