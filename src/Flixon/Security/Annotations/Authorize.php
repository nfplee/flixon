<?php

namespace Flixon\Security\Annotations;

/**
 * @Annotation
 */
class Authorize {
	public $negate, $roles;

	public function __construct(array $values) {
		$this->negate = $values['negate'] ?? false;
		$this->roles = is_array($values['value']) ? $values['value'] : [$values['value']];
	}
	
	public static function __set_state(array $array): Authorize {
        return new Authorize(['negate' => $array['negate'], 'value' => $array['roles']]);
    }
}