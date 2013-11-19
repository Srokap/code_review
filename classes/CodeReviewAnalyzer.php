<?php
class CodeReviewAnalyzer {
	
	/**
	 * @var array
	 */
	protected $stats;
	
	/**
	 * @var string
	 */
	protected $maxVersion;
	
	const T_PLUGINS_ALL = 0;
	const T_PLUGINS_ACTIVE = 1;
	const T_PLUGINS_INACTIVE = 2;
	
	/**
	 * @return array
	 */
	public static function getPluginIds($type) {
		$pluginsDirs = false;
		
		switch ($type) {
			case self::T_PLUGINS_INACTIVE:
				$pluginsDirs = self::getPluginIds(self::T_PLUGINS_ALL);
				$actives = elgg_get_plugins('active');
				foreach ($actives as $plugin) {
					$pluginsDirs = array_diff($pluginsDirs, array($plugin->getID()));
				}
				break;
			case self::T_PLUGINS_ACTIVE:
				$pluginsDirs = elgg_get_plugins('active');
				foreach ($pluginsDirs as $key => $plugin) {
					$pluginsDirs[$key] = $plugin->getID();
				}
				break;
			case self::T_PLUGINS_ALL:
				$pluginsDirs = code_review::getPluginDirsInDir(elgg_get_config('path') . 'mod/');
				break;
							
		}
		return $pluginsDirs;
	}
	
	/**
	 * @param Iterator $i
	 * @return array
	 */
	public function analyze(Iterator $i, $maxVersion = null) {

		$fixer = new CodeFixer();
		$instantReplacements = $fixer->getBasicFunctionRenames();

		$this->stats = array();
		if (!$maxVersion) {
			$maxVersion = elgg_get_version(true);
		}
		$this->maxVersion = $maxVersion;
		
		$functions = code_review::getDeprecatedFunctionsList($maxVersion);
//		echo '<pre>';
//		print_r($functions);
//		echo '</pre>';

		$cnt = 0;
		foreach ($i as $filePath => $val) {
			$result = $this->processFile($filePath, $functions, $instantReplacements);
			if (!empty($result)) {
				$this->stats[$filePath] = $result;
			}
		}
		return $this->stats;
	}
	
	/**
	 * @param string $language
	 */
	public function ouptutReport($skipInactive) {
		$result = '';
		
		$result .= "Max version: " . $this->maxVersion . "\n";
		$result .= "Skipped inactive plugins: " . ($skipInactive ? 'yes' : 'no') . "\n";
		
// 		$result .= print_r($this->stats, true);
		
		$total = 0;
		foreach ($this->stats as $filePath => $items) {
			$total += count($items);
		}
		$result .= "Found $total problems in " . count($this->stats) . " files\n";
		
		/*
		 * Full report
		 */
		foreach ($this->stats as $filePath => $items) {
			$result .= "\nIn file: " . $filePath . "\n";
			foreach ($items as $row) {
				list($data, $function, $line) = $row;
				$version = $data['version'];
				$result .= "    Line $line:\tFunction call: $function (deprecated since $version)" . ($data['fixinfoshort'] ? ' ' . $data['fixinfoshort'] : '') . "\n";
			}
		}
		
		$result .= "\n";
		
		return $result;
	}
	
	/**
	 * Find function calls and extract
	 * @param string $filePath
	 * @return array
	 */
	public function processFile($filePath, $functions, $instantReplacements) {
		$result = array();
		$phpTokens = new PhpFileParser($filePath);
		$changes = 0;
		foreach ($phpTokens as $key => $row) {
			if (is_array($row)) {
				list($token, $functionName, $lineNumber) = $row;
				if ($token == T_STRING && isset($functions[$functionName]) 
					&& !$phpTokens->isEqualToToken(T_OBJECT_OPERATOR, $key-1) //not method
					&& !$phpTokens->isEqualToToken(T_DOUBLE_COLON, $key-1) //not static method
					&& !$phpTokens->isEqualToToken(T_FUNCTION, $key-2) //not definition
				) {
					$definingFunctionName = $phpTokens->getDefiningFunctionName($key);
					//we're skipping deprecated calls that are in feprecated function itself
					if (!$definingFunctionName || !isset($functions[$definingFunctionName])) {
						$result[] = array($functions[$functionName], $functionName, $lineNumber);
//						var_dump($filePath, $row, $phpTokens->getDefiningClassName($key), $phpTokens->getDefiningFunctionName($key));
					} else {
//						var_dump('SKIP', $functionName, $definingFunctionName);
					}

					//do instant replacement
//					if (isset($instantReplacements[$functionName])) {
//						$phpTokens[$key] = array(T_STRING, $instantReplacements[$functionName]);
//						var_dump('fixing', $functionName);
//						$changes++;
//					}

//					if ($phpTokens->exportPhp() != file_get_contents($filePath)) {
//						die($filePath);
//					}
				}
			}
		}
		if ($changes) {
			$phpTokens->exportPhp($filePath);
		}
		unset($phpTokens);
		return $result;
	}
}