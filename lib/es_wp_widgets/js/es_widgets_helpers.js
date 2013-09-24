(function($){

	/**************************************************
	 *
	 * ES_Widget_Advanced_Options
	 *
	 * Handling of Show/Hide of Widget Advanced Options
	 *
	 **************************************************/
	if ( 'undefined' == typeof ES_Widget_Advanced_Options ) {
		var ES_Widget_Advanced_Options = function( dom_context, data ) {

			this.__construct( dom_context, data );

		};
		// Shortcut to Prototype Object
		var p = ES_Widget_Advanced_Options.prototype;
		// Shortcut to Static Object
		var s = ES_Widget_Advanced_Options;

		//
		// Properties
		//

		// Instance Properties
		p.dom = null;
		p.options = null;
		p.default_options = {
			auto_open : true,
			force_open : false
		};

		// Static Properties
		//...

		//
		// Methods
		//

		/** Instance Methods */

		/**
		 * Constructor
		 */
		p.__construct = function( options_container, options ) {

			var _this = this; // Only need this for accessing instance inside of an event handler function
			options_container = $( options_container ).addClass('es-widget-form-advanced-options').hide();

			//
			// Assign Instance Properties
			//
			this.dom = {};
			this.dom.options_container = options_container;

			//
			// Options
			//
			options = options || {};

			this.options = {};
			this.options = $.extend( this.options, this.default_options, options );

			//
			// Open/Close Link
			//
			this.dom.btn = $('<a class="es-widget-form-advanced-options-link" href="#">Show Advanced Options</a>').click(function( e ) {

				e.preventDefault();

				_this.toggle_advanced_options( e );
			});

			this.dom.options_container.before( this.dom.btn );


			// Auto-Show Advanced Options of user is using anything but default options

			var auto_open = this.dom.options_container.find('*[data-is-default="false"]').length > 0;

			if ( this.options.force_open || (auto_open && this.options.auto_open) ) {
				this.toggle_advanced_options();
			}
		};

		/**
		 * Hide/Show Advanced Options Container
		 *
		 * @returns {boolean}
		 */
		p.toggle_advanced_options = function( e ) {

			if ( 'undefined' == typeof e ) {
				e = null;
			}

			// For Carrington Build Popup Only
			var popup_content = this.dom.options_container.closest('.cfct-popup-content');

			if ( this.dom.options_container.is(':visible') ) {
				this.hide_advanced_options();
			}
			else {
				this.show_advanced_options();

				// For Carrington Build Popup Only
				if ( e && popup_content.length > 0 ) {
					popup_content.scrollTop(
						this.dom.options_container.offset().top - popup_content.offset().top + popup_content.scrollTop()
					);
				}
			}

			return false;

		};

		/**
		 * Show Advanced Options Container
		 */
		p.show_advanced_options = function() {

			this.dom.options_container.show();

			this.dom.btn.text('Hide Advanced Options');
		};

		/**
		 * Hide Advanced Options Container
		 */
		p.hide_advanced_options = function() {

			this.dom.options_container.hide();

			this.dom.btn.text('Show Advanced Options');
		};
	}

	// Reveal to outside world
	window.ES_Widget_Advanced_Options = ES_Widget_Advanced_Options;


	/**************************************************
	 *
	 * ES_Widget_Options_Watcher
	 *
	 * Event-Driven Updating of Widget Options when
	 * widget form values change
	 *
	 **************************************************/
	if ( 'undefined' == typeof ES_Widget_Options_Watcher ) {
		var ES_Widget_Options_Watcher = function( dom_context, data ) {

			this.__construct( dom_context, data );

		};
		// Shortcut to Prototype Object
		var p = ES_Widget_Options_Watcher.prototype;
		// Shortcut to Static Object
		var s = ES_Widget_Options_Watcher;

		//
		// Properties
		//

		// Instance Properties
		p.dom = null;
		p.options = null;
		p.default_options = {
			auto_open: true
		};
		p.subscriptions = null;

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
			this.dom.dom_context = dom_context;

			//
			// Options
			//
			options = options || {};

			this.options = {};
			this.options = $.extend( this.options, this.default_options, options );

			p.subscriptions = new Array();
		};

		p.subscribe = function( input_selector, callback ) {

			callback = callback || function( e ){};

			this.dom.dom_context.on('change', input_selector, {}, function( e ) {

				var el = $(this);
				var value = el.val();

				if ( null === value && 'select' == el.prop("tagName") ) {
					value = el.find('option:selected').val();
				}

				callback.call( this, e, value );
			});

			var subscription = { input_selector : input_selector, callback : callback };

			this.subscriptions.push( subscription );

			return subscription;
		};

		p.trigger_all = function() {

			var sub;
			var num_triggered = 0;
			for ( var i in this.subscriptions ) {
				sub = this.subscriptions[ i ];

				this.dom.dom_context.find( sub.input_selector ).trigger('change');

				num_triggered++;
			}

			return num_triggered;
		};
	}

	// Reveal to outside world
	window.ES_Widget_Options_Watcher = ES_Widget_Options_Watcher;

})(jQuery);