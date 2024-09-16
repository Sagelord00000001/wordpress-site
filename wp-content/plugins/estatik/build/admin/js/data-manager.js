( function( $ ) {
    'use strict';

    var tr = Estatik_Data_Manager.tr;
    var nonce = Estatik_Data_Manager.nonce;

    /**
     * Load list items.
     *
     * @param data
     * @param $list_container
     */
    function es_load_terms_list( data, $list_container ) {
        $.extend( data, {
            action: 'es_data_manager_get_list',
            data_manager_nonce: nonce.get_list
        } );

        $list_container.addClass( 'es-preload' );

        $.get( ajaxurl, data, function( response ) {
            if ( response.status === 'success' ) {
                $list_container.html( response.content );
            }
        }, 'json' ).always( function() {
            $list_container.removeClass( 'es-preload' ).find( 'input' ).trigger( 'change' );
        } );
    }

    /**
     * Load term form.
     *
     * @param data
     * @param $form_container
     */
    function es_load_term_form( data, $form_container ) {
        $.extend( data, {
            action: 'es_data_manager_get_form',
            data_manager_nonce: nonce.get_form
        } );

        $form_container.addClass( 'es-preload' );

        $.get( ajaxurl, data, function( response ) {
            if ( response.status === 'success' ) {
                $form_container.html( response.content );

                $([document.documentElement, document.body]).animate({
                    scrollTop: $form_container.offset().top - ( $( window ).height() / 2 )
                }, 500);
            }
        }, 'json' ).always( function() {
            $form_container.removeClass( 'es-preload' ).find( 'input' ).trigger( 'change' );
        } );
    }

    function es_load_term_creator( data, $creator_container ) {
        $.extend( data, {
            action: 'es_data_manager_get_creator',
            data_manager_nonce: nonce.get_creator
        } );

        $creator_container.find( '.js-es-terms' ).addClass( 'es-preload' );

        $.get( ajaxurl, data, function( response ) {
            if ( response.status === 'success' ) {
                $creator_container.replaceWith( response.content );
                // $( response.content ).replaceWith( $creator_container );
            }
        }, 'json' ).always( function() {
            $creator_container.find( '.js-es-terms' ).removeClass( 'es-preload' ).find( 'input' ).trigger( 'change' );
        } );
    }

    $( function() {

        $( document ).on( 'keyup', '.js-es-search-terms', function() {
            var $input, filter;
            $input = $( this );
            filter = $input.val().toUpperCase();

            $input.closest( '.js-es-terms-creator' ).find( '.js-es-term' ).each( function() {
                var $el = $( this );

                if ($el.find( '.es-term-label' ).html().toUpperCase().indexOf( filter ) > -1) {
                    $el.removeClass( 'es-hidden' );
                } else {
                    $el.addClass( 'es-hidden' );
                }
            } );
        } );

        $( document ).on( 'change', '.js-es-location-dep-radio', function() {
            var data = $( this ).data( 'config' );
            var value = $( this ).val();

            if ( $( this ).is( ':checked' ) && typeof data.dependencies !== 'undefined' && data.dependencies.length ) {
                data.dependencies.forEach( function( item ) {
                    var request = {dep: item, parent_id: value, taxonomy: 'es_location'};
                    var $wrapper = $( '#es-terms-' + item + '-creator' );
                    es_load_term_creator( request, $wrapper );
                } );
            }
        } );

        $( document ).on( 'change', '.js-es-field--term .es-field__input', function() {
            var $checkbox = $( this );
            var $item = $( this ).closest( '.js-es-term' );
            var $list = $item.closest( 'ul' );
            var $wrapper = $list.closest( '.js-es-terms-creator' );
            var $delete_selected_item = $wrapper.find( '.js-es-terms-selected-delete' );

            if ( $checkbox.is( ':checked' ) ) {
                $item.addClass( 'es-term--active' );
            } else {
                $item.removeClass( 'es-term--active' );
            }

            if ( $list.find( '.js-es-field--term .es-field__input:checked' ).length ) {
                $delete_selected_item.removeClass( 'es-hidden' );
            } else {
                $delete_selected_item.addClass( 'es-hidden' );
            }
        } );

        $( document ).on( 'change keyup', '.js-es-term-name', function() {
            var $input = $( this );
            var $submit = $( this ).closest( 'form' ).find( '[type=submit]' );

            if ( $input.val().trim().length ) {
                $submit.removeProp( 'disabled' ).removeAttr( 'disabled' );
            } else {
                $submit.prop( 'disabled', 'disabled' );
            }
        } ).trigger( 'change' );

        // Submit terms creator form.
        $( document ).on( 'submit', '.js-es-term-form', function() {
            var $form = $( this );
            var $inputText = $form.find( '.js-es-term-name' );
            var $list = $form.closest( '.js-es-terms-creator' ).find( '.js-es-terms' );
            var $inputs = $form.find( 'input,button' );
            var form_data = $form.serialize();

            $inputs.prop( 'disabled', 'disabled' );

            $.post( ajaxurl, form_data, function( response ) {
                if ( response.status === 'success' ) {
                    if ( response.is_new ) {
                        $list.append( response.content );
                        $inputText.val('');
                    } else {
                        es_load_term_form( { taxonomy: response.taxonomy }, $form.closest( '.js-es-terms-creator__form' ) );
                        $( '.es-' + response.taxonomy + '-term-' + response.term_id  ).replaceWith( response.content );
                    }
                }

                if ( response.status === 'error' ) {
                    if ( response.message ) {
                        Estatik_Admin.renderNotification( response.message );
                    }
                }
            }, 'json' ).fail( function() {
                Estatik_Admin.renderNotification( "<div class='es-notification es-notification--error'>Saving error. Please, contact estatik support.</div>" );
            } ).always( function() {
                $inputs.removeProp( 'disabled' ).removeAttr( 'disabled' ).trigger( 'change' );
            } );

            return false;
        } );

        $( document ).on( 'click', '.js-es-term__delete--confirm, .js-es-terms-selected-delete', function( e ) {
            var message = $( this ).data( 'message' );
            var terms_ids = [];

            var $current_target = $( e.currentTarget );
            var taxonomy = $current_target.data( 'taxonomy' );

            if ( $current_target.hasClass( 'js-es-terms-selected-delete' ) ) {
                var $inputs = $( this ).closest( '.js-es-terms-creator' ).find( '.js-es-term input:checked' );
                message = message.replace( '%d', $inputs.length );
                $inputs.each( function() {
                    terms_ids.push( $( this ).val() );
                } );
            } else {
                terms_ids.push( $( this ).data( 'term' ) );
            }

            $.estatikPopup( {
                inline_html: "<p class='es-center es-popup-text'>" + message + "</p>" +
                "<div class='es-popup__buttons es-center'>" +
                "<button class='js-es-popup__close es-btn es-btn es-btn--link'>" + tr.cancel + "</button>" +
                "<button class='js-es-delete-terms es-btn es-btn--secondary' data-taxonomy='" + taxonomy + "' data-terms='" + JSON.stringify( Object.assign ( {}, terms_ids ) ) + "'>" +
                tr.remove  + "" +
                "</button>" +
                "</div>"
            } ).open();

            return false;
        } );

        $( document ).on( 'click', '.js-es-delete-terms', function() {
            var terms_ids = $( this ).data( 'terms' );
            var taxonomy = $( this ).data( 'taxonomy' );

            var data = {
                terms_ids: terms_ids,
                action: 'es_data_manager_delete_terms',
                data_manager_nonce: nonce.delete_terms,
                taxonomy: taxonomy
            };

            $.post( ajaxurl, data, function( response ) {
                response = response || {};

                if ( response.status === 'success' ) {
                    if ( 'es_location' === taxonomy ) {
                        if ( typeof response.children_terms_deleted !== 'undefined' && response.children_terms_deleted.length ) {
                            response.children_terms_deleted.forEach( function( term_id ) {
                                $( '.js-es-term-' + term_id ).remove();
                            } );
                        }
                        Object.values( terms_ids ).forEach( function( term_id ) {
                            var $item = $( '.js-es-term-' + term_id );
                            $item.closest( '.js-es-terms-creator' ).find( '.js-es-terms-selected-delete' ).addClass( 'es-hidden' );
                            $item.remove();
                        } );
                    } else {
                        es_load_terms_list( { taxonomy: taxonomy }, $( '#es-terms-' + taxonomy + '-creator' ).find( '.js-es-terms' ) );
                    }
                }
            }, 'json' ).fail( function() {

            } ).always( function() {
                $.estatikPopup().close();
            } );

            return false;
        } );

        $( document ).on( 'click', '.js-es-term__edit', function( response ) {
            var $el = $( this );
            var data = $el.data();
            var $form_container = $el.closest( '.js-es-terms-creator' ).find( '.js-es-terms-creator__form' );

            es_load_term_form( data, $form_container );

            return false;
        } );

        $( document ).on( 'click', '.js-es-term__restore', function() {
            var $list = $( this ).closest( '.js-es-terms' );

            var data = $( this ).data();

            $.extend( data, {
                data_manager_nonce: nonce.restore_term,
                action: 'es_data_manager_restore_term'
            } );

            $list.addClass( 'es-preload' );

            $.post( ajaxurl, data, function( response ) {
                if ( response.status === 'success' ) {
                    es_load_terms_list( { taxonomy: data.taxonomy }, $list );
                }
            }, 'json' );
        } );

        // Is icons / checkmarks mode enabled for features and amenities.
        $( document ).on( 'change', '.es-field__is_terms_icons_enabled .es-field__input', function() {
            $( '.es-field__term_icon_type input.es-field__input:checked' ).trigger( 'change' );
        } );

        $( document ).on( 'change', '.es-field__term_icon_type .es-field__input', function() {
            var $el = $( this );
            var enabled = $( '.es-field__is_terms_icons_enabled .es-field__input' ).is( ':checked' );
            var type = enabled ? $( '.es-field__term_icon_type input:checked' ).val() : 'simple';
            var taxonomies = ['es_feature', 'es_amenity'];

            taxonomies.forEach( function( taxonomy ) {
                var $wrapper = $( '#es-terms-' + taxonomy + '-creator' );
                var $ul = $wrapper.find( '.js-es-terms' );
                $ul.addClass( 'es-preload' );

                Estatik_Admin.loadTermsCreator( taxonomy, type, function( response ) {
                    $wrapper.replaceWith( response );
                } ).always( function() {
                    $ul.removeClass( 'es-preload' );
                } );
            } );
        } );

        $( '.js-es-currency' ).on( 'change', function() {
            var code = $( this ).val();

            $( '.js-es-sign' ).val( code ).trigger( 'change' );
        } );

        $( '.js-es-sign' ).on( 'change', function() {
            var sign = $( this ).find( 'option:selected' ).text();
            sign = $( this ).val() ? sign : $( '.js-es-currency option:selected' ).attr( 'value' );

            $( '.js-es-append-sign option' ).each( function () {
                var $option = $( this );
                var $field = $( this ).parent();
                $option.html( $field.data( $option.attr( 'value' ) ).replace( '{sign}', sign ) );
            } );
        } );

        $( document ).on( 'change', '.js-es-default-currency', function() {
            var $wrapper = $( this ).closest( 'table' );

            $wrapper.find( '.js-es-default-currency:not([id=' + $( this ).attr( 'id' ) + '])' ).removeProp( 'checked' ).removeAttr( 'disabled' );
        } );

        $( document ).on( 'click', '.js-es-data-manager-nav a', function() {
            $( '.js-es-data-manager-nav' ).find( 'li' ).removeClass( 'active' );
            $( this ).closest( 'li' ).addClass( 'active' );
            var $elementScroll = $( $( this ).attr( 'href' ) );
            var $container = $elementScroll.closest( '.js-es-data-manager__inner' );

            if ( $container.length ) {
                var $wrapper = $container.closest( '.es-content' );

                $wrapper.find( '.js-es-data-manager__inner' ).addClass( 'es-hidden' );
                $container.removeClass( 'es-hidden' );

                var offset = $elementScroll.offset().top - 122;

                $([document.documentElement, document.body]).stop().animate({
                    scrollTop: offset >= 0 ? offset : 0
                }, 500);
            }

            return false;
        } );

        if ( ! window.location.hash ) {
            $( '.js-es-data-manager-nav:first-child li:first-child a' ).trigger( 'click' );
        } else {
            var $activeLink = $( 'a[href="' + window.location.hash + '"]' );
            if ( $activeLink.length ) {
                $activeLink.trigger( 'click' );
            }
        }
    } );
} )( jQuery );
