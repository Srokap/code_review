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

	/**
	 * Should check that code_review::getDeprecatedFunctionsList returns expected results for functions defined in deprecated-*
	 */
	public function testGetDeprecatedFunctionsList12() {
		$functions = code_review::getDeprecatedFunctionsList('1.2');
		$this->assertCount(5, $functions);
		$this->assertArrayHasKey('dummy_deprecated_function1', $functions);
		$this->assertArrayHasKey('dummy_deprecated_function2', $functions);
		$this->assertArrayHasKey('foobardummyclass::deprecatedprivate', $functions);
		$this->assertArrayHasKey('foobardummyclass::deprecatedprotected', $functions);
		$this->assertArrayHasKey('foobardummyclass::deprecatedpublic', $functions);

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

		$deprecatedFunction3 = $functions['foobardummyclass::deprecatedprivate'];
		$this->assertInstanceOf('CodeReview_Issues_Deprecated', $deprecatedFunction3);
		$this->assertInstanceOf('ArrayAccess', $deprecatedFunction3);
		$this->assertFalse(is_array($deprecatedFunction3));
		$this->assertArrayHasKey('name', $deprecatedFunction3);
		$this->assertArrayHasKey('version', $deprecatedFunction3);
		$this->assertArrayHasKey('file', $deprecatedFunction3);
		$this->assertArrayHasKey('line', $deprecatedFunction3);
		$this->assertArrayHasKey('deprecated', $deprecatedFunction3);
		$this->assertArrayHasKey('reason', $deprecatedFunction3);
		$this->assertArrayHasKey('fixinfoshort', $deprecatedFunction3);
		$this->assertEquals('FoobarDummyClass::deprecatedPrivate', $deprecatedFunction3['name']);
		$this->assertEquals('1.2', $deprecatedFunction3['version']);
		$this->assertEquals(true, $deprecatedFunction3['deprecated']);
		$this->assertEquals('deprecated', $deprecatedFunction3['reason']);
		$this->assertEquals('Deprecated private class method.', $deprecatedFunction3['fixinfoshort']);
		$this->assertEquals("Line " . $deprecatedFunction3['line'] . ":\tFunction call: "
			. $deprecatedFunction3['name'] . " (deprecated since 1.2) Deprecated private class method.",
			(string)$deprecatedFunction3);

		$deprecatedFunction4 = $functions['foobardummyclass::deprecatedprotected'];
		$this->assertInstanceOf('CodeReview_Issues_Deprecated', $deprecatedFunction4);
		$this->assertInstanceOf('ArrayAccess', $deprecatedFunction4);
		$this->assertFalse(is_array($deprecatedFunction4));
		$this->assertArrayHasKey('name', $deprecatedFunction4);
		$this->assertArrayHasKey('version', $deprecatedFunction4);
		$this->assertArrayHasKey('file', $deprecatedFunction4);
		$this->assertArrayHasKey('line', $deprecatedFunction4);
		$this->assertArrayHasKey('deprecated', $deprecatedFunction4);
		$this->assertArrayHasKey('reason', $deprecatedFunction4);
		$this->assertArrayHasKey('fixinfoshort', $deprecatedFunction4);
		$this->assertEquals('FoobarDummyClass::deprecatedProtected', $deprecatedFunction4['name']);
		$this->assertEquals('1.2', $deprecatedFunction4['version']);
		$this->assertEquals(true, $deprecatedFunction4['deprecated']);
		$this->assertEquals('deprecated', $deprecatedFunction4['reason']);
		$this->assertEquals('Deprecated protected class method.', $deprecatedFunction4['fixinfoshort']);
		$this->assertEquals("Line " . $deprecatedFunction4['line'] . ":\tFunction call: "
			. $deprecatedFunction4['name'] . " (deprecated since 1.2) Deprecated protected class method.",
			(string)$deprecatedFunction4);

		$deprecatedFunction5 = $functions['foobardummyclass::deprecatedpublic'];
		$this->assertInstanceOf('CodeReview_Issues_Deprecated', $deprecatedFunction5);
		$this->assertInstanceOf('ArrayAccess', $deprecatedFunction5);
		$this->assertFalse(is_array($deprecatedFunction5));
		$this->assertArrayHasKey('name', $deprecatedFunction5);
		$this->assertArrayHasKey('version', $deprecatedFunction5);
		$this->assertArrayHasKey('file', $deprecatedFunction5);
		$this->assertArrayHasKey('line', $deprecatedFunction5);
		$this->assertArrayHasKey('deprecated', $deprecatedFunction5);
		$this->assertArrayHasKey('reason', $deprecatedFunction5);
		$this->assertArrayHasKey('fixinfoshort', $deprecatedFunction5);
		$this->assertEquals('FoobarDummyClass::deprecatedPublic', $deprecatedFunction5['name']);
		$this->assertEquals('1.1', $deprecatedFunction5['version']);
		$this->assertEquals(true, $deprecatedFunction5['deprecated']);
		$this->assertEquals('deprecated', $deprecatedFunction5['reason']);
		$this->assertEquals('Deprecated public class method.', $deprecatedFunction5['fixinfoshort']);
		$this->assertEquals("Line " . $deprecatedFunction5['line'] . ":\tFunction call: "
			. $deprecatedFunction5['name'] . " (deprecated since 1.1) Deprecated public class method.",
			(string)$deprecatedFunction5);
	}

	/**
	 * Should check that code_review::getDeprecatedFunctionsList returns expected results for functions defined in deprecated-*
	 */
	public function testGetDeprecatedFunctionsList11() {
		$functions = code_review::getDeprecatedFunctionsList('1.1');
		$this->assertCount(1, $functions);
		$this->assertArrayNotHasKey('dummy_deprecated_function1', $functions);
		$this->assertArrayNotHasKey('dummy_deprecated_function2', $functions);
		$this->assertArrayHasKey('foobardummyclass::deprecatedpublic', $functions);

		$deprecatedFunction1 = $functions['foobardummyclass::deprecatedpublic'];
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
		$this->assertEquals('FoobarDummyClass::deprecatedPublic', $deprecatedFunction1['name']);
		$this->assertEquals('1.1', $deprecatedFunction1['version']);
		$this->assertEquals(true, $deprecatedFunction1['deprecated']);
		$this->assertEquals('deprecated', $deprecatedFunction1['reason']);
		$this->assertEquals('Deprecated public class method.', $deprecatedFunction1['fixinfoshort']);
		$this->assertEquals("Line " . $deprecatedFunction1['line'] . ":\tFunction call: "
			. $deprecatedFunction1['name'] . " (deprecated since 1.1) Deprecated public class method.",
			(string)$deprecatedFunction1);
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
