<?php
namespace CodeReview;

class FileFilterIterator extends \FilterIterator {

	/**
	 * @var string
	 */
	private $basePath;

	/**
	 * @param \Iterator         $iterator
	 * @param string           $basePath
	 * @param \CodeReview\Config $config
	 * @throws \CodeReview\IOException
	 */
	public function __construct($iterator, $basePath, \CodeReview\Config $config) {
		if (!is_dir($basePath)) {
			throw new \CodeReview\IOException("Directory $basePath does not exists");
		}
		$basePath = rtrim($basePath, '/\\') . '/';
		$this->basePath = $basePath;

		if ($config->isSkipInactivePluginsEnabled()) {
			$pluginsDirs = $config->getPluginIds(\CodeReview\Config::T_PLUGINS_INACTIVE);
			foreach ($pluginsDirs as $pluginDir) {
				$this->blacklist[] = 'mod/' . $pluginDir . '/.*';
			}
		}
		
		parent::__construct($iterator);
	}
	
	protected $blacklist = array(
		'\..*',
		'engine/lib/upgrades/.*',
//		'engine/lib/deprecated.*',
		'engine/tests/.*',
		'cache/.*',
		'documentation/.*',
		'vendor/.*',//composer default dir
		'vendors/.*',
	);

	public function accept () {
		//TODO blacklisting documentation, disabled plugins and installation script
		$file = $this->current();
		if ($file instanceof \SplFileInfo) {
			$path = $file->getPathname();
			$path = str_replace('\\', '/', $path);
			$path = str_replace('//', '/', $path);
			$path = substr($path, strlen($this->basePath));
			foreach ($this->blacklist as $pattern) {
				if (preg_match("#^$pattern$#", $path)) {
					return false;
				}
			}
			return true;
		}
		return false;
	}
}