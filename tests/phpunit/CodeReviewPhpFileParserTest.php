<?php
namespace Srokap\CodeReview\Tests;

class CodeReviewPhpFileParserTest extends \PHPUnit_Framework_TestCase {

	public function testNotExistingFile() {
		$fileName = '/not/existing/file/path/to/php/file.php';
		$this->setExpectedException('CodeReview_IOException', "File $fileName does not exists");
		new \PhpFileParser($fileName);
	}

	public function testNotAFile() {
		$fileName = dirname(__FILE__);//just a path to directory
		$this->setExpectedException('CodeReview_IOException', "$fileName must be a file");
		new \PhpFileParser($fileName);
	}

	/**
	 * @requires PHP 5.3
	 */
	public function testUnserializeInternalErrorNoFileName() {
		$fileName = dirname(__FILE__) . '/test_files/php/input/sample1.php';
		$tokens = new \PhpFileParser($fileName);

		$reflection = new \ReflectionClass($tokens);
		$reflection_property = $reflection->getProperty('fileName');
		$reflection_property->setAccessible(true);
		$reflection_property->setValue($tokens, null);

		$this->setExpectedException('LogicException', "Missing file's path. Looks like severe internal error.");

		$serializedTokens = serialize($tokens);
		unserialize($serializedTokens);
	}

	/**
	 * @requires PHP 5.3
	 */
	public function testUnserializeInternalErrorBadFileName() {
		$fileName = dirname(__FILE__) . '/test_files/php/input/sample1.php';
		$badPath = dirname(__FILE__) . '/test_files/php/input/not_existing_file.php';
		$tokens = new \PhpFileParser($fileName);

		$reflection = new \ReflectionClass($tokens);
		$reflection_property = $reflection->getProperty('fileName');
		$reflection_property->setAccessible(true);
		$reflection_property->setValue($tokens, $badPath);

		$this->setExpectedException('CodeReview_IOException', "File $badPath does not exists");

		$serializedTokens = serialize($tokens);
		unserialize($serializedTokens);
	}

	/**
	 * @requires PHP 5.3
	 */
	public function testUnserializeInternalErrorNoSha1() {
		$fileName = dirname(__FILE__) . '/test_files/php/input/sample1.php';
		$tokens = new \PhpFileParser($fileName);

		$reflection = new \ReflectionClass($tokens);
		$reflection_property = $reflection->getProperty('sha1hash');
		$reflection_property->setAccessible(true);
		$reflection_property->setValue($tokens, null);

		$this->setExpectedException('LogicException', "Missing file's SHA1 hash. Looks like severe internal error.");

		$serializedTokens = serialize($tokens);
		unserialize($serializedTokens);
	}

	public function testSerializationDataPreserve() {
		$tokens = new \PhpFileParser(__FILE__);
		$serializedTokens = serialize($tokens);
		$unserializedTokens = unserialize($serializedTokens);
		$this->assertEquals($tokens, $unserializedTokens);
	}

	/**
	 * @return array
	 */
	private function getTestsPhpFiles() {
		$tests = array(
			'sample1',
			'sample1',
		);
		$result = array();
		$dir = dirname(__FILE__) . '/test_files/php/';
		foreach ($tests as $test) {
			$result[$test] = array(
				$dir . 'input/' . $test . '.php',
				$dir . 'output/' . $test . '.php',
			);
		}
		return $result;
	}

	public function testSerializationOutput() {
		$tests = $this->getTestsPhpFiles();
		foreach ($tests as $test) {
			list($inPath, ) = $test;
			$tokens = new \PhpFileParser($inPath);

			$serializedClass = serialize($tokens);
			$this->assertTrue(is_string($serializedClass));
			$this->assertTrue(strlen($serializedClass) > 0);

			$actual = unserialize($serializedClass);
			$expected = new \PhpFileParser($inPath);

			$this->assertEquals($expected, $actual);
		}
	}

	public function testSourcePreserve() {
		$tests = $this->getTestsPhpFiles();
		foreach ($tests as $test) {
			list($inPath, ) = $test;
			$tokens = new \PhpFileParser($inPath);

			$actual = $tokens->exportPhp();

			$this->assertTrue(is_string($actual));
			$this->assertTrue(strlen($actual) > 0);

			$expected = file_get_contents($inPath);

			$this->assertEquals($expected, $actual);
		}
	}

	public function testArrayAndIteratorInterfaces() {
		$tests = $this->getTestsPhpFiles();
		foreach ($tests as $test) {
			list($inPath, ) = $test;

			$tokens = new \PhpFileParser($inPath);

			//testing repeated iteration and isset
			foreach (array(1,2) as $i) {
				$result = array();
				foreach ($tokens as $key => $token) {
					$result[] = array(isset($tokens[$key - 1]), isset($tokens[$key]), isset($tokens[$key + 1]));
				}

				$this->assertTrue(count($result) > 2);
				$first = array_shift($result);
				$this->assertEquals(array(false, true, true), $first);
				$last = array_pop($result);
				$this->assertEquals(array(true, true, false), $last);

				foreach ($result as $row) {
					$this->assertEquals(array(true, true, true), $row);
				}
			}

		}
	}

	public function testSerializationFileModifiedDetection() {
		$tests = $this->getTestsPhpFiles();
		foreach ($tests as $test) {
			list($inPath, ) = $test;

			$content = file_get_contents($inPath);
			$this->assertNotEmpty($content);

			$tmpPath = tempnam(sys_get_temp_dir(), 'phpfileparsertest');

			$this->assertNotEquals(file_put_contents($tmpPath, $content), false);

			$tokens = new \PhpFileParser($tmpPath);
			$serializedTokens = serialize($tokens);

			//modify file
			$this->assertNotEquals(file_put_contents($tmpPath, 'modified' . $content), false);

//			$this->setExpectedException('CodeReview_IOException');
			try {
				//this shall fail
				unserialize($serializedTokens);
			} catch (\CodeReview_IOException $e) {
				$this->assertEquals("The file on disk has changed and this PhpFileParser class instance is no longer valid for use. Please create fresh instance.", $e->getMessage());
				continue;
			}
			$this->fail("Expected CodeReview_IOException to be thrown!");
		}
	}
}