<?php

ini_set('max_execution_time', 0);

$version = elgg_extract('version', $vars);
$include_disabled_plugins = elgg_extract('include_disabled_plugins', $vars, false);
$skipInactive = !$include_disabled_plugins;

//sanitize provided path
$subPath = elgg_extract('subpath', $vars, '/ ');
$subPath = trim($subPath, '/\\');
$subPath = str_replace('\\', '/', $subPath);
$subPath = str_replace('..', '', $subPath);
$subPath = $subPath . '/';

$body = '';
$body .= '<pre>';

$mt = microtime(true);

$analyzer = new CodeReviewAnalyzer();
$analyzer->analyze(code_review::getPhpFilesIterator($subPath, $skipInactive), $version);
$body .= "Subpath selected <strong>$subPath</strong>\n";
$body .= $analyzer->ouptutReport($skipInactive);

$body .= sprintf("Time taken: %.4fs\n", microtime(true) - $mt);

$body .= '</pre>';

echo $body;