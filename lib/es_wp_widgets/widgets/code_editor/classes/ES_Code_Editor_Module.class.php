<?php

/*
 * ES Code Editor - Carrington Module Class
 */

class ES_Code_Editor_Module extends ES_Carrington_Module {

	use ES_Code_Editor_Trait;

	//
	// Static Methods
	//

	// No Static Method Overrides

	//
	// Instance Methods
	//

	public function __construct() {

		$this->_id_base = 'es-cfct-code-editor';
		$this->_name = 'Code Editor';
		$this->_widget_dirname = 'code_editor';
		$this->_opts = array(
			'description' => _('Add Raw HTML or Javascript to your page'),
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

			new ES_Code_Editor( dom_context );

		});

		';

		$js = ob_get_contents();
		ob_end_clean();

		return $js;

	}

}