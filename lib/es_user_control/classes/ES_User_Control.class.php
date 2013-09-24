<?php

class ES_User_Control {

	private static $is_instantiated = false;

	public function __construct() {

		if ( true == self::$is_instantiated ) return; // Singleton

		global $es_user_control;

		$es_user_control = $this;

		// Add Custom User Roles
		/** @todo - This should run on plugin activation cuz it saves to DB */
		add_action( 'admin_init', array( $this, '_add_custom_user_roles' ) );

		self::$is_instantiated = true;

		do_action('es_user_control_loaded');

	}

	public function _add_custom_user_roles() {

		$editor_role = get_role('editor');
		$admin_role = get_role('administrator');

		$caps = array(
			'edit_files' => true,
			'edit_theme_options' => true,
			'edit_dashboard' => false,
			'list_users' => false
		);

		$caps = array_merge( $editor_role->capabilities, $caps );

		$role = get_role('client_basic');

		if ( null === $role ) {
			add_role('client_basic', 'Client (Basic)', $caps );
		}
		else {
			foreach( $caps as $k => $v ) {
				$role = get_role('client_basic');
				if ( $v ) {
					$role->add_cap( $k );
				}
				else {
					$role->remove_cap( $k );
				}
			}
		}

		$caps = array(
			'activate_plugins' => false,
			'delete_plugins' => false,
			'edit_dashboard' => false,
			'list_users' => false,
			'promote_users' => false,
			'remove_users' => false,
			'switch_themes' => false,
			'create_product' => false,
			'update_core' => false,
			'update_plugins' => false,
			'update_themes' => false,
			'install_plugins' => false,
			'install_themes' => false,
			'delete_themes' => false,
			'edit_plugins' => true,
			'edit_themes' => false,
			'edit_users' => false,
			'create_users' => false,
			'delete_users' => false
		);

		$caps = array_merge( $admin_role->capabilities, $caps );

		$role = get_role('client_advanced');

		if ( null === $role ) {
			add_role('client_advanced', 'Client (Advanced)', $caps );
		}
		else {
			foreach( $caps as $k => $v ) {
				$role = get_role('client_advanced');
				if ( $v ) {
					$role->add_cap( $k );
				}
				else {
					$role->remove_cap( $k );
				}
			}
		}

	}

}