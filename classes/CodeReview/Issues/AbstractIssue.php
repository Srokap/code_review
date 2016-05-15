<?php
namespace CodeReview\Issues;

abstract class AbstractIssue implements \ArrayAccess {

	/**
	 * @var array
	 */
	protected $data = array();

	/**
	 * @param array $params
	 */
	public function __construct(array $params = array()) {
		$this->data = $params;
	}

	/**
	 * @return string
	 */
	public function toString() {
		return "Line " . $this->data['line'] . ":\tFunction call: " . $this->data['name'] . " " . $this->getExplanation();
	}

	/**
	 * @return string
	 */
	abstract protected function getExplanation();

	/**
	 * @return string
	 */
	public function __toString() {
		return $this->toString();
	}

	/**
	 * @param mixed $offset
	 * @return bool
	 */
	public function offsetExists($offset) {
		return isset($this->data[$offset]);
	}

	/**
	 * @param mixed $offset
	 * @return mixed
	 */
	public function offsetGet($offset) {
		return $this->data[$offset];
	}

	/**
	 * @param mixed $offset
	 * @param mixed $value
	 */
	public function offsetSet($offset, $value) {
		$this->data[$offset] = $value;
	}

	/**
	 * @param mixed $offset
	 */
	public function offsetUnset($offset) {
		unset($this->data[$offset]);
	}
} 