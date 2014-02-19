/* WP External Links Plugin */
(function(){

	var $ = typeof jQuery == 'undefined' ? null : jQuery;

	// add event handler
	function addEvt ( el, evt, fn ) {
		if ( el.attachEvent ) {
			// IE method
			el.attachEvent( 'on'+ evt, fn );
		} else if ( el.addEventListener ) {
			// Standard JS method
			el.addEventListener( evt, fn, false );
		}
	};

	// open external link
	function openExtLink( a, opts, e ) {
		var options = opts ? opts : wpExtLinks,
			href = a.href ? a.href.toLowerCase() : '',
			rel = a.rel ? a.rel.toLowerCase() : '',
			n;

		if ( a.href && ( options.excludeClass.length == 0 || a.className.indexOf( options.excludeClass ) )
					&& ( rel.indexOf( 'external' ) > -1
							|| ( ( href.indexOf( options.baseUrl ) === -1 ) &&
									( href.substr( 0, 7 ) == 'http://'
										|| href.substr( 0, 8 ) == 'https://'
										|| href.substr( 0, 6 ) == 'ftp://'  ) ) ) ) {
			// open link in a new window
			n = window.open( a.href, options.target );
			n.focus();

			// prevent default event action
			if ( e ) {
				if ( e.preventDefault ) {
					e.preventDefault();
				} else {
					e.returnValue = false;
				}
			}
		}
	};

	if ( $ ) {
		// jQuery DOMready method
		$(function(){
			$( 'a' ).live( 'click', function( e ){
				openExtLink( this, null, e );
			});
		});
	} else {
		// use onload when jQuery not available
		addEvt( window, 'load', function () {
			var links = window.document.getElementsByTagName( 'a' ),
				a;

			// check each <a> element
			for ( var i = 0; i < links.length; i++ ) {
				a = links[ i ];

				// click event for opening in a new window
				addEvt( a, 'click', function( a ){
					return function ( e ) {
						openExtLink( a, null, e );
					}
				}( a ));
			}
		});
	}

})();
