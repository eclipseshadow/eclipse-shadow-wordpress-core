<?php

class ES_Interface_Control {

	private static $is_instantiated = false;

	private $removed_menu_items = array(),
			$removed_submenu_items = array(),
			$replaced_interface_texts = array(),
			$css_hidden_elements = array(),
			$applied_css_rules = array(),
			$hidden_screen_options = array(),
			$removed_dashboard_widgets = array(),
			$removed_native_widgets = array(),
			$removed_admin_bar_links = array(),
			$admin_footer_text = '',
			$js = '',
			$current_user_role = null;

	/*
	 private $default_hidden_screen_options = array(
		'genesis_inpost_seo_box',
		'pp_js_editor_box',
		'pp_css_editor_box',
		'postcustom',
		'trackbacksdiv',
		'commentstatusdiv',
		'commentsdiv',
		'slugdiv',
		'authordiv',
		'revisionsdiv'
	);

	private $default_hidden_screen_options_role_basic = array(
		'pageparentdiv',
		'postexcerpt'
	);
	*/

	public function __construct() {

		if ( true == self::$is_instantiated ) return; // Singleton

		global $es_user_control;

		$es_user_control = $this;

		add_action('set_current_user', array( $this, '_init' ));

		self::$is_instantiated = true;

		do_action('es_interface_control_loaded');

	}

	public function _init() {

		require_once dirname(__FILE__) .'/../interface_control.php';

		$this->current_user_role = ES_Interface_Control::get_user_role();

		if ( is_admin() ) {
			add_action('init', array( $this, '_handle_requests' ));
			add_action('wp_dashboard_setup', array( $this, '_remove_dashboard_widgets' ), 1000000000000);

			//add_filter('gettext', array( $this, '_replace_interface_texts'), 0 );
			//add_filter('ngettext', array( $this, '_replace_interface_texts'), 0 );
		}

		add_action('admin_enqueue_scripts', array( $this, '_enqueue_scripts' ));
		add_action('admin_enqueue_scripts', array( $this, '_enqueue_styles' ));
		add_action('admin_head', array( $this, '_hide_elements_by_css' ), 1000000000000);
		add_action('admin_head', array( $this, '_apply_css_rules'), 1000000000000);
		add_action('admin_init', array( $this, '_remove_menu_items' ), 1000000000000);
		add_action('admin_init', array( $this, '_remove_submenu_items' ), 1000000000000);
		add_action('register_sidebar', array( $this, '_remove_native_widgets' ), 1000000000000);
		add_action('wp_before_admin_bar_render', array( $this, '_remove_admin_bar_links'));
		add_filter('admin_footer_text', array( $this, '_replace_admin_footer_text' ), 1000000000000);

	}

	//
	// API
	//

	public function remove_menu_item( $ignore_if_admin = false, $menu_slug ) {

		array_push( $this->removed_menu_items, array('ignore_if_admin' => $ignore_if_admin, 'menu_slug' => $menu_slug) );

	}

	public function remove_submenu_item( $ignore_if_admin = false, $menu_slug, $submenu_slug ) {

		array_push( $this->removed_submenu_items, array('ignore_if_admin' => $ignore_if_admin, 'menu_slug' => $menu_slug, 'submenu_slug' => $submenu_slug) );

	}

	// JS Disable Link Click
	/** @todo - Finish disable_menu_item() */
	public function disable_menu_item( $ignore_if_admin = false, $menu_slug ) {

		array_push( $this->disabled_menu_items, array('ignore_if_admin' => $ignore_if_admin, 'menu_slug' => $menu_slug) );

	}

	// JS Disable Link Click
	/** @todo - Finish disable_submenu_item() */
	public function disable_submenu_item( $ignore_if_admin = false, $menu_slug, $submenu_slug ) {

		array_push( $this->disabled_submenu_items, array('ignore_if_admin' => $ignore_if_admin, 'menu_slug' => $menu_slug, 'submenu_slug' => $submenu_slug) );

	}

	public function remove_admin_bar_link( $ignore_if_admin = false, $link_name ) {

		array_push( $this->removed_admin_bar_links, array('ignore_if_admin' => $ignore_if_admin, 'link_name' => $link_name) );

	}

	public function replace_interface_text( $ignore_if_admin = false, $pattern, $replace, $selectors = array() ) {

		array_push( $this->replaced_interface_texts, array(
			'ignore_if_admin' => $ignore_if_admin,
			'pattern' => $pattern,
			'replace' => $replace,
			'selectors' => $selectors
		));

	}

	public function hide_element_by_css( $ignore_if_admin = false, $selectors = array('.es_interface_control_hidden_element') ) {

		array_push( $this->css_hidden_elements, array('ignore_if_admin' => $ignore_if_admin, 'selectors' => $selectors) );

	}

	public function apply_css_rules( $ignore_if_admin = false, $selectors = array(), $styles = '' ) {

		array_push( $this->applied_css_rules, array('ignore_if_admin' => $ignore_if_admin = false, 'selectors' => $selectors, 'styles' => $styles) );

	}

	public function hide_screen_option( $ignore_if_admin = false, $metabox_id, $post_type ) {

		if ( $ignore_if_admin && $this->is_admin() ) return;



	}

	public function remove_dashboard_widget( $ignore_if_admin = false, $widget_name ) {


		array_push( $this->removed_dashboard_widgets, array('ignore_if_admin' => $ignore_if_admin, 'widget_name' => $widget_name) );

	}

	public function remove_native_widget( $ignore_if_admin = false, $widget_classname ) {

		array_push( $this->removed_native_widgets, array('ignore_if_admin' => $ignore_if_admin, 'widget_classname' => $widget_classname) );

	}

	public function replace_admin_footer_text( $ignore_if_admin = false, $text = '' ) {

		if ( $ignore_if_admin && $this->is_admin() ) return;

		$this->admin_footer_text = $text;

	}

	/** @todo - Finish remove_meta_box() */
	public function remove_meta_box() {

		//...

	}

	//
	// Internal Methods
	//

	public function _enqueue_scripts() {

		wp_enqueue_scripts('jquery');
		wp_enqueue_script('es-interface-control-replace-text', plugins_url('/js/build/jquery.ba-replacetext.min.js', dirname(__FILE__)), array('jquery'), 1.0 );
		wp_enqueue_script('es-interface-control-modernizr', plugins_url('/js/build/modernizr.custom.33001.js', dirname(__FILE__)), array('jquery'), 1.0 );
		$path = SCRIPT_DEBUG ? '/js/src/es-interface-control.js' : '/js/build/es-interface-control.min.js';
		wp_enqueue_script('es-interface-control-js', plugins_url( $path, dirname(__FILE__)), array('es-interface-control-replace-text'), 1.0 );
		wp_enqueue_script('es-interface-control-dynamic-js', admin_url('?es_interface_control_action=es_interface_control_js'), array('es-interface-control-js'), 1.0 );

	}

	public function _enqueue_styles() {

		wp_enqueue_style('es-interface-control-css', plugins_url('/css/build/es-interface-control.css', dirname(__FILE__)), array(), 1.0);

	}

	public function _remove_menu_items() {

		foreach( $this->removed_menu_items as $item ) {
			if ( $item['ignore_if_admin'] ) continue;

			remove_menu_page( $item['menu_slug'] );
		}

	}

	public function _remove_submenu_items() {

		foreach( $this->removed_submenu_items as $item ) {
			if ( $item['ignore_if_admin'] ) continue;

			remove_submenu_page( $item['menu_slug'], $item['submenu_slug'] );
		}

	}

	public function _replace_interface_texts( $translated ) {

		foreach( $this->replaced_interface_texts as $item ) {
			if ( $item['ignore_if_admin'] ) continue;

			$translated = preg_replace( $item['pattern'], $item['replace'], $translated );
			$translated = preg_replace( $item['pattern'], $item['replace'], $translated );
			return $translated;
		}

	}

	public function _js_replace_interface_texts() {

		// See: http://www.benalman.com/projects/jquery-replacetext-plugin/

		$js = "\n/* Eclipse Shadow - Replace Interface Text (fallback for filtering WP's __() using get_text filter) */";

		$js .="\njQuery(document).ready(function() {\n";
		$js .="\tvar startNodeSelector = 'body';\n";
		$js .="\tvar allNodesSelector = ':not(textarea):not(pre):not(code)';\n\n";

			foreach( $this->replaced_interface_texts as $item ) {
				if ( $item['ignore_if_admin'] ) continue;

				if ( !empty( $item['selectors'] ) ) {
					$js .= "\tstartNodeSelector = '". implode(", ", $item['selectors'] ) ."'\n";
				}

				$js .= "\tjQuery( startNodeSelector ).find( allNodesSelector )";

				$js .= "\n\t.replaceText(". $item['pattern'] .", '". $item['replace'] ."');\n\n";
			}

		$js .= "\n;});\n";

		$this->js .= $js . "\n";

	}

	public function _remove_admin_bar_links() {

		global $wp_admin_bar;

		foreach( $this->removed_admin_bar_links as $link ) {
			if ( $link['ignore_if_admin'] ) continue;

			$wp_admin_bar->remove_menu( $link['link_name'] );
		}

		/*
		 * wp-logo - WordPress logo
		 * about - about WordPress link
		 * wporg - WordPress.org link
		 * documentation - WordPress documentation link
		 * support-forums - support forums link
		 * feedback - feedback link
		 * site-name - site name menu
		 * view-site - view site link
		 * updates - updates link
		 * comments - comments link
		 * new-content - content link
		 * w3tc - If you use w3 total cache remove the performance link
		 * my-account - user details tab
		 *
		 */

	}

	public function _hide_elements_by_css() {

		$css = "<style type=\"text/css\">";
		$css .= "/* Eclipse Shadow - Admin Hidden Elements */\n\r";

		$selectors = array();

		foreach( $this->css_hidden_elements as $el ) {
			if ( $el['ignore_if_admin'] ) continue;

			foreach( $el['selectors'] as $sel ) {
				array_push( $selectors, $sel );
			}
		}

		$css .= implode( ",\n", $selectors );
		$css .= " {\n";
		$css .= "\tdisplay: none !important;";
		$css .= "\n}\n";
		$css .= "</style>";

		echo $css;

	}

	public function _apply_css_rules() {

		$css = "<style type=\"text/css\">";
		$css .= "/* Eclipse Shadow - Admin Misc Applied CSS */\n\r";

		$selectors = array();

		foreach( $this->applied_css_rules as $el ) {
			if ( $el['ignore_if_admin'] ) continue;

			foreach( $el['selectors'] as $sel ) {
				array_push( $selectors, $sel );
			}
		}

		$css .= implode( ",\n", $selectors );
		$css .= " {\n";
		$css .= "\t". $el['styles'];
		$css .= "\n}\n";
		$css .= "</style>";

		echo $css;

	}

	public function _hide_screen_options() {

		/*
		// So this can be used without hooking into user_register
		if ( ! $user_id)
			$user_id = get_current_user_id();

		$user_role = $this->get_user_role( $user_id );

		switch ( $user_role ) {
			case 'client_basic':
				$default_hiddens = array_merge( $this->default_hidden_screen_options, $this->default_hidden_screen_options_role_basic );
				break;
			default:
				$default_hiddens = $this->default_hidden_screen_options;
		}

		// Set the default hiddens if it has not been set yet
		if ( ! get_user_meta( $user_id, 'metaboxhidden_post', true) ) {
			update_user_meta( $user_id, 'metaboxhidden_post', $default_hiddens );
		}
		*/

	}

	public function _remove_dashboard_widgets() {

		global $wp_meta_boxes;
		$sidebars = array('side', 'normal');
		$types = array('core');

		foreach( $this->removed_dashboard_widgets as $widget ) {
			if ( $widget['ignore_if_admin'] ) continue;

			foreach( $sidebars as $sidebar ) {
				foreach( $types as $type ) {
					unset( $wp_meta_boxes['dashboard'][ $sidebar ][ $type ][ $widget['widget_name'] ] );
				}
			}
		}

	}

	public function _remove_native_widgets() {

		foreach( $this->removed_native_widgets as $widget ) {
			if ( $widget['ignore_if_admin'] ) continue;

			unregister_widget( $widget['widget_classname'] );
		}

	}

	public function _replace_admin_footer_text( $default_footer_text ) {

		return $this->admin_footer_text;

	}

	public function _render_js() {

		$this->_js_replace_interface_texts();

		header('Content-type: text/javascript');
		$js = "\n\n;/* Eclipse Shadow - Interface Control Javascript */\n\n";

		// safety wrap the included JS so we can safely use $()
		$js .= "(function($) {\n";

		$js .= $this->js;

		$js .= "\n})(jQuery);\n\n";

		// echo and leave
		echo $js;
		exit;

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

	public function is_admin() {

		return 'administrator' == $this->current_role;

	}

	public function _handle_requests() {

		if (isset($_GET['es_interface_control_action'])) {
			switch ($_GET['es_interface_control_action']) {
				case 'es_interface_control_js':
					$this->_render_js();
					break;
			}
		}

	}

}

/**
 * Further Ideas
 */

/**
 * let's tailor TinyMCE a bit based on what they plan to do
 */

//add_filter("mce_external_plugins", "add_nonbreaking_tinymce_plugin"); // let's add a new tinymce plugin

function add_nonbreaking_tinymce_plugin($plugins) {
	$plugins['nonbreaking'] = get_stylesheet_directory_uri() . '/tinymce-plugins/nonbreaking.js'; //this was pulled out of original tinymce plugins
	return $plugins;
}

//add_filter('mce_buttons_2', 'custom_mcetable_buttons'); //let's remove some buttons from the second row, and add this one

function custom_mcetable_buttons($buttons) {
	// var_dump($buttons); // use this to get the names or keys of all the tinymce buttons... or just count

	unset( $buttons[2] ); // full justify
	unset( $buttons[9] ); // embed media

	array_splice( $buttons, 9, 0, "nonbreaking" );	// add new nonbreaking button after the special characters buttons

	return $buttons;
}

/**
 * customizing editor styles - super easy in WordPress 3.0!
 */

//add_action( 'after_setup_theme', 'custom_admin_after_setup' );

function custom_admin_after_setup() {
	add_editor_style(); // that's it! by default it looks for 'editor-style.css', but you can pass can alernate file name if desired
}

/**
 * adding the post ID to the posts list
 */

//add_filter( 'manage_posts_columns', 'custom_post_id_column', 10, 2 );

function custom_post_id_column( $post_columns, $post_type ) {
	if ( $post_type == 'post' ) {
		$beginning = array_slice( $post_columns, 0, 1 );
		$beginning['postid'] = __('ID');
		$ending = array_slice( $post_columns, 1 );
		$post_columns = array_merge( $beginning, $ending );
	}
	return $post_columns;
}

//add_action( 'manage_posts_custom_column', 'custom_post_column_id', 10, 2 );

function custom_post_column_id( $column_name, $postid ) {
	if ( $column_name == "postid" )
		echo $postid;
}