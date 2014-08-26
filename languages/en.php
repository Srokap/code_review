<?php
$result = array(
	'admin:code' => 'Code',
	'admin:code:diagnostic' => 'Code diagnostic',
	'admin:code:diagnostic:deprecated_list' => 'Deprecated functions list',
	'admin:code:diagnostic:functions_list' => 'Defined functions',
	'admin:code:diagnostic:private_list' => 'Private functions list',
	'code_review:menu' => 'Menu',

	'code_review:deprecated_list:title' => 'Deprecated since version %s',
	'code_review:deprecated_list:name' => 'Name',
	'code_review:deprecated_list:remarks' => 'Remarks',
	'code_review:deprecated_list:solution' => 'Solution',
	'code_review:deprecated_list:file_line' => 'In file %s on line %d',

	'code_review:private_list:title' => 'Private functions',
	'code_review:private_list:name' => 'Name',
	'code_review:private_list:file_line' => 'In file %s on line %d',
	'code_review:private_list:reason' => 'Reason of being private',
	'code_review:private_list:reason:private' => 'Marked private',
	'code_review:private_list:reason:not_documented' => 'Not documented',

	'code_review:functions_list:name' => 'Name',
	'code_review:functions_list:file' => 'Definition file',
	'code_review:functions_list:line' => 'Line',

	'code_review:functions_list:title' => 'Functions defined in %s',

	'code_review:solution:basic_replace_with' => 'Simple replacement with %s',

	'code_review:form' => 'Options',
	'code_review:results' => 'Results',
	'code_review:results:initial_stub' => 'Select options and submit form above to perform analysis. May take significant time - please be patient.',
	'code_review:error:request' => 'There was problem during request',
	'code_review:subpath' => 'Subdirectory to analyze',
	'code_review:subpath:placeholder' => '(root of the install)',
	'code_review:version' => 'Max version to analyze',
	'code_review:disabled_plugins_only' => 'Include disabled plugins',
	'code_review:find_deprecated_functions' => 'Search for deprecated functions usage',
	'code_review:find_private_functions' => 'Search for private functions usage',
	'code_review:fix_problems' => 'Attempt to fix problems',
	'code_review:fix_problems:warning:header' => 'Warning! Read this carefully',
	'code_review:fix_problems:warning' => 'Code analyzer will attempt to fix problem it encounters. Changes will be irreversible and do not guarantee compatibility with previous versions.
		Make sure that you have backup version of the code. Using this feature requires write access to the whole installation directory. DO NOT USE ON PRODUCTION SITE!',
	'code_review:js:confirm_changes' => 'Are you sure you want to modify source code? That\'s the last chance to reconsider!',

);
add_translation('en', $result);//let's be nice for 1.8 users
// return $result;//1.9 standard