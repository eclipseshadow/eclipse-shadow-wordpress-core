<?php

/*
 * ES Tabs/Accordion - WP Widget Class
 */

class ES_Tabs_Accordion_Widget extends ES_WP_Widget {

	use ES_Tabs_Accordion_Trait;

	//
	// Static Methods
	//

	// No Static Method Overrides

	//
	// Instance Methods
	//

	public function __construct() {

		$this->_id_base = 'es-cfct-tabs-accordion';
		$this->_name = 'Tabs/Accordion';
		$this->_widget_dirname = 'tabs_accordion';
		$this->_opts = array(
			'description' => _('Add a Tabs or an Accordion to your page'),
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

			window.t = new ES_Tabs_Accordion( dom_context );

		});

		';

		$js = ob_get_contents();
		ob_end_clean();

		return $js;

	}

}