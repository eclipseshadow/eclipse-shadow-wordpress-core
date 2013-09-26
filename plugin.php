<?php

/*
Plugin Name: Eclipse Shadow WP Core
Plugin URI:
Description: Eclipse Shadow's In-House WP Core Functionality & Modifications Plugin
Version: 0.2.6
Author: Zach Lanich
Author URI: https://www.ZachLanich.com
License: Undecided
*/

/**
 * Class Eclipse_Shadow_WP_Core
 *
 * Eclipse Shadow's In-House WP Core Functionality & Modifications Plugin
 *
 * @todo Update all scripts & stylesheets in all extensions to use a single VERSION constant for cache purging
 */
class Eclipse_Shadow_WP_Core {

	public function __construct() {

		if ( !defined('SCRIPT_DEBUG') ) {
			define('SCRIPT_DEBUG', false );
		}

		// Load after Updater

		add_action( 'activated_plugin', array( $this, '_es_wp_core_load_after_updater' ));

		// Check for Updates

		add_action('init', array($this, '_check_for_updates'));

		// Load Scripts & Styles

		add_action( 'admin_enqueue_scripts', array( $this, '_load_scripts' ), 1000 );
		add_action( 'admin_enqueue_scripts', array( $this, '_load_styles' ), 1000 );
		add_action( 'wp_enqueue_scripts', array( $this, '_load_scripts' ), 10000 );
		add_action( 'wp_enqueue_scripts', array( $this, '_load_styles' ), 10000 );
		add_action( 'login_enqueue_scripts', array( $this, '_load_login_styles' ), 10000 );

		//
		// NEW
		//

		// Utilities - Code Editor, Rich Text Editor, Media Manager Dialog, etc

		require_once 'lib/es_utilities/classes/ES_Utilities.class.php';

		new ES_Utilities();

		// User Control

		require_once 'lib/es_user_control/classes/ES_User_Control.class.php';

		new ES_User_Control();

		// Interface Control & Cleanup

		require_once 'lib/es_interface_control/classes/ES_Interface_Control.class.php';

		new ES_Interface_Control();

		// Widget & Module Constants

		define('ES_WIDGETS_DIR', realpath( dirname(__FILE__). '/lib/es_wp_widgets/widgets/') );
		define('ES_WIDGETS_DIR_URL', plugins_url( '/lib/es_wp_widgets/widgets', __FILE__ ) );

		// User Assistance (Help Screens, etc)

		require_once 'lib/es_user_assistance/classes/ES_User_Assistance.class.php';

		new ES_User_Assistance();

		// Media Management

		require_once 'lib/es_media_management/classes/ES_Media_Management.class.php';

		new ES_Media_Management();

		// Load Carrington Build

		require_once 'lib/es_carrington_build/classes/ES_Carrington_Build.class.php';

		new ES_Carrington_Build( ES_WIDGETS_DIR, ES_WIDGETS_DIR_URL );

		// Load ES Widgets

		require_once 'lib/es_wp_widgets/classes/ES_WP_Widget_Admin.class.php';

		// Disable dynamic js & css for ES Widgets when Carrington Build is enabled (duplicate js & css)
		define('ES_WP_WIDGETS_DISPLAY_DYNAMIC_JS', false);
		define('ES_WP_WIDGETS_DISPLAY_DYNAMIC_CSS', false);

		new ES_WP_Widget_Admin( ES_WIDGETS_DIR );

	}

	public function _check_for_updates() {

		if ( is_admin() && class_exists('WP_GitHub_Updater') ) {

			$config = array(
				// These top 2 might not work on older PHP installs - I've seen basename() behave strangely
				'slug' => basename(dirname(__FILE__)) .'/'. basename(__FILE__),
				'proper_folder_name' => basename(dirname(__FILE__)),
				'api_url' => 'https://api.github.com/repos/eclipseshadow/eclipse-shadow-wordpress-core',
				'raw_url' => 'https://raw.github.com/eclipseshadow/eclipse-shadow-wordpress-core/master',
				'github_url' => 'https://github.com/eclipseshadow/eclipse-shadow-wordpress-core',
				'zip_url' => 'https://github.com/eclipseshadow/eclipse-shadow-wordpress-core/archive/master.zip',
				'sslverify' => true,
				'requires' => '3.0',
				'tested' => '3.6',
				'readme' => 'README.md',
				'access_token' => '',
			);

			new WP_GitHub_Updater( $config );

		}

	}

	public function _load_scripts() {

		if ( is_admin() ) {

			// WP Admin

			$path = SCRIPT_DEBUG ? '/lib/js/src/json.js' : '/lib/js/build/json.min.js';
			wp_enqueue_script('es-jquery-ui', plugins_url( $path, __FILE__ ), array(), 1.0 );
		}
		else {
			// Front End

			wp_enqueue_script('es-jquery-ui', plugins_url( '/lib/js/build/jquery-ui-1.10.3.custom/js/jquery-ui-1.10.3.custom.min.js', __FILE__ ), array('jquery'), 1.0 );
		}

	}

	public function _load_styles() {

		if ( is_admin() ) {

			// WP Admin

			wp_enqueue_style( 'es_wp_jquery_ui', plugins_url( '/lib/css/build/jquery-ui/cupertino/jquery-ui-1.10.3.custom.min.css', __FILE__ ), array(), 1.1 );
			wp_enqueue_style( 'es_wp_core_admin', plugins_url( '/lib/css/build/es_wp_core_admin.css', __FILE__ ), array(), 1.1 );
		}
		else {
			// Front End

			wp_enqueue_style( 'es_wp_jquery_ui', plugins_url( '/lib/css/build/jquery-ui/cupertino/jquery-ui-1.10.3.custom.min.css', __FILE__ ), array(), 1.1 );
			wp_enqueue_style( 'es_wp_core_front_end', plugins_url( '/lib/css/build/es_wp_core_front_end.css', __FILE__ ), array(), 1.1 );
		}

	}

	public function _load_login_styles() {

		wp_enqueue_style( 'es_wp_login', plugins_url( '/lib/css/build/es_wp_login.css', __FILE__ ), array(), 1.0 );

	}

	public function _es_wp_core_load_after_updater() {

		$path = basename(dirname(__FILE__)) .'/'. basename(__FILE__);

		if ( $plugins = get_option( 'active_plugins' ) ) {
			if ( $key = array_search( $path, $plugins ) ) {
				if ( $es_updater_key = array_search( 'es_plugin_updater', $plugins ) ) {
					array_splice( $plugins, $es_updater_key++, 0, $path );
				}
				else {
					array_splice( $plugins, $key, 1 );
					array_unshift( $plugins, $path );
				}

				update_option( 'active_plugins', $plugins );
			}
		}
	}

}

new Eclipse_Shadow_WP_Core();