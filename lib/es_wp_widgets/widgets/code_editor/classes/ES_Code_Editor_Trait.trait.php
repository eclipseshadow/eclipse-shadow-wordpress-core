<?php

/*
 * Trait from which both ES_Code_Editor_Widget & ES_Code_Editor_Module inherit their methods
 */

trait ES_Code_Editor_Trait {

	public $default_options = array(
		'title' => 'My Raw Code',
		'content' => '',
		'mode' => 'html'
	);

	//
	// Static Methods
	//

	// init

	public static function init() {

		// Call parent::init()

		parent::init( __CLASS__ );
	}

	//
	// Instance Methods
	//

	// Form

	public function widget_form( $instance ) {

		$content = isset( $instance[ $this->get_field_name('content') ] ) ? $instance[ $this->get_field_name('content') ] : $this->get_default('content');
		$mode = isset( $instance[ $this->get_field_name('mode') ] ) ? $instance[ $this->get_field_name('mode') ] : $this->get_default('mode');

		$params = compact(
			'content',
			'mode'
		);

		return $this->load_admin_widget_view( $instance, $params );
	}

	// Update

	public function widget_update( $new_instance, $old_instance ) {

		$instance = array();

		$instance[ $this->get_field_name('content') ] = $new_instance[ $this->get_field_name('content') ];
		$instance[ $this->get_field_name('mode') ] = $new_instance[ $this->get_field_name('mode') ];

		return $instance;
	}

	// Display

	public function widget_display( $instance ) {

		$content = isset( $instance[ $this->get_field_name('content') ] ) ? $instance[ $this->get_field_name('content') ] : $this->get_default('content');
		$content = html_entity_decode( $content );
		$mode = isset( $instance[ $this->get_field_name('mode') ] ) ? $instance[ $this->get_field_name('mode') ] : $this->get_default('mode');

		$params = compact(
			'content',
			'mode'
		);

		return $this->load_widget_view( $instance, $params );
	}

	// Admin CSS

	public function admin_css() {

		ob_start();

		require dirname(__FILE__) . '/../css/build/admin_style.css';

		$css = ob_get_contents();
		ob_end_clean();

		return $css;
	}
}