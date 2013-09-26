(function($,undefined) {
	esWpWordCount = {

		settings : {
			strip : /<[a-zA-Z\/][^<>]*>/g, // strip HTML tags
			clean : /[0-9.(),;:!?%#$¿'"_+=\\/-]+/g, // regexp to remove punctuation, etc.
			w : /\S\s+/g, // word-counting regexp
			c : /\S/g // char-counting regexp for asian languages
		},

		block : 0,

		wc : function(textarea, tx, type) {

			if ( tx == undefined ) return;

			var t = this, tc = 0, wc_el = textarea.closest('.es_rich_text_container').find('.es-word-count');

			if ( type === undefined )
				type = wordCountL10n.type;
			if ( type !== 'w' && type !== 'c' )
				type = 'w';

			if ( t.block )
				return;

			t.block = 1;

			setTimeout( function() {
				if ( tx ) {
					tx = tx.replace( t.settings.strip, ' ' ).replace( /&nbsp;|&#160;/gi, ' ' );
					tx = tx.replace( t.settings.clean, '' );
					tx.replace( t.settings[type], function(){tc++;} );
				}
				wc_el.html(tc.toString());

				setTimeout( function() { t.block = 0; }, 2000 );
			}, 1 );
		}
	}
}(jQuery));
