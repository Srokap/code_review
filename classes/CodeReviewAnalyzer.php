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
		$this->stats = array();
		if (!$maxVersion) {
			$maxVersion = get_version(true);
		}
		$this->maxVersion = $maxVersion;
		
		$functions = code_review::getDeprecatedFunctionsList($maxVersion);
		
		$cnt = 0;
		foreach ($i as $filePath => $val) {
			$contents = file_get_contents($filePath);
			$result = $this->processFileContents($contents, $functions);
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
				$result .= "    Line $line:\tFunction call: $function (deprecated since $version)" . ($data['fixinfo'] ? ' ' . $data['fixinfo'] : '') . "\n";
			}
		}
		
		$result .= "\n";
		
		return $result;
	}
	
	/**
	 * @param array|string $val
	 * @param int $token
	 * @return boolean
	 */
	public function isToken($val, $token) {
		return is_array($val) && $val[0] == $token;
	}
	
	/**
	 * Find elgg_echo invocations and extract together with parameters
	 * @param string $contents
	 * @return array
	 */
	public function processFileContents($contents, $functions) {
		$result = array();
		$phpTokens = token_get_all($contents);
		foreach ($phpTokens as $key => $row) {
			if (is_array($row)) {
				list($token, $functionName, $lineNumber) = $row;
				if ($token == T_STRING && isset($functions[$functionName]) 
					&& !$this->isToken($phpTokens[$key-1], T_OBJECT_OPERATOR) //not method
					&& !$this->isToken($phpTokens[$key-1], T_DOUBLE_COLON) //not static method
					&& !$this->isToken($phpTokens[$key-2], T_FUNCTION) //not definition
				) {
// 					if (!$this->isToken($phpTokens[$key-1], T_WHITESPACE)) {
// 						var_dump($phpTokens[$key-1], $functionName, $phpTokens[$key+1]);
// 					}
					//T_WHITESPACE / T_OBJECT_OPERATOR
					$result[] = array($functions[$functionName], $functionName, $lineNumber);
				}
			}
		}
		unset($phpTokens);
		return $result;
	}
}