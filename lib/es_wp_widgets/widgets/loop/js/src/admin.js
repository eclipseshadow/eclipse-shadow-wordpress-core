(function($){
	if ( 'undefined' == typeof ES_Loop ) {
		/**
		 * ES_Loop
		 *
		 * Post Type Loop
		 * Options: Filter Relation (and/or), Show (titles, titles + excerpt, titles + content), Num Items, Start Offset, Paginated (link text/num - custom link text)
		 *
		 * @todo Proper height placeholder while filters are loading to avoid page jump
		 * @todo Date Range filter
		 */
		var ES_Loop = function( dom_context, data ) {

			this.__construct( dom_context, data );

		};
		// Shortcut to Prototype Object
		var p = ES_Loop.prototype;
		// Shortcut to Static Object
		var s = ES_Loop;

		//
		// Properties
		//

		//-- Instance Properties
		p.dom = null;
		p.options = null;
		p.watcher = null;
		p.is_initializing = false;

		p.available_post_types = null;
		p.available_taxonomies = null;
		p.available_authors = null;
		p.available_post_statuses = null;

		// Data
		p.data = null;
		p.str_data = '';

		p.filters = null;
		p.post_type = 'post';
		p.custom_template_markup = '';

		//-- Static Properties
		s.default_options = {};

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
			this.dom.dialog_parent = dom_context.find('.widget-inside, .cfct-module-edit-form');
			this.dom.container_loop = this.dom.form.find('.es-loop-container');
			this.dom.container_loop_filters = this.dom.form.find('.es-loop-filters-container');

			this.dom.btn_add_filter = this.dom.form.find('.es-loop-add-filter-btn');
			this.dom.btn_custom_template_markup = this.dom.form.find('.es-custom-template-markup-btn');

			this.dom.input_loop_data = this.dom.form.find('.es-loop-data');
			this.dom.input_post_type = this.dom.form.find('.es-loop-post-type');
			this.dom.input_custom_template_markup = this.dom.form.find('.es-loop-custom-template-markup');

			//
			// Initialize Instance Properties (JS always passes objects by reference, so we need fresh objects)
			//
			this.available_post_types = new Array();
			this.available_taxonomies = new Array();
			this.available_authors = new Array();
			this.available_post_statuses = new Array();

			this.filters = new Array();

			//
			// Set Current Data Values
			//
			var form_data = this.dom.input_loop_data.val();

			if ( $.trim( form_data ).length > 0 ) {
				this.set_values( JSON.parse( _.unescape( form_data ) ), false );
			}

			this.set_custom_template_markup( this.dom.input_custom_template_markup.val(), false );

			//
			// Retrieve Available Post Types, Taxonomies, Authors & Post Statuses
			//
			this.get_wp_data();

			//
			// Add Filter
			//
			this.dom.btn_add_filter.click(function( e ) {

				e.preventDefault();

				_this.dialog('select_filter_type');
			});

			//
			// Edit Filter
			//
			this.dom.container_loop_filters.on('click', '.es_loop_filter_edit_btn', function( e ) {

				e.preventDefault();

				var link = $(this);

				var filter = link.closest('.es_loop_filter');

				_this.dialog( 'configure_filter_'+ filter.data('type'), { filter : filter } );

				return false;
			});

			//
			// Remove Filter
			//
			this.dom.container_loop_filters.on('click', '.es_loop_filter_remove_btn', function( e ) {

				e.preventDefault();

				var link = $(this);

				var filter = link.closest('.es_loop_filter');

				_this.remove_filter( filter.index() );

				return false;
			});

			//
			// Set Custom Template Markup Button
			//
			this.dom.btn_custom_template_markup.click(function( e ) {

				e.preventDefault();

				_this.dialog( 'configure_custom_template_markup' );
			});

			//
			// Form Input Value Watcher
			//
			var watcher = this.watcher = new ES_Widget_Options_Watcher( dom_context );

			// Paginated Checkbox
			watcher.subscribe( '.es-loop-paginated', function( e, value ) {

				var chk = $(this);

				if ( chk.is(':checked') ) {
					$('.es-loop-paginated-dependent').not('.es-loop-field-no-show').show();
				}
				else {
					$('.es-loop-paginated-dependent').not('.es-loop-field-no-show').hide();
				}
			});

			// Page Nums or Text Links Checkbox
			watcher.subscribe( '.es-loop-page-nums-or-text-links-page-numbers, .es-loop-page-nums-or-text-links-text-links', function( e, value ) {

				var chk = $(this);

				if ( chk.is(':checked') && 'text-links' == value ) {
					$('.es-loop-page-nums-or-text-links-dependent').removeClass('es-loop-field-no-show').show();
				}
				else {
					$('.es-loop-page-nums-or-text-links-dependent').addClass('es-loop-field-no-show').hide();
				}
			});

			// Show Post Title Checkbox
			watcher.subscribe( '.es-loop-show-post-title', function( e, value ) {

				var chk = $(this);

				if ( chk.is(':checked') ) {
					$('.es-loop-show-post-title-dependency').show();
				}
				else {
					$('.es-loop-show-post-title-dependency').hide();
				}
			});

			// Show Featured Image Checkbox
			watcher.subscribe( '.es-loop-show-featured-image', function( e, value ) {

				var chk = $(this);

				if ( chk.is(':checked') ) {
					$('.es-loop-show-featured-image-dependency').show();
				}
				else {
					$('.es-loop-show-featured-image-dependency').hide();
				}
			});

			this.watcher.trigger_all();

			// Post Type Select
			watcher.subscribe( '.es-loop-post-type', function( e, value ) {

				_this.post_type = value;

				_this.set_values();

				_this.test_incompatibilities();
			});

			//
			// Advanced Options
			//
			new ES_Widget_Advanced_Options( this.dom.form.find('.es-widget-form-advanced-options'), { auto_open : true } );
		};

		p.init = function() {

			this.is_initializing = true;

			//
			// Set Post Type
			//

			// Add Options

			var pt;
			for ( var i in this.available_post_types ) {
				pt = this.available_post_types[ i ];

				this.dom.input_post_type.append('<option value="'+ pt.name +'">'+ pt.label +'</option>');
			}

			// Set Value

			this.dom.input_post_type.val( this.post_type );

			//
			// Set Filters
			//

			var filter_data;
			for( var i in this.filters ) {
				filter_data = this.filters[ i ];

				this.add_filter( filter_data );
			}

			this.is_initializing = false;

			this.set_values();

			this.test_incompatibilities();
		};

		p.add_filter = function( data, item_to_replace ) {

			var filter_el = this.get_filter_template( data );

			if ( item_to_replace ) {
				var index = item_to_replace.index();
				//delete this.filters[ index ];
				this.filters.splice( index, 1 );

				item_to_replace.replaceWith( filter_el );
			}
			else {
				this.dom.container_loop_filters.append( filter_el );
			}

			if ( !this.is_initializing ) {
				this.filters.push( $.extend( {}, data ) );
				this.set_values();
			}
		};

		p.remove_filter = function( index ) {

			if ( ! confirm('Are you sure you want to remove this filter?') ) return;

			//delete this.filters[ index ];
			this.filters.splice( index, 1 );
			this.dom.container_loop_filters.find('.es_loop_filter').eq( index ).remove();

			this.set_values();
		};

		p.get_wp_data = function() {

			var _this = this;

			var ajax_data = {
				action : 'es_widget_loop_get_wp_data',
				test: 1
			};

			// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
			$.post( ajaxurl, ajax_data, function( response ) {

				_this.available_taxonomies = response.taxonomies;
				_this.available_authors = response.authors;
				_this.available_post_types = response.post_types;
				_this.available_post_statuses = response.post_statuses;

				// Initialize the rest of the widget now that we have this information
				_this.init();
			});
		};

		p.get_filter_data_from_el = function( el ) {

			var index = el.index();
			return $.extend( {}, this.filters[ index ] );
		};

		p.test_incompatibilities = function() {

			var _this = this;

			this.dom.container_loop_filters.find('.es_loop_filter_incompatibility_warning').remove();

			this.dom.container_loop_filters.find('.es_loop_filter').each(function( i, el ) {

				el = $(el);

				switch( el.data('type') ) {
					case 'taxonomy':
						_this.test_taxonomy_compatibility( el );
						break;
				}
			});
		};

		p.test_taxonomy_compatibility = function( filter_el ) {

			var filter_data = this.get_filter_data_from_el( filter_el );

			var compatible_post_types = this.available_taxonomies[ filter_data.taxonomy_name ].post_types;

			var is_compatible = !$.inArray( this.post_type, compatible_post_types );

			if ( ! is_compatible ) {
				// Warn the user that he/she is using a taxonomy that the currently-selected post type doesn't support

				filter_el.append('<div class="es_loop_filter_incompatibility_warning">Warning: This Taxonomy is not compatible with the currently-selected Post Type.</div>');
			}
		};

		p.set_custom_template_markup = function( content, set_form_input ) {

			set_form_input = 'undefined' == typeof set_form_input ? true : false;

			content = _.escape( content );

			this.custom_template_markup = content;

			if ( set_form_input ) {
				this.dom.input_custom_template_markup.val( content );
			}

			if ( $.trim( content ).length > 0 ) {
				this.dom.form.find('.es_loop_custom_template_markup_indicator').show();
			}
			else {
				this.dom.form.find('.es_loop_custom_template_markup_indicator').hide();
			}
		};

		p.get_filter_template = function( data ) {

			var el = $('<div class="es_loop_filter clearfix"></div>');
			$('<div class="es_loop_filter_controls"><a href="" class="es_loop_filter_edit_btn">Edit</a><a href="" class="es_loop_filter_remove_btn">Remove</a></div>').appendTo( el );

			el.attr( 'data-type', data.type );

			switch( data.type ) {

				//
				// Taxonomy
				//
				case 'taxonomy':

					var tax_name = data.taxonomy_name;
					var tax_label = data.taxonomy_label;
					var filter_relationship = data.filter_relationship;

					var term;
					var terms = new Array();
					for( var i in data.selection ) {
						term = data.selection[ i ];

						terms.push( term.name );
					}

					var content_preview = tax_label +' - ';

					content_preview += terms.join(', ');

					if ( content_preview.length > 30 ) {
						content_preview = content_preview.substring( 0, 50 ) + '...';
					}

					// Preview
					$('<div class="es_loop_filter_preview"><span class="es_loop_filter_preview_label">Taxonomy: </span>'+ content_preview +' <span class="es_loop_filter_relationship_preview"><span class="es_loop_filter_preview_label">Relationship: </span>'+ filter_relationship +'</span></div>').appendTo( el );

					el.attr('data-type', 'taxonomy');
					el.attr('data-taxonomy_name', tax_name );
					el.attr('data-taxonomy_label', tax_label );
					el.attr('data-filter_relationship', filter_relationship );

					break;

				//
				// Author
				//
				case 'author':

					var filter_relationship = data.filter_relationship;

					var author;
					var authors = new Array();
					for( var i in data.selection ) {
						author = data.selection[ i ];

						authors.push( author.name );
					}

					var content_preview = authors.join(', ');

					if ( content_preview.length > 30 ) {
						content_preview = content_preview.substring( 0, 50 ) + '...';
					}

					// Preview
					$('<div class="es_loop_filter_preview"><span class="es_loop_filter_preview_label">Author: </span>'+ content_preview +' <span class="es_loop_filter_relationship_preview"><span class="es_loop_filter_preview_label">Relationship: </span>'+ filter_relationship +'</span></div>').appendTo( el );

					el.attr('data-type', 'author');
					el.attr('data-filter_relationship', filter_relationship );

					break;

				//
				// Post ID
				//
				case 'post_id':

					var filter_relationship = data.filter_relationship;

					var post_id;
					var post_ids = new Array();
					for( var i in data.selection ) {
						post_id = data.selection[ i ];

						post_ids.push( post_id.name );
					}

					var content_preview = post_ids.join(', ');

					if ( content_preview.length > 30 ) {
						content_preview = content_preview.substring( 0, 50 ) + '...';
					}

					// Preview
					$('<div class="es_loop_filter_preview"><span class="es_loop_filter_preview_label">Post ID(s): </span>'+ content_preview +' <span class="es_loop_filter_relationship_preview"><span class="es_loop_filter_preview_label">Relationship: </span>'+ filter_relationship +'</span></div>').appendTo( el );

					el.attr('data-type', 'post_id');
					el.attr('data-filter_relationship', filter_relationship );

					break;

				//
				// Post Status
				//
				case 'post_status':

					var filter_relationship = data.filter_relationship;

					var status;
					var statuses = new Array();
					for( var i in data.selection ) {
						status = data.selection[ i ];

						statuses.push( status.name );
					}

					var content_preview = statuses.join(', ');

					if ( content_preview.length > 30 ) {
						content_preview = content_preview.substring( 0, 50 ) + '...';
					}

					// Preview
					$('<div class="es_loop_filter_preview"><span class="es_loop_filter_preview_label">Post Status: </span>'+ content_preview +' <span class="es_loop_filter_relationship_preview"><span class="es_loop_filter_preview_label">Relationship: </span>'+ filter_relationship +'</span></div>').appendTo( el );

					el.attr('data-type', 'post_status');
					el.attr('data-filter_relationship', filter_relationship );

					break;

				//
				// Date Range
				//
				case 'date_range':

					var filter_relationship = data.filter_relationship;

					var from = data.from.length > 0 ? data.from : 'Any Date';
					var to = data.to.length > 0 ? data.to : 'Any Date';

					var content_preview = '<strong>From:</strong> '+ _.escape( from ) +' <strong>To:</strong> '+ _.escape( to );

					// Preview
					$('<div class="es_loop_filter_preview"><span class="es_loop_filter_preview_label">Date Range: </span>'+ content_preview +' <!--<span class="es_loop_filter_relationship_preview"><span class="es_loop_filter_preview_label">Relationship: </span>'+ filter_relationship +'</span>--></div>').appendTo( el );

					el.attr('data-type', 'date_range');
					el.attr('data-filter_relationship', filter_relationship );
					el.attr('data-from', from );
					el.attr('data-to', to );

					break;
			}

			return el;
		};

		p.dialog = function( dialog_type, data ) {

			var _this = this;

			data = data || {};

			switch( dialog_type ) {

				//
				// Select Filter Type
				//
				case 'select_filter_type':
					var template =  $(
					'<div class="es_dialog" title="Add Filter">'+
						'<form>'+
							'<fieldset class="ui-helper-reset">'+

							'<p>Choose the type of Filter you\'d like to add.</p>'+

							'<div class="es_filter_options">'+
								'<a href="#" class="es_loop_filter_option" data-type="taxonomy">'+
									'<span class="es_icon es_icon_taxonomy">'+
										'<span class="es_icon_inner"></span>'+
									'</span>'+
									'<span class="es_loop_filter_option_label">Taxonomy</span>'+
								'</a>'+
								'<a href="#" class="es_loop_filter_option" data-type="author">'+
									'<span class="es_icon es_icon_user">'+
										'<span class="es_icon_inner"></span>'+
									'</span>'+
									'<span class="es_loop_filter_option_label">Author</span>'+
								'</a>'+
								'<a href="#" class="es_loop_filter_option" data-type="post_id">'+
									'<span class="es_icon es_icon_numeric">'+
										'<span class="es_icon_inner"></span>'+
									'</span>'+
									'<span class="es_loop_filter_option_label">Post ID</span>'+
								'</a>'+
								'<a href="#" class="es_loop_filter_option" data-type="post_status">'+
									'<span class="es_icon es_icon_post_status">'+
										'<span class="es_icon_inner"></span>'+
									'</span>'+
									'<span class="es_loop_filter_option_label">Post Status</span>'+
								'</a>'+
								'<a href="#" class="es_loop_filter_option" data-type="date_range">'+
									'<span class="es_icon es_icon_calendar">'+
										'<span class="es_icon_inner"></span>'+
									'</span>'+
									'<span class="es_loop_filter_option_label">Date Range</span>'+
								'</a>'+
							'</div>'+

							'</fieldset>'+
						'</form>'+
					'</div>').appendTo( document.body );

					var dialog;

					// Item Selection Link Events

					template.find('a').click(function( e ) {

						e.preventDefault();

						var link = $(this);
						var type = link.data('type');

						dialog.dialog( "close" );

						_this.dialog('configure_filter_'+ type);
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
				// Configure Filter - Taxonomy
				//
				case 'configure_filter_taxonomy':
					var template =  $('<div class="es_dialog es_loop_dialog" title="Configure Filter - Taxonomy">' +
						'<form>' +
						'<fieldset class="ui-helper-reset">' +
						'<p>Please Choose a Taxonomy</p>' +
						'<label>Taxonomies</label>' +
						'<br/><select class="es_taxonomy_select"><option value="_choose_one">Choose a Taxonomy...</option></select>' +
						'<div class="es_dynatree_container"></div>' +
						'<div class="es_tokenized_input_container"></div>' +
						'<p class="es_loop_filter_relationship_wrapper">' +
						'<span class="es-widget-form-top-label">Filter Relationship</span>' +
						'<label class="es-widget-form-check-radio-label"><input type="radio" name="filter_relationship" value="IN" /> IN</label> <label class="es-widget-form-check-radio-label"><input type="radio" name="filter_relationship" value="NOT IN" />NOT IN</label> <label class="es-widget-form-check-radio-label"><input type="radio" name="filter_relationship" value="AND" />And</label>' +
						'</p>' +
						'</fieldset>' +
						'</form>').appendTo( document.body );

					var tax_select = template.find('.es_taxonomy_select');
					var tokenized_input_container = template.find('.es_tokenized_input_container');
					var tree_container = template.find('.es_dynatree_container');
					var relationship_container = template.find('.es_loop_filter_relationship_wrapper');

					// Add Button Callback

					var callback_add = function() {

						var chosen_tax = tax_select.val();

						var filter_relationship = relationship_container.find('input:checked').val();

						var taxes = _this.available_taxonomies;

						if ( '_choose_one' != chosen_tax || !taxes[ chosen_tax ] ) {

							var tax = taxes[ chosen_tax ];

							var tax_name = chosen_tax;
							var tax_label = $.trim( tax_select.find(':selected').text() );

							if ( tax.hierarchical ) {

								// Get Data from Tree

								var selected = tree_container.find('.dynatree-selected a');
								var selection = new Array();

								selected.each(function( i, el ) {
									var parts = $(el).attr('href').split('|');
									var id = parts[0];
									var name = parts[1];

									selection.push({ id: id, name: name });
								});
							}
							else {

								// Get Data from Tokenized Input

								var input = tokenized_input_container.find('input.es_tokenized_input');

								var selection = input.tokenInput( 'get' );

								input.tokenInput("clear");
							}

							if ( selection.length > 0 ) {
								if ( data.filter ) {
									// Editing Filter

									_this.add_filter({ type: 'taxonomy', taxonomy_name: tax_name, taxonomy_label: tax_label, filter_relationship: filter_relationship, selection: selection }, data.filter );
								}
								else {
									// Adding New Filter

									_this.add_filter({ type: 'taxonomy', taxonomy_name: tax_name, taxonomy_label: tax_label, filter_relationship: filter_relationship,  selection: selection });
								}
							}
						}

						template.dialog( "close" );
					};

					// Cancel Button Callback

					var callback_cancel = function() {

						$( this ).dialog( "close" );
					};

					// Dialog onOpen Callback

					var callback_open = function() {

						var taxonomies = _this.available_taxonomies;

						var tax, compatible_taxonomies = 0;
						for( var i in taxonomies ) {
							tax = taxonomies[ i ];

							if ( -1 !== $.inArray( _this.post_type, tax.post_types ) ) {
								tax_select.append('<option value="'+ tax.name +'">'+ tax.label +'</option>');
								compatible_taxonomies++;
							}
						}

						if ( compatible_taxonomies < 1 ) {
							tax_select.append('<option value="_no_choices" disabled="disabled">Sorry, there are no Taxonomies for this Post Type.</option>');
							tax_select.val('_choose_one');
						}

						tax_select.change(function( e ) {

							tree_container.empty().hide();
							tokenized_input_container.empty().hide();

							var token_input = tokenized_input_container.find('input.es_tokenized_input');

							if ( token_input.length > 0 ) {
								token_input.tokenInput("clear");
							}

							var chosen_tax = $(this).val();

							if ( '_choose_one' == chosen_tax ) return;

							var taxes = _this.available_taxonomies;

							if ( !taxes[ chosen_tax ] ) return;

							var tax = taxes[ chosen_tax ];

							var terms = $.extend( true, new Array(), tax.terms );

							if ( taxes[ chosen_tax ].hierarchical ) {
								// Tree

								tree_container.show();
								relationship_container.show();

								// Set Current Value on Edit

								if ( data.filter && chosen_tax == data.filter.data('taxonomy_name') ) {
									var selection = _this.get_filter_data_from_el( data.filter ).selection;

									var term;
									for ( var i in terms ) {
										term = terms[ i ];
										for ( var ii in selection ) {
											if ( term.id == selection[ ii ].id ) {
												term.select = true;
											}
										}
									}
								}

								var tree_instructions = $('<p>Select one or more Terms in the Tree.</p>');
								var tree = $('<div class="es_dynatree"></div>');

								tree_instructions.appendTo( tree_container );
								tree.appendTo( tree_container );

								tree.dynatree({
									children: s.make_tree( terms ),
									clickFolderMode: 2,
									checkbox: true
								});
							}
							else {
								// Tokenized Input

								tokenized_input_container.show();
								relationship_container.show();

								var current_terms = null;
								if ( data.filter && chosen_tax == data.filter.data('taxonomy_name') ) {
									current_terms = _this.get_filter_data_from_el( data.filter ).selection;
								}

								var input_instructions = $('<p>Start typing a Term\'s name to select one or more Terms.</p>');
								var input = $('<input class="widefat es_tokenized_input" type="text" name="terms" />');

								input_instructions.appendTo( tokenized_input_container );
								input.appendTo( tokenized_input_container );

								input.tokenInput( terms, {
									theme : 'facebook',
									preventDuplicates: true,
									prePopulate: current_terms
								});
							}
						});

						// Set Current Select Tax Value on Edit

						if ( data.filter ) {

							// Chosen Taxonomy

							var taxonomy_name = data.filter.data('taxonomy_name');

							if ( tax_select.find('option[value="'+ taxonomy_name +'"]').length > 0 ) {
								tax_select.val( taxonomy_name );
							}

							tax_select.trigger('change');
						}

						// Filter Relationship

						if ( data.filter && data.filter.data('filter_relationship') ) {

							var filter_relationship = data.filter.data('filter_relationship');

							relationship_container.find('input[value="'+ filter_relationship +'"]').prop('checked', true);
						}
						else {
							relationship_container.find('input[value="IN"]').prop('checked', true);
						}
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
						open: callback_open,
						close: callback_close
					});
					break;

				//
				// Configure Filter - Author
				//
				case 'configure_filter_author':
					var template =  $('<div class="es_dialog" title="Configure Filter - Author">' +
						'<form>' +
						'<fieldset class="ui-helper-reset">' +
						'<p>Start typing an Author\'s name below to choose one or more Authors.</p>' +
						'<label for="author">Authors</label>' +
						'<input class="widefat es_tokenized_input" type="text" name="author" />' +
						'<p class="es_loop_filter_relationship_wrapper">' +
						'<span class="es-widget-form-top-label">Filter Relationship</span>' +
						'<label class="es-widget-form-check-radio-label"><input type="radio" name="filter_relationship" value="IN" /> IN</label> <label class="es-widget-form-check-radio-label"><input type="radio" name="filter_relationship" value="NOT IN" />NOT IN</label>' +
						'</p>' +
						'</fieldset>' +
						'</form>').appendTo( document.body );

					var relationship_container = template.find('.es_loop_filter_relationship_wrapper');

					// Add Button Callback

					var callback_add = function() {

						var input = template.find('input.es_tokenized_input');

						var filter_relationship = relationship_container.find('input:checked').val();

						var selection = input.tokenInput( 'get' );

						input.tokenInput("clear");

						if ( selection.length > 0 ) {
							if ( data.filter ) {
								// Editing Filter

								_this.add_filter({ type: 'author', filter_relationship: filter_relationship, selection: selection }, data.filter );
							}
							else {
								// Adding New Filter

								_this.add_filter({ type: 'author', filter_relationship: filter_relationship, selection: selection });
							}
						}

						template.dialog( "close" );
					};

					// Cancel Button Callback

					var callback_cancel = function() {

						$( this ).dialog( "close" );
					};

					// Dialog onOpen Callback

					var callback_open = function() {

						relationship_container.show();

						var current_authors = null;
						if ( data.filter ) {
							current_authors = _this.get_filter_data_from_el( data.filter ).selection;
						}

						template.find('input.es_tokenized_input').tokenInput( _this.available_authors, {
							theme : 'facebook',
							preventDuplicates: true,
							resultsFormatter: function( item ) {
								return "<li>" + "<div style='display: inline-block; padding-left: 10px;'><div class='full_name'>" + item.display_name + "</div><div class='email'>" + item.email + "</div></div></li>"
							},
							prePopulate: current_authors
						});

						// Filter Relationship

						if ( data.filter && data.filter.data('filter_relationship') ) {

							var filter_relationship = data.filter.data('filter_relationship');

							relationship_container.find('input[value="'+ filter_relationship +'"]').prop('checked', true);
						}
						else {
							relationship_container.find('input[value="IN"]').prop('checked', true);
						}
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
						open: callback_open,
						close: callback_close
					});
					break;

				//
				// Configure Filter - Post ID
				//
				case 'configure_filter_post_id':
					var template =  $('<div class="es_dialog" title="Configure Filter - Post ID(s)">' +
						'<form>' +
						'<fieldset class="ui-helper-reset">' +
						'<p>Type the Post ID and hit enter for each Post that applies.</p>' +
						'<label>Post ID(s)</label>' +
						'<input class="widefat es_tokenized_input" type="text" name="post_ids" />' +
						'<p class="es_loop_filter_relationship_wrapper">' +
						'<span class="es-widget-form-top-label">Filter Relationship</span>' +
						'<label class="es-widget-form-check-radio-label"><input type="radio" name="filter_relationship" value="IN" /> IN</label> <label class="es-widget-form-check-radio-label"><input type="radio" name="filter_relationship" value="NOT IN" />NOT IN</label>' +
						'</p>' +
						'</fieldset>' +
						'</form>').appendTo( document.body );

					var relationship_container = template.find('.es_loop_filter_relationship_wrapper');

					// Add Button Callback

					var callback_add = function() {

						var input = template.find('input.es_tokenized_input');

						var filter_relationship = relationship_container.find('input:checked').val();

						var selection = input.tokenInput( 'get' );

						input.tokenInput("clear");

						if ( selection.length > 0 ) {
							if ( data.filter ) {
								// Editing Filter

								_this.add_filter({ type: 'post_id', filter_relationship: filter_relationship, selection: selection }, data.filter );
							}
							else {
								// Adding New Filter

								_this.add_filter({ type: 'post_id', filter_relationship: filter_relationship, selection: selection });
							}
						}

						template.dialog( "close" );
					};

					// Cancel Button Callback

					var callback_cancel = function() {

						$( this ).dialog( "close" );
					};

					// Dialog onOpen Callback

					var callback_open = function() {

						relationship_container.show();

						var current_post_ids = null;
						if ( data.filter ) {
							current_post_ids = _this.get_filter_data_from_el( data.filter ).selection;
						}

						template.find('input.es_tokenized_input').tokenInput( ajaxurl +'?action=es_widget_loop_get_post_id', {
							theme : 'facebook',
							preventDuplicates: true,
							prePopulate: current_post_ids
						});

						// Filter Relationship

						if ( data.filter && data.filter.data('filter_relationship') ) {

							var filter_relationship = data.filter.data('filter_relationship');

							relationship_container.find('input[value="'+ filter_relationship +'"]').prop('checked', true);
						}
						else {
							relationship_container.find('input[value="IN"]').prop('checked', true);
						}
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
						open: callback_open,
						close: callback_close
					});
					break;

				//
				// Configure Filter - Post Status
				//
				case 'configure_filter_post_status':
					var template =  $('<div class="es_dialog" title="Configure Filter - Post Status">' +
						'<form>' +
						'<fieldset class="ui-helper-reset">' +
						'<p>Choose all Post Statuses that apply.</p>' +
						'<label for="ajax_url">Post Statuses</label>' +
						'<div class="es_loop_post_statuses_container"></div>' +
						'<p class="es_loop_filter_relationship_wrapper">' +
						'<span class="es-widget-form-top-label">Filter Relationship</span>' +
						'<label class="es-widget-form-check-radio-label"><input type="radio" name="filter_relationship" value="IN" /> IN</label> <label class="es-widget-form-check-radio-label"><input type="radio" name="filter_relationship" value="NOT IN" />NOT IN</label>' +
						'</p>' +
						'</fieldset>' +
						'</form>').appendTo( document.body );

					var relationship_container = template.find('.es_loop_filter_relationship_wrapper');

					var post_statuses_container = template.find('.es_loop_post_statuses_container');

					if ( data.filter ) {
						var current_post_statuses = _this.get_filter_data_from_el( data.filter ).selection;
					}

					var ps, chk;
					for( var i in this.available_post_statuses ) {
						ps = this.available_post_statuses[ i ];

						chk = $('<label><input type="checkbox" value="'+ ps.id +'" /> '+ ps.name +'</label>');

						if ( data.filter ) {
							// Set Current Value

							var is_selected = false;

							for( var ii in current_post_statuses ) {
								if ( current_post_statuses[ ii ].id == ps.id ) {
									is_selected = true;
									break;
								}
							}

							if ( is_selected ) {
								chk.find('input').prop('checked', true);
							}
						}

						post_statuses_container.append( chk );
					}

					// Add Button Callback

					var callback_add = function() {

						var selection = new Array();

						var filter_relationship = relationship_container.find('input:checked').val();

						var chks = template.find('.es_loop_post_statuses_container input');

						chks.each(function( i, chk ) {
							chk = $(chk);

							if ( chk.is(':checked') ) {
								selection.push({ id: chk.val(), name: $.trim( chk.closest('label').text() ) });
							}
						});

						if ( selection.length > 0 ) {
							if ( data.filter ) {
								// Editing Filter

								_this.add_filter({ type: 'post_status', filter_relationship: filter_relationship, selection: selection }, data.filter );
							}
							else {
								// Adding New Filter

								_this.add_filter({ type: 'post_status', filter_relationship: filter_relationship, selection: selection });
							}
						}

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

					// Filter Relationship

					if ( data.filter && data.filter.data('filter_relationship') ) {

						var filter_relationship = data.filter.data('filter_relationship');

						relationship_container.find('input[value="'+ filter_relationship +'"]').prop('checked', true);
					}
					else {
						relationship_container.find('input[value="IN"]').prop('checked', true);
					}

					relationship_container.show();

					break;

				//
				// Configure Filter - Date Range
				//
				case 'configure_filter_date_range':
					var template =  $('<div class="es_dialog" title="Configure Filter - Date Range">' +
						'<form>' +
						'<fieldset class="ui-helper-reset">' +
						'<p>Choose a <strong>Date Range</strong> for your Posts</p>' +
						'<label for="from">From: </label>' +
						'<input tabindex="-1" type="text" name="from_date" />' +
						'<a class="button es-widget-inline-form-field" href="">Clear</a>' +
						'<label for="to">To: </label>' +
						'<input tabindex="-1" type="text" name="to_date" />' +
						'<a class="button es-widget-inline-form-field" href="">Clear</a>' +
						'<p>* Only one <strong>Date Range</strong> filter is supported per loop.</p>' +
						'</fieldset>' +
						'</form>').appendTo( document.body );

					//var relationship_container = template.find('.es_loop_filter_relationship_wrapper');

					// Add Button Callback

					var callback_add = function() {

						//var filter_relationship = relationship_container.find('input:checked').val();
						var filter_relationship = 'IN';

							from = $.trim( template.find('input[name="from_date"]').val() );
						to = $.trim( template.find('input[name="to_date"]').val() );

						if ( from.length > 0 || to.length > 0 ) {
							if ( data.filter ) {
								// Editing Filter

								_this.add_filter({ type: 'date_range', filter_relationship: filter_relationship, from: from, to: to }, data.filter );
							}
							else {
								// Adding New Filter

								_this.add_filter({ type: 'date_range', filter_relationship: filter_relationship, from: from, to: to });
							}
						}

						template.dialog( "close" );
					};

					// Cancel Button Callback

					var callback_cancel = function() {

						$( this ).dialog( "close" );
					};

					// Dialog onOpen Callback

					var callback_open = function() {

						var inputs = template.find('input');

						inputs.datepicker({
							changeMonth: true,
							changeYear: true,
							dateFormat: 'yy-mm-dd'
						});

						template.find('a').click(function( e ) {
							e.preventDefault();

							$(this).prev('input').val('');
						});
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
						open: callback_open,
						close: callback_close
					});

					// Filter Relationship && Current Data Values

					if ( data.filter ) {

						//var filter_relationship = data.filter.data('filter_relationship');

						//relationship_container.find('input[value="'+ filter_relationship +'"]').prop('checked', true);

						var from = data.filter.data('from');
						var to = data.filter.data('to');

						template.find('input[name="from_date"]').val( from );
						template.find('input[name="to_date"]').val( to );
					}
					else {
						//relationship_container.find('input[value="IN"]').prop('checked', true);
					}

					//relationship_container.show();

					break;

				//
				// Configure Custom Template (Raw HTML - ACE Editor)
				//
				case 'configure_custom_template_markup':

					var template = $(
						'<div class="es_dialog" title="Add Your Custom Template Code">'+
							'<form>'+
							'<fieldset class="ui-helper-reset">'+
							'<p>The editor below gives you the ability to create a Custom HTML Template for your Loop. Supported Merge Tags are as follows:</p>' +
							'<p>{{title}} - Post Title <br />{{featured_image}} - Post Featured Image (no alignment) <br />{{excerpt}} - Post Excerpt <br />' +
							'{{content}} - Post Content</p>' +
							'<div class="es_raw_html_container"></div>'+
							'</fieldset>'+
							'</form>'+
							'</div>').appendTo( document.body);

					var editor;

					// Save Button Callback

					var callback_save = function() {

						// Grab Ace Editor Value & Save it in the item data
						var content = editor.ace_editor.getValue();

						// Set Custom Template Content

						_this.set_custom_template_markup( content );

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
						supported_modes: ['html'],
						auto_focus : false
					});

					// Set Code Editor Content on Edit
					editor.ace_editor.setValue( _.unescape( this.custom_template_markup ) );

					break;
			}
		};

		p.set_values = function( data, set_form_value ) {

			set_form_value = 'undefined' != set_form_value || true;

			var _data = {};

			if ( data && !_.isEmpty( data ) ) {
				_data = $.extend( {}, data );

				this.filters = $.extend( new Array(), data.filters );
				this.post_type = data.post_type;
			}
			else {
				_data.post_type = this.post_type;

				_data.filters = this.filters;
			}

			this.data = _data;
			this.str_data = _.escape( JSON.stringify( _data ) );

			if ( set_form_value ) {
				this.dom.input_loop_data.val( this.str_data );
			}
		};

		//
		// Static Methods
		//

		s.make_tree = function( terms ) {

			var tree = new Array();

			var term;
			for( var i in terms ) {
				term = terms[ i ];

				if ( term.parent_id == 0 ) {
					tree.push( s.good_parenting( term, terms ) );
				}
			}

			return tree;
		};

		s.good_parenting = function( parent_term, terms ) {

			var new_parent_term = {
				id: parent_term.id,
				parent_id: parent_term.parent_id,
				title: parent_term.name,
				children: new Array(),
				isFolder: false,
				href: parent_term.id +'|'+ parent_term.name,
				select: parent_term.select || false,
				expand: parent_term.select || false,
				addClass: 'es_tree_term'
			};

			var child_term;
			for( var i in terms ) {
				child_term = terms[ i ];

				if ( parent_term.id == child_term.parent_id ) {

					new_parent_term.children.push( s.good_parenting( child_term, terms ) );
					new_parent_term.isFolder = true;
				}
			}

			return new_parent_term;
		};

	}

	// Reveal to outside world
	window.ES_Loop = ES_Loop;

})($);