<?php

echo elgg_view('code_review/navigation');

//fetch all
$functions = get_defined_functions();
$functions = array('user' => $functions['user']);

//$exts = get_loaded_extensions();
//foreach ($exts as $ext) {
////	var_dump($ext);
//	$functions[$ext] = get_extension_funcs($ext);
//}

//group by versions
//$groups = array();
//foreach ($functions as $group => $name) {
//	if (!isset($groups[$group])) {
//		$groups[$group] = array();
//	}
//	$groups[$group][] = $name;
//}

//ksort($groups);

//$fixes = new CodeFixer();
//$replaces = $fixes->getBasicFunctionRenames();

foreach ($functions as $group => $rows) {
	$title = elgg_echo('code_review:functions_list:title', array($group));
	$body = "<table class=\"elgg-table-alt\">";
	$body .= "<tr>"
		. "<th><strong>" . elgg_echo('code_review:functions_list:name') . "</strong></th>"
		. "<th><strong>" . elgg_echo('code_review:functions_list:file') . "</strong></th>"
		. "<th><strong>" . elgg_echo('code_review:functions_list:line') . "</strong></th>"
		. "</tr>";
	asort($rows, SORT_STRING);
	foreach ($rows as $name) {
		$body .= "<tr><td><abbr title=\"$fileLine\">$name</abbr></td>";
		$reflection = new ReflectionFunction($name);

		$body .= "<td>" . $reflection->getFileName() . '</td>';
		$body .= "<td>" . $reflection->getStartLine() . '</td>';

		$body .= "</tr>";
	}
	$body .= '</table>';
	echo elgg_view_module('featured', $title, $body, array(
		'class' => 'mbl',
	));
}



