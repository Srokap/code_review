<?php
/**
 * Simple configuration container handling basic options parsing nad providing convenient methods.
 */
class CodeReviewConfig {

	/**
	 * @var array
	 */
	protected $options = array();

	/**
	 * @param array $options
	 */
	public function __construct(array $options = array()) {
		$this->options = (array)$options;
	}

	/**
	 * @param $key
	 * @return mixed
	 */
	public function __get($key) {
		return $this->options[$key];
	}

	/**
	 * @param      $key
	 * @param null $default
	 * @return null
	 */
	public function getOption($key, $default = null) {
		return isset($this->options[$key]) ? $this->options[$key] : $default;
	}

	/**
	 * @param $key
	 * @param $value
	 */
	public function __set($key, $value) {
		$this->options[$key] = $value;
	}

	/*
	 * Shorthand methods
	 */

	/**
	 * @param array $vars
	 */
	public function parseInput(array $vars) {

		//sanitize provided path
		$subPath = elgg_extract('subpath', $vars, '/');
		$subPath = trim($subPath, '/\\');
		$subPath = str_replace('\\', '/', $subPath);
		$subPath = str_replace('..', '', $subPath);
		$subPath = $subPath . '/';

		$this->subPath = $subPath;
		$this->maxVersion = elgg_extract('version', $vars);
		$this->includeDisabledPlugins = elgg_extract('include_disabled_plugins', $vars, false);
		$this->findDeprecatedFunctions = elgg_extract('find_deprecated_functions', $vars, true);
		$this->findPrivateFunctions = elgg_extract('find_private_functions', $vars, true);
		$this->fixProblems = elgg_extract('fix_problems', $vars);
	}

	/**
	 * @return bool
	 */
	public function isFixProblemsEnabled() {
		return (bool)$this->getOption('fixProblems', false);
	}

	/**
	 * @return string
	 */
	public function getMaxVersion() {
		if (!$this->maxVersion) {
			//TODO decouple Elgg core dependency
			return elgg_get_version(true);
		}
		return $this->maxVersion;
	}

	/**
	 * @return string
	 */
	public function getSubPath() {
		return (string)$this->subPath;
	}

	/**
	 * @return bool
	 */
	public function isIncludeDisabledPluginsEnabled() {
		return (bool)$this->getOption('includeDisabledPlugins', false);
	}

	/**
	 * @return bool
	 */
	public function isSkipInactivePluginsEnabled() {
		return !$this->isIncludeDisabledPluginsEnabled();
	}

	/**
	 * @return bool
	 */
	public function isDeprecatedFunctionsTestEnabled() {
		return (bool)$this->getOption('findDeprecatedFunctions', true);
	}

	/**
	 * @return bool
	 */
	public function isPrivateFunctionsTestEnabled() {
		return (bool)$this->getOption('findPrivateFunctions', true);
	}
}