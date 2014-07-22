<?php

class CodeReview_Issues_NotDocumented extends CodeReview_Issues_Abstract {

	public function __construct(array $params = array()) {
		$params['reason'] = 'not_documented';
		parent::__construct($params);
	}

	protected function getExplanation() {
		return "(use of undocumented core function is unsafe)";
	}
} 