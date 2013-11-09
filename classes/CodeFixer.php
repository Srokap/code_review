<?php
class CodeFixer {

	/**
	 * Just basic function renames from A to B
	 *
	 * @return array
	 */
	public function getBasicFunctionRenames() {
		return array(
			//1.7
			'elgg_validate_action_url' => 'elgg_add_action_tokens_to_url',
			'menu_item' => 'make_register_object',
			'extend_view' => 'elgg_extend_view',
			'get_views' => 'elgg_get_views',

			//1.8
			'register_elgg_event_handler' => 'elgg_register_event_handler',
			'unregister_elgg_event_handler' => 'elgg_unregister_event_handler',
			'trigger_elgg_event' => 'elgg_trigger_event',
			'register_plugin_hook' => 'elgg_register_plugin_hook_handler',
			'unregister_plugin_hook' => 'elgg_unregister_plugin_hook_handler',
			'trigger_plugin_hook' => 'elgg_trigger_plugin_hook',
			'friendly_title' => 'elgg_get_friendly_title',
			'friendly_time' => 'elgg_view_friendly_time',
			'page_owner' => 'elgg_get_page_owner_guid',
			'page_owner_entity' => 'elgg_get_page_owner_entity',
			'set_page_owner' => 'elgg_set_page_owner_guid',
			'set_context' => 'elgg_set_context',
			'get_context' => 'elgg_get_context',
			'get_plugin_name' => 'elgg_get_calling_plugin_id',
			'is_plugin_enabled' => 'elgg_is_active_plugin',
			'set_user_validation_status' => 'elgg_set_user_validation_status',
			'get_loggedin_user' => 'elgg_get_logged_in_user_entity',
			'get_loggedin_userid' => 'elgg_get_logged_in_user_guid',
			'isloggedin' => 'elgg_is_logged_in',
			'isadminloggedin' => 'elgg_is_admin_logged_in',
			'load_plugins' => '_elgg_load_plugins',
			'set_plugin_usersetting' => 'elgg_set_plugin_user_setting',
			'clear_plugin_usersetting' => 'elgg_unset_plugin_user_setting',
			'get_plugin_usersetting' => 'elgg_get_plugin_user_setting',
			'set_plugin_setting' => 'elgg_set_plugin_setting',
			'get_plugin_setting' => 'elgg_get_plugin_setting',
			'clear_plugin_setting' => 'elgg_unset_plugin_setting',
			'clear_all_plugin_settings' => 'elgg_unset_all_plugin_settings',
			'set_view_location' => 'elgg_set_view_location',
			'get_metadata' => 'elgg_get_metadata_from_id',
			'get_annotation' => 'elgg_get_annotation_from_id',
			'register_page_handler' => 'elgg_register_page_handler',
			'unregister_page_handler' => 'elgg_unregister_page_handler',
			'register_entity_type' => 'elgg_register_entity_type',
			'elgg_view_register_simplecache' => 'elgg_register_simplecache_view',
			'elgg_view_regenerate_simplecache' => 'elgg_regenerate_simplecache',
			'elgg_view_enable_simplecache' => 'elgg_enable_simplecache',
			'elgg_view_disable_simplecache' => 'elgg_disable_simplecache',
			'remove_widget_type' => 'elgg_unregister_widget_type',
			'widget_type_exists' => 'elgg_is_widget_type',
			'get_widget_types' => 'elgg_get_widget_types',
			'display_widget' => 'elgg_view_entity',
			'invalidate_cache_for_entity' => '_elgg_invalidate_cache_for_entity',
			'cache_entity' => '_elgg_cache_entity',
			'retrieve_cached_entity' => '_elgg_retrieve_cached_entity',

			//1.9
			'setup_db_connections' => '_elgg_services()->db->setupConnections',
			'get_db_link' => '_elgg_services()->db->getLink',
			'get_db_error' => 'mysql_error',
			'execute_delayed_query' => '_elgg_services()->db->registerDelayedQuery',
			'elgg_regenerate_simplecache' => 'elgg_invalidate_simplecache',
			'elgg_get_filepath_cache' => 'elgg_get_system_cache',
			'elgg_filepath_cache_reset' => 'elgg_reset_system_cache',
			'elgg_filepath_cache_save' => 'elgg_save_system_cache',
			'elgg_filepath_cache_load' => 'elgg_load_system_cache',
			'elgg_enable_filepath_cache' => 'elgg_enable_system_cache',
			'elgg_disable_filepath_cache' => 'elgg_disable_system_cache',
			'unregister_entity_type' => 'elgg_unregister_entity_type',
			'autop' => 'elgg_autop',
			'xml_to_object' => 'new ElggXMLElement',
			'unregister_notification_handler' => 'elgg_unregister_notification_method',

		);
	}

	/**
	 * Function renames from A to B with parameters manipulation
	 *
	 * @return array
	 */
	public function getBasicFunctionSnippets() {
		return array(
			'elgg_get_entity_owner_where_sql' => '_elgg_get_guid_based_where_sql',
			'elgg_get_entity_container_where_sql' => '_elgg_get_guid_based_where_sql',
			'elgg_get_entity_site_where_sql' => '_elgg_get_guid_based_where_sql',

//			'get_entities_from_access_id' => 'elgg_get_entities_from_access_id', //bad params count
//			'get_entities_from_annotations' => 'elgg_get_entities_from_annotations',//bad params count
//			'list_entities_from_annotations' => 'elgg_list_entities_from_annotations',//bad params count
//			'get_library_files' => 'elgg_get_file_list',//bad params count
//			'get_entities' => 'elgg_get_entities',//bad params count
//			'list_registered_entities' => 'elgg_list_registered_entities',//bad params count
//			'list_entities' => 'elgg_list_entities',//bad params count
//			'get_entities_from_metadata' => 'elgg_get_entities_from_metadata',//bad params count
//			'get_entities_from_metadata_multi' => 'elgg_get_entities_from_metadata',//bad params count
//			'get_entities_from_relationship' => 'elgg_get_entities_from_relationship',//bad params count
//			'make_register_object' => 'add_submenu_item',//bad params count
//			'list_entities_from_access_id' => 'elgg_list_entities_from_access_id',//bad params count
//			'register_action' => 'elgg_register_action',//bad params count
//			'get_entities_from_annotations_calculate_x' => 'elgg_get_entities_from_annotation_calculation',//bad params count
//			'get_entities_from_annotation_count' => 'elgg_get_entities_from_annotation_calculation',//bad params count
//			'list_entities_from_annotation_count' => 'elgg_list_entities_from_annotation_calculation',//bad params count
//			'get_objects_in_group' => 'elgg_get_entities',//bad params count
//			'list_entities_groups' => 'elgg_list_entities',//bad params count
//			'get_entities_from_metadata_groups' => 'elgg_get_entities_from_metadata',//bad params count
//			'get_entities_from_metadata_groups_multi' => 'elgg_get_entities_from_metadata',//bad params count
//			'list_entities_in_area' => 'elgg_get_entities_from_location',//bad params count
//			'list_entities_location' => 'elgg_list_entities_from_location',//bad params count
//			'get_entities_in_area' => 'elgg_get_entities_from_location',//bad params count
//			'list_entities_from_metadata' => 'elgg_list_entities_from_metadata',//bad params count
//			'list_entities_from_metadata_multi' => 'elgg_list_entities_from_metadata',//bad params count
//			'regenerate_plugin_list' => '_elgg_generate_plugin_entities',//bad params count
		);
	}

	public function getBasicFunctionRenamesTest() {
		$functs = $this->getBasicFunctionRenames();
		foreach ($functs as $oldFunct => $newFunct) {
			$oldFunctReflection = new ReflectionFunction($oldFunct);
			$newFunctReflection = new ReflectionFunction($newFunct);
			if ($oldFunctReflection->getNumberOfParameters() != $newFunctReflection->getNumberOfParameters()) {
				var_dump($oldFunct);
			}
		}
	}

	/**
	 * @return array of
	 */
	public function getRegExpPairs() {
		return array(
			'([^_a-zA-Z0-9])register_plugin_hook([^_a-zA-Z0-9])' => '$1elgg_register_plugin_hook_handler$2',
		);
	}

	/**
	 * We'll need
	 * - function renames
	 * - function renames with parameter modifications
	 * - function replacements with complex snippets
	 */



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
	'add_menu' => 'USE elgg_register_menu_item'site'',
	'add_to_register' => 'USE the new menu system. This is only USEd for the site-wide menu.  See add_menu',
	'remove_from_register' => 'USE the new menu system. This is USEd to by remove_menu',
	'get_register' => 'USE the new menu system',
	'events' => 'USE explicit register/trigger event functions',
	eg if you want the function "export_USEr" to be called when the hook "export" for "USEr" entities
	is run, USE:
	register_plugin_hook("export", "USEr", "export_USEr");
	"all" is a valid value for both $hook and $entity_type. "none" is a valid value for $entity_type.
	The export_USEr function would then be defined as:
	function export_USEr($hook, $entity_type, $returnvalue, $params);
	Where $returnvalue is the return value returned by the last function returned by the hook, and
	$params is an array containing a set of parameters (or nothing).',
	'list_entities_from_metadata' => 'USE elgg_list_entities_from_metadata',
	'list_entities_from_metadata_multi' => 'USE elgg_list_entities_from_metadata',
	'add_submenu_item' => 'USE the new menu system',
	'remove_submenu_item' => 'USE the new menu system',
	'get_submenu' => 'USE the new menu system. elgg_view_menu',
	'add_menu' => 'USE elgg_register_menu_item'site'',
	'remove_menu' => 'USE the new menu system',
	'filter_string' => 'Don't USE this.',
	'remove_blacklist' => 'Don't USE this.',
	'page_owner' => 'USE elgg_get_page_owner_guid',
	'page_owner_entity' => 'USE elgg_get_page_owner_entity',
	'add_page_owner_handler' => 'USE the 'page_owner', 'system' plugin hook',
	'set_page_owner' => 'USE elgg_set_page_owner_guid',
	'load_plugin_manifest' => 'USE ElggPlugin->getManifest',
	'check_plugin_compatibility' => 'USE ElggPlugin->canActivate',
	'find_plugin_settings' => 'USE elgg_get_calling_plugin_entity',
	'get_installed_plugins' => 'USE elgg_get_plugins',
	'enable_plugin' => 'USE ElggPlugin->activate',
	'disable_plugin' => 'USE ElggPlugin->deactivate',
	'is_plugin_enabled' => 'USE elgg_is_active_plugin',
	'get_entities_from_private_setting' => 'USE elgg_get_entities_from_private_settings',
	'get_entities_from_private_setting_multi' => 'USE elgg_get_entities_from_private_settings',
	'list_entities_from_relationship' => 'USE elgg_list_entities_from_relationship',
	'get_entities_by_relationship_count' => 'USE elgg_get_entities_from_relationship_count',
	'list_entities_by_relationship_count' => 'USE elgg_list_entities_from_relationship_count',
	'get_entities_from_relationships_and_meta' => 'USE elgg_get_entities_from_relationship',
	'get_river_items' => 'USE elgg_get_river',
	'elgg_view_river_items' => 'USE elgg_list_river',
	'get_activity_stream_data' => 'This is outdated and USEs the systemlog table instead of the river table.
	Don't USE it.',
	'authenticate' => 'USE elgg_authenticate',
	'get_site_members' => 'USE ElggSite::getMembers',
	'list_site_members' => 'USE ElggSite::listMembers',
	'add_site_collection' => 'Don't USE this.',
	'remove_site_collection' => 'Don't USE this.',
	'get_site_collections' => 'Don't USE this.',
	'get_tags' => 'USE elgg_get_tags',
	'display_tagcloud' => 'USE elgg_view_tagcloud',
	'get_USEr_objects' => 'USE elgg_get_entities',
	'count_USEr_objects' => 'USE elgg_get_entities',
	'list_USEr_objects' => 'USE elgg_list_entities',
	'get_USEr_objects_by_metadata' => 'USE elgg_get_entities_from_metadata',
	'set_USEr_validation_status' => 'USE elgg_set_USEr_validation_status',
	'request_USEr_validation' => 'Hook into the register, USEr plugin hook and request validation.',
	'page_draw' => 'USE elgg_view_page',
	'elgg_view_listing' => 'USE elgg_view_image_block',
	'get_entity_icon_url' => 'USE $entity->getIconURL',
	'get_loggedin_USEr' => 'USE elgg_get_logged_in_USEr_entity',
	'get_loggedin_USErid' => 'USE elgg_get_logged_in_USEr_guid',
	'isloggedin' => 'USE elgg_is_logged_in',
	'isadminloggedin' => 'USE elgg_is_admin_logged_in',
	'load_plugins' => 'USE elgg_load_plugins',
	'find_plugin_USErsettings' => 'USE elgg_get_all_plugin_USEr_settings',
	'set_plugin_USErsetting' => 'USE elgg_set_plugin_USEr_setting',
	'clear_plugin_USErsetting' => 'USE elgg_unset_plugin_USEr_setting or ElggPlugin->unsetUSErSetting',
	'get_plugin_USErsetting' => 'USE elgg_get_plugin_USEr_setting',
	'set_plugin_setting' => 'USE elgg_set_plugin_setting',
	'get_plugin_setting' => 'USE elgg_get_plugin_setting',
	'clear_plugin_setting' => 'USE elgg_unset_plugin_setting',
	'clear_all_plugin_settings' => 'USE elgg_unset_all_plugin_settings',
	'get_annotations' => 'USE elgg_get_annotations',
	'list_annotations' => 'USE elgg_list_annotations',
	'count_annotations' => 'USE elgg_get_annotations'count' => true',
	'get_annotations_sum' => 'USE elgg_get_annotations'annotation_calculation' => 'sum'',
	'get_annotations_max' => 'USE elgg_get_annotations'annotation_calculation' => 'max'',
	'get_annotations_min' => 'USE elgg_get_annotations'annotation_calculation' => 'min'',
	'get_annotations_avg' => 'USE elgg_get_annotations'annotation_calculation' => 'min'',
	'get_annotations_calculate_x' => 'USE elgg_get_annotations',
	'list_entities_from_annotation_count_by_metadata' => 'USE elgg_list_entities_from_annotation_calculation',
	'set_view_location' => 'USE elgg_set_view_location',
	'register_entity_url_handler' => 'USE elgg_register_entity_url_handler',
	'find_metadata' => 'USE elgg_get_metadata',
	'get_metadata_byname' => 'USE elgg_get_metadata',
	'get_metadata_for_entity' => 'USE elgg_get_metadata',
	'get_metadata' => 'USE elgg_get_metadata_from_id',
	'clear_metadata' => 'USE elgg_delete_metadata',
	'clear_metadata_by_owner' => 'USE elgg_delete_metadata',
	'delete_metadata' => 'USE elgg_delete_metadata',
	'remove_metadata' => 'USE elgg_delete_metadata',
	'get_annotation' => 'USE elgg_get_annotation_from_id',
	'delete_annotation' => 'USE elgg_delete_annotations',
	'clear_annotations' => 'USE elgg_delete_annotations',
	'clear_annotations_by_owner' => 'USE elgg_delete_annotations',
	'register_page_handler' => 'USE elgg_register_page_handler',
	'unregister_page_handler' => 'USE elgg_unregister_page_handler',
	'register_annotation_url_handler' => 'USE elgg_register_annotation_url_handler',
	'register_extender_url_handler' => 'USE elgg_register_extender_url_handler',
	'register_entity_type' => 'USE elgg_register_entity_type',
	'register_metadata_url_handler' => 'USE elgg_register_metadata_url_handler',
	'register_relationship_url_handler' => 'USE elgg_register_relationship_url_handler',
	'elgg_view_register_simplecache' => 'USE elgg_register_simplecache_view',
	'elgg_view_regenerate_simplecache' => 'USE elgg_regenerate_simplecache',
	'elgg_view_enable_simplecache' => 'USE elgg_enable_simplecache',
	'elgg_view_disable_simplecache' => 'USE elgg_disable_simplecache',
	'save_widget_location' => 'USE ElggWidget::move',
	'get_widgets' => 'USE elgg_get_widgets',
	'add_widget' => 'USE elgg_create_widget',
	'add_widget_type' => 'USE elgg_register_widget_type',
	'remove_widget_type' => 'USE elgg_unregister_widget_type',
	'widget_type_exists' => 'USE elgg_is_widget_type',
	'get_widget_types' => 'USE elgg_get_widget_types',
	'save_widget_info' => 'USE elgg_save_widget_settings',
	'reorder_widgets_from_panel' => 'Don't USE.'USE_widgets' => 'Don't USE.',
	'using_widgets' => 'Don't USE.',
	'display_widget' => 'USE elgg_view_entity',
	'remove_from_river_by_subject' => 'USE elgg_delete_river',
	'remove_from_river_by_object' => 'USE elgg_delete_river',
	'remove_from_river_by_annotation' => 'USE elgg_delete_river',
	'remove_from_river_by_id' => 'USE elgg_delete_river',
	'elgg_register_entity_url_handler' => 'USE the plugin hook in ElggEntity::getURL',
	'elgg_register_relationship_url_handler' => 'USE the plugin hook in ElggRelationship::getURL',
	'get_relationship_url' => 'USE ElggRelationship::getURL',
	'elgg_register_extender_url_handler' => 'USE plugin hook in ElggExtender::getURL',
	'get_extender_url' => 'USE method getURL',
	'get_annotation_url' => 'USE method getURL',
	'elgg_register_metadata_url_handler' => 'USE the plugin hook in ElggExtender::getURL',
	'elgg_register_annotation_url_handler' => 'USE the plugin hook in ElggExtender::getURL',
	'get_group_members' => 'USE ElggGroup::getMembers',
	'add_object_to_group' => 'USE ElggGroup::addObjectToGroup',
	'remove_object_from_group' => 'USE ElggGroup::removeObjectFromGroup',
	'is_group_member' => 'USE USE ElggGroup::isMember',
	'get_USErs_membership' => 'USE ElggUSEr::getGroups'USEr_is_friend' => 'USE ElggUSEr::isFriendsOf',
	'get_USEr_friends' => 'USE ElggUSEr::getFriends',
	'get_USEr_friends_of' => 'USE ElggUSEr::getFriendsOf'USEr_add_friend' => 'USE ElggUSEr::addFriend'USEr_remove_friend' => 'USE ElggUSEr::removeFriend',
	'add_site_USEr' => 'USE ElggSite::addEntity',
	'remove_site_USEr' => 'USE ElggSite::removeEntity',
	'add_site_object' => 'USE ElggSite::addEntity',
	'remove_site_object' => 'USE ElggSite::removeEntity',
	'get_site_objects' => 'USE ElggSite::getEntities',
	'get_object_sites' => 'USE ElggEntity::getSites',
	'get_USEr_sites' => 'USE ElggEntity::getSites',
	'can_edit_extender' => 'USE the appropriate canEdit',
	'get_metastring_id' => 'USE elgg_get_metastring_id',
	'add_metastring' => 'USE elgg_get_metastring_id',
	'get_USEr_friends_objects' => 'USE elgg_get_entities_from_relationship',
	'count_USEr_friends_objects' => 'USE elgg_get_entities_from_relationship',
	'list_USEr_friends_objects' => 'USE elgg_list_entities_from_relationship',
	'get_version' => 'USE elgg_get_version',
	'execute_delayed_query' => 'USE execute_delayed_write_query',
	'elgg_regenerate_simplecache' => 'USE elgg_invalidate_simplecache',
	'unregister_entity_type' => 'USE elgg_unregister_entity_type',
	'get_entity_url' => 'USE ElggEntity::getURL',
	'delete_entity' => 'USE ElggEntity::delete',
	'enable_entity' => 'USE elgg_enable_entity',
	'can_edit_entity_metadata' => 'USE ElggEntity::canEditMetadata',
	'disable_entity' => 'USE ElggEntity::disable instead.',
	'can_edit_entity' => 'USE ElggEntity::canEdit instead',
	'join_group' => 'USE ElggGroup::join instead.',
	'leave_group' => 'USE ElggGroup::leave',
	'autop' => 'USE elgg_autop instead',
	'expose_function' => 'Enable the web services plugin and USE elgg_ws_expose_function',
	'unexpose_function' => 'Enable the web services plugin and USE elgg_ws_unexpose_function',
	'register_service_handler' => 'Enable the web services plugin and USE elgg_ws_register_service_handler',
	'unregister_service_handler' => 'Enable the web services plugin and USE elgg_ws_unregister_service_handler',
	'create_site_entity' => 'USE ElggSite constructor',
	'create_group_entity' => 'USE ElggGroup constructor',
	'create_USEr_entity' => 'USE ElggUSEr constructor',
	'create_object_entity' => 'USE ElggObject constructor',
	'xml_to_object' => 'USE ElggXMLElement',
	'register_notification_object' => 'USE elgg_register_notification_event
	as the subject line in a notification. As of Elgg, it is now set by a callback
	for a plugin hook. See the documentation at the top of the notifications library
	titled "Adding a New Notification Event" for more details.',
	'register_notification_interest' => 'USE elgg_add_subscription',
	'remove_notification_interest' => 'USE elgg_remove_subscription',
	'register_notification_handler' => 'USE elgg_register_notification_method',
	'unregister_notification_handler' => 'USE elgg_unregister_notification_method',

	'add_submenu_item' => 'the new menu system',
	'remove_submenu_item' => 'the new menu system',
	'get_submenu' => 'the new menu system. elgg_view_menu',
	'remove_menu' => 'the new menu system',
	'get_plugin_list' => 'elgg_get_plugin_ids_in_dir', //removed completely

	 */
}