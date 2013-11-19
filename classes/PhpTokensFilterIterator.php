<?php
class PhpTokensFilterIterator extends FilterIterator {

	/**
	 * @var array
	 */
	protected $allowedTokens = array();

	/**
	 * @var int
	 */
	protected $offset = 0;

	/**
	 * @param PhpFileParser $iterator
	 * @param array         $allowedTokens
	 */
	function __construct(PhpFileParser $iterator, $allowedTokens = array(), $offset = null) {
		if (is_array($allowedTokens)) {
			$this->allowedTokens = $allowedTokens;
		} else {
			$this->allowedTokens = array($allowedTokens);
		}

		if ($offset !== null) {
			$this->offset = $offset;
		}

		parent::__construct($iterator);
	}

//	/**
//	 * @return mixed
//	 */
//	function key() {
//		$key = parent::key();
//		return $key - $this->offset;
//	}

	/**
	 * @return bool
	 */
	function accept () {
		$key = parent::key();
		$token = $this->getInnerIterator()[$key - $this->offset];
		if (is_array($token)) {
			$label = $token[0];
		} elseif (is_string($token)) {
			$label = $token;
		}
		return in_array($label, $this->allowedTokens);
	}
}