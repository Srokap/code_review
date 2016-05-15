<?php
class CodeReviewElggCompatibilityTest extends PHPUnit_Framework_TestCase {

	/**
	 * Checks if file is loadable for current version of PHP. Regression test after problems with 1.0.2 version.
	 */
	public function testLanguagesSyntax() {

		if (!function_exists('add_translation')) {
			//whatever, we just test syntax correctness

			function add_translation($country_code, $language_array) {
				return true;
			}
		}

		$path = dirname(dirname(dirname(__FILE__))) . '/languages/en.php';
		$this->assertTrue(file_exists($path));
		$languages = include($path);
		$this->assertTrue($languages === 1 || is_array($languages));
	}
} 