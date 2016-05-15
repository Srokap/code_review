<?php

$engine_dir = dirname(__FILE__);

require_once "$engine_dir/lib/deprecated-1.2.php";
require_once "$engine_dir/lib/foobar.php";

require_once "$engine_dir/classes/CodeReviewFakeElggAutoloader.php";

$autoloader = new CodeReviewFakeElggAutoloader();
$autoloader->register();


