<?php

function inactive_init() {

	dummy_deprecated_function1(); // THIS MUST STAY AT LINE 5

	throw new Exception("Should never happen");
}