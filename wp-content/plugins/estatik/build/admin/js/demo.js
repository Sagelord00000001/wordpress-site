( function( $ ) {
    'use strict';

    if ( ! String.prototype.format ) {
        String.prototype.format = function() {
            var args = arguments;
            return this.replace(/{(\d+)}/g, function( match, number ) {
                return typeof args[ number ] != 'undefined' ? args[ number ] : match;
            });
        };
    }

    $( function() {
        $( '.js-es-demo-pagination a, .js-es-next-step' ).click( function() {
            var id = $( this ).attr( 'href' );

            $( '.js-es-demo-pagination .es-active' ).removeClass( 'es-active' );
            $( '.js-es-demo-pagination' ).find( '[href="' + id + '"]' ).addClass( 'es-active' );

            $( '.es-step:not(.es-step--hidden)' ).fadeTo( 'slow', 0, function() {
                $( this ).addClass( 'es-step--hidden' ).css( 'display', 'none' );
                $( id ).fadeTo( 'slow', 1, function() {
                    $( this ).removeClass( 'es-step--hidden' );
                } );
            } );

            return false;
        } );

        $( '.js-es-btn--finish' ).closest( 'form' ).on( 'submit', function() {
            var $btn = $( this ).find( '.js-es-btn--finish' );
            $btn.attr( 'disabled', 'disabled' ).addClass( 'es-preload' );
        } );

        $( '.js-es-country-field' ).on( 'change', function() {
            if ( $( this ).is( ':checked' ) ) {
                var $description = $( this ).closest( '.es-field--multiple-checks' ).find( '.es-field__description' );
                var country_data = Estatik.country_data;
                var value = $( this ).val();
                var description = $( this ).data( 'description' );

                if ( value &&  typeof country_data[value] !== 'undefined' ) {
                    var data = country_data[value];
                    $description.html( description.format(
                        data.language_label,
                        data.currency,
                        data.area_unit.replace( '_', ' ' ),
                        data.lot_size_unit.replace( '_', ' ' ) ) );

                    $description.removeClass( 'es-hidden' );
                } else {
                    $description.addClass( 'es-hidden' );
                }
            }
        } ).trigger( 'change' );
    } );
} )( jQuery );
