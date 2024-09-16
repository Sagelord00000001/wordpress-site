( function( $ ) {
    'use strict';

    var estatikPopup = function( options ) {
        options = options || {};
        var $popup;

        var $skeleton = $( "<div class='es-popup-wrapper js-es-popup-wrapper'>" +
            "<div class='es-popup'>" +
                "<div class='es-popup__inner'>" +
                    "<a href='' class='es-popup__close js-es-popup__close'><span class='es-icon es-icon_close'></a>" +
                "</div>" +
                "<div class='es-popup__content js-es-popup__content'></div>" +
                "</div>" +
            "</div>" ) || options.skeleton;

        options.skeleton = options.skeleton || $skeleton;

        if ( options.inline_html ) {
            $popup = options.skeleton;
            $popup.find( '.js-es-popup__content' ).html( options.inline_html )
        }

        this.open = function() {
            $popup.addClass( 'es-popup--active' );
            $( 'body' ).append( $popup );
        };

        this.close = function() {
            $( '.js-es-popup-wrapper' ).remove();
        };

        $( document ).on( 'click', '.js-es-popup__close', function() {
            $( this ).closest( '.js-es-popup-wrapper' ).remove();

            return false;
        } );

        return this;
    };

    jQuery.estatikPopup = estatikPopup;
} )( jQuery );
