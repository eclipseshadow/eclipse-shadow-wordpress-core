if ( typeof es_widgets_admin == 'undefined' ) {

	es_widgets_admin = {

		widget_callbacks : new Array(),
		url_params : null,

		init : function() {

			//
			// Loading events on widget drop
			//
			jQuery("div.widgets-sortables").bind("sortstop", function( event, ui ) {

				var current_widget_obj = ui.item;

				if ( jQuery.trim( current_widget_obj[0].id ).length < 1 ) {
					// Adding a new widget - No id assigned yet (Wordpress assigns it via JS)

					jQuery(document).ajaxSuccess(function(e, xhr, settings) {
						if ( settings && settings.data && settings.data.search && settings.data.search('action=save-widget') != -1 ) {
							// It's now safe to grab the ID

							es_widgets_admin.bind_widget_events( current_widget_obj );
						}
					});
				}
				else {
					es_widgets_admin.bind_widget_events( current_widget_obj );
				}
			});

			//
			// Loading events on widget save
			//
			jQuery(document).bind("es-widget-form-load", function( event, current_widget_id ) {

				var current_widget_obj = jQuery("#"+ current_widget_id );

				es_widgets_admin.bind_widget_events( current_widget_obj );
			});

			//
			// Loading events on page load
			//
			jQuery(document).ready( function( $ ) {

				// Loop through all es widgets & bind registered events

				jQuery('.widgets-sortables .widget').each( function( index, widget ) {
					var current_widget_obj = jQuery(widget);

					es_widgets_admin.bind_widget_events( current_widget_obj );
				});
			});

			//
			// Fancy Widget Saving Indicator
			//
			jQuery(document).ready(function() {
				jQuery('.widgets-sortables').on('click', '.widget-control-save', function( e ) {

					var saving_modal = jQuery('<div class="es_widget_saving_modal"><div class="es_widget_saving_indicator"></div></div>');

					var container = jQuery(this).closest('.widget').find('.widget-content');

					container.append( saving_modal );

					saving_modal.fadeIn(200);
				});
			});

			//
			// WP Widget Extra Options
			//
			jQuery(document).ready(function() {
				jQuery('.widgets-sortables').on('click', '.es-widget-extra-options-link', function( e ) {
					e.preventDefault();

					var container = jQuery(this).siblings('.es-widget-extra-options');
					var widget_inside = jQuery(this).closest('.widget-inside');

					if ( container.is(':visible') ) {
						widget_inside.removeClass('es-widget-extra-options-visible');
						container.slideUp();
					}
					else {
						widget_inside.addClass('es-widget-extra-options-visible');
						container.slideDown();
					}
				});
			});

			//
			// Auto-Save Widgets when a[data-auto-save-on-unload] elements are clicked
			//
			jQuery(document).ready(function() {
				jQuery('.widgets-sortables').on('click', 'a[data-auto-save-on-unload="true"]', function( e ) {
					e.preventDefault();

					var link = jQuery(this);
					var href = link.attr('href');

					jQuery(document).ajaxStop(function() {

						setTimeout( function() { window.location.href = href; }, 1000);
					});

					link.closest('.widget').find('.widget-control-save').trigger('click');
				});
			});

			//
			// Parse URL Commands
			//
			jQuery(document).ready(function() {

				es_widgets_admin.url_params = es_widgets_admin.get_query_params( document.location.search );

				window.setTimeout( es_widgets_admin.run_url_command, 1000 );
			});

		},

		bind_widget_events : function( current_widget_obj ) {

			// Don't bind events if their already bound
			var load_script = current_widget_obj.find('.es_widget_load_script');

			if ( load_script.length < 1 || true === load_script.data('events-bound') ) return false;

			load_script.data('events-bound', true);

			var current_widget_id = current_widget_obj.attr('id');

			var id_base = es_widgets_admin.get_id_base_from_id( current_widget_id );

			if ( null == id_base ) return;

			if ( es_widgets_admin.widget_callbacks[ id_base ] ) {

				var callback = null;

				for ( var i in es_widgets_admin.widget_callbacks[ id_base ] ) {
					callback = es_widgets_admin.widget_callbacks[ id_base ][ i ].callback;

					callback.call( callback, current_widget_id );
				}
			}

		},

		addWidgetLoadCallback : function( widget_id_base, callback ) {

			if ( !es_widgets_admin.widget_callbacks[ widget_id_base ] ) {
				es_widgets_admin.widget_callbacks[ widget_id_base ] = new Array();
			}

			es_widgets_admin.widget_callbacks[ widget_id_base ].push({ callback : callback });

		},

		get_id_base_from_id : function( id ) {

			var matches = id.match(/(widget-\d+_)([-_A-Za-z0-9]+)(\d+)/);

			if ( matches && matches.length == 4 ) {
				id_base = matches[ 2 ];
				index_last_hyphen = id_base.lastIndexOf("-");
				id_base = id_base.substring( 0, index_last_hyphen );

				return id_base;
			}

			return null;

		},

		get_query_params : function ( qs ) {

			qs = qs.split("+").join(" ");

			var params = {}, tokens,
				re = /[?&]?([^=]+)=([^&]*)/g;

			while (tokens = re.exec(qs)) {
				params[decodeURIComponent(tokens[1])]
					= decodeURIComponent(tokens[2]);
			}

			return params;
		},

		run_url_command : function() {

			try {
				action = es_widgets_admin.url_params.es_wp_widget_action;

				switch( action ) {
					case 'edit_widget':
						var widget_admin_id = es_widgets_admin.url_params.es_wp_widget_id;
						es_widgets_admin.locate_and_scroll_to_widget( widget_admin_id );
						break;
				}
			}
			catch ( e ) {
				console.log( 'ES Widgets Admin - Run URL Command Error', e );
			}

		},

		locate_and_scroll_to_widget : function( widget_admin_id ) {

			/*var matches = widget_admin_id.match(/(widget-\d+_)(.*)/);

			if ( matches.length != 3 ) {
				console.log( 'ES Widgets Admin - Locate & Scroll To Widget Error' );
			}

			var partial_id = matches[2];*/

			var widget = jQuery( 'div[id*='+widget_admin_id+']' );

			if ( widget.length < 1 ) return;

			var widget_is_open = widget.find('.widget-inside').is(':visible');
			var sidebar_is_open = widget.closest('.widgets-sortables').is(':visible');

			if ( false == widget_is_open ) {
				// Open Widget
				widget.find('h4').first().trigger('click');
			}

			if ( false == sidebar_is_open ) {
				// Open Sidebar
				widget.closest('.widgets-holder-wrap').find('.sidebar-name').trigger('click');
			}

			var scroll_to = widget;

			jQuery('body').animate({ scrollTop: scroll_to.offset().top - 50 },{ duration : 500, complete : function() {
				widget.find('.widget-inside')
					.effect("highlight", { duration : 1000 })
					.effect("highlight", { duration : 1000 });
			}});

		}

	};

	jQuery(document).ready(function( $ ) {
		es_widgets_admin.init();
	});

}