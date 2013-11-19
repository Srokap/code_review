<?php

class CodeReviewAutoloaderTest extends PHPUnit_Framework_TestCase {

	public function testClassExists() {
		$classes = array(
			'code_review',
			'CodeFixer',
			'CodeReviewAnalyzer',
			'CodeReviewFileFilterIterator',
			'PhpFileParser',
			'PhpTokensFilterIterator',
			'CodeReview_Foo_TestClass',
		);
		foreach ($classes as $class) {
			$this->assertTrue(class_exists($class));
		}
	}

}