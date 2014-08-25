<?php
class CodeReviewAnalyzer {

	/**
	 * @var CodeReviewConfig
	 */
	protected $options;

	/**
	 * Function names seen as called
	 *
	 * @var array
	 */
	protected $calledFunctions = array();

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
	 * @param CodeReviewConfig $options
	 */
	public function __construct(CodeReviewConfig $options = null) {

		if ($options === null) {
			$options = new CodeReviewConfig();
		}
		$this->options = $options;

		$this->maxVersion = $options->getMaxVersion();
		$this->fixProblems = $options->isFixProblemsEnabled();
	}

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
	 * @return array
	 */
	public function analyze() {

		$options = $this->options;

		$i = code_review::getPhpFilesIterator($options->getSubPath(), $options->isSkipInactivePluginsEnabled());

		$fixer = new CodeFixer();
		$this->instantReplacements = $fixer->getBasicFunctionRenames($this->maxVersion);

		$this->stats = array();

		$functions = array();
		if ($options->isDeprecatedFunctionsTestEnabled()) {
			$functions = array_merge($functions, code_review::getDeprecatedFunctionsList($this->maxVersion));
		}
		if ($options->isPrivateFunctionsTestEnabled()) {
			$functions = array_merge($functions, code_review::getPrivateFunctionsList());
		}

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
	 * @return string
	 */
	private function outputReportHeader() {

		$options = $this->options;

		$result = '';

		$result .= "Subpath selected <strong>" . $options->getSubPath() . "</strong>\n";
		$result .= "Max version: " . $options->getMaxVersion() . "\n";
		$result .= "Skipped inactive plugins: " . ($options->isSkipInactivePluginsEnabled() ? 'yes' : 'no') . "\n";
		$result .= "Attempt to fix problems: " . ($options->isFixProblemsEnabled() ? 'yes' : 'no') . "\n";

		foreach (array('problems', 'fixes') as $type) {
			$total = 0;
			foreach ($this->stats as $items) {
				$total += count($items[$type]);
			}
			$result .= "Found $total $type in " . count($this->stats) . " files\n";
		}
		return $result;
	}

	/**
	 * @return string
	 */
	private function ouptutUnusedFunctionsReport() {
		//prepare unused functions report
		$functions = get_defined_functions();
		$functions = array_filter($functions['user'], 'strtolower');
		$calledFunctions = array_filter($this->calledFunctions, 'strtolower');
		$deprecatedFunctions = array_filter(array_keys(code_review::getDeprecatedFunctionsList($this->maxVersion)), 'strtolower');
		$functions = array_diff($functions, $calledFunctions, $deprecatedFunctions);

		foreach ($functions as $key => $function) {
			if (function_exists($function)) {
				$reflectionFunction = new ReflectionFunction($function);
				if (!$reflectionFunction->isInternal()) {
					continue;
				}
				unset($reflectionFunction);
			}
			unset($functions[$key]);
		}
		sort($functions);

		//unused functions report
		$result = "Not called but defined funcions:\n";
		$baseLenght = strlen(elgg_get_root_path());
		foreach (array_values($functions) as $functionName) {
			$reflectionFunction = new ReflectionFunction($functionName);
			$path = substr($reflectionFunction->getFileName(), $baseLenght);
			if (strpos($path, 'engine') !== 0) {
				continue;
			}
			$result .= "$functionName \t{$path}:{$reflectionFunction->getStartLine()}\n";
		}
		return $result;
	}

	/**
	 * @return string
	 */
	public function outputReport() {

		$options = $this->options;

		$result = $this->outputReportHeader();

		/*
		 * Full report
		 */
		foreach ($this->stats as $filePath => $items) {
			$result .= "\nIn file: " . $filePath . "\n";

			//problems
			foreach ($items['problems'] as $row) {
				list($data, $function, $line) = $row;
				$version = $data['version'];
				$result .= "    " . $data->toString(array(
						'function' => $function,
						'line' => $line
					)) . "\n";
			}

			//fixes
			foreach ($items['fixes'] as $row) {
				list($before, $after, $line) = $row;
				$result .= "    Line $line:\tReplacing: '$before' with '$after'\n";
			}
		}
		
		$result .= "\n";

//		$result .= $this->ouptutUnusedFunctionsReport();

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
			// get non trivial tokens
			if (is_array($row)) {
				list($token, $functionName, $lineNumber) = $row;
				$originalFunctionName = $functionName;

				// prepare normalized version of function name for matching
				$functionName = strtolower($functionName);
//				if ($token == T_CONSTANT_ENCAPSED_STRING && function_exists(trim($functionName, '\'""'))) {
//					$functionName = trim($functionName, '\'""');
//					if (!in_array($functionName, $this->calledFunctions)) {
//						$this->calledFunctions[] = $functionName;
//					}
//				}

				// check for function call
				if ($token == T_STRING
					&& !$phpTokens->isEqualToToken(T_OBJECT_OPERATOR, $key-1) //not method
					&& !$phpTokens->isEqualToToken(T_DOUBLE_COLON, $key-1) //not static method
					&& !$phpTokens->isEqualToToken(T_FUNCTION, $key-2) //not definition
				) {
					// mark function as called
					if (function_exists($functionName) && !in_array($functionName, $this->calledFunctions)) {
						$this->calledFunctions[] = $functionName;
					}
					// is it function we're looking for
					if (isset($functions[$functionName])) {
						$definingFunctionName = $phpTokens->getDefiningFunctionName($key);

						//we're skipping deprecated calls that are in deprecated function itself
						if (!$definingFunctionName || !isset($functions[strtolower($definingFunctionName)])) {
							$result['problems'][] = array($functions[$functionName], $originalFunctionName, $lineNumber);
						}

						//do instant replacement
						if ($this->fixProblems && isset($this->instantReplacements[$functionName])) {
							$phpTokens[$key] = array(T_STRING, $this->instantReplacements[$functionName]);
							$result['fixes'][] = array($originalFunctionName, $this->instantReplacements[$functionName], $lineNumber);
							$changes++;
						}
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