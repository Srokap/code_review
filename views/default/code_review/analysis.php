<?php
$version = elgg_extract('version', $vars);
$include_disabled_plugins = elgg_extract('include_disabled_plugins', $vars, false);
$skipInactive = !$include_disabled_plugins;

$body = '';
$body .= '<pre>';

$mt = microtime(true);

$analyzer = new CodeReviewAnalyzer();
$analyzer->analyze(code_review::getPhpFilesIterator('/', $skipInactive), $version);
$body .= $analyzer->ouptutReport();

$body .= sprintf("Time taken: %.4fs\n", microtime(true) - $mt);

$body .= '</pre>';

echo $body;