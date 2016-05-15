<?php
namespace CodeReview;

/**
 * Very simple class autoloader for code_review plugin
 */
class Autoloader {

	private $classMap = array();

	public function __construct($basePath = null) {
		if ($basePath === null) {
			$basePath = dirname(dirname(__FILE__));
		}
		$this->registerDirectory($basePath);
	}

	/**
	 * Not fully PSR-0 compatible, but good enough for this particular plugin
	 *
	 * @param string $basePath
	 * @param string $prefix
	 */
	private function registerDirectory($basePath, $prefix = '') {
		$basePath = str_replace('\\', '/', $basePath);
		$basePath = rtrim($basePath, '/') . '/';
		$prefix = ($prefix ? $prefix . '_' : '' );
		$files = scandir($basePath);
		foreach ($files as $file) {
			if ($file[0] == '.') {
				continue;
			}
			$path = $basePath . $file;
			if (is_dir($path)) {
				$this->registerDirectory($path, $prefix . pathinfo($path, PATHINFO_FILENAME));
			} elseif (strtolower(pathinfo($path, PATHINFO_EXTENSION)) == 'php') {
				$name = $prefix . pathinfo($path, PATHINFO_FILENAME);
				$this->classMap[$name] = $path;
				$name = str_replace('_', '\\', $name); // register again in case it was namespaced
				$this->classMap[$name] = $path;
			}
		}
	}

	/**
	 * @param string $className
	 * @return bool
	 */
	public function load($className) {
		if (isset($this->classMap[$className]) && file_exists($this->classMap[$className])) {
			return include($this->classMap[$className]);
		}
		return false;
	}

	/**
	 * @return bool
	 */
	public function register() {
		return spl_autoload_register(array($this, 'load'));
	}

	/**
	 * @return bool
	 */
	public function unregister() {
		return spl_autoload_unregister(array($this, 'load'));
	}

}