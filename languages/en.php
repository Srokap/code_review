<?php
$result = array(
	'admin:code' => 'Code',
	'admin:code:diagnostic' => 'Code diagnostic',
	'admin:code:diagnostic:deprecated_list' => 'Deprecated functions list',
	'code_review:menu' => 'Menu',

	'code_review:deprecated_list:title' => 'Deprecated since version %s',
	'code_review:deprecated_list:name' => 'Name',
	'code_review:deprecated_list:remarks' => 'Remarks',
	'code_review:deprecated_list:solution' => 'Solution',

	'code_review:solution:replace_with' => 'Replace with %s',

	'code_review:form' => 'Options',
	'code_review:results' => 'Results',
	'code_review:results:initial_stub' => 'Select options and submit form above to perform analysis. May take significant time - please be patient.',
	'code_review:error:request' => 'There was problem during request',
	'code_review:version' => 'Max version to analyze',
	'code_review:disabled_plugins_only' => 'Include disabled plugins',
);
add_translation('en', $result);//let's be nice for 1.8 users
// return $result;//1.9 standard