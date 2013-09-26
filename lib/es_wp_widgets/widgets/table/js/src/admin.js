(function($){
	if ( 'undefined' == typeof ES_Table ) {
		/**
		 * ES_Table
		 *
		 * Interactive Javascript Table Builder
		 */
		var ES_Table = function( dom_context, data ) {

			this.__construct( dom_context, data );

		};
		// Shortcut to Prototype Object
		var p = ES_Table.prototype;
		// Shortcut to Static Object
		var s = ES_Table;

		//
		// Properties
		//

		// Instance Properties
		p.dom = null;
		p.data = null;
		p.str_data = '';

		// Static Properties
		//...

		//
		// Methods
		//

		/** Instance Methods */

		/**
		 * Constructor
		 */
		p.__construct = function( dom_context, data ) {

			var _this = this; // Only need this for accessing instance inside of an event handler function
			dom_context = $( dom_context );

			//
			// Assign Instance Properties
			//
			this.dom = {};

			this.dom.widget = dom_context; // Already in the DOM
			this.dom.form = this.dom.widget.find('form'); // Already in the DOM
			this.dom.table_container = this.dom.widget.find('.es_table_container'); // Already in the DOM
			this.dom.table_wrapper = $('<div class="es_table_wrapper"></div>');
			this.dom.table = $('<table class="es_table"></table>');
				this.dom.table.appendTo( this.dom.table_wrapper );
			this.dom.tbody = $('<tbody></tbody>');
				this.dom.tbody.appendTo( this.dom.table );
			this.dom.keyboard_shortcuts_link = this.dom.form.find('.es-cfct-keyboard-shortcuts-link');
			this.dom.keyboard_shortcuts = this.dom.form.find('.es-cfct-keyboard-shortcuts');

			this.dom.data_input_el = this.dom.form.find('input.es-cfct-table-data');

			// * We always wait as long as possible to attach items to the DOM because it's less expensive
			//   to operate on them when they're detached

			if ( 'object' != typeof data || s.get_object_size( data ) < 1 ) {
				data = s.decode( this.dom.data_input_el.val() );
			}

			if ( null == data ) {
				data = s.get_blank_data();
			}

			// Set instance data values
			this.set_values( data, false );

			//
			// Sortable Rows
			//
			this.dom.tbody.sortable({

				axis : 'y',

				helper : function( e, ui ) {

					// Fix Row Collapse Issue
					ui.children().each(function() {
						$(this).width($(this).width());
					});

					return ui;
				},
				start : function( e, ui ) {

					// Fix Placeholder Height Issue
					ui.placeholder.height( $( ui.item[0] ).height() );

					_this.remove_remove_buttons();
					_this.remove_column_move_buttons();
				},
				stop : function( e, ui ) {

					_this.attach_remove_buttons();
					_this.attach_column_move_buttons();
					_this.set_values();

					s.highlight( $( ui.item[0] ).find('td') );
				}
			});

			//
			// Build Table HTML from Data
			//
			this.data_to_html( data );

			//
			// Row/Col Remove Buttons
			//
			this.dom.tbody.on('click', '.es_row_remove_btn', function( e ) {

				e.preventDefault();

				var row = $(this).closest('tr');

				_this.remove_row( row );

				return false;
			});

			this.dom.tbody.on('click', '.es_col_remove_btn', function( e ) {

				e.preventDefault();

				_this.remove_column( $(this) );

				return false;
			});

			this.attach_remove_buttons();

			//
			// Col Move Buttons
			//
			this.dom.tbody.on('click', '.es_col_move_left_btn', function( e ) {

				e.preventDefault();

				var td = $(this).closest('td');

				_this.move_column( td, true );

				return false;
			});

			this.dom.tbody.on('click', '.es_col_move_right_btn', function( e ) {

				e.preventDefault();

				var td = $(this).closest('td');

				_this.move_column( td, false );

				return false;
			});

			this.attach_column_move_buttons();

			//
			// Activate Edit Mode when clicking on Table Cell
			//
			$( this.dom.tbody ).on('click', 'td, th', function( e ) {

				var cell = $(this);

				if ( cell.hasClass('edit_active') ) {
					e.stopPropagation();
					return;
				}

				_this.close_and_apply_inputs();

				_this.begin_cell_edit( this );

				return false;
			});

			//
			// Keyboard Shortcuts - Traverse through Table Cells, Exit Cell, Move Row, Add Row/Col
			//
			$( this.dom.tbody ).on('keydown', 'textarea', function( e ) {

				var textarea = $(this);

				var code = e.which;

				if ( code == '9' ) { // TAB Key (w/ & w/o Shift)

					e.preventDefault();
					e.stopPropagation();

					var textarea = $(this);
					var td = textarea.closest('td');

					if ( e.shiftKey ) {
						_this.next_cell( td, true );
					}
					else {
						_this.next_cell( td, false );
					}
				}
				else if ( code == 13 && !e.shiftKey ) { // ENTER Key

					e.preventDefault();
					e.stopPropagation();

					_this.close_and_apply_inputs();
				}
				else if ( e.altKey && code == 38 ) { // Alt -> Up Arrow - Move Row

					e.preventDefault();
					e.stopPropagation();

					var row = textarea.closest('tr');
					var td = textarea.closest('td');

					_this.move_row( row, true );

					_this.set_cell_focus( td );
				}
				else if ( e.altKey && code == 40 ) { // Alt -> Down Arrow - Move Row

					e.preventDefault();
					e.stopPropagation();

					var row = textarea.closest('tr');
					var td = textarea.closest('td');

					_this.move_row( row, false );

					_this.set_cell_focus( td );
				}
				else if ( e.altKey && code == 82 ) { // Alt -> R - New Row

					e.preventDefault();
					e.stopPropagation();

					_this.add_row();
				}
				else if ( e.altKey && code == 67 ) { // Alt -> C - New Col

					e.preventDefault();
					e.stopPropagation();

					_this.add_column();
				}
				else if ( e.altKey && code == 37 ) { // Alt -> Left Arrow - Move Col

					e.preventDefault();
					e.stopPropagation();

					var td = textarea.closest('td');

					_this.move_column( td, true );

					_this.set_cell_focus( td );
				}
				else if ( e.altKey && code == 39 ) { // Alt -> Right Arrow - Move Col

					e.preventDefault();
					e.stopPropagation();

					var td = textarea.closest('td');

					_this.move_column( td, false );

					_this.set_cell_focus( td );
				}
				else if ( e.altKey && code == 8 ) { // Alt -> Delete - Delete Row

					e.preventDefault();
					e.stopPropagation();

					var row = textarea.closest('tr');

					_this.remove_row( row, true );
				}
				// No Shortcut for Remove Column. Couldn't think of an intuitive one.
			});

			//
			// Close all textareas when clicking anything other than the input it_this
			//
			this.dom.table_container.closest('.cfct-popup-content, .widget-inside').click(function( e ) {

				var clicked = $( e.toElement );

				if ( clicked.is('.es_table_add_row, .es_table_add_col') ) return; // Add/Remove Row buttons close & apply automatically

				_this.close_and_apply_inputs();
			});

			//
			// Add Row/Col Controls
			//
			var controls = $('<div class="es_table_controls"></div>');

			$('<a class="es_table_add_row button-primary">Add Row</a>').click(function( e ) {

				e.preventDefault();

				_this.add_row();

			}).appendTo( controls );

			$('<a class="es_table_add_col button-primary">Add Column</a>').click(function( e ) {

				e.preventDefault();

				_this.add_column();

			}).appendTo( controls );

			this.dom.table_container.prepend( controls );

			//
			// Advanced Options
			//
			new ES_Widget_Advanced_Options( this.dom.form.find('.es-widget-form-advanced-options'), { auto_open : true } );

			// Show/Hide Keyboard Shortcuts

			this.dom.keyboard_shortcuts_link.click(function( e ) {

				e.preventDefault();

				var link = $(this);

				if ( _this.dom.keyboard_shortcuts.is(':visible') ) {
					link.text('Show Keyboard Shortcuts');
					_this.dom.keyboard_shortcuts.hide();
				}
				else {
					link.text('Hide Keyboard Shortcuts');
					_this.dom.keyboard_shortcuts.show();
				}
			});

			//
			// Form Input Value Watcher
			//
			var watcher = new ES_Widget_Options_Watcher( dom_context );

			// Toggle Table Headings - Visually witch <td>'s to <th>'s when selecting "Use first row as Table Headings" option
			watcher.subscribe( '.es-cfct-table-first-row-headings', function( e, value ) {

				_this.toggle_first_row_headings( $(this) );
			});

			watcher.trigger_all();

			//
			// Wait until the last to append to DOM for better performance
			//
			this.dom.table_container.append( this.dom.table_wrapper );
		};

		/**
		 * Add New Row to Table
 		 */
		p.add_row = function() {

			var cols = this.dom.tbody.find('> tr').first().find('td');

			var num_cols = cols.length;

			var row_el = s.get_row_template( num_cols );

			this.dom.tbody.append( row_el );

			this.reattach_remove_buttons();

			var td = row_el.find('td:first-child');

			this.close_and_apply_inputs(); // Calls this.set_values()

			this.begin_cell_edit( td );

			s.highlight( row_el.find('td') );
		};

		/**
		 * Remove Specified Row
 		 */
		p.remove_row = function( row, refocus ) {

			var index = row.index();

			var row_to_refocus = row.prev('tr');

			if ( index == 0 ) return;

			if ( confirm("Are you sure you want to remove this Row?") ) {
				var row = this.dom.tbody.find('> tr')[ index ];
				$(row).remove();

				this.reattach_remove_buttons();

				this.set_values();
			}

			if ( 'undefined' == typeof refocus ) {
				refocus = false;
			}

			if ( refocus ) {
				this.begin_cell_edit( row_to_refocus.find('td:first-child') );
			}
		};

		/**
		 * Moves a row up or down (useful for keyboard shortcuts)
		 *
		 * @param row
		 * @param up
		 */
		p.move_row = function( row, up ) {

			this.remove_remove_buttons();
			this.remove_column_move_buttons();

			row = $( row );

			if ( 'undefined' == typeof up ) {
				up = false;
			}

			if ( up ) {

				if ( row.is(':first-child') ) {
					row.appendTo( this.dom.tbody );
				}
				else {
					row.insertBefore( row.prev('tr') );
				}
			}
			else {
				if ( row.is(':last-child') ) {
					row.prependTo( this.dom.tbody );
				}
				else {
					row.insertAfter( row.next('tr') );
				}
			}

			this.attach_remove_buttons();
			this.attach_column_move_buttons();

			this.set_values();

			s.highlight( row.find('td') );
		};

		/**
		 * Add New Column to Table
		 */
		p.add_column = function() {

			var rows = this.dom.tbody.find('> tr');

			var th = false;
			if ( rows.first().find('td').length < 1 ) {
				th = true;
			}

			var col_el, cols = $();
			rows.each(function( i, row ) {

				row = $(row);

				if ( i > 0 ) {
					th = false;
				}

				col_el = s.get_col_template( null, th );

				cols = cols.add( col_el );

				row.append( col_el );
			});

			this.reattach_remove_buttons();
			this.reattach_column_move_buttons();

			var td = this.dom.tbody.find('tr:first-child td:last-child');

			this.close_and_apply_inputs(); // Calls this.set_values()

			this.begin_cell_edit( td );

			s.highlight( cols );
		};

		/**
		 * Remove Specified Column
		 *
		 * @param link
		 * @returns {boolean}
		 */
		p.remove_column = function( link ) {

			var index = link.closest('td').index();

			if ( index == 0 ) return;

			if ( confirm("Are you sure you want to remove this Column?") ) {
				var rows = this.dom.tbody.find('> tr');

				rows.each(function( i, row ) {

					row = $(row);

					var col = row.find('td')[ index ];
					$(col).remove();
				});

				this.reattach_remove_buttons();

				this.set_values();
			}

			return false;
		};

		p.move_column = function( td, left ) {

			this.remove_remove_buttons();

			td = $( td ); // Any <td> will do

			if ( 'undefined' == typeof left ) {
				left = false;
			}

			var index = td.index();

			var is_first_child = td.is(':first-child');
			var is_last_child = td.is(':last-child');

			var rows = this.dom.tbody.find('tr');
			var cols = $();

			rows.each(function( i, row ) {

				row = $(row);

				var td = $( row.find('td')[ index ] );
				cols = cols.add( td );

				if ( left ) {

					if ( is_first_child ) {
						td.appendTo( row );
					}
					else {
						td.insertBefore( td.prev('td') );
					}
				}
				else {
					if ( is_last_child ) {
						td.prependTo( row );
					}
					else {
						td.insertAfter( td.next('td') );
					}
				}
			});

			this.attach_remove_buttons();

			this.set_values();

			s.highlight( cols );
		};

		/**
		 * Visually switch <td>'s to <th>'s when selecting "Use first row as Table Headings" option
		 *
		 * @param chkbox
		 */
		p.toggle_first_row_headings = function( chkbox ) {

			if ( chkbox.is(':checked') ) {
				this.dom.table.addClass('es_table_has_headings');
			}
			else {
				this.dom.table.removeClass('es_table_has_headings');
			}
		};

		/**
		 * Helper - Remove & Reattach Row & Col Remove Buttons to Table
		 */
		p.reattach_remove_buttons = function() {

			this.remove_remove_buttons();
			this.attach_remove_buttons();
		};

		/**
		 * Remove Row & Col Remove Buttons to Table
		 */
		p.remove_remove_buttons = function() {

			this.dom.tbody.find('.es_row_remove_btn, .es_col_remove_btn').remove();
		};

		/**
		 * Attach Row & Col Remove Buttons to Table
		 */
		p.attach_remove_buttons = function() {

			var rows = this.dom.tbody.find('> tr');

			rows.each(function( i, row ) {

				row = $(row);

				var cols = row.find('td, th');

				// Don't Do First Row
				if ( i > 0 ) {
					cols.last().append('<a class="es_row_remove_btn" title="Click to remove this Row"></a>');
				}

				// Last Row - Do all cols except first
				if ( i == rows.length-1 ) {

					cols.each(function( ii, col ) {

						col = $(col);

						// Don't do First Col
						if ( ii > 0 ) {
							col.append('<a class="es_col_remove_btn" title="Click to remove this Column"></a>');
						}
					});
				}
			});
		};

		/**
		 * Helper - Remove & Reattach Col Move Buttons to Table
		 */
		p.reattach_column_move_buttons = function() {

			this.remove_column_move_buttons();
			this.attach_column_move_buttons();
		};

		/**
		 * Remove Row Col Move Buttons to Table
		 */
		p.remove_column_move_buttons = function() {

			this.dom.tbody.find('.es_col_move_btn').remove();
		};

		/**
		 * Attach Col Move Buttons to Table
		 */
		p.attach_column_move_buttons = function() {

			var cells = this.dom.tbody.find('> tr:first-child td');

			cells.each(function( i, cell ) {

				cell = $(cell);

				cell.append('<a class="es_col_move_btn es_col_move_left_btn" title="Click to move this column Left"></a>');
				cell.append('<a class="es_col_move_btn es_col_move_right_btn" title="Click to move this column Right"></a>');
			});
		};

		/**
		 * Convert Table to Data Structure
		 *
		 * @returns {Array}
		 */
		p.html_to_data = function() {

			var rows = new Array();

			this.dom.tbody.find('> tr').each(function( i, row ) {

				row = $(row);

				var cols = new Array();

				row.find('> td').each(function( ii, col ) {

					col = $(col);

					var content = col.find('.col_content').html();
					var html = col.find('input').is(':checked');

					cols.push({ content: content, html: html });
				});

				rows.push( cols );
			});

			var data = {};
			data.rows = rows;

			return data;
		};

		/**
		 * Convert Data Structure to HTML & Apply to DOM
		 *
		 * @param data
		 */
		p.data_to_html = function( data ) {

			var rows = data.rows;

			var cols, col, content, html, row_el, col_el;
			for ( var i in rows ) {

				row_el = $('<tr></tr>');

				cols = rows[ i ];

				for ( var ii in cols ) {

					col = cols[ ii ];

					content = col.content;
					html = col.html;

					col_el = s.get_col_template( content, html );

					row_el.append( col_el );
				}

				this.dom.tbody.append( row_el );
			}
		};

		/**
		 * Close all <textarea>'s, show Cell Content & Save Data
		 */
		p.close_and_apply_inputs = function() {

			var inputs = this.dom.tbody.find('td textarea');
			var html_chk_lbls = this.dom.tbody.find('td label');
			var content_els = this.dom.tbody.find('.col_content');

			inputs.each(function( i, input ) {

				var input_el = $(input);
				var html_chk = input_el.siblings('label').find('input');
				var content_el = input_el.siblings('.col_content');

				var td = input_el.closest('td');

				if ( !td.hasClass('edit_active') ) {
					return;
				}

				td.removeClass('edit_active');

				var value = input_el.val();

				if ( html_chk.is(':checked') ) {
					content_el.html( value );
				}
				else {
					content_el.html( _.escape( value ) );
				}

				input_el.hide();

				s.highlight( td );
			});

			html_chk_lbls.hide();
			content_els.show();

			this.set_values();
		};

		p.begin_cell_edit = function( td ) {

			td = $( td );

			td.addClass('edit_active');

			var content_el = td.find('.col_content');
			var input_el = td.find('textarea');
			var html_chk_lbl = td.find('label');

			content_el.hide();
			input_el.show();
			html_chk_lbl.show();

			// Set Focus & Move Cursor To End
			this.set_cell_focus( td );
		};

		p.set_cell_focus = function( td ) {

			var input_el = td.find('textarea');

			// Set Focus & Move Cursor To End
			input_el.focus();
			tmpStr = input_el.val();
			input_el.val('');
			input_el.val(tmpStr);
			input_el.select();
		};

		/**
		 * Given the current cell, this method will find the next or previous
		 * cell and set that one to active edit
		 *
		 * @param td
		 * @param reverse
		 */
		p.next_cell = function( td, reverse ) {

			if ( 'undefined' == typeof reverse ) {
				reverse = false;
			}

			var next_cell;

			if ( reverse ) {

				// Reverse Direction

				if ( td.is(':first-child') ) {

					// Prev Row or Back to Bottom

					var row = td.closest('tr');

					if ( row.is(':first-child') ) {

						// Back to Bottom

						next_cell = this.dom.tbody.find('tr:last-child td:last-child');
					}
					else {
						// Next Row

						next_cell = row.prev('tr').find('td:last-child');
					}
				}
				else {
					// Next Cell

					next_cell = td.prev('td');
				}
			}
			else {

				// Traverse Forward

				if ( td.is(':last-child') ) {

					// Next Row or Back to Top

					var row = td.closest('tr');

					if ( row.is(':last-child') ) {

						// Back to Top

						next_cell = this.dom.tbody.find('tr:first-child td:first-child');
					}
					else {
						// Next Row

						next_cell = row.next('tr').find('td:first-child');
					}
				}
				else {
					// Next Cell

					next_cell = td.next('td');
				}
			}

			this.close_and_apply_inputs();

			this.begin_cell_edit( next_cell );
		};

		/**
		 * Set Instance Data Values & Table Data Form Element Value
		 *
		 * @param data
		 * @param set_field_value
		 */
		p.set_values = function( data, set_field_value ) {

			if ( 'undefined' == typeof data || !data ) {
				data = this.html_to_data();
			}

			if ( 'undefined' == typeof set_field_value ) {
				set_field_value = true;
			}

			this.data = data;
			this.str_data = s.encode( data );

			if ( true === set_field_value ) {
				this.dom.data_input_el.val( this.str_data );
			}
		};

		/** Static Methods */

		/**
		 * Get Row Markup Template Obj
		 *
		 * @param num_cols
		 * @param th
		 * @returns {*|HTMLElement}
		 */
		s.get_row_template = function( num_cols ) {

			var el = $('<tr></tr>');

			for ( var i=0; i<num_cols; i++ ) {
				el.append( s.get_col_template( null ) );
			}

			return el;
		};

		/**
		 * Get Column Markup Template Obj
		 *
		 * @param value
		 * @param th
		 * @returns {*}
		 */
		s.get_col_template = function( value, html ) {

			html = 'undefined' == typeof html ? false : html;

			if ( 'undefined' == typeof value || !value ) {
				value = 'Click to Edit...';
			}

			var el = $('<td></td>')
				.append('<div class="col_content">'+ value +'</div>')
				.append('<textarea>'+ value +'</textarea>')
				.append('<label><input type="checkbox" '+ (html ? 'checked="checked"' : '') +' /> HTML?</label>');

			return el;
		};

		/**
		 * Get Blank Table Data for "New Table"
		 *
		 * @returns {Array}
		 */
		s.get_blank_data = function() {

			var num_rows = 2;
			var num_cols = 2;
			var data = {};

			var rows = new Array(), cols, col_content;
			for ( var i=0; i<num_rows; i++  ) {

				cols = new Array();

				for ( var ii=0; ii<num_cols; ii++ ) {

					col_content = 'Click to Edit...';

					cols.push( col_content );
				}

				rows.push( cols );
			}

			data.rows = rows;

			return data;
		};

		/**
		 * Measure size of obj (assoc array)
		 *
		 * @param obj
		 * @returns {number}
		 */
		s.get_object_size = function( obj ) {

			var size = 0, key;
			for (key in obj) {
				if (obj.hasOwnProperty(key)) size++;
			}

			return size;
		};

		/**
		 * Encode Data to JSON w/ HTML Entities
		 *
		 * @param data
		 * @returns {string}
		 */
		s.encode = function( data ) {

			try {
				return encodeURIComponent( JSON.stringify( data ) );
			}
			catch ( e ) {
				return null;
			}
		};

		/**
		 * Decode Data from JSON w/ HTML Entities
		 *
		 * @param data
		 * @returns {*}
		 */
		s.decode = function( data ) {

			try {
				return JSON.parse( decodeURIComponent( data ) );
			}
			catch ( e ) {
				return null;
			}
		};

		/**
		 * Highlights an element using jQuery UI Highlight
		 *
		 * @param element
		 */
		s.highlight = function( element ) {

			element.attr('style', function( i, style ) {

				if ( !style ) return;

				return style.replace(/background-color[^;]+;?/g, '');
			});

			element.stop().effect("highlight", { duration : 1000, color : 'rgba(254, 254, 160, 0.51)' });
		};

	}

	// Reveal to outside world
	window.ES_Table = ES_Table;

})(jQuery);