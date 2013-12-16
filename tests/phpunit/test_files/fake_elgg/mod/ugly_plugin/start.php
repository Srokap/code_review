<?php
//code_review::boot();
//elgg_register_event_handler('init', 'system', array('code_review', 'init'));

/*
 * Definitions
 */

function callbackTrueGlobal() {
	return true;
}

/*
 * Elgg 1.7 conventions
 */

if (version_compare(elgg_get_version(true), '1.8') < 1) {

	elgg_register_event_handler('foo', 'bar', 'callbackTrueGlobal');
	elgg_register_event_handler('foo', 'bar', array('ugly_plugin', 'callbackTrue'));
	elgg_register_plugin_hook_handler('foo', 'bar', 'callbackTrueGlobal');
	elgg_register_plugin_hook_handler('foo', 'bar', array('ugly_plugin', 'callbackTrue'));

}

/*
 * Elgg 1.8 conventions
 */

if (version_compare(elgg_get_version(true), '1.9') < 1) {

	elgg_register_event_handler('foo', 'bar', 'callbackTrueGlobal');
	elgg_register_event_handler('foo', 'bar', array('ugly_plugin', 'callbackTrue'));
	elgg_register_plugin_hook_handler('foo', 'bar', 'callbackTrueGlobal');
	elgg_register_plugin_hook_handler('foo', 'bar', array('ugly_plugin', 'callbackTrue'));

}

