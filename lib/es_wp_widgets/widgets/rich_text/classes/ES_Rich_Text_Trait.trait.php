<?php

/*
 * Trait from which both ES_Rich_Text_Widget & ES_Rich_Text_Module inherit their methods
 */

trait ES_Rich_Text_Trait {

	public $default_options = array(
		'title' => 'My Rich Text',
		'content' => ''
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

		$params = compact(
			'content'
		);

		return $this->load_admin_widget_view( $instance, $params );
	}

	// Update

	public function widget_update( $new_instance, $old_instance ) {

		$instance = array();

		$instance[ $this->get_field_name('content') ] = $new_instance[ $this->get_field_name('content') ];

		return $instance;
	}

	// Display

	public function widget_display( $instance ) {

		$content = isset( $instance[ $this->get_field_name('content') ] ) ? $instance[ $this->get_field_name('content') ] : $this->get_default('content');

		$content = html_entity_decode( $content );

		$params = compact(
			'content'
		);

		return $this->load_widget_view( $instance, $params );
	}

	// CSS

	public function css() {

		/*ob_start();

		require dirname(__FILE__) . '/../css/style.css';

		$css = ob_get_contents();
		ob_end_clean();

		return $css;*/
	}

	// Admin CSS

	public function admin_css() {

		ob_start();

		require dirname(__FILE__) . '/../css/admin_style.css';

		$css = ob_get_contents();
		ob_end_clean();

		return $css;
	}

	// JS

	public function js() {

		/*ob_start();

		require dirname(__FILE__) . '/../js/front_end.js';

		$js = ob_get_contents();
		ob_end_clean();

		return $js;*/

	}
}