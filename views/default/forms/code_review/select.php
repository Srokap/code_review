<?php
elgg_load_js('code_review');

$bigVersion = elgg_get_version(true);
if (preg_match('#^([0-9]+\.[0-9]+)#', $bigVersion, $matches)) {
	$bigVersion = $matches[1];
}

$subpath = get_input('subpath');
$version = get_input('version', elgg_extract('version', $vars, $bigVersion));
$include_disabled_plugins = get_input('include_disabled_plugins', 0);
$find_deprecated_functions = get_input('find_deprecated_functions', 1);
$find_private_functions = get_input('find_private_functions', 1);
$fix_problems = get_input('fix_problems', 0);

echo '<p>';
echo '<label>' . elgg_echo('code_review:subpath') . '</label> ';
echo elgg_view('input/text', array(
	'name' => 'subpath',
	'value' => $subpath,
	'placeholder' => elgg_echo('code_review:subpath:placeholder'),
));
echo '</p>';

echo '<p>';
echo '<label>' . elgg_echo('code_review:version') . '</label> ';
echo elgg_view('input/dropdown', array(
	'name' => 'version',
	'value' => $version,
	'options' => code_review::getVersionsList(),
));
echo '</p>';

echo '<p>';
echo '<label>' . elgg_echo('code_review:disabled_plugins_only') . '</label> ';
echo elgg_view('input/dropdown', array(
	'name' => 'include_disabled_plugins',
	'value' => $include_disabled_plugins,
	'options_values' => array(
		0 => elgg_echo('option:no'),
		1 => elgg_echo('option:yes'),
	),
));
echo '</p>';

echo '<p>';
echo '<label>' . elgg_echo('code_review:find_deprecated_functions') . '</label> ';
echo elgg_view('input/dropdown', array(
	'name' => 'find_deprecated_functions',
	'value' => $find_deprecated_functions,
	'options_values' => array(
		0 => elgg_echo('option:no'),
		1 => elgg_echo('option:yes'),
	),
));
echo '</p>';

echo '<p>';
echo '<label>' . elgg_echo('code_review:find_private_functions') . '</label> ';
echo elgg_view('input/dropdown', array(
	'name' => 'find_private_functions',
	'value' => $find_private_functions,
	'options_values' => array(
		0 => elgg_echo('option:no'),
		1 => elgg_echo('option:yes'),
	),
));
echo '</p>';

echo '<p>';
echo '<label>' . elgg_echo('code_review:fix_problems') . '</label> ';
echo elgg_view('input/dropdown', array(
	'name' => 'fix_problems',
	'id' => 'code-review-fix-problems-selector',
	'value' => $fix_problems,
	'options_values' => array(
		0 => elgg_echo('option:no'),
		1 => elgg_echo('option:yes'),
	),
));
echo '</p>';
echo elgg_view_module('main', elgg_echo('code_review:fix_problems:warning:header'), elgg_echo('code_review:fix_problems:warning'), array(
	'id' => 'code-review-fix-problems-module',
	'class' => 'elgg-message elgg-state-error hidden'
));

echo elgg_view('input/submit', array(
	'name' => 'submit',
	'value' => elgg_echo('search:go'),
));

