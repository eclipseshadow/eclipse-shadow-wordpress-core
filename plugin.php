<?php

/*
Plugin Name: Eclipse Shadow WP Core
Plugin URI:
Description: Eclipse Shadow's In-House WP Core Functionality & Modifications Plugin
Version: 0.2.0
Author: Zach Lanich
Author URI: https://www.ZachLanich.com
License: Undecided
*/

/**
 * Class Eclipse_Shadow_WP_Core
 *
 * Eclipse Shadow's In-House WP Core Functionality & Modifications Plugin
 *
 * @todo WYSIWYG Word Count doesn't work when using CKEditor
 * @todo Write custom CKEditor add-on to replace the "Visual/HTML" tabs & "Source" button with an ACE HTML editor! (Note: CKEditor has an Ace Code Block add-on)
 * @todo Give Wordpress Admin a visual overhaul to match Eclipse Shadow's branding
 */
class Eclipse_Shadow_WP_Core {

	public function __construct() {

		$this->check_for_updates();

		// Load Scripts & Styles

		add_action( 'admin_enqueue_scripts', array( $this, '_load_scripts' ), 1000 );
		add_action( 'admin_enqueue_scripts', array( $this, '_load_styles' ), 1000 );
		add_action( 'wp_enqueue_scripts', array( $this, '_load_scripts' ), 10000 );
		add_action( 'wp_enqueue_scripts', array( $this, '_load_styles' ), 10000 );
		add_action( 'login_enqueue_scripts', array( $this, '_load_login_styles' ), 10000 );

		add_action('wp_head', array( $this, '_remove_html_top_margin' ), 10000 );

	}

	private function check_for_updates() {

		require_once 'lib/updater.php';

		if ( !defined('WP_GITHUB_FORCE_UPDATE') ) {
			define( 'WP_GITHUB_FORCE_UPDATE', true );
		}

		if ( is_admin() ) { // note the use of is_admin() to double check that this is happening in the admin

			$config = array(
				'slug' => plugin_basename( __FILE__ ),
				'proper_folder_name' => dirname( plugin_basename( __FILE__ ) ),
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

		//wp_enqueue_script('jquery');
		//wp_enqueue_script( 'pp_css_admin', plugins_url('/lib/js/pp_css_admin.js', __FILE__), array('es_ace_code_editor', 'jquery'), 1.0 );

	}

	public function _load_styles() {

		if ( is_admin() ) {
			// WP Admin

			wp_enqueue_style( 'es_wp_core_admin', WP_PLUGIN_URL .'/'. basename( __DIR__ ) .'/lib/css/es_wp_core_admin.css', array(), 1.0 );
			wp_enqueue_style( 'es_wp_admin_toolbar', WP_PLUGIN_URL .'/'. basename( __DIR__ ) .'/lib/css/es_wp_admin_toolbar.css', array(), 1.0 );
		}
		else {
			// Front End

			wp_enqueue_style( 'es_wp_admin_toolbar', WP_PLUGIN_URL .'/'. basename( __DIR__ ) .'/lib/css/es_wp_admin_toolbar.css', array(), 1.0 );
		}

	}

	public function _load_login_styles() {

		wp_enqueue_style( 'es_wp_login', WP_PLUGIN_URL .'/'. basename( __DIR__ ) .'/lib/css/es_wp_login.css', array(), 1.0 );

	}

	public function _remove_html_top_margin() {

		if ( is_user_logged_in() ) {
			?>
			<style type="text/css">
				html { margin-top: 0 !important; }
			</style>
		<?php }

	}

}

add_action('init', create_function('', '$eclipse_shadow_wp_core = new Eclipse_Shadow_WP_Core();'));