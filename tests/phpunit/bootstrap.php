<?php
date_default_timezone_set('UTC');
error_reporting(E_ALL | E_STRICT);

//echo "Setting up Elgg core autoloader...\n";
//$engine = dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/engine';
//// Set up class auto-loading
//require_once "$engine/lib/autoloader.php";

echo "Setting up CodeReview Autoloader...\n";
require_once(dirname(dirname(dirname(__FILE__))) . '/classes/CodeReview/Autoloader.php');
$autoloader = new \CodeReview\Autoloader();
$autoloader->register();
