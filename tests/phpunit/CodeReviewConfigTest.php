<?php
namespace CodeReview\Tests;

class CodeReviewConfigTest extends \PHPUnit_Framework_TestCase {

	public function getLatestVersion($human_readable = false) {
		return $human_readable ? '11.22' : 2015062900;
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

	public function testDefaultOptionsOnNoInput() {
		$config = new \CodeReview\Config(array(), array($this, 'getLatestVersion'));
		$this->assertEquals(null, $config->getSubPath());
		$this->assertEquals('11.22', $config->getMaxVersion());
		$this->assertEquals(false, $config->isIncludeDisabledPluginsEnabled());
		$this->assertEquals(true, $config->isSkipInactivePluginsEnabled());
		$this->assertEquals(true, $config->isDeprecatedFunctionsTestEnabled());
		$this->assertEquals(true, $config->isPrivateFunctionsTestEnabled());
		$this->assertEquals(false, $config->isFixProblemsEnabled());
	}

	public function testDefaultOptionsOnPersingEmptyInput() {
		$config = new \CodeReview\Config(array(), array($this, 'getLatestVersion'));
		$config->parseInput(array());
		$this->assertEquals('/', $config->getSubPath());
		$this->assertEquals('11.22', $config->getMaxVersion());
		$this->assertEquals(false, $config->isIncludeDisabledPluginsEnabled());
		$this->assertEquals(true, $config->isSkipInactivePluginsEnabled());
		$this->assertEquals(true, $config->isDeprecatedFunctionsTestEnabled());
		$this->assertEquals(true, $config->isPrivateFunctionsTestEnabled());
		$this->assertEquals(false, $config->isFixProblemsEnabled());
	}

	public function testDefaultOptionsOnPersingInput() {
		$config = new \CodeReview\Config(array(), array($this, 'getLatestVersion'));
		$config->parseInput(array(
			'subpath' => 'mod/fancy_plugin',
			'version' => '10.22',
			'include_disabled_plugins' => false,
			'find_deprecated_functions' => true,
			'find_private_functions' => true,
			'fix_problems' => false,
		));
		$this->assertEquals('mod/fancy_plugin/', $config->getSubPath());
		$this->assertEquals('10.22', $config->getMaxVersion());
		$this->assertEquals(false, $config->isIncludeDisabledPluginsEnabled());
		$this->assertEquals(true, $config->isSkipInactivePluginsEnabled());
		$this->assertEquals(true, $config->isDeprecatedFunctionsTestEnabled());
		$this->assertEquals(true, $config->isPrivateFunctionsTestEnabled());
		$this->assertEquals(false, $config->isFixProblemsEnabled());

		$config->parseInput(array(
			'subpath' => '//mod/fancy_plugin/../with/supprises\\not\\cool',
		));
		$this->assertEquals('mod/fancy_plugin//with/supprises/not/cool/', $config->getSubPath());
	}

	public function testDefaultOptionsSetOnConstructor() {
		$config = new \CodeReview\Config(array(
			'subpath' => 'mod/fancy_plugin',
			'subPath' => 'mod/proper_path',
			'version' => '10.22',
			'maxVersion' => '10.23',
			'include_disabled_plugins' => false,
			'find_deprecated_functions' => true,
			'find_private_functions' => true,
			'fix_problems' => false,
		), array($this, 'getLatestVersion'));
		$this->assertEquals('mod/proper_path', $config->getSubPath());
		$this->assertEquals('10.23', $config->getMaxVersion());
		$this->assertEquals(false, $config->isIncludeDisabledPluginsEnabled());
		$this->assertEquals(true, $config->isSkipInactivePluginsEnabled());
		$this->assertEquals(true, $config->isDeprecatedFunctionsTestEnabled());
		$this->assertEquals(true, $config->isPrivateFunctionsTestEnabled());
		$this->assertEquals(false, $config->isFixProblemsEnabled());

		$config->parseInput(array(
			'subpath' => '//mod/fancy_plugin/../with/supprises\\not\\cool',
		));
		$this->assertEquals('mod/fancy_plugin//with/supprises/not/cool/', $config->getSubPath());
	}

	public function testPresistenceOfOptions() {
		$config = new \CodeReview\Config(array(), array($this, 'getLatestVersion'));
		$config->parseInput(array());
		$this->assertEquals('/', $config->getSubPath());
		$this->assertEquals('11.22', $config->getMaxVersion());
		$this->assertEquals(false, $config->isIncludeDisabledPluginsEnabled());
		$this->assertEquals(true, $config->isSkipInactivePluginsEnabled());
		$this->assertEquals(true, $config->isDeprecatedFunctionsTestEnabled());
		$this->assertEquals(true, $config->isPrivateFunctionsTestEnabled());
		$this->assertEquals(false, $config->isFixProblemsEnabled());

		//change stuff
		$config->subPath = '//test/invalid/path';
		$this->assertEquals('//test/invalid/path', $config->getSubPath());

		$config->maxVersion = '10.24';
		$this->assertEquals('10.24', $config->getMaxVersion());

		$config->includeDisabledPlugins = true;
		$this->assertEquals(true, $config->isIncludeDisabledPluginsEnabled());
		$this->assertEquals(false, $config->isSkipInactivePluginsEnabled());
		$config->includeDisabledPlugins = false;
		$this->assertEquals(false, $config->isIncludeDisabledPluginsEnabled());
		$this->assertEquals(true, $config->isSkipInactivePluginsEnabled());

		$config->findDeprecatedFunctions = true;
		$this->assertEquals(true, $config->isDeprecatedFunctionsTestEnabled());
		$config->findDeprecatedFunctions = false;
		$this->assertEquals(false, $config->isDeprecatedFunctionsTestEnabled());

		$config->findPrivateFunctions = true;
		$this->assertEquals(true, $config->isPrivateFunctionsTestEnabled());
		$config->findPrivateFunctions = false;
		$this->assertEquals(false, $config->isPrivateFunctionsTestEnabled());

		$config->fixProblems = true;
		$this->assertEquals(true, $config->isFixProblemsEnabled());
		$config->fixProblems = false;
		$this->assertEquals(false, $config->isFixProblemsEnabled());
	}

	public function testPluginsGetter() {
		$config = new \CodeReview\Config(array(), array($this, 'getLatestVersion'));

		$this->assertEquals(array('injected_plugin', 'ugly_plugin'), $config->getPluginIds($config::T_PLUGINS_ACTIVE));

		$this->assertEquals(array('inactive_plugin'), $config->getPluginIds($config::T_PLUGINS_INACTIVE));

		$this->assertEquals(array('inactive_plugin', 'ugly_plugin'), $config->getPluginIds($config::T_PLUGINS_ALL));
	}
}
