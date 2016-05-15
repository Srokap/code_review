<?php
namespace CodeReview\Issues;

class NotDocumentedIssue extends AbstractIssue {

	public function __construct(array $params = array()) {
		$params['reason'] = 'not_documented';
		parent::__construct($params);
	}

	protected function getExplanation() {
		return "(use of undocumented core function is unsafe)";
	}
} 