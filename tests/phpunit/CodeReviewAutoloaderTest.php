<?php
namespace Srokap\CodeReview\Tests;

class CodeReviewAutoloaderTest extends \PHPUnit_Framework_TestCase {

	public function testRegister() {
		$autoloader = new \CodeReviewAutoloader();

		$this->assertFalse($autoloader->unregister());
		//double register
		$this->assertTrue($autoloader->register());
		$this->assertTrue($autoloader->register());
		//unregister just once
		$this->assertTrue($autoloader->unregister());
		$this->assertFalse($autoloader->unregister());
	}

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

	public function testClassDoesNotExists() {
		$classes = array(
			'code_review_non_existing',
			'CodeReview_FooTestClass',
			'CodeReviewFoo_TestClass',
			'CodeReviewFooTestClass',
			'CodeReview/Foo/TestClass',
			'CodeReview_Foo/TestClass',
			'CodeReview/Foo_TestClass',
		);
		foreach ($classes as $class) {
			$this->assertFalse(class_exists($class));
		}
	}

}