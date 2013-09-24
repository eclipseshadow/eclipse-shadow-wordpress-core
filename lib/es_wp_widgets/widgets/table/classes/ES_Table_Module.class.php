<?php

/*
 * ES Table - Carrington Build Module Class
 */

class ES_Table_Module extends ES_Carrington_Module {

	use ES_Table_Trait;

	//
	// Static Methods
	//

	// No Static Method Overrides

	//
	// Instance Methods
	//

	public function __construct() {

		$this->_id_base = 'es-cfct-table';
		$this->_name = 'Table';
		$this->_widget_dirname = 'table';
		$this->_opts = array(
			'description' => _('Add a Table to your page'),
			'icon' => $this->get_widgets_url() . $this->_widget_dirname .'/icon.png'
		);

		parent::__construct( $this->_id_base, $this->_name, $this->_opts );

	}

	public function admin_js() {

		ob_start();

		require dirname(__FILE__) . '/../js/admin.js';

		echo '

		cfct_builder.addModuleLoadCallback("'. $this->_id_base .'", function( data ) {

			dom_context = jQuery( data[ 0 ] ).closest(".cfct-module-form");

			new ES_Table( dom_context );

		});

		';

		$js = ob_get_contents();
		ob_end_clean();

		return $js;

	}

}