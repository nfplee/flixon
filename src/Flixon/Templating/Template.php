<?php

namespace Flixon\Templating;

use Flixon\Foundation\Traits\Application;

class Template {
	use Application;

	private array $vars = [];

	public function add(string $name, mixed $value): Template {
		$this->vars[$name] = $value;

		return $this;
	}

	public function render(string $templateFile, array $vars = []): string {
		// Merge the variables.
		$vars = array_merge($this->vars, $vars);

		// Extract the variables.
		extract($vars);

		// Start buffering.
		ob_start();

        // Render the file.
		require $templateFile;

		// Return current buffer contents and delete current output buffer.
	    return ob_get_clean();
	}
}