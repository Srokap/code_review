<?php
namespace CodeReview\Issues;

class DeprecatedIssue extends AbstractIssue {

	public function __construct(array $params = array()) {
		$params['reason'] = 'deprecated';
		parent::__construct($params);
	}

	protected function getExplanation() {
		return "(deprecated since " . $this->data['version'] . ")"
			. ($this->data['fixinfoshort'] ? ' ' . $this->data['fixinfoshort'] : '');
	}
} 