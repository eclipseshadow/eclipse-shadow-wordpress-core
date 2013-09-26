(function($){
	if ( 'undefined' == typeof ES_Code_Editor ) {
		/**
		 * ES_Code_Editor
		 *
		 * Interactive Javascript Table Builder
		 */
		var ES_Code_Editor = function( dom_context, data ) {

			this.__construct( dom_context, data );

		};
		// Shortcut to Prototype Object
		var p = ES_Code_Editor.prototype;
		// Shortcut to Static Object
		var s = ES_Code_Editor;

		//
		// Properties
		//

		// Instance Properties
		p.dom = null;
		p.content = '';
		p.esc_content = '';
		p.editor = null;
		p.options = null;
		p.default_options = {
			mode : 'html'
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
		p.__construct = function( dom_context, options ) {

			var _this = this; // Only need this for accessing instance inside of an event handler function
			dom_context = $( dom_context );

			//
			// Assign Instance Properties
			//
			this.dom = {};

			this.dom.widget = dom_context; // Already in the DOM
			this.dom.form = this.dom.widget.find('form'); // Already in the DOM
			this.dom.code_editor_container = this.dom.widget.find('.es_code_editor_container'); // Already in the DOM
			this.dom.data_input_el = this.dom.form.find('input.es-cfct-code-editor-content');
			this.dom.mode_input_el = this.dom.form.find('input.es-cfct-code-editor-mode');

			//
			// Options
			//
			options = options || {};

			this.options = {};
			this.options = $.extend( this.options, this.default_options, options );


			switch( this.dom.mode_input_el.val() ) {
				case 'javascript':
					this.options.mode = 'javascript';
					break;
				default: // html
					this.options.mode = 'html';
					break;
			}

			//
			// Set instance data values
			//
			var content = this.dom.data_input_el.val();

			if ( !content ) {
				content = '';
			}

			this.set_values( _.unescape( content ), false );

			//
			// Create ACE Code Editor
			//
			this.editor = new ES_ACE_Code_Editor({
				element : this.dom.code_editor_container,
				width : '100%',
				height : 250,
				mode : this.options.mode,
				auto_focus : false
			});

			// Set Editor Initial Value

			this.editor.ace_editor.setValue( this.content );

			// Set Code Editor Content on Edit

			this.editor.ace_editor.getSession().on("change", function(e) {

				_this.set_values( _this.editor.ace_editor.getValue() );
			});

			//
			// Mode Selection
			//
			this.dom.mode_buttons = this.dom.code_editor_container.find('.es_code_editor_mode_select a');

			this.dom.mode_buttons.click(function( e ) {

				e.preventDefault();

				_this.dom.mode_input_el.val( $(this).data('mode') );
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
	window.ES_Code_Editor = ES_Code_Editor;

})(jQuery);