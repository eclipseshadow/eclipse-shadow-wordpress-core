(function($){
	if ( 'undefined' == typeof ES_Rich_Text ) {
		/**
		 * ES_Rich_Text
		 *
		 * Interactive Javascript Table Builder
		 */
		var ES_Rich_Text = function( dom_context, data ) {

			this.__construct( dom_context, data );

		};
		// Shortcut to Prototype Object
		var p = ES_Rich_Text.prototype;
		// Shortcut to Static Object
		var s = ES_Rich_Text;

		//
		// Properties
		//

		// Instance Properties
		p.dom = null;
		p.content = '';
		p.esc_content = '';
		p.editor = null;

		// Static Properties
		//...

		//
		// Methods
		//

		/** Instance Methods */

		/**
		 * Constructor
		 */
		p.__construct = function( dom_context ) {

			var _this = this; // Only need this for accessing instance inside of an event handler function
			dom_context = $( dom_context );

			//
			// Assign Instance Properties
			//
			this.dom = {};

			this.dom.widget = dom_context; // Already in the DOM
			this.dom.form = this.dom.widget.find('form'); // Already in the DOM
			this.dom.rich_text_container = this.dom.widget.find('.es_rich_text_container'); // Already in the DOM
			this.dom.data_input_el = this.dom.form.find('input.es-cfct-rich-text-content');

			//
			// Set instance data values
			//
			var content = this.dom.data_input_el.val();

			if ( !content ) {
				content = '';
			}

			this.set_values( _.unescape( content ), false );

			//
			// Create TinyMCE Editor
			//
			this.editor = new ES_WP_Rich_Text_Editor({
				element : this.dom.rich_text_container,
				setup : function( _editor ) {

					_editor.onInit.add(function( _ed, _args ) {

						// Populate TinyMCE

						_ed.setContent( _this.content );

						// Set Instance Content on Editor Change

						_ed.onKeyUp.add(function( __ed, e ) {
							_this.set_values( __ed.getContent() );
						});
					});
				}
			});

			//
			// Advanced Options
			//
			new ES_Widget_Advanced_Options( this.dom.form.find('.es-widget-form-advanced-options') );
		};

		/**
		 * Set Instance Data Values & Form Element Value
		 *
		 * @param data
		 * @param set_field_value
		 */
		p.set_values = function( content, set_field_value ) {

			if ( 'undefined' == typeof content || !content ) {
				//content = ''; TinyMCE Current Value
			}

			if ( 'undefined' == typeof set_field_value ) {
				set_field_value = true;
			}

			this.content = content;
			this.esc_content = _.escape( content );

			if ( true === set_field_value ) {
				this.dom.data_input_el.val( this.esc_content );
			}
		};
	}

	// Reveal to outside world
	window.ES_Rich_Text = ES_Rich_Text;

})(jQuery);