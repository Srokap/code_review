<?php

class CodeReview_Issues_Deprecated extends CodeReview_Issues_Abstract {

	public function __construct(array $params = array()) {
		$params['reason'] = 'deprecated';
		parent::__construct($params);
	}

	protected function getExplanation() {
		return "(deprecated since " . $this->data['version'] . ")"
			. ($this->data['fixinfoshort'] ? ' ' . $this->data['fixinfoshort'] : '');
	}
} 