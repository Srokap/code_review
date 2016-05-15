<?php
namespace CodeReview\Tests;

class CodeFixerTest extends \PHPUnit_Framework_TestCase {

	public function testGetBasicFunctionRenames() {
		$fixes = new \CodeReview\CodeFixer();

		$renames = $fixes->getBasicFunctionRenames();

		$pattern = '/^[a-zA-Z_][\sa-zA-Z_0-9\(\)\->]*$/';

		foreach ($renames as $from => $to) {
			$this->assertNotEmpty($from);
			$this->assertNotEmpty($to);

			$this->assertRegExp($pattern, $from);
			$this->assertRegExp($pattern, $to);
		}

		/*
		 * Check version filtering
		 */
		// no results below 1.7
		$this->assertEmpty($fixes->getBasicFunctionRenames('1.6'));

		// version 1.7
		$renames = $fixes->getBasicFunctionRenames('1.7');
		$this->assertArrayHasKey('elgg_validate_action_url', $renames);
		$this->assertArrayNotHasKey('register_elgg_event_handler', $renames);
		$this->assertArrayNotHasKey('setup_db_connections', $renames);

		// version 1.8
		$renames = $fixes->getBasicFunctionRenames('1.8');
		$this->assertArrayHasKey('elgg_validate_action_url', $renames);
		$this->assertArrayHasKey('register_elgg_event_handler', $renames);
		$this->assertArrayNotHasKey('setup_db_connections', $renames);

		// version 1.9
		$renames = $fixes->getBasicFunctionRenames('1.9');
		$this->assertArrayHasKey('elgg_validate_action_url', $renames);
		$this->assertArrayHasKey('register_elgg_event_handler', $renames);
		$this->assertArrayHasKey('setup_db_connections', $renames);

		// all versions
		$renames = $fixes->getBasicFunctionRenames('');
		$this->assertArrayHasKey('elgg_validate_action_url', $renames);
		$this->assertArrayHasKey('register_elgg_event_handler', $renames);
		$this->assertArrayHasKey('setup_db_connections', $renames);
	}
}
