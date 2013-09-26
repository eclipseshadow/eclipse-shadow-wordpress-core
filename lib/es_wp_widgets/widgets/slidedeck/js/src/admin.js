(function($){
	if ( 'undefined' == typeof ES_SlideDeck ) {
		/**
		 * ES_SlideDeck
		 *
		 * SlideDeck Slideshow Widget
		 */
		var ES_SlideDeck = function( dom_context, data ) {

			this.__construct( dom_context, data );

		};
		// Shortcut to Prototype Object
		var p = ES_SlideDeck.prototype;
		// Shortcut to Static Object
		var s = ES_SlideDeck;

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
		p.__construct = function( dom_context, options ) {

			var _this = this; // Only need this for accessing instance inside of an event handler function
			dom_context = $( dom_context );

			//
			// Assign Instance Properties
			//
			this.dom = {};

			this.dom.widget = dom_context; // Already in the DOM
			this.dom.form = this.dom.widget.find('form'); // Already in the DOM
			this.dom.select = this.dom.form.find('.es-cfct-slidedeck-slidedeck-select');

			//
			// Options
			//
			options = options || {};

			this.options = {};
			this.options = $.extend( this.options, this.default_options, options );

			//
			// Populate Slidedeck Dropdown Options
			//
			var select_event;

			if ( jQuery.browser.webkit || jQuery.browser.safari ) {
				select_event = 'mousedown';
			}
			else {
				select_event = 'click';
			}

			this.dom.select.bind( select_event, function( e ) {

				_this.get_slidedeck_dropdown_options();
			});

			this.get_slidedeck_dropdown_options();

			//
			// Advanced Options
			//
			new ES_Widget_Advanced_Options( this.dom.form.find('.es-widget-form-advanced-options') );
		};

		p.get_slidedeck_dropdown_options = function() {

			var _this = this;

			var data = {
				action : 'es_cfct_slidedeck_get_slidedeck_options',
				slidedeck_id : this.dom.select.data('slidedeck-id')
			};

			// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
			$.post( ajaxurl, data, function( response ) {

				// The action to take
				_this.dom.select.html( response );
			});
		};
	}

	// Reveal to outside world
	window.ES_SlideDeck = ES_SlideDeck;

})(jQuery);