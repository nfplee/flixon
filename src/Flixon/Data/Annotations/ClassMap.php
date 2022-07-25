<?php

namespace Flixon\Data\Annotations;

/**
 * @Annotation
 */
class ClassMap {
	public $table;

	public function __construct(array $values) {
		$this->table = $values['value'];
    }
}