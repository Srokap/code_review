<?php

echo elgg_view('code_review/navigation');

//fetch all
$functions = code_review::getPrivateFunctionsList();

$fixes = new \CodeReview\CodeFixer();

$title = elgg_echo('code_review:private_list:title');
$body = "<table class=\"elgg-table-alt\">";
$body .= "<tr>"
	. "<th><strong>" . elgg_echo('code_review:private_list:name') . "</strong></th>"
	. "<th><strong>" . elgg_echo('code_review:private_list:reason') . "</strong></th>"
	. "</tr>";
ksort($functions, SORT_STRING);
foreach ($functions as $name => $data) {
	$fileLine = elgg_echo('code_review:private_list:file_line', array($data['file'], $data['line']));
	$body .= "<tr><td><abbr title=\"$fileLine\">" . $data['name'] . "</abbr></td>"
		. "<td>" . elgg_echo('code_review:private_list:reason:' . $data['reason']) . "</td></tr>";
}
$body .= '</table>';
echo elgg_view_module('featured', $title, $body, array(
	'class' => 'mbl',
));



