<?php

/*
 * ES Loop - Carrington Build Module Class
 */

class ES_Loop_Module extends ES_Carrington_Module {

	use ES_Loop_Trait;

	//
	// Static Methods
	//

	// No Static Method Overrides

	//
	// Instance Methods
	//

	public function __construct() {

		$this->_id_base = 'es-cfct-loop';
		$this->_name = 'Loop';
		$this->_widget_dirname = 'loop';
		$this->_opts = array(
			'description' => _('Choose and display a set of posts (any post type) on your page'),
			'icon' => self::get_widgets_url() . $this->_widget_dirname .'/icon.png'
		);

		parent::__construct( $this->_id_base, $this->_name, $this->_opts );

	}

	public function admin_js() {

		ob_start();

		$path = SCRIPT_DEBUG ? '/../js/src/admin.js' : '/../js/build/admin.min.js';
		require dirname(__FILE__) . $path;

		echo '

		cfct_builder.addModuleLoadCallback("'. $this->_id_base .'", function( data ) {

			dom_context = jQuery( data[ 0 ] ).closest(".cfct-module-form");

			new ES_Loop( dom_context );

		});

		';

		$js = ob_get_contents();
		ob_end_clean();

		return $js;

	}

}