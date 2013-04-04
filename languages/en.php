<?php
$result = array(
	'admin:code' => 'Code',
	'admin:code:diagnostic' => 'Code diagnostic',
	
	'code_review:form' => 'Options',
	'code_review:results' => 'Results',
	'code_review:results:initial_stub' => 'Select options and submit form above to perform analysis. May take significant time - please be patient.',
	'code_review:error:request' => 'There was problem during request',
	'code_review:version' => 'Max version to analyze',
	'code_review:disabled_plugins_only' => 'Include disabled plugins',
);
add_translation('en', $result);//let's be nice for 1.8 users
// return $result;//1.9 standard