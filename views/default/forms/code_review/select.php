<?php
elgg_load_js('code_review');

$bigVersion = elgg_get_version(true);
if (preg_match('#^([0-9]+\.[0-9]+)#', $bigVersion, $matches)) {
	$bigVersion = $matches[1];
}

$version = elgg_extract('version', $vars, $bigVersion);

echo '<p>';
echo '<label>' . elgg_echo('code_review:subpath') . '</label> ';
echo elgg_view('input/text', array(
	'name' => 'subpath',
	'value' => get_input('subpath'),
	'placeholder' => elgg_echo('code_review:subpath:placeholder'),
));
echo '</p>';

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

