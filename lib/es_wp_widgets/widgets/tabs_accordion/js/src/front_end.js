if ( jQuery ) {
(function($){

	$(document).ready(function(){

		$('.es_tabs').tabs();
		$('.es_accordion').accordion({
			activate: function( event, ui ) {

				var clicked = $(this).find('.ui-state-active');
				var url = clicked.data('ajax-url');
				var panel_id = clicked.attr('id') + '_panel';

				if ( url ) {
					$( '#'+ panel_id ).load( url );
				}
			}
		});

	});

})(jQuery);
}