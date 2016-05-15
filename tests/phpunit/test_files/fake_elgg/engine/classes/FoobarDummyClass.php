<?php
class FoobarDummyClass {
	/**
	 * @throws Exception
	 */
	public function init() {
		throw new Exception("Not expected to happen");
	}

	/**
	 * @throws Exception
	 * @deprecated 1.2 Deprecated private class method.
	 */
	private function deprecatedPrivate() {
		throw new Exception("Not expected to happen");
	}

	/**
	 * @throws Exception
	 * @deprecated 1.2.0 Deprecated protected class method.
	 */
	protected function deprecatedProtected() {
		throw new Exception("Not expected to happen");
	}

	/**
	 * @throws Exception
	 * @deprecated 1.1 Deprecated public class method.
	 */
	public function deprecatedPublic() {
		throw new Exception("Not expected to happen");
	}
}