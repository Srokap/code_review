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
	
	static function init() {
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

	private static function getDeprecatedInfoFromDocBlock($deprecatedInfo) {
		$deprecatedInfo = explode('* @', $deprecatedInfo);
		$prefix = 'deprecated';
		$deprecatedInfo = array_filter($deprecatedInfo, function($e) use ($prefix) {
			return strpos($e, $prefix) === 0;
		});
		$deprecatedInfo = array_shift($deprecatedInfo);
		$deprecatedInfo = substr($deprecatedInfo, strlen($prefix));

		//strip leading whitechars and stars and closing tags
		$deprecatedInfo = preg_replace('#\n\s*(?:\*\/?\s*)+#', "\n", $deprecatedInfo);
		//strip leading version info
		$deprecatedInfo = preg_replace('#\s*[0-9](?:\.[0-9]){1,2}\s*#', "", $deprecatedInfo);
		//strip embedded @link docblock entries
		$deprecatedInfo = preg_replace('#\{\@[a-z]+\s([^\}]+)\}#', '$1', $deprecatedInfo);
		//trim possible whitechars at the end
		$deprecatedInfo = trim($deprecatedInfo);

//		var_dump($deprecatedInfo);

		return array(
			'fixinfo' => strlen($deprecatedInfo) > 0 ? $deprecatedInfo : false,
//			'replacement' => '',
		);
	}

	/**
	 * @param string $maxVersion
	 * @return array
	 */
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
						$data = array(
							'version' => $version,
						);

						//find nearest docblock
						$comPos = $key - 1;
						while (
							isset($tokens[$comPos])
							&& $tokens[$comPos][0] != T_FUNCTION
							&& $tokens[$comPos][0] != T_DOC_COMMENT
							&& ($key - $comPos) < 3
						) {
							$comPos--;
						}
						if ($tokens[$comPos][0] == T_DOC_COMMENT) {
//							$data['docblock'] = $tokens[$comPos][1];
							$data = array_merge($data, self::getDeprecatedInfoFromDocBlock($tokens[$comPos][1]));
						}

						$functs[$functionName] = $data;
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

	/**
	 * Returns a list of plugin directory names from a base directory.
	 * Copied from 1.9 core due to elgg_get_plugin_ids_in_dir removal in 1.9
	 *
	 * @param string $dir A dir to scan for plugins. Defaults to config's plugins_path.
	 *                    Must have a trailing slash.
	 *
	 * @return array Array of directory names (not full paths)
	 */
	static function getPluginDirsInDir($dir = null) {
		if (!$dir) {
			$dir = elgg_get_plugins_path();
		}

		$plugin_dirs = array();
		$handle = opendir($dir);

		if ($handle) {
			while ($plugin_dir = readdir($handle)) {
				// must be directory and not begin with a .
				if (substr($plugin_dir, 0, 1) !== '.' && is_dir($dir . $plugin_dir)) {
					$plugin_dirs[] = $plugin_dir;
				}
			}
		}

		sort($plugin_dirs);

		return $plugin_dirs;
	}
}