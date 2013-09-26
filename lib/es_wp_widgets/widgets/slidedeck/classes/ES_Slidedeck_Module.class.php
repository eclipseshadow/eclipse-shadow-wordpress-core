<?php

/*
 * ES Slidedeck - Carrington Build Module Class
 */

class ES_Slidedeck_Module extends ES_Carrington_Module {

	use ES_Slidedeck_Trait;

	//
	// Static Methods
	//

	// No Static Method Overrides

	//
	// Instance Methods
	//

	public function __construct() {

		$this->_id_base = 'es-cfct-slidedeck';
		$this->_name = 'Slide Show';
		$this->_widget_dirname = 'slidedeck';
		$this->_opts = array(
			'description' => _('Add a Slide Show to your page'),
			'icon' => $this->get_widgets_url() . $this->_widget_dirname .'/icon.png'
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

			new ES_SlideDeck( dom_context );

		});

		';

		$js = ob_get_contents();
		ob_end_clean();

		return $js;

	}

}