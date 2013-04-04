<?php
code_review::boot();
elgg_register_event_handler('init', 'system', array('code_review', 'init'));
