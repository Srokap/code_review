<?php

$version = get_input('version');

$body = elgg_view_form('code_review/select', array(
	'action' => '#',
), array(
	'version' => $version,
));

echo elgg_view_module('main', elgg_echo('code_review:form'), $body);

echo '<br>';

$body = '';
$body .= elgg_view('graphics/ajax_loader', array(
	'id' => 'code-review-loader'
));
$body .= '<div id="code-review-result">';

if ($version) {
	$body .= elgg_view('code_review/analysis', array(
		'version' => $version,
	));
} else {
	$body .= elgg_echo('code_review:results:initial_stub');
}

$body .= '</div>';

echo elgg_view_module('main', elgg_echo('code_review:results'), $body);

//var_dump(code_review::getDeprecatedFunctionsList('1.9'));

//$rows = code_review::getDeprecatedFunctionsList('1.9');
//
//echo '<pre>';
//foreach($rows as $name => $row) {
//	if (strpos(strtolower($row['fixinfo']), 'use') !== false) {
//		echo "'$name' => '{$row['fixinfo']}',\n";
//	}
//}
//echo '</pre>';

/*
'get_entities_from_access_id' => '. Use elgg_get_entities_from_access_id()',
'get_entities_from_annotations' => 'Use elgg_get_entities_from_annotations()',
'list_entities_from_annotations' => 'Use elgg_list_entities_from_annotations()',
'get_library_files' => 'Use elgg_get_file_list() instead',
'get_entities' => '.  Use elgg_get_entities().',
'list_registered_entities' => '.  Use elgg_list_registered_entities().',
'list_entities' => '.  Use elgg_list_entities().',
'get_entities_from_metadata' => 'use elgg_get_entities_from_metadata().',
'get_entities_from_metadata_multi' => '.  Use elgg_get_entities_from_metadata().',
'get_entities_from_relationship' => 'Use elgg_get_entities_from_relationship()',
'extend_view' => '.  Use elgg_extend_view().',
'get_views' => '.  Use elgg_get_views().',
'make_register_object' => 'Use add_submenu_item()',
'list_entities_from_access_id' => 'Use elgg_list_entities_from_access_id()',
'register_action' => 'Use elgg_register_action() instead',
'get_entities_from_annotations_calculate_x' => 'Use elgg_get_entities_from_annotation_calculation()',
'get_entities_from_annotation_count' => 'Use elgg_get_entities_from_annotation_calculation()',
'list_entities_from_annotation_count' => 'Use elgg_list_entities_from_annotation_calculation()',
'add_to_register' => 'Use the new menu system. This is only used for the site-wide menu.  See add_menu().',
'remove_from_register' => 'Use the new menu system. This is used to by remove_menu() to remove site-wide menu items.',
'get_register' => 'Use the new menu system',
'events' => 'Use explicit register/trigger event functions',
'register_elgg_event_handler' => 'Use elgg_register_event_handler() instead',
'unregister_elgg_event_handler' => 'Use elgg_unregister_event_handler instead',
'trigger_elgg_event' => 'Use elgg_trigger_event() instead',
'register_plugin_hook' => 'Use elgg_register_plugin_hook_handler() instead
eg if you want the function "export_user" to be called when the hook "export" for "user" entities
is run, use:
register_plugin_hook("export", "user", "export_user");
"all" is a valid value for both $hook and $entity_type. "none" is a valid value for $entity_type.
The export_user function would then be defined as:
function export_user($hook, $entity_type, $returnvalue, $params);
Where $returnvalue is the return value returned by the last function returned by the hook, and
$params is an array containing a set of parameters (or nothing).',
'unregister_plugin_hook' => 'Use elgg_unregister_plugin_hook_handler() instead',
'trigger_plugin_hook' => 'Use elgg_trigger_plugin_hook() instead
trigger_plugin_hook('foo', 'bar', array('param1' => 'value1'), true);',
'elgg_get_entity_owner_where_sql' => 'Use elgg_get_guid_based_where_sql();',
'elgg_get_entity_container_where_sql' => 'Use elgg_get_guid_based_where_sql();',
'elgg_get_entity_site_where_sql' => 'Use elgg_get_guid_based_where_sql()',
'get_objects_in_group' => 'Use elgg_get_entities() instead',
'list_entities_groups' => 'Use elgg_list_entities() instead',
'get_entities_from_metadata_groups' => 'Use elgg_get_entities_from_metadata()',
'get_entities_from_metadata_groups_multi' => 'Use elgg_get_entities_from_metadata()',
'list_entities_in_area' => 'Use elgg_get_entities_from_location()',
'list_entities_location' => 'Use elgg_list_entities_from_location()',
'get_entities_in_area' => 'Use elgg_get_entities_from_location()',
'list_entities_from_metadata' => 'Use elgg_list_entities_from_metadata',
'list_entities_from_metadata_multi' => 'Use elgg_list_entities_from_metadata() instead',
'add_submenu_item' => 'Use the new menu system',
'remove_submenu_item' => 'Use the new menu system',
'get_submenu' => 'Use the new menu system. elgg_view_menu()',
'add_menu' => 'use elgg_register_menu_item() for the menu 'site'',
'remove_menu' => 'Use the new menu system',
'friendly_title' => 'Use elgg_get_friendly_title()',
'friendly_time' => 'Use elgg_view_friendly_time()',
'filter_string' => 'Don't use this.',
'remove_blacklist' => 'Don't use this.',
'page_owner' => 'Use elgg_get_page_owner_guid()',
'page_owner_entity' => 'Use elgg_get_page_owner_entity()',
'add_page_owner_handler' => 'Use the 'page_owner', 'system' plugin hook',
'set_page_owner' => 'Use elgg_set_page_owner_guid()',
'set_context' => 'Use elgg_set_context()',
'get_context' => 'Use elgg_get_context()',
'get_plugin_list' => 'Use elgg_get_plugin_ids_in_dir() or elgg_get_plugins()',
'regenerate_plugin_list' => 'Use elgg_generate_plugin_entities() and elgg_set_plugin_priorities()',
'get_plugin_name' => 'Use elgg_get_calling_plugin_id()',
'load_plugin_manifest' => 'Use ElggPlugin->getManifest()',
'check_plugin_compatibility' => 'Use ElggPlugin->canActivate()',
'find_plugin_settings' => 'Use elgg_get_calling_plugin_entity() or elgg_get_plugin_from_id()',
'get_installed_plugins' => 'use elgg_get_plugins()',
'enable_plugin' => 'Use ElggPlugin->activate()',
'disable_plugin' => 'Use ElggPlugin->deactivate()',
'is_plugin_enabled' => 'Use elgg_is_active_plugin()',
'get_entities_from_private_setting' => 'Use elgg_get_entities_from_private_settings()',
'get_entities_from_private_setting_multi' => 'Use elgg_get_entities_from_private_settings()',
'list_entities_from_relationship' => 'Use elgg_list_entities_from_relationship()',
'get_entities_by_relationship_count' => 'Use elgg_get_entities_from_relationship_count()',
'list_entities_by_relationship_count' => 'Use elgg_list_entities_from_relationship_count()',
'get_entities_from_relationships_and_meta' => 'Use elgg_get_entities_from_relationship()',
'get_river_items' => 'Use elgg_get_river()',
'elgg_view_river_items' => 'Use elgg_list_river()',
'get_activity_stream_data' => 'This is outdated and uses the systemlog table instead of the river table.
Don't use it.',
'authenticate' => 'Use elgg_authenticate',
'get_site_members' => 'Use ElggSite::getMembers()',
'list_site_members' => 'Use ElggSite::listMembers()',
'add_site_collection' => 'Don't use this.',
'remove_site_collection' => 'Don't use this.',
'get_site_collections' => 'Don't use this.',
'get_tags' => 'Use elgg_get_tags().',
'display_tagcloud' => 'use elgg_view_tagcloud()',
'get_user_objects' => 'Use elgg_get_entities() instead',
'count_user_objects' => 'Use elgg_get_entities() instead',
'list_user_objects' => 'Use elgg_list_entities() instead',
'get_user_objects_by_metadata' => 'Use elgg_get_entities_from_metadata() instead',
'set_user_validation_status' => 'Use elgg_set_user_validation_status()',
'request_user_validation' => 'Hook into the register, user plugin hook and request validation.',
'page_draw' => 'Use elgg_view_page()',
'elgg_view_listing' => 'use elgg_view_image_block()',
'get_entity_icon_url' => 'Use $entity->getIconURL()',
'get_loggedin_user' => 'Use elgg_get_logged_in_user_entity()',
'get_loggedin_userid' => 'Use elgg_get_logged_in_user_guid()',
'isloggedin' => 'Use elgg_is_logged_in();',
'isadminloggedin' => 'Use elgg_is_admin_logged_in()',
'load_plugins' => 'Use elgg_load_plugins()',
'find_plugin_usersettings' => 'Use elgg_get_all_plugin_user_settings() or ElggPlugin->getAllUserSettings()',
'set_plugin_usersetting' => 'Use elgg_set_plugin_user_setting() or ElggPlugin->setUserSetting()',
'clear_plugin_usersetting' => 'Use elgg_unset_plugin_user_setting or ElggPlugin->unsetUserSetting().',
'get_plugin_usersetting' => 'Use elgg_get_plugin_user_setting() or ElggPlugin->getUserSetting()',
'set_plugin_setting' => 'Use elgg_set_plugin_setting() or ElggPlugin->setSetting()',
'get_plugin_setting' => 'Use elgg_get_plugin_setting() or ElggPlugin->getSetting()',
'clear_plugin_setting' => 'Use elgg_unset_plugin_setting() or ElggPlugin->unsetSetting()',
'clear_all_plugin_settings' => 'Use elgg_unset_all_plugin_settings() or ElggPlugin->unsetAllSettings()',
'get_annotations' => 'Use elgg_get_annotations()',
'list_annotations' => 'Use elgg_list_annotations()',
'count_annotations' => 'Use elgg_get_annotations() and pass 'count' => true',
'get_annotations_sum' => 'Use elgg_get_annotations() and pass 'annotation_calculation' => 'sum'',
'get_annotations_max' => 'Use elgg_get_annotations() and pass 'annotation_calculation' => 'max'',
'get_annotations_min' => 'Use elgg_get_annotations() and pass 'annotation_calculation' => 'min'',
'get_annotations_avg' => 'Use elgg_get_annotations() and pass 'annotation_calculation' => 'min'',
'get_annotations_calculate_x' => 'Use elgg_get_annotations() and pass anntoation_calculation => ',
'list_entities_from_annotation_count_by_metadata' => 'Use elgg_list_entities_from_annotation_calculation().',
'set_view_location' => 'Use elgg_set_view_location()',
'register_entity_url_handler' => 'Use elgg_register_entity_url_handler()',
'find_metadata' => 'Use elgg_get_metadata()',
'get_metadata_byname' => 'Use elgg_get_metadata()',
'get_metadata_for_entity' => 'Use elgg_get_metadata()',
'get_metadata' => 'Use elgg_get_metadata_from_id()',
'clear_metadata' => 'Use elgg_delete_metadata()',
'clear_metadata_by_owner' => 'Use elgg_delete_metadata()',
'delete_metadata' => 'Use elgg_delete_metadata()',
'remove_metadata' => 'Use elgg_delete_metadata()',
'get_annotation' => 'Use elgg_get_annotation_from_id()',
'delete_annotation' => 'Use elgg_delete_annotations()',
'clear_annotations' => 'Use elgg_delete_annotations()',
'clear_annotations_by_owner' => 'Use elgg_delete_annotations()',
'register_page_handler' => 'Use elgg_register_page_handler()',
'unregister_page_handler' => 'Use elgg_unregister_page_handler()',
'register_annotation_url_handler' => 'Use elgg_register_annotation_url_handler()',
'register_extender_url_handler' => 'Use elgg_register_extender_url_handler()',
'register_entity_type' => 'Use elgg_register_entity_type()',
'register_metadata_url_handler' => 'Use elgg_register_metadata_url_handler()',
'register_relationship_url_handler' => 'Use elgg_register_relationship_url_handler()',
'elgg_view_register_simplecache' => 'Use elgg_register_simplecache_view()',
'elgg_view_regenerate_simplecache' => 'Use elgg_regenerate_simplecache()',
'elgg_view_enable_simplecache' => 'Use elgg_enable_simplecache()',
'elgg_view_disable_simplecache' => 'Use elgg_disable_simplecache()',
'save_widget_location' => 'use ElggWidget::move()',
'get_widgets' => 'Use elgg_get_widgets()',
'add_widget' => 'use elgg_create_widget()',
'add_widget_type' => 'Use elgg_register_widget_type',
'remove_widget_type' => 'Use elgg_unregister_widget_type',
'widget_type_exists' => 'Use elgg_is_widget_type',
'get_widget_types' => 'Use elgg_get_widget_types',
'save_widget_info' => 'Use elgg_save_widget_settings',
'reorder_widgets_from_panel' => 'Don't use.',
'use_widgets' => 'Don't use.',
'using_widgets' => 'Don't use.',
'display_widget' => 'Use elgg_view_entity()',
'remove_from_river_by_subject' => 'Use elgg_delete_river()',
'remove_from_river_by_object' => 'Use elgg_delete_river()',
'remove_from_river_by_annotation' => 'Use elgg_delete_river()',
'remove_from_river_by_id' => 'Use elgg_delete_river()',
'elgg_register_entity_url_handler' => 'Use the plugin hook in ElggEntity::getURL()',
'elgg_register_relationship_url_handler' => 'Use the plugin hook in ElggRelationship::getURL()',
'get_relationship_url' => 'Use ElggRelationship::getURL()',
'elgg_register_extender_url_handler' => 'Use plugin hook in ElggExtender::getURL()',
'get_extender_url' => 'Use method getURL()',
'get_annotation_url' => 'Use method getURL() on annotation object',
'elgg_register_metadata_url_handler' => 'Use the plugin hook in ElggExtender::getURL()',
'elgg_register_annotation_url_handler' => 'Use the plugin hook in ElggExtender::getURL()',
'get_group_members' => 'Use ElggGroup::getMembers()',
'add_object_to_group' => 'Use ElggGroup::addObjectToGroup()',
'remove_object_from_group' => 'Use ElggGroup::removeObjectFromGroup()',
'is_group_member' => 'Use Use ElggGroup::isMember()',
'get_users_membership' => 'Use ElggUser::getGroups()',
'user_is_friend' => 'Use ElggUser::isFriendsOf() or ElggUser::isFriendsWith()',
'get_user_friends' => 'Use ElggUser::getFriends()',
'get_user_friends_of' => 'Use ElggUser::getFriendsOf()',
'user_add_friend' => 'Use ElggUser::addFriend()',
'user_remove_friend' => 'Use ElggUser::removeFriend()',
'add_site_user' => 'Use ElggSite::addEntity()',
'remove_site_user' => 'Use ElggSite::removeEntity()',
'add_site_object' => 'Use ElggSite::addEntity()',
'remove_site_object' => 'Use ElggSite::removeEntity()',
'get_site_objects' => 'Use ElggSite::getEntities()',
'get_object_sites' => 'Use ElggEntity::getSites()',
'get_user_sites' => 'Use ElggEntity::getSites()',
'can_edit_extender' => 'Use the appropriate canEdit() method on metadata or annotations',
'get_metastring_id' => 'Use elgg_get_metastring_id()',
'add_metastring' => 'Use elgg_get_metastring_id()',
'get_user_friends_objects' => 'Use elgg_get_entities_from_relationship()',
'count_user_friends_objects' => 'Use elgg_get_entities_from_relationship()',
'list_user_friends_objects' => 'Use elgg_list_entities_from_relationship()',
'get_version' => 'Use elgg_get_version()',
'execute_delayed_query' => 'Use execute_delayed_write_query() or execute_delayed_read_query()',
'elgg_regenerate_simplecache' => 'Use elgg_invalidate_simplecache()',
'unregister_entity_type' => 'Use elgg_unregister_entity_type()',
'get_entity_url' => 'Use ElggEntity::getURL()',
'delete_entity' => 'Use ElggEntity::delete() instead.',
'enable_entity' => 'Use elgg_enable_entity()',
'can_edit_entity_metadata' => 'Use ElggEntity::canEditMetadata',
'disable_entity' => 'Use ElggEntity::disable instead.',
'can_edit_entity' => 'Use ElggEntity::canEdit instead',
'join_group' => 'Use ElggGroup::join instead.',
'leave_group' => 'Use ElggGroup::leave()',
'autop' => 'Use elgg_autop instead',
'expose_function' => 'Enable the web services plugin and use elgg_ws_expose_function().',
'unexpose_function' => 'Enable the web services plugin and use elgg_ws_unexpose_function().',
'register_service_handler' => 'Enable the web services plugin and use elgg_ws_register_service_handler().',
'unregister_service_handler' => 'Enable the web services plugin and use elgg_ws_unregister_service_handler().',
'create_site_entity' => 'Use ElggSite constructor',
'create_group_entity' => 'Use ElggGroup constructor',
'create_user_entity' => 'Use ElggUser constructor',
'create_object_entity' => 'Use ElggObject constructor',
'xml_to_object' => 'Use ElggXMLElement',
'register_notification_object' => 'Use elgg_register_notification_event(). The 3rd argument was used
as the subject line in a notification. As of Elgg, it is now set by a callback
for a plugin hook. See the documentation at the top of the notifications library
titled "Adding a New Notification Event" for more details.',
'register_notification_interest' => 'Use elgg_add_subscription()',
'remove_notification_interest' => 'Use elgg_remove_subscription()',
'register_notification_handler' => 'Use elgg_register_notification_method()',
'unregister_notification_handler' => 'Use elgg_unregister_notification_method()',
 */