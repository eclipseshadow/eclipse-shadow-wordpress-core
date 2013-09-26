<?php

/*
 * Trait from which both ES_Slidedeck_Widget & ES_Slidedeck_Module inherit their methods
 */

trait ES_Slidedeck_Trait {

	public $default_options = array(
		'slidedeck_id' => '',
		'title' => 'My Slide Show',
		'text_before_deck' => '',
		'text_after_deck' => '',
		'deploy_as_iframe' => '0',
		'use_ress' => '1',
		'proportional' => '1'
	);

	//
	// Static Methods
	//

	// Init

	public static function init() {

		// Call parent::init()

		parent::init( __CLASS__ );

		// Add Ajax Handlers

		// -- Get Slidedeck Dropdown Options
		$action = 'es_cfct_slidedeck_get_slidedeck_options';
		add_action('wp_ajax_'. $action, array( __CLASS__, '_ajax_get_slidedeck_options' ) );
	}

	// Ajax

	public static function _ajax_get_slidedeck_options() {

		global $SlideDeckPlugin;

		$slidedeck_id = $_POST['slidedeck_id'];

		$slidedecks = $SlideDeckPlugin->SlideDeck->get( null, 'post_title', 'ASC', 'publish' );

		$html = '';

		foreach( (array) $slidedecks as $slidedeck ) {
			$selected = $slidedeck_id == $slidedeck['id'] ? ' selected="selected"' : '';
			$html .= '<option value="'. $slidedeck['id'] .'" '. $selected .'>'. $slidedeck['title'] .'</option>';
		}

		header('Content-type: text/html');

		echo $html;

		die(); // this is required to return a proper result

	}

	//
	// Instance Methods
	//

	// Form

	public function widget_form( $instance ) {

		global $SlideDeckPlugin, $pagenow;

		$slidedeck_id = isset( $instance[ $this->get_field_name('slidedeck_id') ] ) ? strip_tags( $instance[ $this->get_field_name('slidedeck_id') ] ) : $this->get_default('slidedeck_id');
		$text_before_deck = isset( $instance[ $this->get_field_name('text_before_deck') ] ) ? $instance[ $this->get_field_name('text_before_deck') ] : $this->get_default('text_before_deck');
		$text_after_deck = isset( $instance[ $this->get_field_name('text_after_deck') ] ) ? $instance[ $this->get_field_name('text_after_deck') ] : $this->get_default('text_after_deck');

		$deploy_as_iframe = isset( $instance[ $this->get_field_name('deploy_as_iframe') ] ) ? $instance[ $this->get_field_name('deploy_as_iframe') ] : $this->get_default('deploy_as_iframe');
		$use_ress = isset( $instance[ $this->get_field_name('use_ress') ] ) ? $instance[ $this->get_field_name('use_ress') ] : $this->get_default('use_ress');
		$proportional = isset( $instance[ $this->get_field_name('proportional') ] ) ? $instance[ $this->get_field_name('proportional') ] : $this->get_default('proportional');

		// Return-To Admin Message

		$is_widget_admin = in_array( $pagenow, array( 'widgets.php' ));
		$widget_id = $this->get_widget_id( $instance );
		$return_to_url = $is_widget_admin ? $this->get_widget_edit_url( $widget_id ) : 'current_window';

		$params = compact(
			'slidedeck_id',
			'text_before_deck',
			'text_after_deck',
			'deploy_as_iframe',
			'use_ress',
			'proportional',
			'return_to_url'
		);

		return $this->load_admin_widget_view( $instance, $params );
	}

	// Update

	public function widget_update( $new_instance, $old_instance ) {

		$instance = array();

		$instance[ $this->get_field_name('slidedeck_id') ] = $new_instance[ $this->get_field_name('slidedeck_id') ];
		$instance[ $this->get_field_name('text_before_deck') ] = $new_instance[ $this->get_field_name('text_before_deck') ];
		$instance[ $this->get_field_name('text_after_deck') ] = $new_instance[ $this->get_field_name('text_after_deck') ];

		$instance[ $this->get_field_name('deploy_as_iframe') ] = isset( $new_instance[ $this->get_field_name('deploy_as_iframe') ] ) ? '1' : '0';
		$instance[ $this->get_field_name('use_ress') ] = isset( $new_instance[ $this->get_field_name('use_ress') ] ) ? '1' : '0';
		$instance[ $this->get_field_name('proportional') ] 	= isset( $new_instance[ $this->get_field_name('proportional') ] ) ? '1' : '0';

		return $instance;
	}

	// Display

	public function widget_display( $instance ) {

		global $SlideDeckPlugin;
		global $slidedeck_footer_scripts;

		$slidedeck_id = isset( $instance[ $this->get_field_name('slidedeck_id') ] ) ? strip_tags( $instance[ $this->get_field_name('slidedeck_id') ] ) : $this->get_default('slidedeck_id');
		$text_before_deck = isset( $instance[ $this->get_field_name('text_before_deck') ] ) ? $instance[ $this->get_field_name('text_before_deck') ] : $this->get_default('text_before_deck');
		$text_after_deck = isset( $instance[ $this->get_field_name('text_after_deck') ] ) ? $instance[ $this->get_field_name('text_after_deck') ] : $this->get_default('text_after_deck');

		$deploy_as_iframe = isset( $instance[ $this->get_field_name('deploy_as_iframe') ] ) ? $instance[ $this->get_field_name('deploy_as_iframe') ] : $this->get_default('deploy_as_iframe');
		$use_ress = isset( $instance[ $this->get_field_name('use_ress') ] ) ? $instance[ $this->get_field_name('use_ress') ] : $this->get_default('use_ress');
		$proportional = isset( $instance[ $this->get_field_name('proportional') ] ) ? $instance[ $this->get_field_name('proportional') ] : $this->get_default('proportional');


		$shortcode = "[SlideDeck2 id={$slidedeck_id}";

		if ( '1' == $deploy_as_iframe ) $shortcode.= " iframe=1";

		if ( '1' == $use_ress ) {
			$shortcode.= " ress=1";

			// If this widget makes this page have a RESS deck...
			$SlideDeckPlugin->page_has_ress_deck = true;
		}

		/**
		 * The proportional option is negative only. Proportional
		 * is default, and this option being false only overrides it.
		 */
		if ( '0' == $proportional ) {
			$shortcode.= " proportional=false";
		}

		$shortcode.= "]";

		$params = compact(
			'text_before_deck',
			'text_after_deck',
			'shortcode'
		);

		return $this->load_widget_view( $instance, $params );;
	}

	// Text

	public function text( $instance ) {

		return $instance[ $this->get_field_name('_title') ];
	}

	// CSS

	public function css() {

		ob_start();

		require dirname(__FILE__) . '/../css/build/style.css';

		$css = ob_get_contents();
		ob_end_clean();

		return $css;
	}

	// Admin CSS

	public function admin_css() {

		ob_start();

		require dirname(__FILE__) . '/../css/build/admin_style.css';

		$css = ob_get_contents();
		ob_end_clean();

		return $css;
	}

	// List Admin

	public function list_admin() {

		global $SlideDeckPlugin;

		if ( empty($SlideDeckPlugin) ) {
			return false;
		}
		else {
			return true;
		}

	}

}