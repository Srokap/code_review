<?php
namespace CodeReview\Tests;

class CodeReviewAnalyzerTest extends \PHPUnit_Framework_TestCase {

	public function getLatestVersion($human_readable = false) {
		return $human_readable ? '1.2' : 2015062900;
	}

	public function pluginsGetter($type) {
		return array(
			'injected_plugin',
			'ugly_plugin'
		);
	}

	public function setUp() {
		$path = dirname(__FILE__) . '/test_files/fake_elgg/';

		require_once($path . 'engine/start.php');

		\code_review::initConfig(array(
			'path' => $path,
			'engine_path' => $path . 'engine/',
			'pluginspath' => $path . 'mod/',
			'plugins_getter' => array($this, 'pluginsGetter'),
		));
	}

	public function testPluginsGetter() {
		$config = new \CodeReview\Config(array(), array($this, 'getLatestVersion'));

		$this->assertEquals(array('injected_plugin', 'ugly_plugin'), $config->getPluginIds($config::T_PLUGINS_ACTIVE));

		$this->assertEquals(array('inactive_plugin'), $config->getPluginIds($config::T_PLUGINS_INACTIVE));

		$this->assertEquals(array('inactive_plugin', 'ugly_plugin'), $config->getPluginIds($config::T_PLUGINS_ALL));
	}

	public function testAnalyzerFailOnBadPath() {
		$config = new \CodeReview\Config(array(), array($this, 'getLatestVersion'));
		$config->parseInput(array(
			'subpath' => 'does/not/exist',
			'version' => '1.2',
			'include_disabled_plugins' => false,
			'find_deprecated_functions' => true,
			'find_private_functions' => false,
			'fix_problems' => false,
		));

		$generalConfig = \code_review::getConfig();
		$this->assertEquals(dirname(__FILE__) . '/test_files/fake_elgg/', $generalConfig['path']);

		$analyzer = new \CodeReview\Analyzer($config);
		$this->setExpectedException('CodeReview\IOException', "Invalid subPath specified. " . dirname(__FILE__) . "/test_files/fake_elgg/does/not/exist/ does not exists!");
		$analyzer->analyze();
	}

	public function testAnalyzerNoFilesProcessed() {
		$config = new \CodeReview\Config(array(), array($this, 'getLatestVersion'));
		$config->parseInput(array(
			'subpath' => 'mod/inactive_plugin',
			'version' => '1.2',
			'include_disabled_plugins' => false,
			'find_deprecated_functions' => true,
			'find_private_functions' => false,
			'fix_problems' => false,
		));

		$analyzer = new \CodeReview\Analyzer($config);
		$analyzer->analyze();
		$stringOutput = $analyzer->outputReport();

		$this->assertContains("Subpath selected <strong>mod/inactive_plugin/</strong>", $stringOutput);
		$this->assertContains("Max version: 1.2", $stringOutput);
		$this->assertContains("Skipped inactive plugins: yes", $stringOutput);
		$this->assertContains("Search for deprecated functions usage: yes", $stringOutput);
		$this->assertContains("Search for private functions usage: no", $stringOutput);
		$this->assertContains("Attempt to fix problems: no", $stringOutput);
		$this->assertContains("Found 0 problems in 0 files", $stringOutput);
		$this->assertContains("Found 0 fixes in 0 files", $stringOutput);
		$this->assertContains("*** No files were processed! *** Analysis input parameters did not resolve to any files.", $stringOutput);
		$this->assertNotContains("Time taken: ", $stringOutput);
	}

	public function testAnalysisActivePluginsNoPrivate12() {
		$config = new \CodeReview\Config(array(), array($this, 'getLatestVersion'));
		$config->parseInput(array(
			'subpath' => '',
			'version' => '1.2',
			'include_disabled_plugins' => false,
			'find_deprecated_functions' => true,
			'find_private_functions' => false,
			'fix_problems' => false,
		));

		$analyzer = new \CodeReview\Analyzer($config);
		$analyzer->analyze();
		$stringOutput = $analyzer->outputReport();

		$this->assertContains("Subpath selected <strong>/</strong>", $stringOutput);
		$this->assertContains("Max version: 1.2", $stringOutput);
		$this->assertContains("Skipped inactive plugins: yes", $stringOutput);
		$this->assertContains("Search for deprecated functions usage: yes", $stringOutput);
		$this->assertContains("Search for private functions usage: no", $stringOutput);
		$this->assertContains("Attempt to fix problems: no", $stringOutput);
		$this->assertContains("Found 2 problems in 2 files", $stringOutput);
		$this->assertContains("Found 0 fixes in 2 files", $stringOutput);
		$this->assertContains("Processed 12 files total", $stringOutput);
		$this->assertNotContains("Time taken: ", $stringOutput);

		$ds = DIRECTORY_SEPARATOR;
		$errorMessage = 'Function call: dummy_deprecated_function1 (deprecated since 1.1) Remove it';

		$instance1Path = 'test_files/fake_elgg/' . $ds . 'engine' . $ds . 'lib' . $ds . 'foobar.php';
		$this->assertContains($instance1Path . "\n    Line 8:\t" . $errorMessage, $stringOutput);

		$instance2Path = 'test_files/fake_elgg/' . $ds . 'mod' . $ds . 'ugly_plugin' . $ds . 'classes' . $ds . 'ugly_plugin.php';
		$this->assertContains($instance2Path . "\n    Line 9:\t" . $errorMessage, $stringOutput);
	}

	public function testAnalysisAllPluginsNoPrivate12() {
		$config = new \CodeReview\Config(array(), array($this, 'getLatestVersion'));
		$config->parseInput(array(
			'subpath' => '',
			'version' => '1.2',
			'include_disabled_plugins' => true,
			'find_deprecated_functions' => true,
			'find_private_functions' => false,
			'fix_problems' => false,
		));

		$analyzer = new \CodeReview\Analyzer($config);
		$analyzer->analyze();
		$stringOutput = $analyzer->outputReport();

		$this->assertContains("Subpath selected <strong>/</strong>", $stringOutput);
		$this->assertContains("Max version: 1.2", $stringOutput);
		$this->assertContains("Skipped inactive plugins: no", $stringOutput);
		$this->assertContains("Search for deprecated functions usage: yes", $stringOutput);
		$this->assertContains("Search for private functions usage: no", $stringOutput);
		$this->assertContains("Attempt to fix problems: no", $stringOutput);
		$this->assertContains("Found 3 problems in 3 files", $stringOutput);
		$this->assertContains("Found 0 fixes in 3 files", $stringOutput);
		$this->assertContains("Processed 13 files total", $stringOutput);
		$this->assertNotContains("Time taken: ", $stringOutput);

		$ds = DIRECTORY_SEPARATOR;
		$errorMessage = 'Function call: dummy_deprecated_function1 (deprecated since 1.1) Remove it';

		$instance1Path = 'test_files/fake_elgg/' . $ds . 'engine' . $ds . 'lib' . $ds . 'foobar.php';
		$this->assertContains($instance1Path . "\n    Line 8:\t" . $errorMessage, $stringOutput);

		$instance2Path = 'test_files/fake_elgg/' . $ds . 'mod' . $ds . 'ugly_plugin' . $ds . 'classes' . $ds . 'ugly_plugin.php';
		$this->assertContains($instance2Path . "\n    Line 9:\t" . $errorMessage, $stringOutput);

		$instance3Path = 'test_files/fake_elgg/' . $ds . 'mod' . $ds . 'inactive_plugin' . $ds . 'start.php';
		$this->assertContains($instance3Path . "\n    Line 5:\t" . $errorMessage, $stringOutput);
	}

	public function testAnalysisAllPluginsPrivate12() {
		$config = new \CodeReview\Config(array(), array($this, 'getLatestVersion'));
		$config->parseInput(array(
			'subpath' => '',
			'version' => '1.2',
			'include_disabled_plugins' => true,
			'find_deprecated_functions' => true,
			'find_private_functions' => true,
			'fix_problems' => false,
		));

		$analyzer = new \CodeReview\Analyzer($config);
		$analyzer->analyze();
		$stringOutput = $analyzer->outputReport();

		$this->assertContains("Subpath selected <strong>/</strong>", $stringOutput);
		$this->assertContains("Max version: 1.2", $stringOutput);
		$this->assertContains("Skipped inactive plugins: no", $stringOutput);
		$this->assertContains("Search for deprecated functions usage: yes", $stringOutput);
		$this->assertContains("Search for private functions usage: yes", $stringOutput);
		$this->assertContains("Attempt to fix problems: no", $stringOutput);
		$this->assertContains("Found 4 problems in 3 files", $stringOutput);
		$this->assertContains("Found 0 fixes in 3 files", $stringOutput);
		$this->assertContains("Processed 13 files total", $stringOutput);
		$this->assertNotContains("Time taken: ", $stringOutput);

		$ds = DIRECTORY_SEPARATOR;
		$errorMessage1 = 'Function call: dummy_deprecated_function1 (deprecated since 1.1) Remove it';
		$errorMessage2 = 'Function call: foobar_private_api (use of function marked private is unsafe)';

		$instance1Path = 'test_files/fake_elgg/' . $ds . 'engine' . $ds . 'lib' . $ds . 'foobar.php';
		$this->assertContains($instance1Path . "\n    Line 8:\t" . $errorMessage1, $stringOutput);

		$instance2Path = 'test_files/fake_elgg/' . $ds . 'mod' . $ds . 'ugly_plugin' . $ds . 'classes' . $ds . 'ugly_plugin.php';
		$this->assertContains($instance2Path . "\n    Line 9:\t" . $errorMessage1 . "\n    Line 13:\t" . $errorMessage2, $stringOutput);

		$instance3Path = 'test_files/fake_elgg/' . $ds . 'mod' . $ds . 'inactive_plugin' . $ds . 'start.php';
		$this->assertContains($instance3Path . "\n    Line 5:\t" . $errorMessage1, $stringOutput);
	}

	public function testAnalysisAllPluginsPrivateSubpathEngine12() {
		$config = new \CodeReview\Config(array(), array($this, 'getLatestVersion'));
		$config->parseInput(array(
			'subpath' => 'engine',
			'version' => '1.2',
			'include_disabled_plugins' => true,
			'find_deprecated_functions' => true,
			'find_private_functions' => true,
			'fix_problems' => false,
		));

		$analyzer = new \CodeReview\Analyzer($config);
		$analyzer->analyze();
		$stringOutput = $analyzer->outputReport();

		$this->assertContains("Subpath selected <strong>engine/</strong>", $stringOutput);
		$this->assertContains("Max version: 1.2", $stringOutput);
		$this->assertContains("Skipped inactive plugins: no", $stringOutput);
		$this->assertContains("Search for deprecated functions usage: yes", $stringOutput);
		$this->assertContains("Search for private functions usage: yes", $stringOutput);
		$this->assertContains("Attempt to fix problems: no", $stringOutput);
		$this->assertContains("Found 1 problems in 1 files", $stringOutput);
		$this->assertContains("Found 0 fixes in 1 files", $stringOutput);
		$this->assertContains("Processed 8 files total", $stringOutput);
		$this->assertNotContains("Time taken: ", $stringOutput);

		$ds = DIRECTORY_SEPARATOR;
		$errorMessage1 = 'Function call: dummy_deprecated_function1 (deprecated since 1.1) Remove it';

		$instance1Path = 'test_files/fake_elgg/' . 'engine' . $ds . 'lib' . $ds . 'foobar.php';
		$this->assertContains($instance1Path . "\n    Line 8:\t" . $errorMessage1, $stringOutput);
	}

	public function testAnalysisAllPluginsPrivateSubpathInactive12() {
		$config = new \CodeReview\Config(array(), array($this, 'getLatestVersion'));
		$config->parseInput(array(
			'subpath' => 'mod/inactive_plugin/',
			'version' => '1.2',
			'include_disabled_plugins' => true,
			'find_deprecated_functions' => true,
			'find_private_functions' => true,
			'fix_problems' => false,
		));

		$analyzer = new \CodeReview\Analyzer($config);
		$analyzer->analyze();
		$stringOutput = $analyzer->outputReport();

		$this->assertContains("Subpath selected <strong>mod/inactive_plugin/</strong>", $stringOutput);
		$this->assertContains("Max version: 1.2", $stringOutput);
		$this->assertContains("Skipped inactive plugins: no", $stringOutput);
		$this->assertContains("Search for deprecated functions usage: yes", $stringOutput);
		$this->assertContains("Search for private functions usage: yes", $stringOutput);
		$this->assertContains("Attempt to fix problems: no", $stringOutput);
		$this->assertContains("Found 1 problems in 1 files", $stringOutput);
		$this->assertContains("Found 0 fixes in 1 files", $stringOutput);
		$this->assertContains("Processed 1 files total", $stringOutput);
		$this->assertNotContains("Time taken: ", $stringOutput);

		$ds = DIRECTORY_SEPARATOR;
		$errorMessage1 = 'Function call: dummy_deprecated_function1 (deprecated since 1.1) Remove it';

		$instance3Path = 'test_files/fake_elgg/mod/inactive_plugin' . $ds . 'start.php';
		$this->assertContains($instance3Path . "\n    Line 5:\t" . $errorMessage1, $stringOutput);
	}

	public function testAnalysisAllPluginsPrivateSubpathUgly12() {
		$config = new \CodeReview\Config(array(), array($this, 'getLatestVersion'));
		$config->parseInput(array(
			'subpath' => 'mod/ugly_plugin',
			'version' => '1.2',
			'include_disabled_plugins' => true,
			'find_deprecated_functions' => true,
			'find_private_functions' => true,
			'fix_problems' => false,
		));

		$analyzer = new \CodeReview\Analyzer($config);
		$analyzer->analyze();
		$stringOutput = $analyzer->outputReport();

		$this->assertContains("Subpath selected <strong>mod/ugly_plugin/</strong>", $stringOutput);
		$this->assertContains("Max version: 1.2", $stringOutput);
		$this->assertContains("Skipped inactive plugins: no", $stringOutput);
		$this->assertContains("Search for deprecated functions usage: yes", $stringOutput);
		$this->assertContains("Search for private functions usage: yes", $stringOutput);
		$this->assertContains("Attempt to fix problems: no", $stringOutput);
		$this->assertContains("Found 2 problems in 1 files", $stringOutput);
		$this->assertContains("Found 0 fixes in 1 files", $stringOutput);
		$this->assertContains("Processed 4 files total", $stringOutput);
		$this->assertNotContains("Time taken: ", $stringOutput);

		$ds = DIRECTORY_SEPARATOR;
		$errorMessage1 = 'Function call: dummy_deprecated_function1 (deprecated since 1.1) Remove it';
		$errorMessage2 = 'Function call: foobar_private_api (use of function marked private is unsafe)';

		$instance2Path = 'test_files/fake_elgg/mod/ugly_plugin' . $ds . 'classes' . $ds . 'ugly_plugin.php';
		$this->assertContains($instance2Path . "\n    Line 9:\t" . $errorMessage1 . "\n    Line 13:\t" . $errorMessage2, $stringOutput);
	}

	public function testAnalysisAllPluginsPrivate11() {
		$config = new \CodeReview\Config(array(), array($this, 'getLatestVersion'));
		$config->parseInput(array(
			'subpath' => '',
			'version' => '1.1',
			'include_disabled_plugins' => true,
			'find_deprecated_functions' => true,
			'find_private_functions' => true,
			'fix_problems' => false,
		));

		$analyzer = new \CodeReview\Analyzer($config);
		$analyzer->analyze();
		$stringOutput = $analyzer->outputReport();

		$this->assertContains("Subpath selected <strong>/</strong>", $stringOutput);
		$this->assertContains("Max version: 1.1", $stringOutput);
		$this->assertContains("Skipped inactive plugins: no", $stringOutput);
		$this->assertContains("Search for deprecated functions usage: yes", $stringOutput);
		$this->assertContains("Search for private functions usage: yes", $stringOutput);
		$this->assertContains("Attempt to fix problems: no", $stringOutput);
		$this->assertContains("Found 1 problems in 1 files", $stringOutput);
		$this->assertContains("Found 0 fixes in 1 files", $stringOutput);
		$this->assertContains("Processed 13 files total", $stringOutput);
		$this->assertNotContains("Time taken: ", $stringOutput);

		$ds = DIRECTORY_SEPARATOR;
		$errorMessage2 = 'Function call: foobar_private_api (use of function marked private is unsafe)';

		$instance2Path = 'test_files/fake_elgg/' . $ds . 'mod' . $ds . 'ugly_plugin' . $ds . 'classes' . $ds . 'ugly_plugin.php';
		$this->assertContains($instance2Path . "\n    Line 13:\t" . $errorMessage2, $stringOutput);
	}
}
