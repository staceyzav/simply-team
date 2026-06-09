( function () {
	'use strict';

	document.addEventListener( 'DOMContentLoaded', function () {

		var overlay = document.querySelector( '.st-overlay' );

		function openPanel( panel, trigger ) {
			panel.setAttribute( 'aria-hidden', 'false' );
			panel.classList.add( 'is-open' );
			if ( trigger ) trigger.setAttribute( 'aria-expanded', 'true' );
			if ( overlay ) overlay.classList.add( 'is-visible' );
			document.body.classList.add( 'st-panel-open' );

			// Focus the close button for accessibility
			var closeBtn = panel.querySelector( '.st-panel__close' );
			if ( closeBtn ) closeBtn.focus();
		}

		function closeAll() {
			document.querySelectorAll( '.st-panel.is-open' ).forEach( function ( panel ) {
				panel.setAttribute( 'aria-hidden', 'true' );
				panel.classList.remove( 'is-open' );
			} );
			document.querySelectorAll( '.st-card__more[aria-expanded="true"]' ).forEach( function ( btn ) {
				btn.setAttribute( 'aria-expanded', 'false' );
			} );
			if ( overlay ) overlay.classList.remove( 'is-visible' );
			document.body.classList.remove( 'st-panel-open' );
		}

		// More Info buttons — open panel
		document.querySelectorAll( '.st-card__more' ).forEach( function ( btn ) {
			btn.addEventListener( 'click', function () {
				var panelId = btn.getAttribute( 'aria-controls' );
				if ( ! panelId ) return;
				var panel = document.getElementById( panelId );
				if ( ! panel ) return;

				if ( panel.classList.contains( 'is-open' ) ) {
					closeAll();
				} else {
					closeAll();
					openPanel( panel, btn );
				}
			} );
		} );

		// Close buttons inside panels
		document.querySelectorAll( '.st-panel__close' ).forEach( function ( btn ) {
			btn.addEventListener( 'click', closeAll );
		} );

		// Overlay click closes all
		if ( overlay ) {
			overlay.addEventListener( 'click', closeAll );
		}

		// Escape key closes all
		document.addEventListener( 'keydown', function ( e ) {
			if ( e.key === 'Escape' ) closeAll();
		} );

	} );

} )();
