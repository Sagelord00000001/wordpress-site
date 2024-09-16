( function( $ ) {
    'use strict';

    function initBaseField() {
        var $base_field;

        for( var i = 0; i < 100; i++ ) {
            $base_field = $( '.js-es-location[data-initialize="' + i + '"]' );
            if ( $base_field.length ) {
                if ( ! $base_field.val() ) {
                    esLoadFieldLocations( $base_field );
                }

                break;
            }
        }
    }

    function esToggleBulkActions() {
        var $checkboxes = $( '.es-entities-archive .wp-list-table tbody .active .check-column input[type=checkbox]' );
        var $container = $( '.es-actions__container' );

        if ( $checkboxes.length ) {
            $container.removeClass( 'es-hidden' );
            $('.es-entities-archive .wp-list-table .type-properties input').each(function() {
                if ($(this).attr('type') === 'checkbox') {
                    $(this).prop('checked', true);
                }
            });
        } else {
            $container.addClass( 'es-hidden' );
            $('.es-entities-archive .wp-list-table .type-properties input').each(function() {
                if ($(this).attr('type') === 'checkbox') {
                    $(this).prop('checked', false);
                }
            });
        }

        $container.find( '.js-es-selected-num' ).html( $checkboxes.length );
    }

    function esToggleBulkAction() {
        var $checkboxes = $( '.es-entities-archive .wp-list-table tbody .active .check-column input[type=checkbox]' );
        var $container = $( '.es-actions__container' );
        
            $('.es-entities-archive .wp-list-table .type-properties input').change(function() {
                
                if ($(this).attr('type') === 'checkbox') {
                    $(this).prop('checked', true);
                }
            });
        
            $('.es-entities-archive .wp-list-table .active.type-properties input').change(function() {
                if ($(this).attr('type') === 'checkbox') {
                    $(this).prop('checked', false);
                }
            });


        $container.find( '.js-es-selected-num' ).html( $checkboxes.length );

        if ($checkboxes.length) {
            $container.removeClass( 'es-hidden' );
        } else {
            $container.addClass( 'es-hidden' );
        }
    }

    $( function() {
        $( '.js-es-location' ).change( function() {
            var $field = $( this );
            var dep_fields = $field.data( 'dependency-fields' );
            var $dep_field;

            if ( dep_fields ) {
                dep_fields.forEach( function( i ) {
                    $dep_field = $( '#es-field-' + i );
                    esLoadFieldLocations( $dep_field, $field );
                } );
            }
        } );

        initBaseField();

        $( document ).on( 'change', '.js-es-submit-on-change', function() {
            $( this ).closest( 'form' ).submit();
        } );

        $( '.es-entities-archive #actions-hide' ).closest( 'label' ).remove();

        // Set bulk actions container width equals wp content width.
        $( window ).resize( function() {
            var content_width = $( '.es-entities-archive #wpbody-content' ).width();
            $( '.es-entities-archive .es-actions__container' ).width( +content_width - 100 );
        } ).trigger( 'resize' );

        // Add custom checkboxes for entities table.
        $( '.es-entities-archive .wp-list-table .check-column input[type=checkbox]' ).each( function() {
            $( this ).wrap( "<div class='es-field'></div>" );
        } );

        $( '.es-entities-archive .wp-list-table .check-column' ).on( 'change', 'input[type=checkbox]', function() {
            if ( $( this ).is( ':checked' ) ) {
                $( this ).closest( 'tr' ).addClass( 'active' );
            } else {
                $( this ).closest( 'tr' ).removeClass( 'active' );
            }

            esToggleBulkAction();
        } );

        $( '.es-entities-archive .wp-list-table thead .check-column' ).on( 'change', 'input[type=checkbox]', function() {

            var $tr = $( this ).closest( '.wp-list-table' ).find( 'tbody tr' );

            if ( $( this ).is( ':checked' ) ) {
                $tr.addClass( 'active' );
            } else {
                $tr.removeClass( 'active' );
            }

            esToggleBulkActions();
        } );


        // Get Quick Edit form.
        $( document ).on( 'click', '.js-es-quick-edit ', function() {
            var $btn = $( this );
            var post_id = $btn.data( 'post-id' );

            $btn.closest( 'ul' ).find( '.editinline' ).trigger( 'click' );

            var $tr = $( document ).find( '#edit-' + post_id );
            $tr.find( 'fieldset' ).hide();

            var data = {
                action: 'es_property_quick_edit_form',
                post_id: post_id,
                nonce: Estatik.nonces.quick_edit_nonce
            };

            $.get( ajaxurl, data, function( response ) {
                if ( response.message ) {
                    Estatik_Admin.renderNotification( response.message );
                } else {
                    $tr.find( '.colspanchange' ).append( response );
                }
            }, 'json' );

            return false;
        } );

        $( document ).on( 'click', '.js-es-submit-quick-save', function() {
            $( this ).addClass( 'es-preload' ).closest( '.colspanchange' ).find( '.save' ).trigger( 'click' );
            return false;
        } );

        $( document ).on( 'click', '.js-es-submit-quick-save-bulk', function() {
            $( this ).addClass( 'es-preload' ).closest( '.colspanchange' ).find( '#bulk_edit' ).trigger( 'click' );
            return false;
        } );

        $( document ).on( 'click', '.js-es-cancel-quick-save', function() {
            $( this ).closest( '.colspanchange' ).find( '.cancel' ).trigger( 'click' );
            $( '.es-overlay' ).remove();
            return false;
        } );

        $( document ).on( 'click', '.js-es-more', function() {
            return false;
        } );

        $( document ).on( 'click', '.js-es-bulk-quick-edit', function() {
            $( '#bulk-action-selector-top' ).val( 'edit' );
            $( '#doaction' ).trigger( 'click' );
            var $tr = $( '.bulk-edit-properties' );

            $tr.find( 'fieldset' ).hide();

            var data = {
                action: 'es_property_quick_edit_bulk_form',
                nonce: Estatik.nonces.quick_edit_bulk_nonce
            };

            $( 'body' ).find( '.es-overlay' ).remove();
            $( 'body' ).append( "<div class='es-overlay es-overlay--dark es-overlay--show'></div>" );

            $.get( ajaxurl, data, function( response ) {
                if ( response.message ) {
                    Estatik_Admin.renderNotification( response.message );
                } else {
                    $tr.find( '.es-property-quick-edit' ).remove();
                    var $checkboxes = $( '.es-entities-archive .wp-list-table tbody .check-column input[type=checkbox]:checked' );
                    $tr.find( '.colspanchange' ).append( response )
                       .find( '.js-es-selected-num' ).html( $checkboxes.length );
                }
            }, 'json' );

            return false;
        } );

        $( document ).on( 'click', '.js-es-delete-bulk, .js-es-duplicate-bulk, .js-es-action-bulk', function() {
            var $checkboxes = $( '.es-entities-archive .wp-list-table tbody .check-column input[type=checkbox]:checked' );
            var post_ids = [];
            var action = $( this ).data( 'action' );

            $checkboxes.each( function() {
                post_ids.push( $( this ).val() );
            } );

            if ( post_ids.length ) {
                var url = new URL( window.location.href );
                url.searchParams.append( 'action', action );
                url.searchParams.append( 'post_ids', post_ids );
                url.searchParams.append( '_nonce', $( this ).data( 'nonce' ) );

                window.location.href = url.href;
            }

            return false;
        } );

        $( '.es-entities-archive #wpbody-content' ).prepend( '<div class="js-es-notifications"></div>' );
    } );
} )( jQuery );
