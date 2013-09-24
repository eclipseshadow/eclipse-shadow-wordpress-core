<?php

/*
 * ES Linked Image - Carrington Build Module Class
 */

class ES_Linked_Image_Module extends ES_Carrington_Module {

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

		parent::__construct( $this->_id_base, $this->_name, $this->_opts );

	}

	public function admin_js() {

		ob_start();

		require dirname(__FILE__) . '/../js/admin.js';

		echo '

		cfct_builder.addModuleLoadCallback("'. $this->_id_base .'", function( data ) {

			dom_context = jQuery( data[ 0 ] ).closest(".cfct-module-form");

			new ES_Linked_Image( dom_context );

		});

		';

		$js = ob_get_contents();
		ob_end_clean();

		return $js;

	}

}