<?php

class CodeReviewGeneralTest extends PHPUnit_Framework_TestCase {

	public function setUp() {
		$path = dirname(__FILE__) . '/test_files/fake_elgg/';

		require_once($path . 'engine/start.php');

		code_review::initConfig(array(
			'path' => $path,
			'engine_path' => $path . 'engine/',
			'pluginspath' => $path . 'mod/',
		));
	}

	public function testGetDeprecatedFunctionsList() {

		$functions = code_review::getDeprecatedFunctionsList('1.2');
		$this->assertArrayHasKey('dummy_deprecated_function1', $functions);
		$this->assertArrayHasKey('dummy_deprecated_function2', $functions);

		$deprecatedFunction1 = $functions['dummy_deprecated_function1'];
		$this->assertInstanceOf('CodeReview_Issues_Deprecated', $deprecatedFunction1);
		$this->assertInstanceOf('ArrayAccess', $deprecatedFunction1);
		$this->assertFalse(is_array($deprecatedFunction1));

		$this->assertArrayHasKey('name', $deprecatedFunction1);
		$this->assertArrayHasKey('version', $deprecatedFunction1);
		$this->assertArrayHasKey('file', $deprecatedFunction1);
		$this->assertArrayHasKey('line', $deprecatedFunction1);
		$this->assertArrayHasKey('deprecated', $deprecatedFunction1);
		$this->assertArrayHasKey('reason', $deprecatedFunction1);
		$this->assertArrayHasKey('fixinfoshort', $deprecatedFunction1);

		$this->assertEquals('dummy_deprecated_function1', $deprecatedFunction1['name']);
		$this->assertEquals('1.1', $deprecatedFunction1['version']);
		$this->assertEquals(true, $deprecatedFunction1['deprecated']);
		$this->assertEquals('deprecated', $deprecatedFunction1['reason']);
		$this->assertEquals('Remove it', $deprecatedFunction1['fixinfoshort']);

		$this->assertEquals("Line " . $deprecatedFunction1['line'] . ":\tFunction call: "
			. $deprecatedFunction1['name'] . " (deprecated since 1.1) Remove it",
			(string)$deprecatedFunction1);

		$deprecatedFunction2 = $functions['dummy_deprecated_function2'];
		$this->assertInstanceOf('CodeReview_Issues_Deprecated', $deprecatedFunction2);
		$this->assertInstanceOf('ArrayAccess', $deprecatedFunction2);
		$this->assertFalse(is_array($deprecatedFunction2));

		$this->assertArrayHasKey('name', $deprecatedFunction2);
		$this->assertArrayHasKey('version', $deprecatedFunction2);
		$this->assertArrayHasKey('file', $deprecatedFunction2);
		$this->assertArrayHasKey('line', $deprecatedFunction2);
		$this->assertArrayHasKey('deprecated', $deprecatedFunction2);
		$this->assertArrayHasKey('reason', $deprecatedFunction2);
		$this->assertArrayHasKey('fixinfoshort', $deprecatedFunction2);

		$this->assertEquals('dummy_deprecated_function2', $deprecatedFunction2['name']);
		$this->assertEquals('1.2', $deprecatedFunction2['version']);
		$this->assertEquals(true, $deprecatedFunction2['deprecated']);
		$this->assertEquals('deprecated', $deprecatedFunction2['reason']);
		$this->assertEquals(false, $deprecatedFunction2['fixinfoshort']);

		$this->assertEquals("Line " . $deprecatedFunction2['line'] . ":\tFunction call: "
			. $deprecatedFunction2['name'] . " (deprecated since 1.2)",
			(string)$deprecatedFunction2);

		$functions = code_review::getDeprecatedFunctionsList('1.1');
		$this->assertArrayNotHasKey('dummy_deprecated_function1', $functions);
		$this->assertArrayNotHasKey('dummy_deprecated_function2', $functions);
	}

	public function testGetPrivateFunctionsList() {

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
