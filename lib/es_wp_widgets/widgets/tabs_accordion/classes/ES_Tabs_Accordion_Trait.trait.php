<?php

/*
 * Trait from which both ES_Tabs_Accordion_Widget & ES_Tabs_Accordion_Module inherit their methods
 */

trait ES_Tabs_Accordion_Trait {

	public $default_options = array(
		'title' => 'My Tabs',
		'tabs-accordion-data' => '',
		'display-as-accordion' => '0'
	);

	//
	// Static Methods
	//

	// init

	public static function init() {

		// Call parent::init()

		parent::init( __CLASS__ );

		// Add Ajax Handlers

		// -- Get Widget Area Dropdown Options
		$action = 'es_cfct_tabs_accordion_get_widget_areas';
		add_action('wp_ajax_'. $action, array( __CLASS__, '_ajax_get_widget_areas' ) );
	}

	// Ajax

	public static function _ajax_get_widget_areas() {

		global $wp_registered_sidebars;

		$r = new stdClass();

		$r->sidebars = $wp_registered_sidebars;

		header('Content-type: application/json');

		echo json_encode( $r );

		die(); // this is required to return a proper result
	}

	//
	// Instance Methods
	//

	// Form

	public function widget_form( $instance ) {

		$tabs_accordion_data = isset( $instance[ $this->get_field_name('tabs-accordion-data') ] ) ? $instance[ $this->get_field_name('tabs-accordion-data') ] : $this->get_default('tabs-accordion-data');

		$display_as_accordion = isset( $instance[ $this->get_field_name('display-as-accordion') ] ) ? $instance[ $this->get_field_name('display-as-accordion') ] : $this->get_default('display-as-accordion');

		$params = compact(
			'tabs_accordion_data',
			'display_as_accordion'
		);

		return $this->load_admin_widget_view( $instance, $params );
	}

	// Update

	public function widget_update( $new_instance, $old_instance ) {

		$instance = array();

		$instance[ $this->get_field_name('tabs-accordion-data') ] = $new_instance[ $this->get_field_name('tabs-accordion-data') ];

		$instance[ $this->get_field_name('display-as-accordion') ] = isset( $new_instance[ $this->get_field_name('display-as-accordion') ] ) ? '1' : '0';

		return $instance;
	}

	// Display

	public function widget_display( $instance ) {

		$tabs_accordion_data = isset( $instance[ $this->get_field_name('tabs-accordion-data') ] ) ? $instance[ $this->get_field_name('tabs-accordion-data') ] : $this->get_default('tabs-accordion-data');

		$display_as_accordion = isset( $instance[ $this->get_field_name('display-as-accordion') ] ) ? $instance[ $this->get_field_name('display-as-accordion') ] : $this->get_default('display-as-accordion');

		$tabs_accordion_html = $this->data_to_html( $tabs_accordion_data, $display_as_accordion );

		$params = compact(
			'tabs_accordion_html',
			'display_as_accordion'
		);

		return $this->load_widget_view( $instance, $params );
	}

	// CSS

	public function css() {

		ob_start();

		require dirname(__FILE__) . '/../css/style.css';

		$css = ob_get_contents();
		ob_end_clean();

		return $css;
	}

	// Admin CSS

	public function admin_css() {

		ob_start();

		require dirname(__FILE__) . '/../css/admin_style.css';

		$css = ob_get_contents();
		ob_end_clean();

		return $css;
	}

	// JS

	public function js() {

		ob_start();

		require dirname(__FILE__) . '/../js/front_end.js';

		$js = ob_get_contents();
		ob_end_clean();

		return $js;

	}

	//
	// Helpers
	//

	public function data_to_html( $data, $display_as_accordion = false ) {

		$data = json_decode( urldecode( $data ) );

		if ( $display_as_accordion ) {
			return $this->get_accordion_output( $data );
		}
		else {
			return $this->get_tabs_output( $data );
		}
	}

	public function get_tabs_output( $data ) {

		$uid = uniqid('es_tabs_');

		$html = '<div class="es_tabs" id="'. $uid .'">';
		$tabs_html = '<ul class="es_tabs_links">';
		$panels_html = '<div class="es_tabs_panels">';

		if ( !property_exists( $data, 'tabs' ) || !is_array( $data->tabs ) ) return $html;

		$tab_count = 1;
		foreach( $data->tabs as $tab ) {

			$tab_id = $uid .'-'. $tab_count;

			$link_href = '#'. $tab_id;

			// If using an AJAX server page, ignore other items and use UI Tabs AJAX functionality

			$has_ajax_item = false;
			if ( is_array( $tab->tab_items ) ) {
				foreach( $tab->tab_items as $item ) {
					if ( 'server_page' == $item->type && true == $item->load_via_ajax ) {
						$has_ajax_item = true;
						$link_href = $item->server_page_url;
						break;
					}
				}
			}

			$tabs_html .= '<li><a href="'. $link_href .'">'. $tab->tab_name .'</a></li>';

			$panels_html .= '<div class="es_tabs_panel" id="'. $tab_id .'">';

			if ( is_array( $tab->tab_items ) && ! $has_ajax_item ) {

				foreach( $tab->tab_items as $item ) {

					$panels_html .= '<div class="es-tabs-item">';

					switch( $item->type ) {
						case 'rich_text':
							$panels_html .= html_entity_decode( $item->content );
							break;
						case 'raw_html':
							$panels_html .= html_entity_decode( $item->content );
							break;
						case 'widget_area':
							ob_start();
							dynamic_sidebar( $item->widget_area_id );
							$panels_html .= ob_get_clean();
							break;
						case 'server_page':
							ob_start();
							require ABSPATH . ltrim( html_entity_decode( $item->server_page_url ), '/' );
							$panels_html .= ob_get_clean();
							break;
						default:
							$panels_html .= '<!-- Unknown Item Type -->';
							break;
					}

					$panels_html .= '</div>';

				}
			}

			$panels_html .= '</div>';

			$tab_count++;
		}

		$tabs_html .= '</ul>';
		$panels_html .= '</div>';

		$html .= $tabs_html;
		$html .= $panels_html;
		$html .= '</div>';

		return $html;
	}

	public function get_accordion_output( $data ) {

		$uid = uniqid('es_accordion_');

		$html = '<div class="es_accordion" id="'. $uid .'">';

		if ( !property_exists( $data, 'tabs' ) || !is_array( $data->tabs ) ) return $html;

		$tab_count = 1;
		foreach( $data->tabs as $tab ) {

			$tab_id = $uid .'-'. $tab_count;

			// If using an AJAX server page, ignore other items and use UI Tabs AJAX functionality

			$has_ajax_item = false;
			$server_page_url = '';
			if ( is_array( $tab->tab_items ) ) {
				foreach( $tab->tab_items as $item ) {
					if ( 'server_page' == $item->type && true == $item->load_via_ajax ) {
						$has_ajax_item = true;
						$server_page_url = $item->server_page_url;
						break;
					}
				}
			}

			$html .= '<h5 data-ajax-url="'. $server_page_url .'" id="'. $tab_id .'" >'. $tab->tab_name .'</h5>';

			$html .= '<div class="es_accordion_panel" id="'. $tab_id .'_panel">';

			if ( is_array( $tab->tab_items ) && ! $has_ajax_item ) {

				foreach( $tab->tab_items as $item ) {

					$html .= '<div class="es-accordion-item">';

					switch( $item->type ) {
						case 'rich_text':
							$html .= html_entity_decode( $item->content );
							break;
						case 'raw_html':
							$html .= html_entity_decode( $item->content );
							break;
						case 'widget_area':
							ob_start();
							dynamic_sidebar( $item->widget_area_id );
							$html .= ob_get_clean();
							break;
						case 'server_page':
							ob_start();
							require ABSPATH . ltrim( html_entity_decode( $item->server_page_url ), '/' );
							$html .= ob_get_clean();
							break;
						default:
							$html .= '<!-- Unknown Item Type -->';
							break;
					}

					$html .= '</div>';

				}
			}

			$html .= '</div>';
			$tab_count++;
		}
		$html .= '</div>';

		return $html;
	}

}