<?php

class CodeReview_Issues_DeprecatedTest extends PHPUnit_Framework_TestCase {

	public function testIsEditable() {
		$object = new CodeReview_Issues_Deprecated(array(
			'name' => 'function_name',
			'version' => '1.0',
			'file' => 'info.php',
			'line' => 321,
		));

		$this->assertInstanceOf('ArrayAccess', $object);
		$this->assertFalse(is_array($object));

		$this->assertArrayHasKey('name', $object);
		$this->assertArrayHasKey('version', $object);
		$this->assertArrayHasKey('file', $object);
		$this->assertArrayHasKey('line', $object);
		$this->assertArrayHasKey('reason', $object);

		$this->assertEquals('function_name', $object['name']);
		$this->assertEquals('1.0', $object['version']);
		$this->assertEquals('info.php', $object['file']);
		$this->assertEquals(321, $object['line']);
		$this->assertEquals('deprecated', $object['reason']);

		$object['version'] = '1.1';

		$this->assertEquals('1.1', $object['version']);
		$this->assertTrue(isset($object['version']));

		unset($object['version']);

		$this->assertFalse(isset($object['version']));
	}
}
