<?php
class CodeFixer {

	/**
	 * @return array of
	 */
	public function getRegExpPairs() {
		return array(
			'([^_a-zA-Z0-9])register_plugin_hook([^_a-zA-Z0-9])' => '$1elgg_register_plugin_hook_handler$2',
		);
	}

}