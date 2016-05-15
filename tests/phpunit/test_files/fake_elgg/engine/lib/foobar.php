<?php

/**
 * This one is documented, so not private
 */
function foobar_init() {
	// let's to nasty stuff!
	dummy_deprecated_function1(); // THIS MUST STAY AT LINE 8
}

/**
 * Explicitly marked as private
 *
 * @access private
 */
function foobar_private_api() {
	return array(true, false);
}

//not documented means private
function foobar_undocumented() {
	return 123;
}
