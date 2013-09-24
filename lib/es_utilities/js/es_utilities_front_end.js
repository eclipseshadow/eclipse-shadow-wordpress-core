(function($, undefined) {

	$(document).ready(function() {
		$('.widget_es-cfct-loop, .es-cfct-loop').each(function( i, widget ) {
			widget = $(widget);
			if ( widget.hasClass('cfct-module') ) {
				var id = widget.closest('.cfct-module-border').attr('id');
			}
			else {
				var id = widget.attr('id');
			}

			widget.on('click', '.Zebra_Pagination a', function( e ) {
				e.preventDefault();

				var link = $(this);
				var href = link.attr('href');

				if ( href != 'javascript:void(0)' ) {
					link.closest('.cfct-mod-content').load( href +' #'+ id +' .es_loop_widget_inner_wrap' );
				}
			});
		});
	});

})(jQuery);