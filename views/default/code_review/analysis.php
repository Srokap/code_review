<?php

elgg_admin_gatekeeper();

ini_set('max_execution_time', 0);

$maxVersion = elgg_extract('version', $vars);
$skipInactive = !elgg_extract('include_disabled_plugins', $vars, false);
$fixProblems = elgg_extract('fix_problems', $vars);

//sanitize provided path
$subPath = elgg_extract('subpath', $vars, '/');
$subPath = trim($subPath, '/\\');
$subPath = str_replace('\\', '/', $subPath);
$subPath = str_replace('..', '', $subPath);
$subPath = $subPath . '/';


$options = array(
	'maxVersion' => $maxVersion,
	'fixProblems' => $fixProblems,
);


/*
 * Produce output
 */
echo '<pre>';
$body = '';

$mt = microtime(true);

try {
	$analyzer = new CodeReviewAnalyzer();
	$analyzer->analyze(code_review::getPhpFilesIterator($subPath, $skipInactive), $options);
	$body .= "Subpath selected <strong>$subPath</strong>\n";
	$body .= $analyzer->ouptutReport($skipInactive);
} catch (CodeReview_IOException $e) {
	echo "*** Error: " . $e->getMessage() . " ***\n";
}

$body .= sprintf("Time taken: %.4fs\n", microtime(true) - $mt);

echo $body;
echo '</pre>';
