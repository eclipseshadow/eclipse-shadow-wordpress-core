<?php

class ES_Carrington_Module extends cfct_build_module {

	public $_id_base;
	public $_name;
	public $_opts;
	public $_widget_dirname;
	public $is_widget_update = false;
	public $is_pre_widget_form = false;
	public $is_widget_display = false;
	public $is_widget_viewfile = false;

	public $_default_options = array(
		'title' => '',
		'show-title' => '0'
	);

	//
	// Static Methods
	//

	// If overridden, must call parent::init()
	public static function init( $module_classname ) {

		cfct_build_register_module( $module_classname );

	}

	//
	// Helper Methods
	//

	public function get_widgets_dir() {

		return trailingslashit( ES_CARRINGTON_WIDGETS_DIR );

	}

	public function get_widgets_url() {

		return trailingslashit( ES_CARRINGTON_WIDGETS_DIR_URL );

	}

	public function get_widget_dir() {

		return trailingslashit( ES_CARRINGTON_WIDGETS_DIR .'/'. $this->_widget_dirname );

	}

	public function get_widget_dir_url() {

		return trailingslashit( ES_CARRINGTON_WIDGETS_DIR_URL .'/'. $this->_widget_dirname );

	}

	/**
	 * Retrieves widget/module ID for edit link purposes
	 */
	public function get_widget_id( $instance ) {

		return substr( $instance['module_id'], 12 );
	}

	// Carrington - /wp-admin/post.php?post=13&action=edit#cfct-module-5859ba83b19f825b2ea273bd71eae03f
	public function get_widget_edit_url( $widget_id, $post_id = '-1' ) {

		if ( $post_id == -1 ) {
			$post_id = '{{carrington_widget_post_id}}';
		}

		$url = admin_url( 'post.php?post='. $post_id .'&action=edit#'. $widget_id );

		return $url;
	}

	//
	// Instance Methods
	//

	public function __construct( $id_base, $name, $opts ) {

		parent::__construct( $id_base, $name, $opts );

	}

	// Do Not Override - Use admin_update() instead
	public function update( $new_instance, $old_instance ) {

		$this->is_widget_update = true;

		$_instance = array();

		$_instance[ $this->get_field_name('title') ] = isset( $new_instance[ $this->get_field_name('title') ] ) ? $new_instance[ $this->get_field_name('title') ] : $this->get_default('title');
		$_instance[ $this->get_field_name('show-title') ] = isset( $new_instance[ $this->get_field_name('show-title') ] ) ? '1' : '0';

		$_instance[ 'module_id_base' ] = $new_instance[ 'module_id_base' ];
		$_instance[ 'module_type' ] = $new_instance[ 'module_type' ];
		$_instance[ 'module_id' ] = $new_instance[ 'module_id' ];
		$_instance[ 'render' ] = $new_instance[ 'render' ];

		$instance = $this->widget_update( $new_instance, $old_instance );

		$instance = array_merge( $_instance, $instance );

		$this->is_widget_update = false;

		return $instance;

	}

	// Meant to be overridden if need be
	public function widget_update( $new_instance, $old_instance ) {

		//...

	}

	// Do Not Override - Use widget_form() instead
	public function admin_form( $instance ) {

		$this->is_pre_widget_form = true;

		$instance['title'] = isset( $instance[ $this->get_field_name('title') ] ) ? $instance[ $this->get_field_name('title') ] : '';
		$instance['show_title'] = isset( $instance[ $this->get_field_name('show-title') ] ) ? $instance[ $this->get_field_name('show-title') ] : '0';

		$output = $this->widget_form( $instance );

		$this->is_pre_widget_form = false;

		$output .= '
		<script>

			jQuery(document).triggerHandler("es-carrington-module-form-load");

		</script>
		';

		return $output;


	}

	// Meant to be overridden if need be
	public function widget_form( $instance ) {

		//...

	}

	public function display( $instance ) {

		$this->is_widget_display = true;

		$display = '';

		$title = isset( $instance[ $this->get_field_name('title') ] ) ? $instance[ $this->get_field_name('title') ] : $this->get_default('title');
		$show_title = isset( $instance[ $this->get_field_name('show-title') ] ) ? $instance[ $this->get_field_name('show-title') ] : $this->get_default('show-title');

		if ( $title != '' && '1' == $show_title ) {
			$display .= apply_filters('es-cfct-before-title', '<h4>') . $title . apply_filters('es-cfct-after-title', '</h4>');
		}

		$display .= $this->widget_display( $instance );

		$this->is_widget_display = false;

		return $display;

	}

	public function widget_display( $instance ) {

		//...

	}

	// Meant to be overridden if desired

	public function text( $instance ) {

		return $instance[ $this->get_field_name('title') ];
	}

	// Meant to be overridden if desired

	public function list_admin() {

		return true;

	}

	// Optional to use inside of widget_display() to load a front end view file
	public function load_widget_view( $instance, $params ) {

		$this->is_widget_viewfile = true;

		$view_output = $this->_load_widget_view( $instance, $params );

		$this->is_widget_viewfile = false;

		return $view_output;

	}

	private function _load_widget_view( $instance, $params ) {

		$view_path = $this->get_widget_dir() .'views/view.php';

		if ( file_exists( $view_path ) ) {
			extract( $params );

			if ( !isset( $title ) ) {
				$title = isset( $instance[ $this->get_field_name('title') ] ) ? $instance[ $this->get_field_name('title') ] : $this->get_default('title');
			}

			if ( !isset( $show_title ) ) {
				$show_title = isset( $instance[ $this->get_field_name('show-title') ] ) ? $instance[ $this->get_field_name('show-title') ] : $this->get_default('show-title');
			}

			if ( !isset( $custom_css_classes ) ) {
				$custom_css_classes = '';
			}

			ob_start();

			include ( $view_path );

			$buffer = ob_get_clean();
			return $buffer;
		}

		return '';

	}

	// Optional to use inside of widget_form() to load a front end view file
	public function load_admin_widget_view( $instance, $params ) {

		$this->is_widget_viewfile = true;

		$view_output = $this->_load_admin_widget_view( $instance, $params );

		$this->is_widget_viewfile = false;

		return $view_output;

	}

	private function _load_admin_widget_view( $instance, $params ) {

		$view_path = $this->get_widget_dir() .'views/admin_view.php';

		if ( file_exists( $view_path ) ) {
			extract( $params );

			if ( !isset( $title ) ) {
				$title = isset( $instance[ $this->get_field_name('title') ] ) ? $instance[ $this->get_field_name('title') ] : $this->get_default('title');
			}

			if ( !isset( $show_title ) ) {
				$show_title = isset( $instance[ $this->get_field_name('show-title') ] ) ? $instance[ $this->get_field_name('show-title') ] : $this->get_default('show-title');
			}

			ob_start();

			include ( $view_path );

			$buffer = ob_get_clean();
			return $buffer;
		}

		return '';

	}

	// Can only be used in load_view(), admin_form(), display(), text()
	public function get_instance_id( $instance = null ) {

		if ( !is_null( $instance ) ) return $instance['module_id'];

		return null;

	}

	public function get_default( $field_name ) {

		if ( property_exists( $this, 'default_options' ) && array_key_exists( $field_name, $this->default_options ) ) {
			return $this->default_options[ $field_name ];
		}
		else if ( array_key_exists( $field_name, $this->_default_options ) ) {
			return $this->_default_options[ $field_name ];
		}

		return null;
	}

	public function is_default( $field_name, $field_value, $echo_data_attr = true ) {

		$default = $this->get_default( $field_name );
		$is_default = ($default == $field_value);

		if ( $echo_data_attr ) {
			if ( $is_default ) {
				echo ' data-is-default="true" ';
			}
			else {
				echo ' data-is-default="false" ';
			}
		}

		if ( null == $default ) return null;

		return $is_default;
	}
}