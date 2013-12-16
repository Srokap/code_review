<?php
class CodeReviewFileFilterIteratorTest extends PHPUnit_Framework_TestCase {

	/**
	 * Tests normal operation
	 */
	public function testIteratingOverFiles() {
		$paths = array(
			dirname(__FILE__) . '/test_files/fake_elgg/',
			dirname(__FILE__) . '/test_files/fake_elgg' . DIRECTORY_SEPARATOR,
			dirname(__FILE__) . '/test_files/fake_elgg',
		);
		foreach ($paths as $path) {
			$baseFileInfo = new SplFileInfo($path);
			$i = new RecursiveDirectoryIterator($path);
			$i = new RecursiveIteratorIterator($i, RecursiveIteratorIterator::LEAVES_ONLY);
			$i = new CodeReviewFileFilterIterator($i, $path, false);

			$filesFound = array();
			/** @var $file SplFileInfo */
			foreach ($i as $file) {
				$this->assertInstanceOf('SplFileInfo', $file);
				$this->assertNotEquals('.dummy_config', $file->getBasename());
				$entry = substr($file->getRealPath(), strlen($path));
				if ($entry) {
					$entry = trim(str_replace('\\', '/', $entry), '/');
					$filesFound[] = $entry;
				} else {
					//we allow only root dir as exception
					$this->assertEquals($baseFileInfo->getInode(), $file->getInode());
				}
			}
			$expected = array(
				'not_filtered_file',
				'mod/ugly_plugin/start.php',
				'mod/ugly_plugin/pages/page17.php',
				'mod/ugly_plugin/manifest.xml',
//				'mod/ugly_plugin',//FIXME fails on PHP 5.2.17
			);
			$this->assertEquals(array_diff($expected, $filesFound), array(), print_r($filesFound, true));

			$unexpected = array(
				'engine/lib/deprecated-1.2.php',
				'.dummy_config',
			);
			$this->assertEquals(array_intersect($unexpected, $filesFound), array(), print_r($filesFound, true));
		}
	}

	/**
	 * Passing not existing base dir parameter
	 */
	public function testNonExistingPath() {
		$path = dirname(__FILE__) . '/test_files/fake_elgg/';
		$bad_path = dirname(__FILE__) . '/test_files/non_existing_path/';
		$i = new RecursiveDirectoryIterator($path);
		$i = new RecursiveIteratorIterator($i, RecursiveIteratorIterator::LEAVES_ONLY);
		$this->setExpectedException('CodeReview_IOException', "Directory $bad_path does not exists");
		new CodeReviewFileFilterIterator($i, $bad_path, false);
	}

}