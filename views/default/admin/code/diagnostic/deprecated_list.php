<?php

echo elgg_view('code_review/navigation');

//fetch all
$functions = code_review::getDeprecatedFunctionsList('');

//group by versions
$groups = array();
foreach ($functions as $name => $data) {
	$version = elgg_extract('version', $data, 'Unknown');
	if (!isset($groups[$version])) {
		$groups[$version] = array();
	}
	$groups[$version][$name] = $data;
}

ksort($groups);

$fixes = new CodeFixer();
$replaces = $fixes->getBasicFunctionRenames();

foreach ($groups as $version => $group) {
	$title = elgg_echo('code_review:deprecated_list:title', array($version));
	$body = "<table class=\"elgg-table-alt\">";
	$body .= "<tr>"
		. "<th><strong>" . elgg_echo('code_review:deprecated_list:name') . "</strong></th>"
		. "<th><strong>" . elgg_echo('code_review:deprecated_list:remarks') . "</strong></th>"
		. "<th><strong>" . elgg_echo('code_review:deprecated_list:solution') . "</strong></th>"
		. "</tr>";
	ksort($group, SORT_STRING);
	foreach ($group as $name => $data) {
		$fileLine = elgg_echo('code_review:deprecated_list:file_line', array($data['file'], $data['line']));
		$body .= "<tr><td><abbr title=\"$fileLine\">" . $data['name'] . "</abbr></td>";
		$body .= "<td>" . ($data['fixinfoshort'] ? $data['fixinfoshort'] : '') . '</td>';
		$solution = '';
		if (isset($replaces[$name])) {
			$solution = elgg_echo('code_review:solution:basic_replace_with', array($replaces[$name]));
		}
		$body .= "<td>" . $solution . "</td>"
			. "</tr>";
	}
	$body .= '</table>';
	echo elgg_view_module('featured', $title, $body, array(
		'class' => 'mbl',
	));
}



