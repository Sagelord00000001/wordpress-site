( function( $ ) {
    'use strict';

    $( function() {
        $( document ).on( 'click', '.js-es-create-page', function() {
            var $button = $( this );
            $button.prop( 'disabled', 'disabled' );
            $.post( ajaxurl, $( this ).data( 'request' ), function( response ) {
                response = response || {};

                if ( response.message ) {
                    Estatik_Admin.renderNotification( response.message );
                }
            }, 'json' ).fail( function() {
                Estatik_Admin.renderNotification( "<div class='es-notification es-notification--error'>Saving error. Please, contact estatik support.</div>" );
            } ).always( function() {
                $button.addClass( 'es-hidden' );
                $button.removeProp( 'disabled' ).removeAttr( 'disabled' );
            } );

            return false;
        } );

        $( '[name="es_settings[listings_layout]"]' ).on( 'change', function() {
            if ( $( this ).is( ':checked' ) ) {
                $( '.es-field__is_layout_switcher_enabled .es-field__label' ).html( $( '#es-field-is_layout_switcher_enabled' ).data( $( this ).val() + '-label' ) );
            }
        } ).trigger( 'change' );
    } );
} )( jQuery );
