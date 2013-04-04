<?php
elgg_load_js('code_review');

$version = elgg_extract('version', $vars, get_version(true));

echo '<p>';
echo '<label>' . elgg_echo('code_review:version') . '</label> ';
echo elgg_view('input/dropdown', array(
	'name' => 'version',
	'value' => get_input('version', $version),
	'options' => code_review::getVersionsList(),
));
echo '</p>';

echo '<p>';
echo '<label>' . elgg_echo('code_review:disabled_plugins_only') . '</label> ';
echo elgg_view('input/dropdown', array(
	'name' => 'include_disabled_plugins',
	'value' => get_input('include_disabled_plugins', 0),
	'options_values' => array(
		0 => elgg_echo('option:no'),
		1 => elgg_echo('option:yes'),
	),
));
echo '</p>';

echo elgg_view('input/submit', array(
	'name' => 'submit',
	'value' => elgg_echo('search:go'),
));

