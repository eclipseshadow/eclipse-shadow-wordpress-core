<?php

/*
 * ES Slidedeck - WP Widget Class
 */

class ES_Slidedeck_Widget extends ES_WP_Widget {

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
		$this->_control_opts = array(
			'width' => 600
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

			new ES_SlideDeck( dom_context );

		});

		';

		$js = ob_get_contents();
		ob_end_clean();

		return $js;

	}

}