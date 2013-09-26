<?php

class ES_User_Assistance {

	private static $is_instantiated = false;

	private $help_tabs = array(),
			$removed_help_tabs = array(),
			$remove_all_tabs = false,
			$help_sidebar_content = '',
			$wp_screen_obj;

	public function __construct() {

		if ( true == self::$is_instantiated ) return; // Singleton

		global $es_user_assistance;

		$es_user_assistance = $this;

		define('ES_USER_ASSISTANCE_DIR', plugins_url( '/', dirname(__FILE__) ) );

		add_action('set_current_user', array( $this, '_init' ));
		add_action('admin_enqueue_scripts', array( $this, '_enqueue_scripts' ));
		add_action('admin_enqueue_scripts', array( $this, '_enqueue_styles' ));

		self::$is_instantiated = true;

		do_action('es_user_assistance_loaded');

	}

	public function _init() {

		global $pagenow;

		require_once dirname(__FILE__) .'/../classes/ES_Help_Tab.class.php';
		require_once dirname(__FILE__) .'/../user_assistance.php';

		add_action('current_screen', array( $this, '_get_current_screen' ), 0);
		add_action('admin_head', array( $this, '_remove_help_tabs' ), 1);
		add_action('admin_head', array( $this, '_add_help_tabs' ), 2);
		add_action('admin_head', array( $this, '_set_help_sidebar' ), 3);

	}

	//
	// API
	//

	public function add_help_tab( $tab_name = '', $tab_id = '', $tab_content = '', $priority = 0, $pages = array(), $post_types = array() ) {

		$tab = new ES_Help_Tab( $tab_name, $tab_id, $tab_content, $priority, $pages, $post_types );

		array_push( $this->help_tabs, $tab );

		return $tab;

	}

	public function remove_help_tab( $tab_id = '', $pages = array(), $post_types = array() ) {

		if ( '_all' == $tab_id ) {
			$this->remove_all_tabs = true;
			$this->help_tabs = array();
		}
		else {
			global $pagenow;

			$post_type = $this->get_post_type();

			array_push( $this->removed_help_tabs, array('tab_id' => $tab_id, 'pages' => $pages, 'post_types' => $post_types ));

			foreach( $this->help_tabs as $i => $tab ) {

				if ( !empty($tab->pages) && !in_array( $pagenow, $tab->pages ) ) continue;

				if ( !empty($tab->post_types) && !in_array( $post_type, $tab->post_types ) ) continue;

				if ( $tab_id == $tab->id ) {
					unset( $this->help_tabs[ $i ] );
				}
			}
		}

	}

	public function set_help_sidebar( $content = '' ) {

		if ( is_array( $content ) && method_exists( $content[0], $content[1] ) ) {
			$this->help_sidebar_content = call_user_func( $content, $this->wp_screen_obj );
		}
		else if ( function_exists( $content ) ) {
			$this->help_sidebar_content = call_user_func( $content, $this->wp_screen_obj );
		}
		else {
			$this->help_sidebar_content = $content;
		}

	}

	//
	// Internal Methods
	//

	public function _enqueue_scripts() {

		wp_enqueue_script('jquery');
		wp_enqueue_script('es-jquery-ui', plugins_url('/js/build/jquery-ui-1.10.3.custom/js/jquery-ui-1.10.3.custom.min.js', dirname(__FILE__) ), array('jquery'), 1.0 );
		$path = SCRIPT_DEBUG ? '/js/src/es_user_assistance.js' : '/js/build/es_user_assistance.min.js';
		wp_enqueue_script('es-user-assistance-js', plugins_url( $path, dirname(__FILE__)), array('es-jquery-ui'), 1.0);

	}

	public function _enqueue_styles() {

		wp_enqueue_style('es-user-assitance-css', plugins_url('/css/build/es-user-assistance.css', dirname(__FILE__)), array(), 1.0);

	}

	public function _get_current_screen() {

		$this->wp_screen_obj = get_current_screen();

	}

	public function _sort_help_tabs ($a, $b ) {

		return $a->priority > $b->priority;

	}

	public function _add_help_tabs() {

		global $pagenow;

		$post_type = $this->get_post_type();

		usort( $this->help_tabs, array( $this, '_sort_help_tabs' ));

		foreach( $this->help_tabs as $tab ) {

			if ( !empty($tab->pages) && !in_array( $pagenow, $tab->pages ) ) continue;

			if ( !empty($tab->post_types) && !in_array( $post_type, $tab->post_types ) ) continue;

			$this->wp_screen_obj->add_help_tab( array(
				'id' => $tab->id,
				'title' => $tab->name,
				'content' => $tab->content,
				'callback' => $tab->callback
			));
		}

	}

	public function _remove_help_tabs() {

		global $pagenow;

		$post_type = $this->get_post_type();

		if ( true == $this->remove_all_tabs ) {
			$this->wp_screen_obj->remove_help_tabs();
		}
		else {
			foreach( $this->removed_help_tabs as $tab ) {

				if ( !empty($tab['pages']) && !in_array( $pagenow, $tab['pages'] ) ) continue;

				if ( !empty($tab['post_types']) && !in_array( $post_type, $tab['post_types'] ) ) continue;

				$this->wp_screen_obj->remove_help_tab( $tab['tab_id'] );
			}
		}

	}

	public function _set_help_sidebar() {

		$this->wp_screen_obj->set_help_sidebar( $this->help_sidebar_content );

	}

	//
	// Helper Methods
	//

	public static function get_post_type() {

		global $pagenow;

		if (in_array($pagenow, array('post-new.php'))) {
			if (!empty($_GET['post_type'])) {
				// custom post type or wordpress 3.0 pages
				$type = esc_attr($_GET['post_type']);
			}
			else {
				$type = 'post';
			}
		}
		elseif (in_array( $pagenow, array('page-new.php'))) {
			// pre 3.0 new pages
			$type = 'page';
		}
		else {
			// post/page-edit
			if (isset($_GET['post']))
				$post_id = (int) $_GET['post'];
			elseif (isset($_POST['post_ID'])) {
				$post_id = (int) $_POST['post_ID'];
			}
			else {
				$post_id = 0;
			}

			$type = false;
			if ($post_id > 0) {
				$post = get_post($post_id, OBJECT, 'edit');

				if ($post && !empty($post->post_type) && !in_array($post->post_type, array('attachment', 'revision'))) {
					$type = $post->post_type;
				}
			}
		}

		return $type;

	}

	public static function get_post_type_label( $post_type = '', $echo = false ) {

		static $post_types, $labels = '';

		// Get all post type *names*, that are shown in the admin menu
		empty( $post_types ) AND $post_types = get_post_types(
			array(
				'show_in_menu' => true
				//,'_builtin'     => false
			)
			,'objects'
		);

		empty( $labels ) AND $labels = wp_list_pluck( $post_types, 'labels' );
		$names = wp_list_pluck( $labels, 'singular_name' );
		$name = $names[ $post_type ];

		// return or print?
		return $echo ? print $name : $name;
	}

	public static function get_user_role( $user_id = NULL ) {

		global $wp_roles, $wpdb;

		if ( ! $user_id)
			$user_id = get_current_user_id();

		$user = get_userdata( $user_id );

		$capabilities = $user->{$wpdb->prefix . 'capabilities'};

		if ( !isset( $wp_roles ) )
			$wp_roles = new WP_Roles();

		foreach ( $wp_roles->role_names as $role => $name ) :

			if ( array_key_exists( $role, $capabilities ) )
				return $role;

		endforeach;

		return null;

	}

}