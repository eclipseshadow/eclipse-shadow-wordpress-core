<?php

/*
 * Trait from which both ES_Table_Widget & ES_Table_Module inherit their methods
 */

trait ES_Table_Trait {

	public $default_options = array(
		'title' => 'My Table',
		'table-data' => '',
		'show-title' => '0',
		'first-row-headings' => '1',
		'sortable-headings' => '0',
		'filterable' => '0',
		'paginate' => '0'
	);

	//
	// Static Methods
	//

	// init

	public static function init() {

		// Call parent::init()

		parent::init( __CLASS__ );
	}

	//
	// Instance Methods
	//

	// Form

	public function widget_form( $instance ) {

		$table_data = isset( $instance[ $this->get_field_name('table-data') ] ) ? $instance[ $this->get_field_name('table-data') ] : $this->get_default('table-data');

		// Advanced Options
		$first_row_headings = isset( $instance[ $this->get_field_name('first-row-headings') ] ) ? $instance[ $this->get_field_name('first-row-headings') ] : $this->get_default('first-row-headings');
		$sortable_headings = isset( $instance[ $this->get_field_name('sortable-headings') ] ) ? $instance[ $this->get_field_name('sortable-headings') ] : $this->get_default('sortable-headings');
		$filterable = isset( $instance[ $this->get_field_name('filterable') ] ) ? $instance[ $this->get_field_name('filterable') ] : $this->get_default('filterable');
		$paginate = isset( $instance[ $this->get_field_name('paginate') ] ) ? $instance[ $this->get_field_name('paginate') ] : $this->get_default('paginate');

		$params = compact(
			'table_data',

			'show_title',
			'first_row_headings',
			'sortable_headings',
			'filterable',
			'paginate'
		);

		return $this->load_admin_widget_view( $instance, $params );
	}

	// Update

	public function widget_update( $new_instance, $old_instance ) {

		$instance = array();

		$instance[ $this->get_field_name('table-data') ] = $new_instance[ $this->get_field_name('table-data') ];

		// Advanced Options
		$instance[ $this->get_field_name('first-row-headings') ] = isset( $new_instance[ $this->get_field_name('first-row-headings') ] ) ? '1' : '0';
		$instance[ $this->get_field_name('sortable-headings') ] = isset( $new_instance[ $this->get_field_name('sortable-headings') ] ) ? '1' : '0';
		$instance[ $this->get_field_name('filterable') ] = isset( $new_instance[ $this->get_field_name('filterable') ] ) ? '1' : '0';
		$instance[ $this->get_field_name('paginate') ] = isset( $new_instance[ $this->get_field_name('paginate') ] ) ? '1' : '0';

		return $instance;
	}

	// Display

	public function widget_display( $instance ) {

		$table_data = isset( $instance[ $this->get_field_name('table-data') ] ) ? $instance[ $this->get_field_name('table-data') ] : $this->get_default('table-data');

		// Advanced Options
		$first_row_headings = isset( $instance[ $this->get_field_name('first-row-headings') ] ) ? $instance[ $this->get_field_name('first-row-headings') ] : $this->get_default('first-row-headings');
		$sortable_headings = isset( $instance[ $this->get_field_name('sortable-headings') ] ) ? $instance[ $this->get_field_name('sortable-headings') ] : $this->get_default('sortable-headings');
		$filterable = isset( $instance[ $this->get_field_name('filterable') ] ) ? $instance[ $this->get_field_name('filterable') ] : $this->get_default('filterable');
		$paginate = isset( $instance[ $this->get_field_name('paginate') ] ) ? $instance[ $this->get_field_name('paginate') ] : $this->get_default('paginate');

		$table_html = $this->data_to_html( $table_data, (bool)$first_row_headings, (bool)$sortable_headings, (bool)$filterable, (bool)$paginate );

		$params = compact(
			'table_html',

			'first_row_headings',
			'sortable_headings',
			'filterable',
			'paginate'
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

		return '';

	}

	//
	// Helpers
	//

	public function data_to_html( $data, $headings = true, $sortable = true, $filterable = false, $paginate = false ) {

		$data = json_decode( urldecode( $data ) );

		$data_attributes = array();

		if ( !$sortable ) { array_push( $data_attributes, 'data-sort="false"' ); }
		if ( !$filterable ) { array_push( $data_attributes, 'data-filter="false"' ); }
		if ( !$paginate ) { array_push( $data_attributes, 'data-page="false"' ); }

		$table_el = '<table class="es-cfct-table" '. implode( " ", $data_attributes ) .'>';

		$rows = $data->rows;
		//$rows = $data;

		foreach( $rows as $i => $row ) {

			if ( true == $headings && $i == 0 ) {
				$table_el .= '<thead>';
			}
			else if ( $i == 0 ) {
				$table_el .= '<tbody>';
			}

			$row_el = '<tr>';

			$cols = $row;

			foreach( $cols as $ii => $col ) {

				$content = $col->content;
				$html = $col->html;

				if ( ! $html ) {
					$content = esc_attr( $content );
				}

				if ( true == $headings && $i == 0 ) {
					$col_el = '<th>'. $content .'</th>';
				}
				else {
					$col_el = '<td>'. $content .'</td>';
				}

				$row_el .= $col_el;
			}

			$row_el .= '</tr>';

			$table_el .= $row_el;

			if ( true == $headings && $i == 0 ) {
				$table_el .= '</thead><tbody>';
			}
		}

		$table_el .= '</tbody></table>';

		return $table_el;
	}

}