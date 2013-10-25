<?php

$version = get_input('version');

$body = elgg_view_form('code_review/select', array(
	'action' => '#',
), array(
	'version' => $version,
));

echo elgg_view_module('main', elgg_echo('code_review:form'), $body);

echo '<br>';

$body = '';
$body .= elgg_view('graphics/ajax_loader', array(
	'id' => 'code-review-loader'
));
$body .= '<div id="code-review-result">';

if ($version) {
	$body .= elgg_view('code_review/analysis', array(
		'version' => $version,
	));
} else {
	$body .= elgg_echo('code_review:results:initial_stub');
}

$body .= '</div>';

echo elgg_view_module('main', elgg_echo('code_review:results'), $body);

//var_dump(code_review::getDeprecatedFunctionsList('1.9'));