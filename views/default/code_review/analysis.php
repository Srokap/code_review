<?php

admin_gatekeeper();

ini_set('max_execution_time', 0);

$options = new CodeReviewConfig();
$options->parseInput($vars);

/*
 * Produce output
 */
echo '<pre>';
$body = '';

$mt = microtime(true);

try {
	$analyzer = new CodeReviewAnalyzer($options);
	$analyzer->analyze();
	$body .= $analyzer->outputReport();
} catch (CodeReview_IOException $e) {
	echo "*** Error: " . $e->getMessage() . " ***\n";
}

$body .= sprintf("Time taken: %.4fs\n", microtime(true) - $mt);

echo $body;
echo '</pre>';
