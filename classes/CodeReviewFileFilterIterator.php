<?php
class CodeReviewFileFilterIterator extends FilterIterator {

	/**
	 * @var string
	 */
	private $basePath;

	/**
	 * @param Iterator $iterator
	 * @param string   $basePath
	 * @param bool     $skipInactive
	 * @throws CodeReview_IOException
	 */
	public function __construct($iterator, $basePath, $skipInactive = false) {
		if (!is_dir($basePath)) {
			throw new CodeReview_IOException("Directory $basePath does not exists");
		}
		$basePath = rtrim($basePath, '/\\') . '/';
		$this->basePath = $basePath;

		if ($skipInactive) {
			$pluginsDirs = CodeReviewAnalyzer::getPluginIds(CodeReviewAnalyzer::T_PLUGINS_INACTIVE);
			foreach ($pluginsDirs as $pluginDir) {
				$this->blacklist[] = 'mod/' . $pluginDir . '/.*';
			}
// 			var_dump($this->blacklist);
		}
		
		parent::__construct($iterator);
	}
	
	protected $blacklist = array(
		'\..*',
		'engine/lib/upgrades/.*',
		'engine/lib/deprecated.*',
		'engine/tests/.*',
		'cache/.*',
		'documentation/.*',
		'vendors/.*',
	);

	public function accept () {
		//TODO blacklisting documentation, disabled plugins and installation script
		$file = $this->current();
		if ($file instanceof SplFileInfo) {
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