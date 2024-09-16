( function( $ ) {
    'use strict';

    if ( typeof elementorFrontend !== 'undefined' ) {
        $( window ).on( 'elementor/frontend/init', function () {
            if ( typeof window.ElementorInlineEditor == 'undefined' ) {
                return;
            }
            // Initialize js for properties hfm.
            elementorFrontend.hooks.addAction( 'frontend/element_ready/global', function ( e, $scope ) {
                EstatikResponsinator.init();

                if ( $scope.find( '.js-es-slick' ) ) {
                    EstatikProperties.initCarousel( e, $scope );
                }

                if ( $scope.find( '.js-es-properties' ) ) {
                    $( document ).find( '.js-es-properties__map.es-properties__map--visible' ).each( function() {
                        var $properties_wrap = $( this ).closest( '.js-es-properties' );
                        var map_instance = new EstatikHalfMap( $properties_wrap );
                        var $listings_wrapper = $properties_wrap.find( '.js-es-listings' );
                        var hash = new EstatikEntitiesHash( $listings_wrapper.data( 'hash' ) );

                        EstatikProperties.halfMapInstances[ hash.getValue( 'loop_uid' ) ] = map_instance;

                        if ( map_instance ) {
                            map_instance.init();
                        }
                    } );
                }
            } );
        } );
    }
} )( jQuery );