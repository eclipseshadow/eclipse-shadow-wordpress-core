if ( typeof ES_User_Assistance == 'undefined' ) {
	ES_User_Assistance = {

		init : function() {

			jQuery(document).ready(function(){

				jQuery('.es_goto_link').click( ES_User_Assistance.do_goto_link );

				ES_Admin_Message.init();
			});

			//
			// Initialize all "Return-To" links
			//

			jQuery(document).bind('es-admin-messages-loaded', initialize_return_to_messages);

			function initialize_return_to_messages() {

				// Attach Javascript for "Return-To" links that refer to parent window
				var msg;
				for ( var i in ES_Admin_Message.message_instances ) {
					msg = ES_Admin_Message.message_instances[ i ];

					if ( 'undefined' != typeof msg.options.return_to && 'parent_window' == msg.options.return_to ) {
						msg.el.find('.es_return_to_message_link').click(function( e ) {
							e.preventDefault();

							msg.destroy();

							parent.focus();
							window.close();
						});
					}
					else if ( 'undefined' != typeof msg.options.is_return_to_message ) {
						msg.el.find('.es_return_to_message_link').click(function( e ) {
							msg.destroy();
						});
					}
				}

				// Create new "Return-To" message bindings
				jQuery('#cfct-popup, .widgets-sortables .widget').on('click', 'a[data-return-to-url]', function( e ) {

					// Remove all existing "Return-To" messages before creating a new one
					var msg;
					for ( var i in ES_Admin_Message.message_instances ) {
						msg = ES_Admin_Message.message_instances[ i ];

						if ( 'undefined' != typeof msg.options.is_return_to_message ) {
							msg.destroy();
						}
					}

					var link = jQuery(this);
					var message = link.data('return-to-message');
					var url = link.data('return-to-url');

					var options = {
						type : 'notice',
						msg_ttl : 60 /* mins */ *60*1000,
						persist_across_page_loads : true,
						show_on_next_page_load : true,
						is_return_to_message : true
					};

					if ( 'current_window' == url ) {
						url = '#';
						options.return_to = 'parent_window';
					}

					var content = '<a class="es_return_to_message_link" href="'+ url +'">'+ message +'</a>';

					var msg = new ES_Admin_Message( content, options );

					//
					// Message Destroy on Widget Save
					//

					// WP Widget Admin
					/*link.closest('.widget').find('.widget-control-save').click(function( e ) {
						msg.destroy();
					});*/

					// Carrington Build
					var popup = jQuery('#cfct-popup');
					popup.find('input[value="Save"], #cfct-edit-module-cancel').click(function( e ) {
						msg.destroy();
					});
				});
			}

		},

		do_goto_link : function( e ) {

			e.preventDefault();

			var link = jQuery(this);
			var target_selectors = link.attr('href');
			var parent_selector = link.data('parent-selector');
			var pad_parent_h = link.data('pad-parent-h');
			var pad_parent_v = link.data('pad-parent-v');

			var target = jQuery( target_selectors ).first();

			var closest_parent;

			if ( 'undefined' != parent_selector ) {
				if ( 'self' == parent_selector ) {
					closest_parent = target;
				}
				else {
					closest_parent = target.closest( parent_selector );
				}
			}
			else {
				closest_parent = target.parent().first();
			}

			jQuery('body').animate({ scrollTop: closest_parent.offset().top - 150 },{ duration : 500, complete : function() {

				var closest_target_display = closest_parent.css('display');

				if ( 'inline' == closest_target_display ) {
					closest_parent.addClass('es-goto-link-target-display-inline-block');
				}

				if ( false !== pad_parent_h ) {
					closest_parent.addClass('es-goto-link-target-pad-h');
				}

				if ( false !== pad_parent_v ) {
					closest_parent.addClass('es-goto-link-target-pad-v');
				}

				closest_parent.effect("highlight", { duration : 1000 })
					.effect("highlight", { duration : 1000 })
					.effect("highlight", { duration : 1000, complete : function() {
						closest_parent
							.removeClass('es-goto-link-target-active')
							.removeClass('es-goto-link-target-display-inline-block')
							.removeClass('es-goto-link-target-pad-h')
							.removeClass('es-goto-link-target-pad-v');
					} })
			}});

		},

		/**
		 * Set Cookie - Utility Method
		 *
		 * @param name
		 * @param value
		 * @param days
		 */
		set_cookie : function( name, value, days ) {

			if ( days ) {
				var date = new Date();
				date.setTime( date.getTime() + (days*24*60*60*1000) );
				var expires = "; expires="+ date.toGMTString();
			}
			else {
				var expires = "";
			}
			document.cookie = name +"="+ value + expires +"; path=/";
		},

		/**
		 * Get Cookie - Utility Method
		 *
		 * @param name
		 * @returns {*}
		 */
		get_cookie : function( name ) {

			var nameEQ = name +"=";
			var ca = document.cookie.split(';');
			for ( var i=0; i < ca.length; i++ ) {
				var c = ca[ i ];
				while (c.charAt(0) == ' ' ) {
					c = c.substring( 1, c.length );
					if ( c.indexOf( nameEQ ) == 0 ) {
						return c.substring( nameEQ.length, c.length );
					}
				}
			}
			return null;
		},

		/**
		 * Erase Cookie - Utility Method
		 *
		 * @param name
		 */
		erase_cookie : function( name ) {

			s.set_cookie( name, "", -1 );
		}

	};

	ES_User_Assistance.init();
}

if ( 'undefined' == typeof ES_Admin_Message ) {
	(function($) {

		/**
		 * Main Class Definition
		 *
		 * @todo Add request scope to Return-To messages to kill persistent messages if user navigates away from the intended scope (ie. slidedeck.php when editing slidedecks)
		 */
		var ES_Admin_Message = function( message, options ) {

			this.__construct( message, options );

			// Reveal Public Properties/Methods by overriding the return value
			/*return {
				message : this.message,
				el : this.el,
				id : this.id,
				options : this.options,
				time_created : this.time_created,

				show : this.show,
				hide : this.hide,
				destroy : this.destroy
			}*/
		};
		// Shortcut to Prototype Object
		var p = ES_Admin_Message.prototype;
		// Shortcut to Static Object
		var s = ES_Admin_Message;

		//
		// Properties
		//

		// Instance Properties
		p.message = '';
		p.id = '';
		p.options = {};
		p.default_options = {
			type : 'notice', // Message Type (see p.message_types)
			msg_ttl :  .5 /* mins */ * 60 * 1000, // Message Time to Live in ms
			persist_across_page_loads : false,
			btn_close : true, // Button: Close
			btn_dsa : true, // Button: Don't Show Again (dsa)
			animate : true,
			show_on_next_page_load : false // Set show_on_next_page_load to false to wait until next page load to show
		};
		p.message_types = {
			notice : { color : 'f8f8db', alt_color_1 : 'f8f8db', alt_color_2 : 'f7f7be' },
			success : { color : 'def6dc', alt_color_1 : 'def6dc', alt_color_2 : 'c6f5c1' },
			warning : { color : 'ffd8d4', alt_color_1 : 'ffd8d4', alt_color_2 : 'fcbdb8' }
		};

		p.el = null;
		p.time_created = null;
		p.expiry_timer = null;

		p.bg_animate_1 = null;
		p.bg_animate_2 = null;

		// Static Properties
		s.message_instances = new Array();
		s.cookie_name = 'es_admin_messages';
		s.cookie_ttl = 30; // days

		//
		// Methods
		//


		// Instance Methods

		/**
		 * Constructor
		 */
		p.__construct = function( message, options ) {

			if ( 'undefined' == typeof message ) message = '';
			if ( 'undefined' == typeof options ) options = new Array();

			var _this = this; // Only need this for accessing instance inside of an event handler function

			// Message
			this.message = message;

			// HTML ID
			this.id = 'es_admin_message_'+ (s.message_instances.length + 1).toString();

			// Message Options
			var new_options = {};
			this.options = $.extend( new_options, this.default_options, options );

			// Time Created
			if ( 'undefined' != typeof options.time_created ) {
				this.time_created = options.time_created;
			}
			else {
				var current_date = new Date();
				this.time_created = current_date.getTime();
			}

			// Expiry Timer
			this.expiry_timer = setTimeout( function() { _this.destroy(); }, this.options.msg_ttl );

			// Dom Element
			this.el = $('<div id="'+ this.id +'" class="es_admin_message es_admin_message_'+ this.options.type +'"><div class="es_admin_message_content">'+ message +'</div></div>');
			this.el.css('display', 'none');

			// Message Controls (buttons)
			var message_controls = $('<div class="es_admin_message_controls"></div>').prependTo( this.el );

			if ( this.options.btn_close ) {
				var btn_close = $('<a href="#" class="es_admin_message_close">Close</a>').click(function( e ) {
					e.preventDefault();

					_this.hide();
				});

				message_controls.append( btn_close );
			}

			if ( this.options.btn_dsa ) {
				var btn_dsa = $('<a href="#" class="es_admin_message_dsa">Don\'t Show This Message Again</a>').click(function( e ) {
					e.preventDefault();

					_this.destroy();
				});

				message_controls.append( btn_dsa );
			}

			this.show();

			// Add to current instances array
			s.message_instances.push( this );

			s.save_messages_to_cookie();
		};

		/**
		 * Show Admin Message
		 */
		p.show = function() {

			if ( this.options.show_on_next_page_load ) return;

			var _this = this;

			$('#wpcontent').prepend( this.el );

			this.el.slideDown( 500, function() {
				if ( _this.options.animate ) {
					_this.start_animation();
				}
			});
		};

		/**
		 * Hide Admin Message
		 */
		p.hide = function() {

			this.stop_animation();

			this.el.slideUp( 500, function() {
				$(this).remove();
			});

			clearTimeout( this.expiry_timer );
		};

		/**
		 * Permanently Destroy Admin Message
		 */
		p.destroy = function() {

			this.options.msg_ttl = -1;
			this.hide();
			s.save_messages_to_cookie();
		};

		/**
		 * Start Admin Message BG Color Animation
		 */
		p.start_animation = function() {

			var _this = this;

			this.bg_animate_1 = function() {
				_this.el.animate({ backgroundColor: '#'+ _this.message_types[ _this.options.type ].alt_color_1 }, { duration : 1000, queue : false, complete : function() { _this.bg_animate_2(); } });
			}

			this.bg_animate_2 = function() {
				_this.el.animate({ backgroundColor: '#'+ _this.message_types[ _this.options.type ].alt_color_2 }, { duration : 1000, queue : false, complete : function() { _this.bg_animate_1(); } });
			}

			this.bg_animate_1();
		};

		/**
		 * Stop Admin Message BG Color Animation
		 */
		p.stop_animation = function() {

			this.el.stop();
		};

		// Static Methods

		/**
		 * Initializes All Messages on Page Load
		 */
		s.init = function() {

			var message_objects = s.get_messages_from_cookie();
			var current_time = new Date().getTime();

			s.message_instances = new Array();

			if ( null != message_objects ) {
				var msg = null;

				for ( var i in message_objects ) {
					msg = message_objects[ i ];

					if ( msg.time_created + msg.options.msg_ttl < current_time ) continue;

					var options = {
						time_created : msg.time_created
					};

					new ES_Admin_Message( msg.message, $.extend( msg.options, options ) );
				}
			}

			setTimeout(function() {
				var msg;
				for ( var i in s.message_instances ) {
					msg = s.message_instances[ i ];
					msg.show();
				}
			}, 1000);

			$(document).triggerHandler('es-admin-messages-loaded');
		};

		/**
		 * Retrieves message objects stored in cookie
		 *
		 * @returns {*}
		 */
		s.get_messages_from_cookie = function() {

			var messages = JSON.parse( ES_User_Assistance.get_cookie( s.cookie_name ) );

			return messages;
		};

		/**
		 * Stores message objects in cookie
		 */
		s.save_messages_to_cookie = function() {

			var message_objects = new Array();
			var msg_obj;
			for ( var i in s.message_instances ) {
				msg_obj = s.message_instances[ i ];

				var current_time = new Date().getTime();

				// Avoid re-initializing messages that were most likely inteded to be short-lived
				if ( msg_obj.time_created + msg_obj.options.msg_ttl < current_time + 5000 ) continue;

				if ( false == msg_obj.options.persist_across_page_loads ) continue;

				if ( 'undefined' != typeof msg_obj.options.show_on_next_page_load ) delete msg_obj.options.show_on_next_page_load;

				message_objects.push({
					message : msg_obj.message,
					options : msg_obj.options,
					time_created : msg_obj.time_created
				});
			}

			var str_json = JSON.stringify( message_objects );

			ES_User_Assistance.set_cookie( s.cookie_name, str_json, s.cookie_ttl );
		};

		// Reveal to outside world
		window.ES_Admin_Message = ES_Admin_Message;

	})(jQuery);
}