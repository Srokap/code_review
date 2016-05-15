<?php
class ugly_plugin {

	static function boot() {

	}

	static function init() {
		dummy_deprecated_function1(); // THIS MUST STAY AT LINE 9
	}

	public function privateCaller() {
		foobar_private_api(); // THIS MUST STAY AT LINE 13
	}

	static function callbackTrue() {
		return true;
	}


}