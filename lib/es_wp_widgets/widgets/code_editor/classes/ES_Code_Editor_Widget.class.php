<?php

/*
 * ES Code Editor - WP Widget Class
 */

class ES_Code_Editor_Widget extends ES_WP_Widget {

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
		$this->_control_opts = array(
			'width' => 800
		);

		parent::__construct( $this->_id_base, $this->_name, $this->_opts, $this->_control_opts );

	}

	public function admin_js() {

		ob_start();

		$path = SCRIPT_DEBUG ? '/../js/src/admin.js' : '/../js/build/admin.min.js';
		require dirname(__FILE__) . $path;

		echo '

		es_widgets_admin.addWidgetLoadCallback("'. $this->_id_base .'", function( current_loaded_widget_id ) {

			dom_context = jQuery( "#"+ current_loaded_widget_id );

			new ES_Code_Editor( dom_context );

		});

		';

		$js = ob_get_contents();
		ob_end_clean();

		return $js;

	}

}