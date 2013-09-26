<?php

/*
 * Linked Image - WP Widget Class
 */

class ES_Linked_Image_Widget extends ES_WP_Widget {

	use ES_Linked_Image_Trait;

	//
	// Static Methods
	//

	// No Static Method Overrides

	//
	// Instance Methods
	//

	public function __construct() {

		$this->_id_base = 'es-cfct-linked-image';
		$this->_name = 'Image/Ad';
		$this->_widget_dirname = 'linked_image';
		$this->_opts = array(
			'description' => _('Add an Image or Linked Ad to your page'),
			'icon' => self::get_widgets_url() . $this->_widget_dirname .'/icon.png'
		);
		$this->_control_opts = array(
			'width' => 800
		);

		parent::__construct( 'es-cfct-linked-image', 'Image/Ad', $this->_opts, $this->_control_opts );

	}

	public function admin_js() {

		ob_start();

		$path = SCRIPT_DEBUG ? '/../js/src/admin.js' : '/../js/build/admin.min.js';
		require dirname(__FILE__) . $path;

		echo '

		es_widgets_admin.addWidgetLoadCallback("'. $this->_id_base .'", function( current_loaded_widget_id ) {

			dom_context = jQuery( "#"+ current_loaded_widget_id );

			new ES_Linked_Image( dom_context );

		});

		';

		$js = ob_get_contents();
		ob_end_clean();

		return $js;

	}

}