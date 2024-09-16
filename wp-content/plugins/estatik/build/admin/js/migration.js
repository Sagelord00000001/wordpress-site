( function( $ ) {
    'use strict';

    var $progressContainer;
    var $form;
    var $loggerContainer;

    function esMigrate( data ) {
        $.ajax({
            url: Estatik_Migration.ajaxurl,
            data: data,
            type: "POST",
            processData: false,  // tell jQuery not to process the data
            contentType: false,   // tell jQuery not to set contentType
            success: function( response ) {
                response = response || {};

                var formData = new FormData();

                for ( var i in response ) {
                    formData.append( i, response[i] );
                }

                if ( response.progress ) {
                    $progressContainer.show().estatikProgress( {progress: response.progress} );
                }

                if ( response.messages && response.messages ) {
                    for ( var type in response.messages ) {
                        if ( response.messages[type] && response.messages[type][response.index] !== 'undefined' ) {
                            for ( var j in response.messages[type] ) {
                                $loggerContainer.append( '<div class="es-notify es-notify--' + type + '">' + response.messages[type][j] + '</div>' );
                            }
                        }
                    }
                }

                if ( ! response.done ) {
                    esMigrate( formData );
                } else {
                    setTimeout( function() {
                        window.location.href = response.redirect_url;
                    }, 2000 );
                }
            },
            dataType: 'json'
        } ).fail( function() {
            $loggerContainer.append( "<div class='es-notify es-notify--error'>" + Estatik_Migration.tr.internal_error + "</div>" );
            $form.find('[type=submit]').removeAttr( 'disabled' ).removeClass( 'es-preload' );
        } );
    }

    $(function() {
        $progressContainer = $('#es-migration-progress');
        $form = $('#es-migrate-form');
        $loggerContainer = $('#es-logger-container');
        var $msg = $('.es-msg-1');
        var $msg2 = $('.es-msg-2');

        $progressContainer.estatikProgress().hide();

        $form.on( 'submit', function() {
            var form = document.getElementById( this.id );
            var formData = new FormData( form );

            $( this ).find('[type=submit]').prop( 'disabled', true ).addClass( 'es-preload' );

            esMigrate( formData );

            $msg.addClass( 'es-hidden' );
            $msg2.removeClass( 'es-hidden' );

            return false;
        } );
    });
} )( jQuery );
