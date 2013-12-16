<?php
class CodeReviewFileFilterIterator extends FilterIterator {
	
	public function __construct($iterator, $skipInactive = false) {
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
			$path = substr($path, strlen(elgg_get_config('path')));
			foreach ($this->blacklist as $pattern) {
				if (preg_match("#^$pattern$#", $path)) {
// 					var_dump($path);
					return false;
				}
			}
			return true;
		}
		return false;
	}
}