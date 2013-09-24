<?php


class ES_WP_Widget extends WP_Widget {

	public $_id_base;
	public $_name;
	public $_opts;
	public $_widget_classname;
	public $_widget_dirname;
	public $is_widget_update = false;
	public $is_pre_widget_form = false;
	public $is_widget_display = false;
	public $is_widget_admin_viewfile = false;

	public $errors = array();

	public $_default_options = array(
		'title' => '',
		'show-title' => '0',
		'custom_css_classes' => ''
	);

	//
	// Static Methods
	//

	// If overridden, must call parent::init()
	public static function init( $widget_classname ) {

		add_action( 'widgets_init', create_function('',' register_widget( "'. $widget_classname .'" ); '), 1, 1 );

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
	public function get_widget_id( $instance = null ) {

		return $this->id;
	}

	// Widget Admin - /wp-admin/widgets.php?es_wp_widget_action=edit_widget&es_wp_widget_id=es-cfct-slidedeck-2
	public static function get_widget_edit_url( $widget_id, $post_id = null ) {

		$url = admin_url( 'widgets.php?es_wp_widget_action=edit_widget&es_wp_widget_id='. $widget_id );

		return $url;
	}

	//
	// Instance Methods
	//

	public function __construct( $id_base, $name, $opts, $control_opts = array() ) {

		// Possibly return false here based on list_admin to disable?

		parent::__construct( $id_base, $name, $opts, $control_opts );

	}

	// Do Not Override - Use admin_form() instead
	public function form( $instance ) {

		$this->is_pre_widget_form = true;

		$instance['title'] = isset( $instance[ 'title' ] ) ? $instance[ 'title' ] : $this->get_default('title');
		$instance['show-title'] = isset( $instance[ 'show-title' ] ) ? $instance[ 'show-title' ] : $this->get_default('show-title');
		$custom_css_classes = isset( $instance[ 'custom_css_classes' ] ) ? $instance[ 'custom_css_classes' ] : $this->get_default('custom_css_classes');

		$this->is_pre_widget_form = false;

		echo '<div class="es-widget-extra-options"><label>Custom CSS Classes: </label> <input type="text" class="widefat es-widget-custom-css-classes" id="'. $this->get_field_id('custom_css_classes') .'" name="'. $this->get_field_name('custom_css_classes') .'" value="'. $custom_css_classes .'" /></div><a class="es-widget-extra-options-link" href="">Extra Options</a>';

		$this->is_pre_widget_form = true;

		echo $this->widget_form( $instance );

		$this->is_pre_widget_form = false;

		$es_load_script_num = rand(1000,9999);

		echo '
		<script class="es_widget_load_script" id="es_widget_load_script_'. $es_load_script_num .'">

			current_widget = jQuery("#es_widget_load_script_'. $es_load_script_num .'").closest(".widget");

			current_widget_id = current_widget.attr("id");

			jQuery(document).triggerHandler("es-widget-form-load", [ current_widget_id ]);

		</script>
		';

	}

	// Meant to be overridden if need be
	public function widget_form( $instance ) {

		//...

	}

	// Do Not Override - Use display() instead
	public function widget ( $args, $instance ) {

		$this->is_widget_display = true;

		$title = isset( $instance[ 'title' ] ) ? $instance[ 'title' ] : $this->get_default('title');
		$show_title = isset( $instance[ 'show-title' ] ) ? $instance[ 'show-title' ] : $this->get_default('show-title');

		echo $args['before_widget'];

		if ( $title != '' && '1' == $show_title ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		echo $this->widget_display( $instance, $args );

		echo $args['after_widget'];

		$this->is_widget_display = false;

	}

	// Meant to be overridden if need be
	public function widget_display( $instance, $args ) {

		//...

	}

	// Do Not Override - Use admin_update() instead
	public function update ( $new_instance, $old_instance ) {//var_dump($new_instance);exit;

		$this->is_widget_update = true;

		$_instance = array();

		$_instance['custom_css_classes'] = $new_instance['custom_css_classes'];
		$_instance['title'] = isset( $new_instance['title'] ) ? $new_instance['title'] : $this->get_default('title');
		$_instance['show-title'] = isset( $new_instance[ 'show-title' ] ) ? '1' : '0';

		$instance = $this->widget_update( $new_instance, $old_instance );

		$instance = array_merge( $_instance, $instance );

		$this->is_widget_update = false;

		return $instance;

	}

	// Meant to be overridden if need be
	public function widget_update( $new_instance, $old_instance ) {

		return $new_instance;

	}

	public function render_edit_link( $instance ) {

		echo '<a class="es-wp-widget-edit-link" href="'. admin_url('widgets.php?es_wp_widget_action=edit_widget&es_wp_widget_id='. $instance['widget_admin_id']) .'">Edit</a>';

	}

	// Override to provide a textual representation of your widget's data
	public function text( $instance ) {
		//.. Does nothing yet
	}

	// Optional to use inside of widget_display() to load a front end view file
	public function load_widget_view( $instance, $params ) {

		$view_output = $this->_load_widget_view( $instance, $params );

		return $view_output;

	}

	private function _load_widget_view( $instance, $params ) {

		$view_path = $this->get_widget_dir() .'views/view.php';

		if ( file_exists( $view_path ) ) {
			extract( $params );

			if ( !isset( $title ) ) {
				$title = isset( $instance[ 'title' ] ) ? $instance[ 'title' ] : $this->get_default('title');
			}

			if ( !isset( $show_title ) ) {
				$show_title = isset( $instance[ 'show-title' ] ) ? $instance[ 'show-title' ] : $this->get_default('show-title');
			}

			if ( !isset( $custom_css_classes ) ) {
				$custom_css_classes = isset( $instance[ 'custom_css_classes' ] ) ? $instance[ 'custom_css_classes' ] : $this->get_default('custom_css_classes');
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

		$this->is_widget_admin_viewfile = true;

		$view_output = $this->_load_admin_widget_view( $instance, $params );

		$this->is_widget_admin_viewfile = false;

		return $view_output;

	}

	private function _load_admin_widget_view( $instance, $params ) {

		$view_path = $this->get_widget_dir() .'views/admin_view.php';

		if ( file_exists( $view_path ) ) {
			extract( $params );

			if ( !isset( $title ) ) {
				$title = isset( $instance[ 'title' ] ) ? $instance[ 'title' ] : $this->get_default('title');
			}

			if ( !isset( $show_title ) ) {
				$show_title = isset( $instance[ 'show-title' ] ) ? $instance[ 'show-title' ] : $this->get_default('show-title');
			}

			ob_start();

			include ( $view_path );

			$buffer = ob_get_clean();
			return $buffer;
		}

		return '';

	}

	// Cannot be used in admin_form()
	public function get_instance_id( $instance = null ) {

		if ( !is_null( $instance ) && isset( $instance->id ) ) return $instance->id;

		return $this->id;

	}

	// Override to add Front End CSS
	public function css() {
		//...
	}

	// Override to add Admin CSS
	public function admin_css() {
		//...
	}

	// Override to add Front End JS
	public function js() {
		//...
	}

	// Override to add Admin JS
	public function admin_js() {
		//...
	}

	// Extend & return false to disable widget
	public function list_admin( $context = null ) {

		return true;

	}

	function get_field_name( $field_name ) {

		if ( ($this->is_widget_update || $this->is_pre_widget_form || $this->is_widget_display) && !$this->is_widget_admin_viewfile ) {
			return $field_name;
		}

		return parent::get_field_name( $field_name );

	}

	public function set_error( $field, $message ) {

		return $this->errors[$field] = $message;

	}

	public function get_error( $field ) {

		return isset($this->errors[$field]) ? $this->errors[$field] : false;

	}

	public function get_errors() {

		return is_array($this->errors) && count($this->errors) ? $this->errors : false;

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