<?php

namespace Flixon\Localization\Services;

use Flixon\Foundation\Application;
use Flixon\Http\Request;

class LanguageService {
    use \Flixon\Foundation\Traits\Application;

    private array $data = [];
    private string $path;

    public function __construct(Application $app) {
        $this->path = $app->rootPath . '/resources/lang/';
    }

    /**
     * Load language by locale.
     *
     * @param string $locale
     * @param string $name
     */
    public function load(string $locale): void {
        $file = $this->path . $locale . '.php';

        if (is_readable($file)) {
            $this->data[$locale] = require $file;
        }
    }

    /**
     * Get language value.
     *
     * @param string $value
     * @param string $locale
     *
     * @return string
     */
    public function get(string $value, string ...$arguments): string {
        // Make sure the local is not null (this prevents an error during testing).
        if ($this->request->locale !== null && !empty($this->data[$this->request->locale->format][$value])) {
            return vsprintf($this->data[$this->request->locale->format][$value], $arguments);
        } else if (!empty($this->data['default'][$value])) {
            return vsprintf($this->data['default'][$value], $arguments);
        } else {
            return vsprintf($value, $arguments);
        }
    }
}