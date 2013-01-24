<?php
srokap_code_review::boot();
elgg_register_event_handler('init', 'system', array('srokap_code_review', 'init'));
