<?php

class CodeReview_Issues_Private extends CodeReview_Issues_Abstract {

	public function __construct(array $params = array()) {
		$params['reason'] = 'private';
		parent::__construct($params);
	}

	protected function getExplanation() {
		return "(use of function marked private is unsafe)";
	}
} 