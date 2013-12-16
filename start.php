<?php
/**
 * Elgg 1.8 compatibility
 */
if (!function_exists('elgg_get_version')) {
	function elgg_get_version($human_readable = false) {
		global $CONFIG;

		static $version, $release;

		if (isset($CONFIG->path)) {
			if (!isset($version) || !isset($release)) {
				if (!include($CONFIG->path . "version.php")) {
					return false;
				}
			}
			return $human_readable ? $release : $version;
		}

		return false;
	}
}

code_review::boot();
elgg_register_event_handler('init', 'system', array('code_review', 'init'));
