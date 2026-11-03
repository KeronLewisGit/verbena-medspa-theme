/**
 * Verbena Aesthetics front-end behaviour. No dependencies, loaded with defer.
 *
 * - dataLayer events: book_now_click, phone_click (generate_lead is printed
 *   server-side after a successful form submission).
 * - Mobile navigation.
 * - Treatment area filter (progressive enhancement of real links).
 * - Click-to-load facades for the booking widget and the map.
 */
( function () {
	'use strict';

	var config = window.verbenaConfig || {};
	window.dataLayer = window.dataLayer || [];

	function track( event, params ) {
		var payload = { event: event };
		Object.keys( params || {} ).forEach( function ( key ) {
			payload[ key ] = params[ key ];
		} );
		window.dataLayer.push( payload );
		if ( config.analyticsMode === 'gtag' && typeof window.gtag === 'function' ) {
			window.gtag( 'event', event, params || {} );
		}
	}
	window.verbenaTrack = track;

	// Analytics: delegated clicks.
	document.addEventListener( 'click', function ( e ) {
		var target = e.target instanceof Element ? e.target : null;
		if ( ! target ) {
			return;
		}
		var book = target.closest( '[data-book]' );
		if ( book ) {
			track( 'book_now_click', {
				treatment_name: book.getAttribute( 'data-treatment' ) || '(not set)',
				link_location: book.getAttribute( 'data-location' ) || 'content',
			} );
		}
		var tel = target.closest( 'a[href^="tel:"]' );
		if ( tel ) {
			track( 'phone_click', {
				link_location: tel.getAttribute( 'data-location' ) || 'content',
			} );
		}
	} );

	// Mobile navigation.
	var toggle = document.querySelector( '[data-nav-toggle]' );
	var panel = document.querySelector( '[data-nav-panel]' );
	if ( toggle && panel ) {
		var label = toggle.querySelector( '[data-nav-label]' );
		var setOpen = function ( open, returnFocus ) {
			toggle.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
			panel.hidden = ! open;
			if ( label ) {
				label.textContent = open ? 'Close menu' : 'Open menu';
			}
			document.documentElement.classList.toggle( 'overflow-hidden', open );
			if ( open ) {
				var first = panel.querySelector( 'a' );
				if ( first ) {
					first.focus();
				}
			} else if ( returnFocus ) {
				toggle.focus();
			}
		};
		toggle.addEventListener( 'click', function () {
			setOpen( toggle.getAttribute( 'aria-expanded' ) !== 'true', false );
		} );
		document.addEventListener( 'keydown', function ( e ) {
			if ( e.key === 'Escape' && ! panel.hidden ) {
				setOpen( false, true );
			}
		} );
		panel.addEventListener( 'focusout', function ( e ) {
			if ( ! panel.hidden && e.relatedTarget && ! panel.contains( e.relatedTarget ) && e.relatedTarget !== toggle ) {
				setOpen( false, false );
			}
		} );
		window.matchMedia( '(min-width: 64rem)' ).addEventListener( 'change', function ( mq ) {
			if ( mq.matches ) {
				setOpen( false, false );
			}
		} );
	}

	// Treatment filter.
	document.querySelectorAll( '[data-treatment-filter]' ).forEach( function ( wrap ) {
		var chips = wrap.querySelectorAll( '[data-filter]' );
		var cards = wrap.querySelectorAll( '[data-treatment-card]' );
		var status = wrap.querySelector( '[data-filter-status]' );
		var apply = function ( value ) {
			var shown = 0;
			cards.forEach( function ( card ) {
				var areas = ( card.getAttribute( 'data-area' ) || '' ).split( ' ' );
				var match = value === 'all' || areas.indexOf( value ) !== -1;
				card.hidden = ! match;
				if ( match ) {
					shown++;
				}
			} );
			chips.forEach( function ( chip ) {
				chip.setAttribute( 'aria-pressed', chip.getAttribute( 'data-filter' ) === value ? 'true' : 'false' );
			} );
			if ( status ) {
				status.textContent = shown + ( shown === 1 ? ' treatment shown' : ' treatments shown' );
			}
		};
		chips.forEach( function ( chip ) {
			chip.setAttribute( 'role', 'button' );
			chip.addEventListener( 'click', function ( e ) {
				e.preventDefault();
				apply( chip.getAttribute( 'data-filter' ) );
			} );
			chip.addEventListener( 'keydown', function ( e ) {
				if ( e.key === ' ' ) {
					e.preventDefault();
					apply( chip.getAttribute( 'data-filter' ) );
				}
			} );
		} );
		var current = wrap.querySelector( '[data-filter][aria-pressed="true"]' );
		if ( current && current.getAttribute( 'data-filter' ) !== 'all' ) {
			apply( current.getAttribute( 'data-filter' ) );
		}
	} );

	// Click-to-load facades (booking widget, map).
	document.addEventListener( 'click', function ( e ) {
		var btn = e.target instanceof Element ? e.target.closest( '[data-facade-load]' ) : null;
		if ( ! btn ) {
			return;
		}
		var host = btn.closest( '[data-facade]' );
		var src = btn.getAttribute( 'data-src' );
		if ( ! host || ! src ) {
			return;
		}
		var frame = document.createElement( 'iframe' );
		frame.src = src;
		frame.title = btn.getAttribute( 'data-title' ) || '';
		frame.loading = 'eager';
		frame.referrerPolicy = 'strict-origin-when-cross-origin';
		frame.className = 'block w-full border-0 bg-white';
		if ( host.getAttribute( 'data-facade-kind' ) === 'booking' ) {
			frame.style.height = ( parseInt( btn.getAttribute( 'data-height' ), 10 ) || 760 ) + 'px';
			track( 'book_now_click', {
				treatment_name: btn.getAttribute( 'data-treatment' ) || '(not set)',
				link_location: 'booking_widget',
			} );
		} else {
			frame.className += ' absolute inset-0 h-full';
		}
		var placeholder = host.querySelector( '[data-facade-placeholder]' );
		if ( placeholder ) {
			placeholder.remove();
		}
		host.appendChild( frame );
		frame.focus();
	} );

	// Move focus to form feedback after a redirect.
	var feedback = document.querySelector( '[data-focus-on-load]' );
	if ( feedback ) {
		feedback.focus();
	}
}() );
