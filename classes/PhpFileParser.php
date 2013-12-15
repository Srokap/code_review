<?php
/**
 * Splits file source to tokens, provides ways to manipulate tokens list and output modified source.
 * Intended to help in code replacements on language syntax level.
 */
class PhpFileParser implements Iterator, ArrayAccess {

	/**
	 * @var string original file name
	 */
	private $fileName = null;

	/**
	 * @var array
	 */
	private $tokens = null;

	/**
	 * @var string
	 */
	private $sha1hash = null;

	/**
	 * @param $fileName
	 * @throws CodeReview_IOException
	 * @throws Exception
	 */
	public function __construct($fileName) {
		$this->validateFilePath($fileName);
		$this->fileName = $fileName;

		$contents = file_get_contents($fileName);
		if ($contents === false) {
			throw new CodeReview_IOException("Error while fetching contents of file $fileName");
		}

		$this->sha1hash = sha1_file($fileName);
		if ($this->sha1hash === false) {
			throw new CodeReview_IOException("Error while computing SHA1 hash of file $fileName");
		}

		$this->tokens = token_get_all($contents);
		$this->computeNestingParentTokens();
		if (!is_array($this->tokens)) {
			throw new Exception("Failed to parse PHP contents of $fileName");
		}
	}

	/**
	 * Return fileds to serialize.
	 *
	 * @return array
	 */
	public function __sleep() {
		return array('fileName', 'sha1hash', 'tokens');
	}

	/**
	 * Verify class contents against original file to detect changes.
	 */
	public function __wakeup() {
		$this->validateFileContents();
	}

	/**
	 * Uses SHA1 hash to determine if file contents has changed since analysis.
	 *
	 * @return bool
	 * @throws CodeReview_IOException
	 * @throws LogicException
	 */
	private function validateFileContents() {
		if (!$this->fileName) {
			throw new LogicException("Missing file's path. Looks like severe internal error.");
		}
		$this->validateFilePath($this->fileName);
		if (!$this->sha1hash) {
			throw new LogicException("Missing file's SHA1 hash. Looks like severe internal error.");
		}
		if ($this->sha1hash !== sha1_file($this->fileName)) {
			throw new CodeReview_IOException("The file on disk has changed and this " . get_class($this) . " class instance is no longer valid for use. Please create fresh instance.");
		}
		return true;
	}

	/**
	 * Checks if file exists and is readable.
	 *
	 * @param $fileName
	 * @return bool
	 * @throws CodeReview_IOException
	 */
	private function validateFilePath($fileName) {
		if (!file_exists($fileName)) {
			throw new CodeReview_IOException("File $fileName does not exists");
		}
		if (!is_file($fileName)) {
			throw new CodeReview_IOException("$fileName must be a file");
		}
		if (!is_readable($fileName)) {
			throw new CodeReview_IOException("File $fileName is not readable");
		}
		return true;
	}

	/**
	 * Compute parents of the tokens to easily determine containing methods and classes.
	 *
	 * @param bool $debug
	 */
	private function computeNestingParentTokens($debug = false) {
		$nesting = 0;
		$parents = array();
		$lastParent = null;
		foreach ($this->tokens as $key => $token) {
			if (is_array($token)) {
				//add info about parent to array
				$parent = $parents ? $parents[count($parents)-1] : null;
				$this->tokens[$key][3] = $parent;
				$this->tokens[$key][4] = $nesting;

				//is current token possible parent in current level?
				if ($this->isEqualToAnyToken(array(T_CLASS, T_INTERFACE, T_FUNCTION), $key)) {
					$lastParent = $key + 2;
				} elseif ($this->isEqualToAnyToken(array(T_CURLY_OPEN, T_DOLLAR_OPEN_CURLY_BRACES), $key)) {
					$nesting++;
					array_push($parents, '');//just a placeholder
					if ($debug) {
						echo "$nesting\{\$\n";
					}
				}
//				elseif ($this->isEqualToToken(T_DO, $key)) {
//					$lastParent = $key;
//				} elseif ($this->isEqualToToken(T_WHILE, $key)) {
//					$lastParent = $key;
//				} elseif ($this->isEqualToToken(T_FOR, $key)) {
//					$lastParent = $key;
//				}
			} else {
				if ($token == '{') {
					$nesting++;
					if ($debug) {
						echo "$nesting{\n";
					}
					array_push($parents, $lastParent);
				} elseif ($token == '}') {
					if ($debug) {
						echo "$nesting}\n";
					}
					$nesting--;
					array_pop($parents);
				}
			}
		}
	}

	/**
	 * @param array $tokens
	 * @param int   $offset
	 * @return bool
	 */
	public function isEqualToAnyToken($tokens, $offset = null) {
		foreach ($tokens as $token) {
			if ($this->isEqualToToken($token, $offset)) {
				return true;
			}
		}
		return false;
	}

	/**
	 * @param $token string|int individual token identifier or predefined T_* constant value for complex tokens
	 * @param int $offset optional offset when checking other than current
	 * @return bool
	 */
	public function isEqualToToken($token, $offset = null) {
		if ($offset === null) {
			$offset = $this->key();
		}
		if (!isset($this[$offset])) {
			return false;
		}
		$val = $this[$offset];
		if (is_string($token)) {
			//assume one char token that gets passed directly as string
			return $val == $token;
		}
		return is_array($val) && $val[0] == $token;
	}

	/**
	 * @param int $offset optional offset when checking other than current
	 * @return mixed
	 */
	public function getDefiningFunctionName($offset = null) {
		if ($offset === null) {
			$offset = $this->key();
		}
		$parentKey = $this->tokens[$offset][3];
		while ($parentKey !== null && !$this->isEqualToToken(T_FUNCTION, $parentKey - 2)) {
			$parentKey = $this->tokens[$parentKey][3];
		}
		if ($parentKey !== null) {
			$class = $this->getDefiningClassName($parentKey);
			if ($class) {
				return $class . '::' . $this->tokens[$parentKey][1];
			} else {
				return $this->tokens[$parentKey][1];
			}
		}
		return null;
	}

	/**
	 * @param int $offset optional offset when checking other than current
	 * @return mixed
	 */
	public function getDefiningClassName($offset = null) {
		if ($offset === null) {
			$offset = $this->key();
		}
		$parentKey = $this->tokens[$offset][3];
		while ($parentKey !== null && !$this->isEqualToToken(T_CLASS, $parentKey - 2)) {
			$parentKey = $this->tokens[$parentKey][3];
		}
		if ($parentKey !== null) {
			return $this->tokens[$parentKey][1];
		}
		return null;
	}

	/**
	 * @param string $fileName
	 * @return bool|string
	 * @throws CodeReview_IOException
	 */
	public function exportPhp($fileName = null) {
		$source = '';
		$data = $this->tokens;
		reset($data);
		foreach ($data as $val) {
			if (is_array($val)) {
				$source .= $val[1];
			} else {
				$source .= $val;
			}
		}

		if ($fileName !== null) {
			if (!is_writable($fileName)) {
				throw new CodeReview_IOException("$fileName must be writable");
			}
			return file_put_contents($fileName, $source) !== false;
		} else {
			return $source;
		}
	}

	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Return the current element
	 *
	 * @link http://php.net/manual/en/iterator.current.php
	 * @return mixed Can return any type.
	 */
	public function current() {
		return current($this->tokens);
	}

	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Move forward to next element
	 * @link http://php.net/manual/en/iterator.next.php
	 * @return void Any returned value is ignored.
	 */
	public function next() {
		next($this->tokens);
	}

	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Return the key of the current element
	 * @link http://php.net/manual/en/iterator.key.php
	 * @return mixed scalar on success, or null on failure.
	 */
	public function key() {
		return key($this->tokens);
	}

	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Checks if current position is valid
	 * @link http://php.net/manual/en/iterator.valid.php
	 * @return boolean The return value will be casted to boolean and then evaluated.
	 *       Returns true on success or false on failure.
	 */
	public function valid() {
		$key = key($this->tokens);
		$var = ($key !== null && $key !== false);
		return $var;
	}

	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Rewind the Iterator to the first element
	 * @link http://php.net/manual/en/iterator.rewind.php
	 * @return void Any returned value is ignored.
	 */
	public function rewind() {
		reset($this->tokens);
	}

	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Whether a offset exists
	 * @link http://php.net/manual/en/arrayaccess.offsetexists.php
	 * @param mixed $offset <p>
	 *                      An offset to check for.
	 * </p>
	 * @return boolean true on success or false on failure.
	 * </p>
	 * <p>
	 *       The return value will be casted to boolean if non-boolean was returned.
	 */
	public function offsetExists($offset) {
		return isset($this->tokens[$offset]);
	}

	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Offset to retrieve
	 * @link http://php.net/manual/en/arrayaccess.offsetget.php
	 * @param mixed $offset <p>
	 *                      The offset to retrieve.
	 * </p>
	 * @return mixed Can return all value types.
	 */
	public function offsetGet($offset) {
		return $this->tokens[$offset];
	}

	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Offset to set
	 * @link http://php.net/manual/en/arrayaccess.offsetset.php
	 * @param mixed $offset <p>
	 *                      The offset to assign the value to.
	 * </p>
	 * @param mixed $value  <p>
	 *                      The value to set.
	 * </p>
	 * @return void
	 */
	public function offsetSet($offset, $value) {
		$this->tokens[$offset] = $value;
	}

	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Offset to unset
	 * @link http://php.net/manual/en/arrayaccess.offsetunset.php
	 * @param mixed $offset <p>
	 *                      The offset to unset.
	 * </p>
	 * @return void
	 */
	public function offsetUnset($offset) {
		unset($this->tokens[$offset]);
	}
}