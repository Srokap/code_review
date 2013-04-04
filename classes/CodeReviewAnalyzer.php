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
				$pluginsDirs = elgg_get_plugin_ids_in_dir(elgg_get_config('path') . 'mod/');
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
	
	public function compareCountReversePredicate($a, $b) {
		$ac = count($a);
		$bc = count($b);
		if ($ac < $bc) {
			return 1;
		} elseif ($ac > $bc) {
			return -1;
		} else {
			return 0;
		}
	}
	
	/**
	 * @param string $language
	 */
	public function ouptutReport() {
		$result = '';
		
		$result .= "Max version: " . $this->maxVersion . "\n";
// 		$result .= "Skipped inactive plugins: " . ($skipInactive ? 'yes' : 'no') . "\n";
// 		$result .= "Simple use cases: " . $this->totalS . "\n";
// 		$result .= "Complex use cases: " . $this->totalC . "\n";
// 		$result .= "\n";
		
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
				list($version, $function, $line) = $row;
				$result .= "    Line $line:\tFunction call: $function (deprecated since $version)\n";
			}
		}
		
		$result .= "\n";
		
// 		$this->forceRegisterAllTranslations($language, $skipInactive);
// 		$translations = elgg_get_config('translations');
// 		$defined = (array)array_keys($translations[$language]);
// 		$used = array_keys($this->stats);
// 		// sort($defined);
// 		// sort($used);
		
// 		$diff = array_diff($defined, $used);
// 		$missing = array_diff($used, $defined);
// 		$defAndUsed = array_intersect($used, $defined);
// 		sort($diff);
// 		sort($missing);
		
// 		$result .= "Translation definitions count: " . count($defined) . "\n";
// 		$result .= "Translations recognized as used: " . count($defAndUsed) . "\n";
// 		$result .= "Tokens recognized as used: " . count($used) . "\n";
// 		$result .= "Potentially unused tokens: " . count($diff) . "\n";
// 		$result .= "\n";
		
// 		// echo "Potentially unused:\n";
// 		// print_r($diff);
// 		$result .= "Translation tokens definitely missing definition (" . count($missing) . "):\n";
// 		$result .= print_r($missing, true);
// 		$result .= "\n";
		
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
	
	/**
	 * @param array $hits
	 * @return array
	 */
	public function splitSimpleMatches($hits) {
		$simple = array();
		$complex = array();
		foreach ($hits as $hit) {
			list($call, $params) = $hit;
			if ($params[1][0] == T_CONSTANT_ENCAPSED_STRING && ($params[2] == ')' || $params[2] == ',')) {
				//one text parameter case regardless of the parameters to be printf'ed
				$simple[] = array($call, trim($params[1][1], '\'"'));
			} else {
				$complex[] = $hit;
			}
		}
		unset($hits);
		return array(
			$simple,
			$complex
		);
	}
}