<?php

namespace Flixon\Mvc\Annotations;

/**
 * @Annotation
 */
class Layout {
	public $layout;

	public function __construct(array $values) {
		$this->layout = $values['value'];
	}
	
	public static function __set_state(array $array): Layout {
        return new Layout(['value' => $array['layout']]);
    }
}