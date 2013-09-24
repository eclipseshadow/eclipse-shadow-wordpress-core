<?php

/*
 * ES Table - WP Widget Class
 */

class ES_Table_Widget extends ES_WP_Widget {

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
		$this->_control_opts = array(
			'width' => 800
		);

		parent::__construct( $this->_id_base, $this->_name, $this->_opts, $this->_control_opts );

	}

	public function admin_js() {

		ob_start();

		require dirname(__FILE__) . '/../js/admin.js';

		echo '

		es_widgets_admin.addWidgetLoadCallback("'. $this->_id_base .'", function( current_loaded_widget_id ) {

			dom_context = jQuery( "#"+ current_loaded_widget_id );

			new ES_Table( dom_context );

		});

		';

		$js = ob_get_contents();
		ob_end_clean();

		return $js;

	}

}