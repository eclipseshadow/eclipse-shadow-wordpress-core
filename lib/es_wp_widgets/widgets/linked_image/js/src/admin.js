(function($){
	if ( 'undefined' == typeof ES_Linked_Image ) {
		/**
		 * ES_Linked_Image
		 *
		 * Responsive Image/Advertisement Widget
		 * @todo Add Custom Image Size option
		 */
		var ES_Linked_Image = function( dom_context, data ) {

			this.__construct( dom_context, data );

		};
		// Shortcut to Prototype Object
		var p = ES_Linked_Image.prototype;
		// Shortcut to Static Object
		var s = ES_Linked_Image;

		//
		// Properties
		//

		// Instance Properties
		p.dom = null;
		p.options = null;
		p.default_options = {};

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
			this.dom.image_preview = this.dom.form.find('.es-cfct-linked-image-image-preview');
			this.dom.choose_image_btn = this.dom.form.find('.es-cfct-linked-image-choose-image-btn');
			this.dom.link_to_object_search_results = this.dom.form.find('.es-cfct-linked-image-link-to-object-search-results');
			this.dom.link_to_object_search_selection = this.dom.form.find('.es-cfct-linked-image-link-to-object-search-selection');
			this.dom.link_to_object_settings = this.dom.form.find('.es-cfct-linked-image-link-to-object-settings');
			this.dom.link_to_url_settings = this.dom.form.find('.es-cfct-linked-image-link-to-url-settings');

			this.dom.input_image_ids = this.dom.form.find('.es-cfct-linked-image-image_ids');
			this.dom.input_link_to_object_search = this.dom.form.find('.es-cfct-linked-image-link-to-object-search');
			this.dom.input_link_to_object_id = this.dom.form.find('.es-cfct-linked-image-link_to_object_id');

			//
			// Choose Image Button
			//
			this.dom.choose_image_btn.click(function( e ) {

				_this.launch_media_uploader();
			});

			//
			// Load Image Preview
			//
			this.load_image_preview();

			//
			// Form Input Value Watcher
			//
			var watcher = new ES_Widget_Options_Watcher( dom_context );

			// Link to (Nothing, Lightbox, URL, Object) Events

			watcher.subscribe('.es-cfct-linked-image-link-to-checkbox', function( e, value ) {

				_this.toggle_link_to_settings();
			});

			watcher.trigger_all();

			//
			// Link to Object - Search onChange event(s)
			//
			this.dom.input_link_to_object_search.keyup(function( e ) {

				_this.link_to_object_search_on_change();
			});

			this.dom.link_to_object_search_results.on('click', '.es-cfct-linked-image-link-to-object-search-result', {}, function( e ) {

				_this.set_link_to_object_id( $(this) );
			});

			this.dom.link_to_object_search_selection.on('click', '.es-cfct-linked-image-post-information-remove-selected', {}, function( e ) {

				_this.unset_link_to_object_id();
			});

			//
			// Advanced Options
			//
			new ES_Widget_Advanced_Options( this.dom.form.find('.es-widget-form-advanced-options'), { auto_open : true } );
		};

		p.launch_media_uploader = function() {

			var _this = this;

			if ( this.dom.choose_image_btn.data('disabled') ) return;

			var media_control = {

				frame : function() {
					if ( this._frame )
						return this._frame;

					this._frame = wp.media({
						title: 'Choose Your Image',

						library: {
							type: 'image'
						},

						button: {
							text: 'Choose Your Image'
						},

						multiple: false
					});

					this._frame.on( 'open', this.updateFrame ).state('library').on( 'select', this.select);
					console.log(this._frame.states);

					return this._frame;
				},

				select : function() {
					// Grab our attachment selection and construct a JSON representation of the model.
					var media_attachment = media_control.frame().state().get('selection').toJSON();

					// Send the attachment URL to our custom input field via jQuery.

					var image_ids = new Array();

					for ( var i in media_attachment ) {
						var image_obj = media_attachment[ i ];

						image_ids.push( image_obj.id );
					}

					_this.set_image( image_ids );
				},

				updateFrame : function() {
					//...
				}

			};

			media_control.frame().open();
		};

		p.set_image = function( image_ids ) {

			if ( image_ids.length < 1 ) return;

			var str_image_ids = image_ids.join(',');

			this.dom.input_image_ids.val( str_image_ids );

			this.load_image_preview( str_image_ids );
		};

		p.load_image_preview = function( str_image_ids ) {

			var _this = this;

			this.disable_choose_button();

			if ( !str_image_ids ) {
				str_image_ids = this.dom.input_image_ids.val();
			}

			if ( !str_image_ids ) {
				this.enable_choose_button();
				return;
			}

			var data = {
				action : 'es_cfct_linked_image_get_image_preview',
				image_ids : str_image_ids
			};

			// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
			$.post( ajaxurl, data, function( response ) {
				_this.dom.image_preview.html( response );

				_this.enable_choose_button();
			});
		};

		p.enable_choose_button = function() {

			this.dom.choose_image_btn.removeClass('es-cfct-linked-image-button-disabled').data('disabled', false );
		};

		p.disable_choose_button = function() {

			this.dom.choose_image_btn.addClass('es-cfct-linked-image-button-disabled').data('disabled', true );
		};

		p.toggle_link_to_settings = function() {

			var checked = this.dom.widget.find('.es-cfct-linked-image-link-to-checkbox:checked');
			var all_settings = this.dom.widget.find('.es-cfct-linked-image-link-to-settings');

			all_settings.hide();

			switch ( true ) {

				case checked.hasClass('es-cfct-linked-image-link_to_lightbox'):
					//ES_Linked_Image.show_advanced_options( dom_context );

					break;

				case checked.hasClass('es-cfct-linked-image-link_to_url'):
					this.dom.link_to_url_settings.show();
					//ES_Linked_Image.show_advanced_options( dom_context );

					break;

				case checked.hasClass('es-cfct-linked-image-link_to_object'):
					this.dom.link_to_object_settings.show();

					var post_id = this.dom.input_link_to_object_id.val();
					this.get_post_information( post_id );

					//ES_Linked_Image.show_advanced_options( dom_context );

					break;

			}
		};

		p.pendingObjectSearchCall = {

			timeStamp: null,
				procID: null
		};

		p.link_to_object_search_on_change = function() {

			var _this = this;

			var search_query = this.dom.input_link_to_object_search.val();

			this.dom.link_to_object_search_results.html( $('<div class="es-cfct-linked-image-ajax-loader"></div>') );

			// A timestamp for this call
			var timeStamp = Date.now();

			var fetchSearchResults = function () {

				var data = {
					action : 'es_cfct_linked_image_get_object_search_results',
					search_query : search_query
				};

				// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
				$.post( ajaxurl, data, function( response ) {

					// Short-circuit execution if the timestamp on this call doesn't match the last one made
					if ( _this.pendingObjectSearchCall.timeStamp != timeStamp) { return false; }

					// The action to take
					_this.dom.link_to_object_search_results.html( response );

					// Clear the reference to this timeout call
					_this.pendingObjectSearchCall.procID = null;
				});
			};

			// Clear the timeout on the last call made if it exists
			if ( _this.pendingObjectSearchCall.procID ) {
				clearTimeout( _this.pendingObjectSearchCall.procID );
			}

			// Update the timeout call tracker
			_this.pendingObjectSearchCall = { timeStamp: timeStamp, procID: setTimeout(fetchSearchResults, 500) };
		};
		
		p.set_link_to_object_id = function( result_item ) {

			var post_id = result_item.data('id');

			this.dom.input_link_to_object_id.val( post_id );

			this.get_post_information( post_id );

		};

		p.unset_link_to_object_id = function() {

			this.dom.form.find('.es-cfct-linked-image-post-information').remove();

			this.dom.input_link_to_object_id.val( null );
		};

		p.get_post_information = function( post_id, dom_context ) {

			var _this = this;

			var data = {
				action : 'es_cfct_linked_image_get_post_information',
				post_id : post_id
			};

			// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
			$.post( ajaxurl, data, function( response ) {

				// The action to take
				_this.dom.link_to_object_search_selection.html( response );
			});
		};

		//
		// Static Methods
		//

		/**
		 * Highlights an element using $ UI Highlight
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
	window.ES_Linked_Image = ES_Linked_Image;

})($);