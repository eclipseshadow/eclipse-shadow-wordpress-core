(function($){
	if ( 'undefined' == typeof ES_Tabs_Accordion ) {
		/**
		 * ES_Tabs_Accordion
		 *
		 * Interactive Javascript Table Builder
		 */
		var ES_Tabs_Accordion = function( dom_context, data ) {

			this.__construct( dom_context, data );

		};
		// Shortcut to Prototype Object
		var p = ES_Tabs_Accordion.prototype;
		// Shortcut to Static Object
		var s = ES_Tabs_Accordion;

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
			this.dom.dialog_parent = dom_context.find('.widget-inside, .cfct-module-edit-form');
			this.dom.form = this.dom.widget.find('form'); // Already in the DOM
			this.dom.tabs_container = this.dom.widget.find('.es_tabs_container'); // Already in the DOM
			this.dom.tabs_wrapper = $('<div class="es_tabs_wrapper"></div>').appendTo( this.dom.tabs_container );
			this.dom.tabs_link_container = $('<ul class="es_tabs_link_container"></ul>').appendTo( this.dom.tabs_wrapper );
			this.dom.tabs_content_container = $('<div class="es_tabs_content_container"></div>').appendTo( this.dom.tabs_wrapper );
			this.dom.data_input_el = this.dom.form.find('input.es-cfct-tabs-accordion-data');

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
			// Build Tabs from Data
			//
			this.data_to_html( data );

			// Wait until the last to append to DOM for better performance
			this.dom.tabs_container.append( this.dom.tabs_wrapper );

			//
			// UI Tabs/Accordion
			//
			this.dom.tabs_wrapper.tabs({
				keyboard: false
			});

			//
			// Add Tab Control
			//
			var controls = $('<div class="es_tabs_controls"></div>');

			$('<a class="es_tabs_add_row button-primary">Add Tab</a>').click(function( e ) {

				e.preventDefault();

				_this.dialog('add_tab');

			}).appendTo( controls );

			this.dom.tabs_container.prepend( controls );

			//
			// Tab Remove Buttons
			//
			this.dom.tabs_link_container.on('click', '.es_remove_tab_btn', function( e ) {

				e.preventDefault();

				var tab = $(this).closest('li');

				_this.remove_tab( tab );

				return false;
			});

			//
			// Tab Title Edit Buttons
			//
			this.dom.tabs_link_container.on('click', '.es_edit_tab_btn', function( e ) {

				e.preventDefault();

				var tab = $(this).closest('li');

				_this.edit_tab_title( tab );

				return false;
			});

			//
			// Sortable Tabs
			//
			this.initialize_sortable_tabs();

			//
			// Tab Content Buttons
			//

			// Add Tab Item

			this.dom.tabs_content_container.on('click', '.es_tab_content_add_item_btn', function( e ) {

				e.preventDefault();

				_this.dialog( 'add_tab_item' );

				return false;
			});

			// Remove Tab Item

			this.dom.tabs_content_container.on('click', '.es_tab_content_item_remove_btn', function( e ) {

				e.preventDefault();

				var link = $(this);

				var item = link.closest('.es_tab_content_item');

				_this.remove_tab_item( item );

				return false;
			});

			// Edit Tab Item

			this.dom.tabs_content_container.on('click', '.es_tab_content_item_edit_btn', function( e ) {

				e.preventDefault();

				var link = $(this);

				var item = link.closest('.es_tab_content_item');

				_this.dialog( 'configure_tab_item_'+ item.data('type'), { item : item } );

				return false;
			});

			// Sortable Tab Items

			this.initialize_sortable_tab_items();

			//
			// Advanced Options
			//
			new ES_Widget_Advanced_Options( this.dom.form.find('.es-widget-form-advanced-options') );
		};

		/**
		 * Add New Tab
		 */
		p.add_tab = function( tab_name, tab_content ) {

			var index = this.dom.tabs_link_container.find('> li').length;

			var tab_id = 'tab-'+ index;

			var tab_link_el = s.get_tab_link_template( tab_name, tab_id );

			var tab_content_el = s.get_tab_content_template( tab_content, tab_id );

			this.dom.tabs_link_container.append( tab_link_el );

			this.dom.tabs_content_container.append( tab_content_el );

			this.dom.tabs_wrapper.tabs('refresh');

			this.initialize_sortable_tab_items();
			this.initialize_sortable_tabs();

			this.set_values();

			return tab_link_el;
		};

		/**
		 * Remove Specified Tab
		 */
		p.remove_tab = function( tab_link_el ) {

			if ( ! confirm('Are you sure you want to delete this Tab?') ) return;

			var tab_content_el = this.dom.tabs_content_container.find( tab_link_el.find('a').attr('href') );

			tab_link_el.remove();
			tab_content_el.remove();

			this.dom.tabs_wrapper.tabs('refresh');

			this.initialize_sortable_tab_items();
			this.initialize_sortable_tabs();

			this.set_values();
		};

		/**
		 * Edit Tab Title
		 *
		 * @param tab
		 */
		p.edit_tab_title = function( tab ) {

			var _this = this;

			var tab_link = tab.find('a');
			tab_link.hide();

			var widget_click_handler = function( e ) {

				var clicked = $( e.toElement );

				if ( clicked.is('.ui-tabs-nav *') && ( clicked[0] == tab[0] || clicked.closest('li')[0] == tab[0] ) ) return; // Don't close when clicking in input

				_this.close_and_apply_tab_title( tab, widget_click_handler );
			};

			var input_el = $('<input type="text" value="'+ tab_link.text() +'" />');

			input_el.bind('keydown', function( e ) {

				var code = e.which;

				if ( code == 13 && !e.shiftKey ) { // Enter Key



					_this.close_and_apply_tab_title( tab, widget_click_handler );
				}
				else {
					e.stopPropagation();
				}
			});

			tab.append( input_el );

			// Set Focus & Move Cursor To End
			input_el.focus();
			tmpStr = input_el.val();
			input_el.val('');
			input_el.val(tmpStr);
			input_el.select();

			this.dom.widget.click( widget_click_handler );
		};

		p.close_and_apply_tab_title = function( tab, widget_click_handler ) {

			this.dom.widget.unbind('click', widget_click_handler );

			var link = tab.find('a');
			var input = tab.find('input');

			link.text( input.val()).show();
			input.remove();

			this.set_values();
		};

		/**
		 * Add New Content Item to Tab
		 */
		p.add_tab_item = function( data, item_to_replace ) {

			var active_tab = this.dom.tabs_content_container.find('div[aria-hidden="false"], div[aria-expanded="true"]');

			if ( active_tab.length > 1 ) throw 'ES Tabs - More than 1 "Active Tab" return in Method: add_tab_item()';

			var item_el = s.get_tab_item_template( data );

			if ( item_to_replace ) {
				item_to_replace.replaceWith( item_el );
			}
			else {
				var last_item = active_tab.find('> .es_tab_content_item').last();

				if ( last_item.length < 1 ) {
					active_tab.prepend( item_el );
				}
				else {
					item_el.insertAfter( last_item );
				}
			}

			this.initialize_sortable_tab_items();

			this.set_values();
		};

		p.remove_tab_item = function( item ) {

			if ( ! confirm('Are you sure you want to remove this item?') ) return;

			item.remove();

			this.initialize_sortable_tab_items();

			this.set_values();
		};

		p.initialize_sortable_tabs = function() {

			var _this = this;

			try {
				this.dom.tabs_link_container.sortable('refresh');
			}
			catch ( e ) {
				this.dom.tabs_link_container.sortable({
					axis : 'x',
					items : '> li',
					distance : 5,
					stop : function( e, ui ) {
						_this.dom.tabs_wrapper.tabs('refresh');

						// Reorder Content Panels Before Save

						var tabs = _this.dom.tabs_link_container.find('li');

						tabs.each(function( i, tab ) {

							tab = $(tab);

							tab_id = tab.find('a').attr('href');

							var matching_panel = _this.dom.tabs_content_container.find( tab_id );

							matching_panel.appendTo( _this.dom.tabs_content_container );
						});

						_this.set_values();
					}
				});
			}
		};

		p.initialize_sortable_tab_items = function() {

			var _this = this;

			try {
				this.dom.tabs_content_container.sortable('refresh');
			}
			catch ( e ) {
				this.dom.tabs_content_container.sortable({
					axis : 'y',
					items : '> .es_tab_content > .es_tab_content_item',
					distance : 5,
					stop : function( e, ui ) {
						_this.set_values();
					}
				});
			}
		};

		/**
		 * Convert Tabs to Data Structure
		 *
		 * @returns {Array}
		 */
		p.html_to_data = function() {

			var tab_link_els = this.dom.tabs_link_container.find('> li');
			var tab_content_els = this.dom.tabs_content_container.find('> div');

			var data = {};
			data.tabs = new Array();

			tab_link_els.each(function( i, tab_link_el ) {

				tab_link_el = $( tab_link_el );

				var tab_name = tab_link_el.find('a').text();

				data.tabs.push({ tab_name : tab_name });
			});

			tab_content_els.each(function( i, tab_content_el ) {

				tab_content_el = $( tab_content_el );

				var tab_items = new Array();
				var tab_item_els = tab_content_el.find('> .es_tab_content_item');

				tab_item_els.each(function( i, tab_item_el ) {

					tab_item_el = $(tab_item_el);

					var tab_item_data = s.get_item_data( tab_item_el );

					tab_items.push( tab_item_data );
				});

				data.tabs[ i ].tab_items = tab_items;
			});
			//console.log(data);
			return data;
		};

		/**
		 * Convert Data Structure to HTML & Apply to DOM
		 *
		 * @param data
		 */
		p.data_to_html = function( data ) {

			var tabs = data.tabs;

			var tab, tab_id, tab_link_el, tab_content_el, tab_item, tab_content;
			for ( var i in data.tabs ) {

				tab = tabs[ i ];

				tab_id = 'tab-'+ i;

				tab_content = $();

				for ( var ii in tab.tab_items ) {
					tab_item = tab.tab_items[ ii ];

					tab_content = tab_content.add( s.get_tab_item_template( tab_item ) );
				}

				tab_link_el = s.get_tab_link_template( tab.tab_name, tab_id );
				tab_content_el = s.get_tab_content_template( tab_content, tab_id );

				this.dom.tabs_link_container.append( tab_link_el );
				this.dom.tabs_content_container.append( tab_content_el );
			}
		};

		/**
		 * Set Instance Data Values & Form Element Value
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

		/**
		 * Get Dialog Markup Template Obj
		 *
		 * @returns {*|HTMLElement}
		 */
		p.dialog = function( dialog_type, data ) {

			var _this = this;

			switch( dialog_type ) {

				//
				// Add Tab
				//
				case 'add_tab':

					var template =  $('<div class="es_dialog" title="Add Tab">' +
						'<form>' +
						'<fieldset class="ui-helper-reset">' +
							'<label for="tab_name">Tab Name</label>' +
							'<input type="text" name="tab_name" id="tab_name" value="" class="widefat ui-widget-content ui-corner-all" />' +
						'</fieldset>' +
						'</form>').appendTo( document.body );

					// Add Button Callback

					var callback_add = function() {

						var tab_name = template.find('input[name="tab_name"]').val();

						var tab = _this.add_tab( tab_name );
						tab.find('a').trigger('click');
						template.dialog( "close" );
					};

					// Cancel Button Callback

					var callback_cancel = function() {

						$( this ).dialog( "close" );
					};

					// Dialog onClose Callback

					var callback_close = function() {

						template.find('form')[ 0 ].reset();
						template.remove();
					};

					// Form Submit Callback

					template.find( "form" ).submit(function( event ) {

						event.preventDefault();

						callback_add();
					});

					// Dialog

					template.dialog({
						autoOpen: true,
						width: _this.dom.dialog_parent.outerWidth(),
						height: _this.dom.dialog_parent.outerHeight(),
						modal: false,
						appendTo: _this.dom.dialog_parent,
						buttons: {
							Add: function() { callback_add(); },
							Cancel: callback_cancel
						},
						close: callback_close
					});
					break;

				//
				// Add Tab Item
				//
				case 'add_tab_item':

					var dialog;

					var template =  $(
						'<div class="es_dialog" title="Add Tab Item">'+
						'<form>'+
						'<fieldset class="ui-helper-reset">'+

						'<p>Choose the type of item you\'d like to add to your <strong>Tab</strong>.</p>'+

						'<div class="es_tab_item_options">'+
							'<a href="#" class="es_tab_item_option" data-item-type="rich_text">'+
								'<span class="es_icon es_icon_rich_text">'+
									'<span class="es_icon_inner"></span>'+
								'</span>'+
								'<span class="es_tab_item_option_label">Rich Text</span>'+
							'</a>'+
							'<a href="#" class="es_tab_item_option" data-item-type="raw_html">'+
								'<span class="es_icon es_icon_raw_html">'+
									'<span class="es_icon_inner"></span>'+
								'</span>'+
								'<span class="es_tab_item_option_label">Raw HTML/JS</span>'+
							'</a>'+
							'<a href="#" class="es_tab_item_option" data-item-type="widget_area">'+
								'<span class="es_icon es_icon_widget_area">'+
									'<span class="es_icon_inner"></span>'+
								'</span>'+
								'<span class="es_tab_item_option_label">Widget Area<br /><em>(sidebar)</em></span>'+
							'</a>'+
							'<a href="#" class="es_tab_item_option" data-item-type="server_page">'+
								'<span class="es_icon es_icon_callout">'+
									'<span class="es_icon_inner"></span>'+
								'</span>'+
								'<span class="es_tab_item_option_label">Server Page</span>'+
							'</a>'+
						'</div>'+

						'</fieldset>'+
						'</form>'+
						'</div>').appendTo( document.body );

					// Item Selection Link Events

					template.find('a').click(function( e ) {

						e.preventDefault();

						var link = $(this);
						var type = link.data('item-type');

						dialog.dialog( "close" );

						_this.dialog('configure_tab_item_'+ type);
					});

					// Cancel Button Callback

					var callback_cancel = function() {

						$( this ).dialog( "close" );
					};

					// Dialog onClose Callback

					var callback_close = function() {

						template.find('form')[ 0 ].reset();
						template.remove();
					};

					// Form Submit Callback

					template.find( "form" ).submit(function( event ) {

						event.preventDefault();
					});

					// Dialog

					dialog = template.dialog({
						width: _this.dom.dialog_parent.outerWidth(),
						height: _this.dom.dialog_parent.outerHeight(),
						resizable: false,
						autoOpen: true,
						modal: false,
						appendTo: _this.dom.dialog_parent,
						buttons: {
							Cancel: callback_cancel
						},
						close: callback_close
					});
					break;

				//
				// Configure Tab Item - Rich Text
				//
				case 'configure_tab_item_rich_text':

					var template = $(
						'<div class="es_dialog" title="Add Your Rich Text">'+
							'<form>'+
								'<fieldset class="ui-helper-reset">'+
									'<div class="es_rich_text_container"></div>'+
								'</fieldset>'+
							'</form>'+
						'</div>').appendTo( document.body );

					var editor;

					// Save Button Callback

					var callback_save = function() {

						// Grab TinyMCE Value & Save it in the item data
						var content = editor.get_editor().getContent();

						// Clean up empty <p> tags in the beginning of content
						if ( $.trim( $( content ).find('p').first().html()).length < 1 ) {
							content = content.replace(/<p><\/p>/, '');
						}

						if ( !data || !data.item ) {
							// New Item

							_this.add_tab_item( { type : 'rich_text', content : _.escape( content ) } );
						}
						else {
							// Editing Item

							_this.add_tab_item( { type : 'rich_text', content : _.escape( content ) }, data.item );
						}

						editor.destroy();
						$( this ).dialog( "close" );
					};

					// Cancel Button Callback

					var callback_cancel = function() {

						editor.destroy();
						$( this ).dialog( "close" );
					};

					// Dialog onOpen Callback

					var callback_open = function() {

						// Remove focus on all buttons within the
						// div with class ui-dialog
						$('.ui-dialog :button').blur();
					};

					// Dialog onClose Callback

					var callback_close = function() {

						template.find('form')[ 0 ].reset();
						template.remove();
					};

					// Form Submit Callback

					template.find( "form" ).submit(function( event ) {

						event.preventDefault();

						callback_save();
					});

					// Dialog

					template.dialog({
						width: _this.dom.dialog_parent.outerWidth(),
						height: _this.dom.dialog_parent.outerHeight(),
						resizable: true,
						autoOpen: true,
						modal: false,
						appendTo: _this.dom.dialog_parent,
						buttons: {
							Save: callback_save,
							Cancel: callback_cancel
						},
						open: callback_open,
						close: callback_close
					});

					editor = new ES_WP_Rich_Text_Editor({
						element : template.find('.es_rich_text_container'),
						setup : function( _editor ) {

							_editor.onInit.add(function( _ed, _args ) {

								if ( data && data.item ) {
									_ed.setContent( _.unescape( data.item.data('content') ) );
								}
							});
						}
					});

					break;

				//
				// Configure Tab Item - Raw HTML/JS
				//
				case 'configure_tab_item_raw_html':

					var template = $(
						'<div class="es_dialog" title="Add Your Raw HTML/JS">'+
							'<form>'+
								'<fieldset class="ui-helper-reset">'+
									'<div class="es_raw_html_container"></div>'+
								'</fieldset>'+
							'</form>'+
						'</div>').appendTo( document.body);

					var editor;

					// Save Button Callback

					var callback_save = function() {

						// Grab Ace Editor Value & Save it in the item data
						var content = editor.ace_editor.getValue();
						var mode = editor.ace_editor.getSession().getMode().$id.substring(9);

						if ( !data || !data.item ) {
							// New Item

							_this.add_tab_item( { type : 'raw_html', content : _.escape( content ), mode : mode } );
						}
						else {
							// Editing Item

							_this.add_tab_item( { type : 'raw_html', content : _.escape( content ), mode : mode }, data.item );
						}

						$( this ).dialog( "close" );
					};

					// Cancel Button Callback

					var callback_cancel = function() {

						$( this ).dialog( "close" );
					};

					// Dialog onOpen Callback

					var callback_open = function() {

						// Remove focus on all buttons within the
						// div with class ui-dialog
						$('.ui-dialog :button').blur();
					};

					// Dialog onClose Callback

					var callback_close = function() {

						template.find('form')[ 0 ].reset();
						template.remove();
						editor.destroy();
					};

					// Form Submit Callback

					template.find( "form" ).submit(function( event ) {

						event.preventDefault();

						callback_save();
					});

					// Dialog

					template.dialog({
						width: _this.dom.dialog_parent.outerWidth(),
						height: _this.dom.dialog_parent.outerHeight(),
						resizable: true,
						autoOpen: true,
						modal: false,
						appendTo: _this.dom.dialog_parent,
						buttons: {
							Save: callback_save,
							Cancel: callback_cancel
						},
						open: callback_open,
						close: callback_close
					});

					// Code Editor

					editor = new ES_ACE_Code_Editor({
						element : template.find('.es_raw_html_container'),
						width : '100%',
						height : 250,
						mode : 'html',
						auto_focus : false
					});

					// Set Code Editor Content on Edit

					if ( data && data.item ) {
						editor.ace_editor.setValue( _.unescape( data.item.data('content') ) );
						editor.set_mode( data.item.data('mode') );
					}
					break;

				//
				// Configure Tab Item - Widget Area
				//
				case 'configure_tab_item_widget_area':

					var template = $(
						'<div class="es_dialog" title="Select a Widget Area">'+
							'<form>'+
							'<fieldset class="ui-helper-reset">'+
							'<label for="ajax_url">Widget Area</label>' +
							'<select class="widefat" name="widget_area"></select>'+
							'</fieldset>'+
							'</form>'+
							'</div>').appendTo( document.body);

					// Save Button Callback

					var callback_save = function() {

						// Grab form input value and store it in the item data
						var widget_area_id = template.find('select[name="widget_area"]').val();

						var widget_area_name = template.find('select[name="widget_area"] > option:selected').text();

						if ( !data || !data.item ) {
							// New Item

							_this.add_tab_item( { type : 'widget_area', widget_area_name : widget_area_name, widget_area_id : widget_area_id } );
						}
						else {
							// Editing Item

							_this.add_tab_item( { type : 'widget_area', widget_area_name : widget_area_name, widget_area_id : widget_area_id }, data.item );
						}

						$( this ).dialog( "close" );
					};

					// Cancel Button Callback

					var callback_cancel = function() {

						$( this ).dialog( "close" );
					};

					// Dialog onClose Callback

					var callback_close = function() {

						template.find('form')[ 0 ].reset();
						template.remove();
					};

					// Form Submit Callback

					template.find( "form" ).submit(function( event ) {

						event.preventDefault();

						callback_save();
					});

					// Dialog

					template.dialog({
						width: _this.dom.dialog_parent.outerWidth(),
						height: _this.dom.dialog_parent.outerHeight(),
						resizable: true,
						autoOpen: true,
						modal: false,
						appendTo: _this.dom.dialog_parent,
						buttons: {
							Save: callback_save,
							Cancel: callback_cancel
						},
						close: callback_close
					});

					// Ajax Call - Get Registered Sidebars

					var ajax_data = {
						action : 'es_cfct_tabs_accordion_get_widget_areas'
					};

					// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
					$.post( ajaxurl, ajax_data, function( response ) {

						// On Ajax Complete, populate form

						var select_el = template.find('select[name="widget_area"]');

						var sidebar, sidebar_name, sidebar_id, sidebar_option_value;
						for ( var i in response.sidebars ) {
							sidebar = response.sidebars[ i ];

							sidebar_name = sidebar.name;
							sidebar_id = sidebar.id;

							sidebar_option_value = sidebar_id;

							select_el.append('<option value="'+ sidebar_option_value +'">'+ sidebar_name +'</option>');
						}

						// Set Form Values on Edit

						if ( data && data.item ) {
							var widget_area_name = data.item.data('widget_area_name');
							widget_area_name = widget_area_name.replace(/\w\S*/g, function(txt){return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();});

							var widget_area_id = data.item.data('widget_area_id');

							select_el.val( widget_area_id );
						}
					});
					break;

				//
				// Configure Tab Item - Server Page
				//
				case 'configure_tab_item_server_page':
					var template = $(
						'<div class="es_dialog" title="Specify Your Server Page">'+
							'<form>'+
							'<fieldset class="ui-helper-reset">'+
							'<label for="ajax_url">Server Page URL <em>(must be publicly accessible)</em></label><br />' +
							'<input type="text" name="server_page_url" id="server_page_url" value="" class="widefat ui-widget-content ui-corner-all" />' +
							'<br /><br /><input type="checkbox" name="load_via_ajax" id="load_via_ajax" class="ui-widget-content ui-corner-all" />' +
							'<label for="load_via_ajax"> Load Via Ajax</label>' +
							'</fieldset>'+
							'</form>'+
							'</div>').appendTo( document.body );

					// Save Button Callback

					var callback_save = function() {

						// Grab form input values and store it in the item data
						var server_page_url = template.find('input[name="server_page_url"]').val();

						var load_via_ajax = template.find('input[name="load_via_ajax"]').is(':checked');

						if ( !data || !data.item ) {
							// New Item

							_this.add_tab_item( { type : 'server_page', server_page_url : _.escape( server_page_url ), load_via_ajax : load_via_ajax } );
						}
						else {
							// Editing Item

							_this.add_tab_item( { type : 'server_page', server_page_url : _.escape( server_page_url ), load_via_ajax : load_via_ajax }, data.item );
						}

						$( this ).dialog( "close" );
					};

					// Cancel Button Callback

					var callback_cancel = function() {

						$( this ).dialog( "close" );
					};

					// Dialog onClose Callback

					var callback_close = function() {

						template.find('form')[ 0 ].reset();
						template.remove();
					};

					// Form Submit Callback

					template.find( "form" ).submit(function( event ) {

						event.preventDefault();

						callback_save();
					});

					// Dialog

					template.dialog({
						width: _this.dom.dialog_parent.outerWidth(),
						height: _this.dom.dialog_parent.outerHeight(),
						resizable: true,
						autoOpen: true,
						modal: false,
						appendTo: _this.dom.dialog_parent,
						buttons: {
							Save: callback_save,
							Cancel: callback_cancel
						},
						close: callback_close
					});

					// Set Form Values on Edit

					if ( data && data.item ) {
						template.find('input[name="server_page_url"]').val( _.unescape( data.item.data('server_page_url') ) );

						if ( data.item.data('load_via_ajax') ) {
							template.find('input[name="load_via_ajax"]').prop('checked', true);;
						}
					}
					break;
			}
		};

		/** Static Methods */

		/**
		 * Get Tab Link Markup Template Obj
		 *
		 * @returns {*|HTMLElement}
		 */
		s.get_tab_link_template = function( tab_name, tab_id ) {

			if ( ! tab_name ) {
				tab_name = 'Tab';
			}

			var el = $('<li><a href="#'+ tab_id +'">'+ tab_name +'</a> <span class="es_edit_tab_btn ui-icon ui-icon-pencil" role="presentation">Edit Tab Title</span> <span class="es_remove_tab_btn ui-icon ui-icon-close" role="presentation">Remove Tab</span></li>');

			return el;
		};

		/**
		 * Get Tab Content Markup Template Obj
		 *
		 * @returns {*|HTMLElement}
		 */
		s.get_tab_content_template = function( tab_content, tab_id ) {

			if ( ! tab_content ) {
				tab_content = '';
			}

			var el = $('<div class="es_tab_content" id="'+ tab_id +'"><div class="es_tab_content_controls"><a href="#" class="es_tab_content_add_item_btn button-primary">+ Add Item</a></div></div>');
			el.prepend( tab_content );

			return el;
		};

		/**
		 * Get Tab Content Item Markup Template Obj
		 *
		 * @returns {*|HTMLElement}
		 */
		s.get_tab_item_template = function( data ) {

			var el = null;

			if ( !data ) return el;

			switch( data.type ) {

				//
				// Rich Text
				//
				case 'rich_text':

					var content = _.unescape( data.content ); // Always Escaped

					// Strip Tags (if need be) from URL for Preview
					var content_preview = $('<span>'+ content +'</span>').text();

					var myReg = /\[.+\]/g;
					content_preview = content_preview.replace(myReg, '');

					if ( content_preview.length > 30 ) {
						content_preview = content_preview.substring( 0, 50 ) + '...';
					}

					// Element
					el = $('<div class="es_tab_content_item clearfix"></div>');

					// Preview
					$('<div class="es_tab_content_item_preview"><span class="es_tab_content_item_preview_label">Rich Text: </span>'+ content_preview +'</div>').appendTo( el );

					// Controls
					$('<div class="es_tab_content_item_controls"><a href="" class="es_tab_content_item_edit_btn">Edit</a><a href="#" class="es_tab_content_item_remove_btn">Remove</a></div>').prependTo( el );

					// Data
					el.attr('data-type', data.type );
					el.attr('data-content', _.escape( content ) );
					break;

				//
				// Raw HTML/JS
				//
				case 'raw_html':

					var content = _.unescape( data.content ); // Always Escaped
					var mode = data.mode;

					var content_preview = _.escape( content );

					if ( content_preview.length > 30 ) {
						content_preview = content_preview.substring( 0, 50 ) + '...';
					}

					// Element
					el = $('<div class="es_tab_content_item clearfix"></div>');

					// Preview
					$('<div class="es_tab_content_item_preview"><span class="es_tab_content_item_preview_label">Raw '+ (mode == 'html' ? 'HTML' : 'Javascript') +': </span>'+ content_preview +'</div>').appendTo( el );

					// Controls
					$('<div class="es_tab_content_item_controls"><a href="" class="es_tab_content_item_edit_btn">Edit</a><a href="#" class="es_tab_content_item_remove_btn">Remove</a></div>').prependTo( el );

					// Data
					el.attr('data-type', data.type );
					el.attr('data-content', _.escape( content ) );
					el.attr('data-mode', mode );
					break;

				//
				// Widget Area
				//
				case 'widget_area':

					var widget_area_name = data.widget_area_name; // Never Escaped
					var widget_area_id = data.widget_area_id; // Never Escaped

					widget_area_name = widget_area_name.replace(/\w\S*/g, function(txt){return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();});

					var content_preview = widget_area_name;

					if ( content_preview.length > 30 ) {
						content_preview = content_preview.substring( 0, 50 ) + '...';
					}

					// Element
					el = $('<div class="es_tab_content_item clearfix"></div>');

					// Preview
					$('<div class="es_tab_content_item_preview"><span class="es_tab_content_item_preview_label">Widget Area: </span>'+ content_preview +'</div>').appendTo( el );

					// Controls
					$('<div class="es_tab_content_item_controls"><a href="" class="es_tab_content_item_edit_btn">Edit</a><a href="#" class="es_tab_content_item_remove_btn">Remove</a></div>').prependTo( el );

					// Data
					el.attr('data-type', data.type );
					el.attr('data-widget_area_name', widget_area_name );
					el.attr('data-widget_area_id', widget_area_id );
					break;

				//
				// Server Page
				//
				case 'server_page':

					var server_page_url = _.unescape( data.server_page_url ); // Always Escaped
					var load_via_ajax = data.load_via_ajax; // Never Escaped

					// Strip Tags (if need be) from URL for Preview
					var content_preview = $('<span>'+ server_page_url +'</span>').text();

					if ( content_preview.length > 30 ) {
						content_preview = content_preview.substring( 0, 50 ) + '...';
					}

					if ( load_via_ajax ) {
						content_preview += '<strong> - Ajax: True</strong>';
					}

					// Element
					el = $('<div class="es_tab_content_item clearfix"></div>');

					// Preview
					$('<div class="es_tab_content_item_preview"><span class="es_tab_content_item_preview_label">Server Page: </span>'+ content_preview +'</div>').appendTo( el );

					// Controls
					$('<div class="es_tab_content_item_controls"><a href="" class="es_tab_content_item_edit_btn">Edit</a><a href="#" class="es_tab_content_item_remove_btn">Remove</a></div>').prependTo( el );

					// Data
					el.attr('data-type', data.type );
					el.attr('data-server_page_url', _.escape( server_page_url ) );
					el.attr('data-load_via_ajax', load_via_ajax );
					break;
			}

			return el;
		}

		/**
		 * Get Blank Tab Data for "New Tab"
		 *
		 * @returns {Array}
		 */
		s.get_blank_data = function() {

			var num_tabs = 1;
			var data = {};
			data.tabs = new Array();

			for ( var i=0; i<num_tabs; i++ ) {

				data.tabs.push( { tab_name : 'Tab', tab_items : new Array() } );
			}

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
		 * Retrieves Desired Data from Tab Item for Storage
		 *
		 * @param item
		 * @returns {*}
		 */
		s.get_item_data = function( item ) {

			item = $( item );

			var data = item.data();

			for ( var key in data ) {

				if ( ! item.is('[data-'+ key +']') ) {
					delete data[ key ];
				}
			}

			return data;
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
	window.ES_Tabs_Accordion = ES_Tabs_Accordion;

})(jQuery);