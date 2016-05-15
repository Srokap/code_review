<?php

/**
 * @return bool
 * @deprecated 1.1 Remove it
 */
function dummy_deprecated_function1 () {
	return true;
}

/**
 * Function without deprecated tag should still be seen as such if in deprecated-* file.
 *
 * @return bool
 */
function dummy_deprecated_function2 () {
	return true;
}