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

		elgg_register_plugin_hook_handler('register', 'menu:code_review', array(__CLASS__, 'menu_register'));

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

	static function menu_register() {
		$result = array();
		$result[] = ElggMenuItem::factory(array(
			'name' => 'admin/code/diagnostic',
			'href' => 'admin/code/diagnostic',
			'text' => elgg_echo('admin:code:diagnostic'),
		));
		$result[] = ElggMenuItem::factory(array(
			'name' => 'admin/code/diagnostic/deprecated_list',
			'href' => 'admin/code/diagnostic/deprecated_list',
			'text' => elgg_echo('admin:code:diagnostic:deprecated_list'),
//			'target' => '_blank',
		));
		return $result;
	}

	/**
	 * @param string $subPath
	 * @return RegexIterator
	 */
	static function getDeprecatedIterator($subPath = 'engine/') {
		$i = new RecursiveDirectoryIterator(elgg_get_config('path') . $subPath, RecursiveDirectoryIterator::SKIP_DOTS);
		$i = new RecursiveIteratorIterator($i, RecursiveIteratorIterator::LEAVES_ONLY);
		$i = new RegexIterator($i, "/.*\.php/");
		return $i;
	}

	/**
	 * @param string $subPath
	 * @param bool   $skipInactive
	 * @throws CodeReview_IOException
	 * @return CodeReviewFileFilterIterator
	 */
	static function getPhpFilesIterator($subPath = 'engine/', $skipInactive = false) {
		$path = elgg_get_config('path') . $subPath;
		if (!file_exists($path)) {
			throw new CodeReview_IOException("Invalid subPath specified. $path does not exists!");
		}
		$i = new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS);
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
			if ($file instanceof SplFileInfo) {
				if (preg_match('#^deprecated-([0-9\.]*)$#', $file->getBasename('.php'), $matches)) {
					$version = $matches[1];
				} else {
					$version = null;
				}
				if ($version) {
					$vv[] = $version;
				}
			}
		}
		return $vv;
	}

	/**
	 * @val string
	 */
	const DEPRECATED_TAG_PREFIX = 'deprecated';

	/**
	 * Filtering predicate
	 *
	 * @param $e
	 * @return bool
	 */
	public static function filterTagsByDeprecatedPrefix($e) {
		return strpos($e, self::DEPRECATED_TAG_PREFIX) === 0;
	}

	private static function getDeprecatedInfoFromDocBlock($deprecatedInfo) {
		if (strpos($deprecatedInfo, '@' . self::DEPRECATED_TAG_PREFIX) === false){
			return false;
		} else {
			$deprecatedInfo = explode('* @', $deprecatedInfo);
			$deprecatedInfo = array_filter($deprecatedInfo, array(__CLASS__, 'filterTagsByDeprecatedPrefix'));
//			$deprecatedInfo = array_filter($deprecatedInfo, function($e) use ($prefix) {
//				return strpos($e, $prefix) === 0;
//			});
			$deprecatedInfo = array_shift($deprecatedInfo);
			$deprecatedInfo = substr($deprecatedInfo, strlen(self::DEPRECATED_TAG_PREFIX));

			//strip leading whitechars and stars and closing tags
			$deprecatedInfo = preg_replace('#\n\s*(?:\*\/?\s*)+#', "\n", $deprecatedInfo);
			//save and strip leading version info
			$version = null;
			preg_match('#^\s*([0-9]+\.[0-9]+)#', $deprecatedInfo, $matches);
			if ($matches) {
				$version = $matches[1];
			}
			$deprecatedInfo = preg_replace('#\s*[0-9](?:\.[0-9]){1,2}\.?\s*#', "", $deprecatedInfo);
			//strip embedded @link docblock entries
			$deprecatedInfo = preg_replace('#\{\@[a-z]+\s([^\}]+)\}#', '$1', $deprecatedInfo);
			//trim possible whitechars at the end
			$deprecatedInfo = trim($deprecatedInfo);

	//		var_dump($deprecatedInfo);

			$shortDeprecatedInfo = $deprecatedInfo;
			if (($pos = strpos($shortDeprecatedInfo, "\n")) !== false) {
				$shortDeprecatedInfo = substr($shortDeprecatedInfo, 0, $pos);
			}

			$result = array(
				'deprecated' => true,
//				'fixinfo' => strlen($deprecatedInfo) > 0 ? $deprecatedInfo : false,
				'fixinfoshort' => strlen($shortDeprecatedInfo) > 0 ? $shortDeprecatedInfo : false,
			);
			if ($version) {
				$result['version'] = $version;
			}
			return $result;
		}
	}

	/**
	 * @param string $maxVersion
	 * @return array
	 */
	static function getDeprecatedFunctionsList($maxVersion = '') {
		$i1 = self::getDeprecatedIterator('engine/lib/');
		$i1 = new RegexIterator($i1, "/deprecated-.*/");
		$i2 = self::getDeprecatedIterator('engine/classes/');

		$i = new AppendIterator();
		$i->append($i1);
		$i->append($i2);
		
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

				$tokens = new PhpFileParser($file->getPathname());
				$className = null;
//				$nesting = 0;

				foreach ($tokens as $key => $token) {
					if ($tokens->isEqualToToken(T_INTERFACE, $key)) {
						//we don't process interfaces for deprecated functions
						break;
					}
					if ($tokens->isEqualToToken(T_CLASS, $key)) {
						$className = $tokens[$key+2][1];
					}

					if (is_array($token) && $token[0] == T_FUNCTION) {
						if ($className) {
							$functionName = $className . '::' . $tokens[$key+2][1];
							$reflection = new ReflectionMethod($className, $tokens[$key+2][1]);
						} else {
							$functionName = $tokens[$key+2][1];
							$reflection = new ReflectionFunction($functionName);
						}

						//is it deprecated? may not be

//						var_dump($token, $tokens->isEqualToToken(T_FUNCTION, $key));
						//check if non empty version and try go guess
						$data = array(
							'version' => $version,
							'file' => $file->getPathname(),
							'line' => $token[2],
						);

						$docBlock = $reflection->getDocComment();
						if ($docBlock) {
							$info = self::getDeprecatedInfoFromDocBlock($docBlock);
							if (!$info) {
								//skipping - not deprecated
								continue;
							}
							$data = array_merge($data, $info);
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