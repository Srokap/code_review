<?php
class CodeReviewPhpFileParserTest extends PHPUnit_Framework_TestCase {

	public function testNotExistingFile() {
		$fileName = '/not/existing/file/path/to/php/file.php';
		$this->setExpectedException('CodeReview_IOException', "File $fileName does not exists");
		new PhpFileParser($fileName);
	}

	public function testNotAFile() {
		$fileName = dirname(__FILE__);//just a path to directory
		$this->setExpectedException('CodeReview_IOException', "$fileName must be a file");
		new PhpFileParser($fileName);
	}

	public function testSerializationDataPreserve() {
		$tokens = new PhpFileParser(__FILE__);
		$serializedTokens = serialize($tokens);
		$unserializedTokens = unserialize($serializedTokens);
		$this->assertEquals($tokens, $unserializedTokens);
	}

	public function testSerializationFileModifiedDetection() {

	}
}