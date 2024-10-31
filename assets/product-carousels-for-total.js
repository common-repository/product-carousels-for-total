( function( $, settings ) {

	'use strict';

	const init = () => {
		if ( 'undefined' === typeof $.fn.wpexOwlCarousel ) {
			return;
		}

		$( '.related.products, .up-sells.upsells.products' ).each( function() {
			const $this = $( this );
			const $target = $this.find( '> .products' );
			const $items = $target.find( 'li' );

			const prevIcon = settings.prevIcon || '<span class="ticon ticon-chevron-left" aria-hidden="true"></span>';
			const nextIcon = settings.nextIcon || '<span class="ticon ticon-chevron-right" aria-hidden="true"></span>';

			const defaults = {
				animateIn: false,
				animateOut: false,
				lazyLoad: false,
				autoplayHoverPause: true,
				autoHeight: false,
				autoWidth: false,
				loop: true,
				center: false,
				slideBy: 1,
				margin: 15,
				nav: true,
				dots: false,
				navText: [
					'<span class="screen-reader-text">' + settings.i18n.next + '</span>' + prevIcon,
					'<span class="screen-reader-text">' + settings.i18n.prev + '</span>' + nextIcon
				]
			};

			if ( ! $items.length ) {
				return;
			}

			if ( $this.hasClass( 'upsells' ) ) {
				defaults.items = settings.upsellsItems.items;
				defaults.responsive = settings.upsellsItems.responsive;
			} else if ( $this.hasClass( 'related' ) ) {
				defaults.items = settings.relatedItems.items;
				defaults.responsive = settings.relatedItems.responsive;
			}

			$target.addClass( 'wpex-carousel owl-carousel wpex-carousel-woocommerce-loop' );
			$target.removeClass( 'wpex-row' );

			$items.css( {
				'width': '100%',
				'margin': 0,
				'padding': 0
			} );

			if ( 'undefined' === typeof $.fn.imagesLoaded ) {
				$target.wpexOwlCarousel( $.extend( true, {}, defaults, settings ) );
			} else {
				$target.imagesLoaded( function() {
					$target.wpexOwlCarousel( $.extend( true, {}, defaults, settings ) );
				} );
			}
		} );

	};

	$( document ).ready( function() {
		init();
	} );

} ) ( jQuery, totalWooCarouselSettings );