jQuery( function ( $ ) {

	var $tbody = $( '#the-list' );
	if ( ! $tbody.length ) return;

	$tbody.sortable( {
		items:  'tr',
		handle: '.st-drag-handle',
		axis:   'y',
		helper: function ( e, tr ) {
			tr.children().each( function () {
				$( this ).width( $( this ).width() );
			} );
			return tr;
		},
		start: function ( e, ui ) {
			ui.item.addClass( 'st-dragging' );
		},
		stop: function ( e, ui ) {
			ui.item.removeClass( 'st-dragging' );
			saveOrder();
		},
	} );

	function saveOrder() {
		var order = [];
		$tbody.find( 'tr' ).each( function () {
			var id = $( this ).attr( 'id' );
			if ( id ) {
				order.push( id.replace( 'post-', '' ) );
			}
		} );

		$.post( stSort.ajaxurl, {
			action: 'st_save_order',
			nonce:  stSort.nonce,
			order:  order,
		} );
	}

} );
