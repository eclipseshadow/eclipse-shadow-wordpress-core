if ( 'undefined' == typeof ES_Carrington_Build ) {

	ES_Carrington_Build = {

		init : function() {

			//
			// Fancy Widget Saving Indicator
			//
			jQuery(document).ready(function() {
				jQuery('#cfct-popup').on('click', 'input[value="Save"]', function( e ) {

					var popup = jQuery('#cfct-popup');

					var saving_modal = jQuery('<div class="es_widget_saving_modal"><div class="es_widget_saving_indicator"></div></div>');

					var container = popup.find('.cfct-popup-content');

					container.append( saving_modal );

					saving_modal.width( container.outerWidth() );
					saving_modal.height( container.outerHeight() );

					saving_modal.fadeIn(200);
				});
			});

			//
			// Auto-Save Widgets & Post when a[data-auto-save-on-unload] elements are clicked
			//
			/*jQuery(document).ready(function() {

				jQuery('#cfct-popup').on('click', 'a[data-auto-save-on-unload="true"]', function( e ) {
					e.preventDefault();

					var link = jQuery(this);
					var href = link.attr('href');

					var popup = jQuery('#cfct-popup');

					jQuery(document).ajaxStop(function() {

						setTimeout( function() { window.location.href = href; }, 1000);
					});

					// WP Autosave Attempt
					autosave();

					// Trigger Save Action on CB Module
					popup.find('input[value="Save"]').trigger('click');
				});
			});*/

			//
			// Set links "Return-To" data attr to open in new tab
			//
			jQuery(document).bind('es-carrington-module-form-load', function( e ) {

				var popup = jQuery('#cfct-popup');

				var links = popup.find('a[data-return-to-url]');

				links.each(function( index, link ) {

					link = jQuery(link);

					link.attr('target', '_blank');
				});
			});

			//
			// String replace {{carrington_widget_post_id}} placeholder in return-to urls with current post ID
			//
			/*jQuery(document).bind('es-carrington-module-form-load', function( e ) {

				var popup = jQuery('#cfct-popup');
				var post_id = jQuery('#post_ID').val();

				var links = popup.find('a[data-return-to-url]');

				links.each(function( index, link ) {

					link = jQuery(link);

					var url = link.data('return-to-url');
					var new_url = url.replace( '{{carrington_widget_post_id}}', post_id );

					link.attr('data-return-to-url', new_url);
				});
			});*/

		}

	};
	ES_Carrington_Build.init();
}