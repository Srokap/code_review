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

	/**
	 * Array of basic function names replacements
	 *
	 * @var array
	 */
	protected $instantReplacements;

	/**
	 * @var bool
	 */
	protected $fixProblems;

	const T_PLUGINS_ALL = 0;
	const T_PLUGINS_ACTIVE = 1;
	const T_PLUGINS_INACTIVE = 2;

	/**
	 * @param $type
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
	 * @param array    $options
	 * @return array
	 */
	public function analyze(Iterator $i, array $options = array()) {

		$this->maxVersion = elgg_extract('maxVersion', $options);
		if (!$this->maxVersion) {
			$this->maxVersion = elgg_get_version(true);
		}

		$this->fixProblems = elgg_extract('fixProblems', $options, false);

		$fixer = new CodeFixer();
		$this->instantReplacements = $fixer->getBasicFunctionRenames($this->maxVersion);

		$this->stats = array();

		$functions = code_review::getDeprecatedFunctionsList($this->maxVersion);

		foreach ($i as $filePath => $file) {
			if ($file instanceof SplFileInfo) {
				$result = $this->processFile($filePath, $functions);
				if (!empty($result['problems'])) {
					$this->stats[$filePath] = $result;
				}
			}
		}
		return $this->stats;
	}

	/**
	 * @param $skipInactive
	 * @return string
	 */
	public function ouptutReport($skipInactive) {
		$result = '';
		
		$result .= "Max version: " . $this->maxVersion . "\n";
		$result .= "Skipped inactive plugins: " . ($skipInactive ? 'yes' : 'no') . "\n";
		$result .= "Attempt to fix problems: " . ($this->fixProblems ? 'yes' : 'no') . "\n";

		foreach (array('problems', 'fixes') as $type) {
			$total = 0;
			foreach ($this->stats as $items) {
				$total += count($items[$type]);
			}
			$result .= "Found $total $type in " . count($this->stats) . " files\n";
		}

		/*
		 * Full report
		 */
		foreach ($this->stats as $filePath => $items) {
			$result .= "\nIn file: " . $filePath . "\n";

			//problems
			foreach ($items['problems'] as $row) {
				list($data, $function, $line) = $row;
				$version = $data['version'];
				$result .= "    Line $line:\tFunction call: $function (deprecated since $version)" . ($data['fixinfoshort'] ? ' ' . $data['fixinfoshort'] : '') . "\n";
			}

			//fixes
			foreach ($items['fixes'] as $row) {
				list($before, $after, $line) = $row;
				$result .= "    Line $line:\tReplacing: '$before' with '$after'\n";
			}
		}
		
		$result .= "\n";
		
		return $result;
	}

	/**
	 * Find function calls and extract
	 *
	 * @param string $filePath
	 * @param array $functions
	 * @return array
	 */
	public function processFile($filePath, $functions) {
		$result = array(
			'problems' => array(),
			'fixes' => array(),
		);
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
						$result['problems'][] = array($functions[$functionName], $functionName, $lineNumber);
					}

					//do instant replacement
					if ($this->fixProblems && isset($this->instantReplacements[$functionName])) {
						$phpTokens[$key] = array(T_STRING, $this->instantReplacements[$functionName]);
						$result['fixes'][] = array($functionName, $this->instantReplacements[$functionName], $lineNumber);
						$changes++;
					}
				}
			}
		}
		if ($changes) {
			try {
				$phpTokens->exportPhp($filePath);
			} catch (CodeReview_IOException $e) {
				echo '*** Error: ' . $e->getMessage() . " ***\n";
			}
		}
		unset($phpTokens);
		return $result;
	}
}