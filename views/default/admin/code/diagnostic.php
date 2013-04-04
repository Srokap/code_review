<?php

$language = get_input('language');

$body = elgg_view_form('code_review/select', array(
	'action' => '#',
), array(
	'language' => $language,
));

echo elgg_view_module('main', elgg_echo('code_review:form'), $body);

echo '<br>';

$body = '';
$body .= elgg_view('graphics/ajax_loader', array(
	'id' => 'code-review-loader'
));
$body .= '<div id="code-review-result">';

if ($language) {
	$body .= elgg_view('code_review/analysis', array(
		'language' => $language,
	));
} else {
	$body .= elgg_echo('code_review:results:initial_stub');
}

$body .= '</div>';

echo elgg_view_module('main', elgg_echo('code_review:results'), $body);

// $body = '';
// $body .= '<pre>';

// $mt = microtime(true);

// $analyzer = new CodeReviewAnalyzer();
// $analyzer->analyze(code_review::getPhpFilesIterator('/'));
// $body .= $analyzer->ouptutReport();

// $body .= sprintf("Time taken: %.4fs\n", microtime(true) - $mt);

// $body .= '</pre>';

// // $body .= print_r($result, true);

// echo elgg_view_module('main', elgg_echo('code_review:results'), $body);

///////////////////////////////////////////////////////////////////////////////////
// $language = get_input('language');

// $body = elgg_view_form('code_review/select', array(
// 	'action' => '#',
// ), array(
// 	'language' => $language,
// ));

// echo elgg_view_module('main', elgg_echo('code_review:form'), $body);

// echo '<br>';

// $body = '';
// $body .= elgg_view('graphics/ajax_loader', array(
// 	'id' => 'code-review-loader'
// ));
// $body .= '<div id="code-review-result">';

// if ($language) {
// 	$body .= elgg_view('code_review/analysis', array(
// 		'language' => $language,
// 	));
// } else {
// 	$body .= elgg_echo('code_review:results:initial_stub');
// }

// $body .= '</div>';

// echo elgg_view_module('main', elgg_echo('code_review:results'), $body);
