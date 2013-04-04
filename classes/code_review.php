<?php
/**
 * Main plugin class. 
 * Storage for various handlers.
 * @author Paweł Sroka (srokap@gmail.com)
 */
class code_review {
	static function boot() {
// 		require_once elgg_get_config('pluginspath').__CLASS__.'/vendors/Zend/Loader/StandardAutoloader.php';
// 		$loader = new Zend\Loader\StandardAutoloader(array('autoregister_zf' => true));
// 		$loader->register();
	}
	
	static function init($foo, $bar = 'abc') {
// 		self::playground();

		elgg_register_event_handler('pagesetup', 'system', array(__CLASS__, 'pagesetup'));
		
		elgg_register_ajax_view('graphics/ajax_loader');
		elgg_register_ajax_view('code_review/analysis');
		
		elgg_register_js('code_review', elgg_get_config('wwwroot') . 'mod/'
			. __CLASS__ . '/views/default/js/code_review.js');
	}
	
	static function pagesetup() {
		if (elgg_get_context() == 'admin') {
			elgg_register_menu_item('page', array(
				'name' => 'code/diagnostic',
				'href' => 'admin/code/diagnostic',
				'text' => elgg_echo('admin:code:diagnostic'),
				'context' => 'admin',
				'section' => 'develop'
			));
		}
	}
	
	/**
	 * @return RegexIterator
	 */
	static function getDeprecatedIterator($subPath = 'engine/') {
		$i = new RecursiveDirectoryIterator(elgg_get_config('path') . $subPath, RecursiveDirectoryIterator::SKIP_DOTS);
		$i = new RecursiveIteratorIterator($i, RecursiveIteratorIterator::LEAVES_ONLY);
		$i = new RegexIterator($i, "/.*\.php/");
		return $i;
	}
	
	/**
	 * @return RegexIterator
	 */
	static function getPhpFilesIterator($subPath = 'engine/', $skipInactive = false) {
		$i = new RecursiveDirectoryIterator(elgg_get_config('path') . $subPath, RecursiveDirectoryIterator::SKIP_DOTS);
		$i = new RecursiveIteratorIterator($i, RecursiveIteratorIterator::LEAVES_ONLY);
		$i = new RegexIterator($i, "/.*\.php/");
		$i = new CodeReviewFileFilterIterator($i, $skipInactive);
		return $i;
	}
	
	static function getVersionsList() {
		$i = self::getDeprecatedIterator('engine/lib/');
		$i = new RegexIterator($i, "/deprecated-.*/");
		
		$vv = array();
		
		foreach ($i as $file) {
			if (preg_match('#^deprecated-([0-9\.]*)$#', $file->getBasename('.php'), $matches)) {
				$version = $matches[1];
			} else {
				$version = null;
			}
			if ($version) {
				$vv[] = $version;
			}
		}
		return $vv;
	}
	
	static function getDeprecatedFunctionsList($maxVersion = '1.8') {
		$i = self::getDeprecatedIterator('engine/lib/');
		$i = new RegexIterator($i, "/deprecated-.*/");
		
		$functs = array();
		
		foreach ($i as $file) {
			if ($file instanceof SplFileInfo) {
// 				var_dump($file->getPathname());
				if (preg_match('#^deprecated-([0-9\.]*)$#', $file->getBasename('.php'), $matches)) {
					$version = $matches[1];
				} else {
					$version = null;
				}
				
				//skip versions higher than selected
				if ($maxVersion && $version && version_compare($version, $maxVersion) > 0) {
					continue;
				}
				
				$source = file_get_contents($file->getPathname());
				$tokens = token_get_all($source);
				
				foreach ($tokens as $key => $token) {
					if (is_array($token) && $token[0] == T_FUNCTION) {
						$functionName = $tokens[$key+2][1];
						$functs[$functionName] = $version;
					}
				}
					
// 				require_once $file->getPathname();
// 		 		FIXME not implemented in ZF2
// 				$ref = new Zend\Code\Reflection\FileReflection($file->getPathname());
// 				$db = $ref->getDocBlock();
// 				if ($db) {
// 					var_dump($db->getContents());
// 				}
// 				var_dump($ref->getFunctions());
// 				foreach ($ref->getFunctions() as $function) {
// 					var_dump($function->getDocBlock()->getContents());
// 				}
			}
		}
		return $functs;
	}
}