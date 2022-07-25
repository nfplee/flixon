<?php

namespace Flixon\DependencyInjection\Annotations;

/**
 * @Annotation
 */
class Inject {
	public $class;

	public function __construct(array $values) {
		$this->class = $values['value'];
    }
}