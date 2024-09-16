( function( $ ) {
    'use strict';

    jQuery.fn.estatikProgress = function(options) {
        var $el = $( this );
        options = options || {};

        options = Object.assign( {}, {
            progress: 0
        }, options );

        if ( ! $el.hasClass( 'es-progress--initialized' ) ) {
            $el.addClass( 'es-progress--initialized' );
            $el.append( "<div class='es-progress'>" +
                "<div class='es-progress__inner'></div>" +
                "<span class='es-progress__percent'>" + options.progress + "%</span>" +
                "</div>" );
        }

        $el.find( '.es-progress__inner' ).animate( {width: options.progress + '%'}, 200 );
        $el.find( '.es-progress__percent' ).html( options.progress + "%" );

        return this;
    };

} )( jQuery );
