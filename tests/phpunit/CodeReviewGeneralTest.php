<?php

class CodeReviewGeneralTest extends PHPUnit_Framework_TestCase {

	public function setUp() {
		$path = __DIR__ . '/test_files/fake_elgg/';

		require_once(__DIR__ . '/test_files/fake_elgg/engine/start.php');

		code_review::initConfig(array(
			'path' => $path,
			'pluginspath' => $path . 'mod/',
		));
	}

	function testGetDeprecatedFunctionsList() {

		$functions = code_review::getDeprecatedFunctionsList('1.2');
		$this->assertArrayHasKey('dummy_deprecated_function1', $functions);

		$deprecatedFunction = $functions['dummy_deprecated_function1'];
		$this->assertInstanceOf('CodeReview_Issues_Deprecated', $deprecatedFunction);
		$this->assertInstanceOf('ArrayAccess', $deprecatedFunction);
		$this->assertFalse(is_array($deprecatedFunction));

		$this->assertArrayHasKey('name', $deprecatedFunction);
		$this->assertArrayHasKey('version', $deprecatedFunction);
		$this->assertArrayHasKey('file', $deprecatedFunction);
		$this->assertArrayHasKey('line', $deprecatedFunction);
		$this->assertArrayHasKey('deprecated', $deprecatedFunction);
		$this->assertArrayHasKey('reason', $deprecatedFunction);
		$this->assertArrayHasKey('fixinfoshort', $deprecatedFunction);

		$this->assertEquals('dummy_deprecated_function1', $deprecatedFunction['name']);
		$this->assertEquals('1.1', $deprecatedFunction['version']);
		$this->assertEquals(true, $deprecatedFunction['deprecated']);
		$this->assertEquals('deprecated', $deprecatedFunction['reason']);
		$this->assertEquals('Remove it', $deprecatedFunction['fixinfoshort']);

		$this->assertEquals("Line " . $deprecatedFunction['line'] . ":\tFunction call: "
			. $deprecatedFunction['name'] . " (deprecated since 1.1) Remove it",
			(string)$deprecatedFunction);

		$functions = code_review::getDeprecatedFunctionsList('1.1');
		$this->assertArrayNotHasKey('dummy_deprecated_function1', $functions);
	}

	function testGetPrivateFunctionsList() {

		$functions = code_review::getPrivateFunctionsList();
		$this->assertArrayNotHasKey('dummy_deprecated_function1', $functions);

		/*
		 * foobar_private_api
		 */
		$this->assertArrayHasKey('foobar_private_api', $functions);
		$privateFunction = $functions['foobar_private_api'];
		$this->assertInstanceOf('CodeReview_Issues_Private', $privateFunction);
		$this->assertInstanceOf('ArrayAccess', $privateFunction);
		$this->assertFalse(is_array($privateFunction));

		$this->assertArrayHasKey('name', $privateFunction);
		$this->assertArrayHasKey('file', $privateFunction);
		$this->assertArrayHasKey('line', $privateFunction);
		$this->assertArrayHasKey('reason', $privateFunction);

		$this->assertEquals('foobar_private_api', $privateFunction['name']);
		$this->assertEquals('private', $privateFunction['reason']);

		$this->assertEquals("Line " . $privateFunction['line'] . ":\tFunction call: "
			. $privateFunction['name'] . " (use of function marked private is unsafe)",
			(string)$privateFunction);

		/*
		 * foobar_undocumented
		 */
		$this->assertArrayHasKey('foobar_undocumented', $functions);
		$undocumentedFunction = $functions['foobar_undocumented'];
		$this->assertInstanceOf('CodeReview_Issues_NotDocumented', $undocumentedFunction);
		$this->assertInstanceOf('ArrayAccess', $undocumentedFunction);
		$this->assertFalse(is_array($undocumentedFunction));

		$this->assertArrayHasKey('name', $undocumentedFunction);
		$this->assertArrayHasKey('file', $undocumentedFunction);
		$this->assertArrayHasKey('line', $undocumentedFunction);
		$this->assertArrayHasKey('reason', $undocumentedFunction);

		$this->assertEquals('foobar_undocumented', $undocumentedFunction['name']);
		$this->assertEquals('not_documented', $undocumentedFunction['reason']);

		$this->assertEquals("Line " . $undocumentedFunction['line'] . ":\tFunction call: "
			. $undocumentedFunction['name'] . " (use of undocumented core function is unsafe)",
			(string)$undocumentedFunction);
	}
}
