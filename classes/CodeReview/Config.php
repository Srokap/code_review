<?php
namespace CodeReview;

/**
 * Simple configuration container handling basic options parsing nad providing convenient methods.
 *
 * @property string|null $subPath
 * @property string $maxVersion
 * @property bool $includeDisabledPlugins
 * @property bool $findDeprecatedFunctions
 * @property bool $findPrivateFunctions
 * @property bool $fixProblems
 */
class Config {

	const T_PLUGINS_ALL = 0;
	const T_PLUGINS_ACTIVE = 1;
	const T_PLUGINS_INACTIVE = 2;

	/**
	 * @var array
	 */
	protected $options = array();

	/**
	 * @var null|function
	 */
	protected $versionGetter = null;

	/**
	 * @param array $options
	 */
	public function __construct(array $options = array(), $versionGetter = null) {
		$this->options = (array)$options;
		if (is_callable($versionGetter)) {
			$this->versionGetter = $versionGetter;
		} else {
			//TODO possibly further decouple Elgg core dependency
			$this->versionGetter = 'elgg_get_version';
		}
	}

	/**
	 * @param $key
	 * @return mixed
	 */
	public function __get($key) {
		return isset($this->options[$key]) ? $this->options[$key] : null;
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

	/**
	 * @param $type
	 * @return array
	 */
	public function getPluginIds($type) {
		$pluginsDirs = false;

		$config = \code_review::getConfig();

		switch ($type) {
			case self::T_PLUGINS_INACTIVE:
				$pluginsDirs = $this->getPluginIds(self::T_PLUGINS_ALL);
				$actives = call_user_func($config['plugins_getter'], 'active');
				foreach ($actives as $plugin) {
					if ($plugin instanceof \ElggPlugin) {
						$pluginsDirs = array_diff($pluginsDirs, array($plugin->getID()));
					} else {
						$pluginsDirs = array_diff($pluginsDirs, array($plugin));
					}
				}
				break;
			case self::T_PLUGINS_ACTIVE:
				$pluginsDirs = call_user_func($config['plugins_getter'], 'active');
				foreach ($pluginsDirs as $key => $plugin) {
					if ($plugin instanceof \ElggPlugin) {
						$pluginsDirs[$key] = $plugin->getID();
					}
				}
				break;
			case self::T_PLUGINS_ALL:
				$pluginsDirs = \code_review::getPluginDirsInDir($config['pluginspath']);
				break;

		}
		return $pluginsDirs;
	}

	/*
	 * Shorthand methods
	 */

	/**
	 * @param       $key
	 * @param array $array
	 * @param null  $default
	 * @param bool  $strict
	 * @return null
	 *
	 * Function is a part of Elgg framework with following license:

		Copyright (c) 2013. See COPYRIGHT.txt
		http://elgg.org/

		Permission is hereby granted, free of charge, to any person obtaining
		a copy of this software and associated documentation files (the
		"Software"), to deal in the Software without restriction, including
		without limitation the rights to use, copy, modify, merge, publish,
		distribute, sublicense, and/or sell copies of the Software, and to
		permit persons to whom the Software is furnished to do so, subject to
		the following conditions:

		The above copyright notice and this permission notice shall be
		included in all copies or substantial portions of the Software.

		Except as contained in this notice, the name(s) of the above copyright
		holders shall not be used in advertising or otherwise to promote the
		sale, use or other dealings in this Software without prior written
		authorization.

		THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
		EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
		MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
		NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
		LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
		OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
		WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
	 */
	private function elggExtract($key, array $array, $default = null, $strict = true) {
		if (!is_array($array)) {
			return $default;
		}

		if ($strict) {
			return (isset($array[$key])) ? $array[$key] : $default;
		} else {
			return (isset($array[$key]) && !empty($array[$key])) ? $array[$key] : $default;
		}
	}

	/**
	 * @param array $vars
	 */
	public function parseInput(array $vars) {

		//sanitize provided path
		$subPath = $this->elggExtract('subpath', $vars, '/');
		$subPath = trim($subPath, '/\\');
		$subPath = str_replace('\\', '/', $subPath);
		$subPath = str_replace('..', '', $subPath);
		$subPath = $subPath . '/';

		$this->subPath = $subPath;
		$this->maxVersion = $this->elggExtract('version', $vars, '');
		$this->includeDisabledPlugins = $this->elggExtract('include_disabled_plugins', $vars, false);
		$this->findDeprecatedFunctions = $this->elggExtract('find_deprecated_functions', $vars, true);
		$this->findPrivateFunctions = $this->elggExtract('find_private_functions', $vars, true);
		$this->fixProblems = $this->elggExtract('fix_problems', $vars, false);
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
			return call_user_func($this->versionGetter, true);
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