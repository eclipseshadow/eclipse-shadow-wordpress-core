<?php

class ES_WP_Widget_Admin {

	private static $is_instantiated = false;

	public  $registered_widgets = array(),
			$widgets_dir,
			$registered_widget_dirs = array();

	//
	// API
	//

	public static function register_widget( $widget_classname, $widget_dirname ) {

		global $es_wp_widget_admin;

		$es_wp_widget_admin->_register_widget( $widget_classname, $widget_dirname );

	}

	public function register_widget_directory( $dir = '' ) {

		array_push( $this->registered_widget_dirs, $dir );

	}

	//
	// Internal Methods
	//

	public function __construct( $widgets_dir ) {

		if ( true == self::$is_instantiated ) return; // Singleton

		global $es_wp_widget_admin;

		$es_wp_widget_admin = $this;

		$this->widgets_dir = $widgets_dir;

		// Request Handler

		add_action('init', array( $this, '_handle_requests' ));

		// Enqueue styles

		add_action('wp_enqueue_scripts', array( $this, '_enqueue_styles' ));

		// Enqueue dynamic widget script & dynamic widget stylesheet

		add_action('admin_enqueue_scripts', array( $this, '_enqueue_scripts' ));
		add_action('wp_enqueue_scripts', array( $this, '_enqueue_scripts' ));

		$this->_register_widgets();

		self::$is_instantiated = true;

		do_action('es_widget_admin_loaded');

	}

	public function _enqueue_scripts() {

		global $pagenow;

		$is_widgets_admin = false;
		$is_carrington_editor = false;
		if (in_array($pagenow, array('widgets.php'))) {
			$is_widgets_admin = true;
		}
		else if ( in_array($pagenow, array('post.php', 'post-new.php')) ) {
			$is_carrington_editor = true;
		}

		if ( is_admin() && true == $is_widgets_admin ) {
			wp_enqueue_script('es-jquery-ui', plugins_url( '/js/build/jquery-ui-1.10.3.custom/js/jquery-ui-1.10.3.custom.min.js', dirname(__FILE__) ), array('jquery'), 1.0 );
			$path = SCRIPT_DEBUG ? '/js/src/es_widgets_helpers.js' : '/js/build/es_widgets_helpers.min.js';
			wp_enqueue_script('es-widget-helpers', plugins_url(  $path, dirname(__FILE__) ), array('jquery'), 1.0 );
			$path = SCRIPT_DEBUG ? '/js/src/es_widgets_admin.js' : '/js/build/es_widgets_admin.min.js';
			wp_enqueue_script('es-widget-admin-js', plugins_url( $path, dirname(__FILE__) ), array('es-jquery-ui', 'es-widget-helpers'), 1.0 );
			wp_enqueue_script('es-widgets-admin-js', admin_url('?es_widget_action=es_widget_admin_js'), array('jquery', 'es-widget-admin-js'), 1.0 );
		}
		else if ( $is_carrington_editor ) {
			$path = SCRIPT_DEBUG ? '/js/src/es_widgets_helpers.js' : '/js/build/es_widgets_helpers.min.js';
			wp_enqueue_script('es-widget-helpers', plugins_url( $path, dirname(__FILE__) ), array('jquery'), 1.0 );
		}
		else if ( ! is_admin() ) {
			wp_enqueue_script('jquery');
			$path = SCRIPT_DEBUG ? '/js/src/es_widgets_front_end.js' : '/js/build/es_widgets_front_end.min.js';
			wp_enqueue_script('es_widgets_front_end_js', plugins_url( $path, dirname(__FILE__) ), array('jquery'), 1.0 );

			if ( defined('ES_WP_WIDGETS_DISPLAY_DYNAMIC_JS') && true == ES_WP_WIDGETS_DISPLAY_DYNAMIC_JS ) {
				wp_enqueue_script('es-widgets-dynamic-js', admin_url('?es_widget_action=es_widget_js'), array('es_widgets_front_end_js'), 1.0 );
			}
		}

	}

	public function _enqueue_styles() {

		global $pagenow;

		$is_widgets_admin = false;
		$is_carrington_editor = false;
		if ( in_array($pagenow, array('widgets.php')) ) {
			$is_widgets_admin = true;
		}
		else if ( in_array($pagenow, array('post.php', 'post-new.php')) ) {
			$is_carrington_editor = true;
		}

		if (  is_admin() && true == $is_widgets_admin ) {
			wp_enqueue_style('es-widget-admin-css', plugins_url( '/css/build/es_widgets_admin.css', dirname(__FILE__) ), array(), 1.0 );
			wp_enqueue_style('es-widgets-admin-css',admin_url('?es_widget_action=es_widget_admin_css'), array(), 1.0, 'screen');
		}
		else if ( $is_carrington_editor ) {
			wp_enqueue_style('es-widget-admin-css', plugins_url( '/css/build/es_widgets_admin.css', dirname(__FILE__) ), array(), 1.0 );
		}
		else if ( ! is_admin() ) {
			wp_enqueue_style('es-widgets-css', plugins_url('/css/build/es_widgets_front_end.css', dirname(__FILE__) ), array(), 1.0, 'screen');

			if ( defined('ES_WP_WIDGETS_DISPLAY_DYNAMIC_CSS') && true == ES_WP_WIDGETS_DISPLAY_DYNAMIC_CSS ) {
				wp_enqueue_style('es-widgets-dynamic-css', admin_url('?es_widget_action=es_widget_css'), array('es-widgets-css'), 1.0, 'screen');
			}
		}

	}

	public function _register_widget( $widget_classname, $widget_dirname ) {

		if ( ! array_key_exists( $widget_dirname, $this->registered_widgets) ) {
			$widget_classname::init();

			$this->registered_widgets[ $widget_dirname ] = array( 'classname' => $widget_classname );
		}
		else {
			// Throw error - duplicate widget register
		}

	}

	public function _register_widgets() {

		require_once dirname(__FILE__) .'/ES_WP_Widget.class.php';

		array_push( $this->registered_widget_dirs, $this->widgets_dir );

		do_action('es_widget_admin_pre_load_widgets');
		
		foreach( $this->registered_widget_dirs as $dir ) {

			$dir = trailingslashit( $this->widgets_dir ) .'*';

			// Open a known directory, and proceed to read its contents
			foreach( glob($dir, GLOB_ONLYDIR) as $widget_dir ) {

				$dirname = basename( $widget_dir );
				$widget_file = trailingslashit( $widget_dir ) . $dirname . '_widget.php';

				if ( file_exists( $widget_file ) ) {
					require_once $widget_file;
				}

				// The widget file should call ES_WP_Widget_Admin::register_widget('< widget_classname >');
			}
		}

		do_action('es_widget_admin_widgets_loaded');

	}

	public function _render_js( $admin = true ) {

		header('Content-type: text/javascript');
		$js = '';

		// safety wrap the included JS so we can safely use $()
		$js .= ';(function($) {
		';

		$func = true == $admin ? 'admin_js' : 'js';

		foreach( $this->registered_widgets as $widget_dirname => $widget_info ) {

			$widget = new $widget_info['classname']();

			if ( method_exists( $widget, $func ) ) {
				$js .= $widget->$func();
			}
		}

		$js .= '
		})(jQuery);';

		// echo and leave
		echo $js;
		exit;

	}

	public function _render_css( $admin = true ) {

		header('Content-type: text/css');
		$css = '';

		$func = true == $admin ? 'admin_css' : 'js';

		foreach( $this->registered_widgets as $widget_dirname => $widget_info ) {

			$widget = new $widget_info['classname']();

			if ( method_exists( $widget, $func ) ) {
				$css .= $widget->$func();
			}
		}

		// echo and leave
		echo $css;
		exit;

	}

	public function _handle_requests() {

		if (isset($_GET['es_widget_action'])) {
			switch ($_GET['es_widget_action']) {
				case 'es_widget_js':
					$this->_render_js( false );
					break;
				case 'es_widget_admin_js':
					$this->_render_js( true );
					break;
				case 'es_widget_css':
					$this->_render_css( false );
					break;
				case 'es_widget_admin_css':
					$this->_render_css( true );
					break;
			}
		}

	}

}