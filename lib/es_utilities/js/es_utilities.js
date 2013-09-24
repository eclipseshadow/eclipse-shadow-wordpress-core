/**
 * ES Utilities
 */
if ( 'undefined' == typeof ES_Utilities ) {
(function($){

	var ES_Utilities = function() {

		this.__construct();
	};
	// Shortcut to Prototype Object
	var p = ES_Utilities.prototype;
	// Shortcut to Static Object
	var s = ES_Utilities;

	//
	// Properties
	//

	// Instance Properties
	//...

	// Static Properties
	//...

	//
	// Methods
	//

	/** Instance Methods */

	/**
	 * Constructor
	 */
	p.__construct = function() {

		//...
	};

	window.ES_Utilities = ES_Utilities;
	window.es_utilities = new ES_Utilities();

})(jQuery);
}

/**
 * ACE Syntax-Highlighted Code Editor
 */
if ( 'undefined' == typeof ES_ACE_Code_Editor ) {
(function($){

	var ES_ACE_Code_Editor = function( options ) {

		this.__construct( options );
	};
	// Shortcut to Prototype Object
	var p = ES_ACE_Code_Editor.prototype;
	// Shortcut to Static Object
	var s = ES_ACE_Code_Editor;

	//
	// Properties
	//

	/** Instance Properties */
	p.ace_editor = null;
	p.options = null;
	p.default_options = {
		theme : 'twilight',
		mode : 'html',
		supported_modes : ['html', 'javascript'],
		width : '400px',
		height : '250px',
		auto_focus : true
	};
	p.supported_modes = {
		html : 'HTML',
		javascript : 'JS'
	};
	p.dom = null;

	/** Static Properties */
	s.instance_count = 0;

	//
	// Methods
	//

	/** Instance Methods */

	/**
	 * Constructor
	 */
	p.__construct = function( options ) {

		var _this = this;

		s.instance_count++;

		if ( !options.id ) {
			options.id = 'es_code_editor_'+ s.instance_count.toString();
		}

		this.dom = {};
		this.dom.editor_container = $( options.element ).addClass('es_code_editor');
		this.dom.editor_controls = $('<div class="es_code_editor_controls"></div>').appendTo( this.dom.editor_container );
		this.dom.editor_wrapper = $('<div id="'+ options.id +'" class="es_code_editor_wrapper"></div>').appendTo( this.dom.editor_container );

		this.options = {};
		this.options = $.extend( this.options, this.default_options, options );

		this.dom.editor_wrapper.css('width', this.options.width);
		this.dom.editor_wrapper.css('height', this.options.height);

		//
		// Controls
		//

		// Fullscreen

		this.dom.editor_controls.append('<a href="" class="button">Fullscreen</a>').click(function( e ) {

			e.preventDefault();

			var btn = $(this);

			_this.fullscreen();
		});

		// Mode Selection

		this.dom.editor_mode_select = $('<span class="es_code_editor_mode_select"><span class="es_code_editor_mode_select_label">Syntax Mode:</span> </span>').click(function( e ) {

			e.preventDefault();

			var btn = $(this);

			_this.fullscreen();
		}).appendTo( this.dom.editor_controls );

		// Create Mode Buttons

		for ( var mode in this.supported_modes ) {

			if ( ! _.contains( this.options.supported_modes, mode ) ) continue;

			this.dom.editor_mode_select.append('<a href="" data-mode="'+ mode +'" class="button es_code_editor_mode_btn es_code_editor_mode_btn_'+ mode +'">'+ this.supported_modes[ mode ] +'</a>');
		}

		// Mode Button Events

		this.dom.editor_mode_select.find('a').click(function( e ) {

			e.preventDefault();

			_this.set_mode( $(this).data('mode') );
		});

		//
		// Create Editor
		//
		this.ace_editor = ace.edit( this.dom.editor_wrapper[0] );

		this.ace_editor.setTheme("ace/theme/"+ this.options.theme );

		this.set_mode( this.options.mode );

		this.dom.editor_container.addClass('es_code_editor_mode_'+ this.options.mode);

		this.dom.editor_fullscreen_container = $('<div class="es_code_editor_fullscreen"></div>').addClass('es_code_editor_mode_'+ this.options.mode).addClass('es_code_editor');

		if ( this.options.auto_focus ) {
			this.ace_editor.focus();
		}
	};

	p.fullscreen = function() {

		var body = $(document.body);

		if ( body.hasClass('ace_fullscreen') ) {
			this.dom.editor_controls.appendTo( this.dom.editor_container );
			this.dom.editor_wrapper.appendTo( this.dom.editor_container );
			this.dom.editor_fullscreen_container.detach();
			body.removeClass('ace_fullscreen');
		}
		else {
			this.dom.editor_controls.appendTo( this.dom.editor_fullscreen_container );
			this.dom.editor_wrapper.appendTo( this.dom.editor_fullscreen_container );
			this.dom.editor_fullscreen_container.appendTo( body );
			body.addClass('ace_fullscreen');
		}

		this.ace_editor.resize();
	};

	p.set_mode = function( mode ) {

		mode = mode || 'html';

		this.ace_editor.getSession().setMode("ace/mode/"+ mode );

		this.dom.editor_mode_select.find('a').removeClass('es_code_editor_active_mode').removeClass('button-primary');

		this.dom.editor_mode_select.find('a[data-mode='+ mode +']').addClass('es_code_editor_active_mode').addClass('button-primary');

		return mode;
	};

	p.destroy = function() {

		var el;
		for ( var i in this.dom ) {

			el = this.dom[ i ];
			el.remove();
		}

		this.ace_editor = null;
	};

	/** Static Methods */
	//...

	// Reveal to the World
	window.ES_ACE_Code_Editor = ES_ACE_Code_Editor;

})(jQuery);
}

/**
 * TinyMCE Rich Text WYSIWYG (wp_editor via ajax)
 */
if ( 'undefined' == typeof ES_WP_Rich_Text_Editor ) {
	(function($){

		var ES_WP_Rich_Text_Editor = function( options ) {

			this.__construct( options );
		};
		// Shortcut to Prototype Object
		var p = ES_WP_Rich_Text_Editor.prototype;
		// Shortcut to Static Object
		var s = ES_WP_Rich_Text_Editor;

		//
		// Properties
		//

		/** Instance Properties */
		p.editor_id = null;
		p.dom = null;
		p.options = null;
		p.default_options = {

		};

		/** Static Properties */
		s.instance_count = 0;

		//
		// Methods
		//

		/** Instance Methods */

		/**
		 * Constructor
		 */
		p.__construct = function( options ) {

			s.instance_count++;

			if ( !options.id ) {
				options.id = 'es_rich_text_editor_'+ s.instance_count.toString();
			}

			this.editor_id = options.id;

			this.dom = {};
			this.dom.editor_container = $( options.element ).addClass('es_rich_text_editor');
			this.dom.post_status_info = $('<table class="es-post-status-info" cellspacing="0"><tbody><tr><td>Word count: <span class="es-word-count">0</span></td></tr></tbody></table>');
			this.dom.loading_indicator = $('<div class="es_rich_text_editor_loading_indicator"></div>').appendTo( this.dom.editor_container );

			this.options = {};
			this.options = $.extend( this.options, this.default_options, options );

			this.options.selector = '#'+ this.options.id;

			this.get_wp_editor();
		};

		p.init = function() {

			var _this = this;

			var _this = this, callback_setup = null;

			if ( 'undefined' != typeof this.options.setup ) {
				callback_setup = this.options.setup;
				delete this.options.setup;
			}
			else {
				callback_setup = function( _editor ){};
			}

			// HTML Mode Quicktags

			quicktags({
				id : _this.editor_id,
			});
			QTags._buttonsInit();

			var params = tinyMCEPreInit.mceInit.es_rich_text_editor;

			// Remove External Plugins (No way to make them all work outside post editor)

			if ( 'undefined' != typeof params.plugins ) {
				var plugin, plugins = params.plugins.split(',');
				for ( var i in plugins ) {
					plugin = plugins[ i ];
					if ( plugin.indexOf('-') === 0 ) delete plugins[ i ];
				}
				params.plugins = plugins.join(',');
			}

			params.editor_selector = this.editor_id;
			params.selector = '#'+ this.editor_id;
			params.elements = this.editor_id;

			params.setup = function( _editor ) {

				_editor.onInit.add(function( ed, args ) {

					var textarea = $('#'+ _this.editor_id );

					// Set TinyMCE as Default Mode

					var editor_wrap = textarea.closest('.wp-editor-wrap');

					editor_wrap.removeClass('html-active').addClass('tmce-active');

					// Word Count Markup

					_this.dom.post_status_info.insertAfter( editor_wrap );

					// Set Active Editor

					editor_wrap.mousedown(function( e ) {
						if ( this.id )
							wpActiveEditor = this.id.slice(3, -5);
					});

					// Word Count Events

					textarea.keyup(function( e ) {
						esWpWordCount.wc( textarea, textarea.val() );
					});

					ed.onKeyUp.add(function( _ed, e ) {
						esWpWordCount.wc( textarea, _ed.getContent() );
					});

					_this.dom.post_status_info.fadeIn( 200 );
					editor_wrap.fadeIn( 200, function() {
						_this.dom.loading_indicator.remove();
					});
				});

				callback_setup.call( this, _editor );
			};

			// TinyMCE Init

			tinyMCE.init( params );
		};

		p.get_editor = function() {

			return tinyMCE.get( this.editor_id );
		};

		p.destroy = function() {

			// Kill TinyMCE Instance (Doesn't work quite like I'd like it to yet)
			//this.get_editor().destroy();

			$('#wp_editbtns').hide();

			var el;
			for ( var i in this.dom ) {

				el = this.dom[ i ];
				el.remove();
			}
		};

		p.get_wp_editor = function() {

			var _this = this;

			var data = {
				action : 'es_cfct_tabs_accordion_get_wp_editor',
				editor_id : this.editor_id
			};

			// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
			jQuery.post( ajaxurl, data, function( response ) {
				$( response ).appendTo( _this.dom.editor_container );
				_this.init();
			});
		};

		/** Static Methods */
		//...

			// Reveal to the World
		window.ES_WP_Rich_Text_Editor = ES_WP_Rich_Text_Editor;

		// Fix TinyMCE/jQuery UI Compatibility
		$(document).on('focusin', function(e) {
			if ($(event.target).closest(".mce-window").length) {
				e.stopImmediatePropagation();
			}
		});

	})(jQuery);
}

/**
 * WP Media Manager Dialog
 *
 * @todo Default to "Library" tab
 */
if ( 'undefined' == typeof ES_WP_Media_Manager_Dialog ) {
(function($){

	var ES_WP_Media_Manager_Dialog = function( options ) {

		this.__construct( options );
	};
	// Shortcut to Prototype Object
	var p = ES_WP_Media_Manager_Dialog.prototype;
	// Shortcut to Static Object
	var s = ES_WP_Media_Manager_Dialog;

	//
	// Properties
	//

	/** Instance Properties */
	p.dom = null;
	p.frame = null;
	p.options = null;
	p.default_options = {
		title : 'Choose Your Image(s)',
		type : 'image',
		button_text : 'Choose Your Image(s)',
		multiple : false,
		post_id : -1
	};
	p.events = null;
	p.default_events = {
		select : function() {},
		update_frame : function() {}
	};

	/** Static Properties */
	//...

	//
	// Methods
	//

	/** Instance Methods */

	/**
	 * Constructor
	 */
	p.__construct = function( options ) {

		this.dom = {};

		this.options = {};
		this.options = $.extend( this.options, this.default_options, options );

		this.default_events = {};
		this.events = $.extend( this.events, this.default_events, (options && options.events) || {} );

		this.frame = wp.media({
			title: this.options.title,

			library: {
				type: this.options.type
			},

			button: {
				text: this.options.button_text
			},

			multiple: this.options.multiple
		});

		this.frame.on( 'open', this.events.update_frame ).state('library').on( 'select', this.events.select );

		// Set post_id to -1 so we query all images
		wp.media.model.settings.post.id = this.options.post_id;

		this.frame.open();
	};

	p.destroy = function() {

		var el;
		for ( var i in this.dom ) {

			el = this.dom[ i ];
			el.remove();
		}
	};

	/** Static Methods */
		//...

		// Reveal to the World
	window.ES_WP_Media_Manager_Dialog = ES_WP_Media_Manager_Dialog;

})(jQuery);
}