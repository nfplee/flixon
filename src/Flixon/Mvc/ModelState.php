<?php

namespace Flixon\Mvc;

class ModelState {
	private array $errors = [];

	public function addError(string $key, string $message, string $prefix = ''): ModelState {
		$this->errors[$prefix][$key] = $message;

		return $this;
	}

	public function getErrors(string $prefix = ''): array {
		return $this->errors[$prefix];
	}

	public function isValid(string $prefix = ''): bool {
		return !array_key_exists($prefix, $this->errors) || count($this->errors[$prefix]) == 0;
	}

	public function setErrors(array $errors, string $prefix = ''): ModelState {
		$this->errors[$prefix] = $errors;

		return $this;
	}
}