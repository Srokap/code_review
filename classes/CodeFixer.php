<?php
class CodeFixer {

	/**
	 * Just basic function renames from A to B
	 *
	 * @param string $maxVersion Maximum Elgg version to support
	 * @return array
	 */
	public function getBasicFunctionRenames($maxVersion = '') {
		$data = array(
			'1.7' => array(
				'elgg_validate_action_url' => 'elgg_add_action_tokens_to_url',
				'menu_item' => 'make_register_object',
				'extend_view' => 'elgg_extend_view',
				'get_views' => 'elgg_get_views',
			),
			'1.8' => array(
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
			),
			'1.9' => array(
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
			),
			'1.10' => array(
				'file_get_general_file_type' => 'elgg_get_file_simple_type',
				'file_get_simple_type' => 'elgg_get_file_simple_type',
			),
		);

		$result = array();
		foreach ($data as $version => $rows) {
			if (!$maxVersion || version_compare($version, $maxVersion, '<=')) {
				$result = array_merge($result, $rows);
			}
		}
		return $result;
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
}