if ( typeof es_widgets_admin == 'undefined' ) {

	es_widgets_admin = {

		init : function() {

			jQuery('.widget-area .widget').each(function( index, widget ) {
				widget = jQuery(widget);

				var edit_link = jQuery('<a class="es-wp-widget-edit-link" href="/wp-admin/widgets.php?es_wp_widget_action=edit_widget&es_wp_widget_id='+ widget.attr('id') +'">Edit</a>');

				widget.append( edit_link );

				widget.hover(
				function(){
					jQuery(this).addClass('es-cfct-widget-hover');
					edit_link.show();
				},
				function() {
					jQuery(this).removeClass('es-cfct-widget-hover');
					edit_link.hide();
				});
			});

		}

	}

	jQuery(document).ready(function() {
		es_widgets_admin.init();
	});
}