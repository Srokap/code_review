<?php
namespace CodeReview;

class PhpTokensFilterIterator extends \FilterIterator {

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
	 * @param int           $offset
	 */
	public function __construct(PhpFileParser $iterator, $allowedTokens = array(), $offset = null) {
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

	/**
	 * @return bool
	 */
	public function accept () {
		$key = $this->key();
		$innerIterator = $this->getInnerIterator();
		$token = $innerIterator[$key - $this->offset];
		$label = null;
		if (is_array($token)) {
			$label = $token[0];
		} elseif (is_string($token)) {
			$label = $token;
		}
		return in_array($label, $this->allowedTokens);
	}
}