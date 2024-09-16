( function( $ ) {
    'use strict';

    function loadFieldLocations( $field, $dep_field ) {
        var data = {
            action: 'es_get_locations',
            nonce: Estatik.nonces.nonce_locations,
            types: $field.data( 'address-components' )
        };

        var placeholder = $field.data( 'placeholder' );

        if ( placeholder ) {
            $field.html( "<option value=''>" + placeholder + "</option>" );
        } else {
            $field.html('');
        }

        if ( typeof $dep_field !== 'undefined' ) {
            data.dependency_id = $dep_field.val();
        }

        $.get( ajaxurl, data, function( response ) {
            if ( response ) {
                Object.keys( response ).map(function(objectKey, index) {
                    var label = response[objectKey];

                    if ( $field.data('value') && $field.data('value') == objectKey ) {
                        $field.append( "<option value='" + objectKey + "' selected>" + label + "</option>" );
                    } else {
                        $field.append( "<option value='" + objectKey + "'>" + label + "</option>" );
                    }
                });

                if ( $field.data('value') ) {
                    $field.trigger( 'change' );
                }
            }
        }, 'json' );
    }

    var Estatik_Admin = {
        notificationActionsInitialized: false,
        notificationsTimeout: false,
        notificationsTimeoutStarted: false,

        renderNotification: function( notify ) {
            var _this = this;
            $( '.js-es-notifications' ).html( notify );

            _this.notificationsTimeoutStarted = false;
            clearTimeout( _this.notificationsTimeout );

            if ( ! this.notificationActionsInitialized ) {
                $( window ).on( 'mousemove keypress click scroll', function() {
                    if ( $( '.es-notification', $( document ) ).length ) {

                        if ( ! _this.notificationsTimeoutStarted ) {
                            _this.notificationsTimeout = setTimeout( function() {
                                $( '.es-notification', $( document ) ).remove();
                            }, 4000 );

                            _this.notificationsTimeoutStarted = true;
                        }
                    }
                } );

                $( document ).on( 'click', '.js-es-notification-close', function() {
                    var $wrapper = $( this ).parent();
                    $wrapper.remove();
                    return false;
                } );

                this.notificationActionsInitialized = true;
            }
        },

        loadTermsCreator: function( taxonomy, type, callback ) {
            return $.get( ajaxurl, {
                taxonomy: taxonomy,
                type: type,
                action: 'es_get_terms_creator',
                nonce: Estatik.nonces.get_terms_creator_nonce
            }, callback );
        },

        initFields: function() {
            $( '.js-es-tags, .js-es-select2' ).each( function() {
                if ( ! $( this ).hasClass( 'select2-hidden-accessible' ) ) {
                    // $( this ).next().remove();

                    $( this ).select2( {
                        width: '100%',
                        placeholder: $( this ).data( 'placeholder' )
                    } );
                }
            } );
        }
    };

    var elementorInterval;

    function initElementorCustomizedSelect2( panel ) {
        var $panel = panel.$el;

        elementorInterval = setInterval( function() {
            var $select_fields = $panel.find( '.elementor-select2[multiple]:not(.es-drag-tags-enabled)' );

            if ( $select_fields.length ) {
                $select_fields.each( function() {
                    var $select = $( this );

                    var $ul = $select.next('.select2-container').first( 'ul.select2-selection__rendered' );
                    $select.addClass( 'es-drag-tags-enabled' );

                    $ul.sortable( {
                        placeholder: "ui-state-highlight",
                        forcePlaceholderSize: true,
                        items: "li:not(.select2-search__field)",
                        tolerance: "pointer",
                        create: function( event, ui ) {
                            var $_select2 = $( $ul ).closest( '.select2' ).prev();
                            var values = $_select2.data( 'value' );

                            if ( values ) {
                                values = values.split( ',' ).reverse();

                                for ( var j in values ) {
                                    var selected = $select.select2('data');
                                    var value = values[j];
                                    for ( var i = 0; i < selected.length; i++ ) {
                                        if ( selected[i].id === value ) {
                                            value = selected[i].id;
                                        }
                                    }
                                    var option = $select.find('option[value="' + value + '"]')[0];
                                    $select.prepend( option );
                                }
                            }
                        },
                        stop: function () {
                            $( $ul.find(".select2-selection__choice").get().reverse() ).each( function () {
                                var selected = $select.select2('data');
                                var value = $(this).attr('title');
                                for ( var i = 0; i < selected.length; i++ ) {
                                    if ( selected[i].text === value ) {
                                        value = selected[i].id;
                                    }
                                }
                                var option = $select.find('option[value="' + value + '"]')[0];
                                $select.prepend( option );
                            } );

                            $select.trigger( 'change' );
                        }
                    } );
                } );

                // clearInterval( elementorInterval );
            }
        }, 500 );
    }

    $( function() {
        Estatik_Admin.initFields();

        if ( typeof elementor !== 'undefined' ) {
            elementor.hooks.addAction( 'panel/open_editor/widget/es-search-form-widget', initElementorCustomizedSelect2 );
            elementor.hooks.addAction( 'panel/open_editor/widget/es-listings-widget', initElementorCustomizedSelect2 );
            elementor.hooks.addAction( 'panel/open_editor/widget/es-hfm-widget', initElementorCustomizedSelect2 );
        }

        $( document ).on( 'click', '.js-es-entity-form #post [type=submit]', function() {
            $( '.js-es-metabox' ).find('input, textarea, select').filter('[required]:hidden, [type=url]:hidden, [type=email]:hidden').each( function() {
                if ( ! $(this)[0].checkValidity() ) {
                    var tabId = $( this ).closest('.js-es-tabs__content').attr( 'id' );
                    $( '.js-es-tabs' ).find( '[href="#' + tabId + '"]' ).trigger( 'click' );
                    return false;
                }
            } );
        } );

        $( document ).on( 'change', '.js-es-field__recipient-type', function() {
            var $el = $( this );
            var value = $el.val();
            var $field = $el.closest( '.es-widget__form' ).find( '.es-field__custom_email' );

            if ( '-1' === value ) {
                $field.removeClass( 'es-hidden' );
            } else {
                $field.addClass( 'es-hidden' );
            }
        } );

        $( '.js-es-field__recipient-type' ).trigger( 'change' );

        $( document ).on( 'change', '.js-es-show-properties-by:checked', function() {
            var $el = $( this );
            var value = $el.val();
            var $widget_form = $el.closest( '.es-widget__form' );
            $widget_form.find( '[class^=es-listings-by-]' ).hide();
            $widget_form.find( '.es-listings-by-' + value ).show();
        } );

        $( document ).on( 'widget-added', function ( $control ) {
            $( '.js-es-show-properties-by' ).trigger( 'change' );
        } );

        // $( document ).on( 'change', '.js-es-check-all', function() {
        //     var $wrapper = $( this ).closest( '.es-field--checkboxes' );
        //
        //     if ( ! $( this ).is( ':checked' ) ) {
        //         $wrapper.find( 'input:not(.js-es-check-all)' ).prop( 'checked', false );
        //     } else {
        //         $wrapper.find( 'input:not(.js-es-check-all)' ).prop( 'checked', true );
        //     }
        // } );
        //
        // $( document ).on( 'change', '.js-es-check-state', function() {
        //     var $wrapper = $( this ).closest( '.es-field--checkboxes' );
        //     var $fields = $wrapper.find( 'input[type=checkbox]:not(.js-es-check-all)' );
        //     var $fields_checked = $wrapper.find( 'input[type=checkbox]:checked:not(.js-es-check-all)' );
        //
        //     $wrapper.find( '.js-es-check-all' ).prop( 'checked', $fields.length === $fields_checked.length );
        // } );

        $( document ).ajaxComplete( function() {
            Estatik_Admin.initFields();
            $( '.js-es-show-properties-by' ).trigger( 'change' );
        } );

        $( document ).on( 'change', '.js-es-search-type', function() {
            var $el = $( this );
            var value = $el.val();
            var $wrapper = $el.closest( '.es-widget__form' );
            var $background = $wrapper.find( '.es-field__background' );
            var $main_fields = $wrapper.find( '.js-es-search-fields' );
            var $fields = $wrapper.find( '.es-field__fields' );

            if ( 'main' === value ) {
                $background.removeClass( 'es-hidden' ).addClass( 'es-field--color--break-label' );
            } else {
                $background.addClass( 'es-hidden' );
            }

            if ( 'advanced' === value ) {
                $main_fields.addClass( 'es-hidden' );
                $fields.removeClass( 'es-hidden' );
            } else {
                $main_fields.removeClass( 'es-hidden' );
                $fields.addClass( 'es-hidden' );
            }
        } );

        $( '.js-es-accordion__toggle' ).click( function() {
            var $wrapper = $(this).closest('.js-es-accordion');
            $wrapper.toggleClass('es-accordion--active');

            $wrapper.find( '.es-accordion__body' ).slideToggle( "slow" );

            return false;

            // if ( $wrapper.hasClass('es-accordion--active') ) {
            //     $wrapper.find( '.es-accordion__body' ).show( 300 );
            // } else {
            //     $wrapper.find( '.es-accordion__body' ).hide( 300 );
            // }
        } );

        $( document ).on( 'change', '[data-save-container]', function() {
            var value;

            if ( $( this ).attr( 'type' ) === 'checkbox' ) {
                value = $( this ).is( ':checked' ) ? $( this ).val() : 0;
            } else {
                value = $( this ).val();
            }

            $.post( ajaxurl, {
                field: $( this ).data( 'save-field' ),
                save_field_nonce: Estatik.nonces.save_field_nonce,
                container: $( this ).data( 'save-container' ),
                value: value,
                action: 'es_save_field'
            } );
        } );

        // Save settings form. Uses on data manager and settings page.
        $( document ).on( 'submit', '.js-es-settings-form', function() {
            var $form = $( this );
            var $submit_btn = $form.find( '.js-es-save-settings' );
            $submit_btn.prop( 'disabled', 'disabled' ).addClass( 'es-preload' );

            $.post( ajaxurl, $( this ).serialize(), function( response ) {
                if ( response.message ) {
                    Estatik_Admin.renderNotification( response.message );
                }
            }, 'json' ).fail( function() {
                Estatik_Admin.renderNotification( "<div class='es-notification es-notification--error'>Saving error. Please, contact estatik support.</div>" );
            } ).always( function() {
                $submit_btn.removeProp( 'disabled' ).removeAttr( 'disabled' ).removeClass( 'es-preload' );
                $( 'html,body' ).animate( { scrollTop: 0 }, 'slow' );
            } );

            return false;
        } );

        $( window ).scroll( function() {
            var $el = $( '.js-es-fixed-nav, .es-tabs__fields-builder .es-tabs__nav-inner' );
            var $window = $( this );

            $el.each( function() {
                var $one = $( this );
                var height = $one.height();

                if ( height < ( $window.height() - 40 ) ) {
                    var isPositionFixed = ( $one.css('position') === 'fixed' );
                    if ( $window.scrollTop() > 48 && !isPositionFixed ) {
                        $one.css( {'position': 'fixed', 'top': '72px'} );
                    }
                    if ( $window.scrollTop() < 48 && isPositionFixed ) {
                        $one.css( {'position': 'static', 'top': '72px'} );
                    }
                }
            } );
        } );

        if ( typeof $.fn.slick !== 'undefined' ) {
            $( '.js-es-slick' ).slick();
        }

        new ClipboardJS('.js-es-copy');

        var $elementorMode = $( '#elementor-switch-mode-input' );

        if ( $elementorMode.length && $elementorMode.val() ) {
            var interval;

            interval = setInterval( function() {
                if ( ! $elementorMode.val() ) {
                    var content;
                    var editor = tinyMCE.get( 'alternative_description' );
                    if ( null !== editor && false === editor.hidden ) {
                        // Ok, the active tab is Visual
                        content = editor.getContent();
                    } else {
                        // The active tab is HTML, so just query the textarea
                        content = $('#alternative_description' ).val();
                    }

                    var wp_editor = tinyMCE.get( 'content' );

                    if ( null !== wp_editor && false === wp_editor.hidden ) {
                        // Ok, the active tab is Visual
                        wp_editor.setContent( content );
                    } else {
                        // The active tab is HTML, so just query the textarea
                        $( '#content' ).val( content );
                    }

                    clearInterval( interval );
                }
            }, 500 );
        }

        $( document ).on( 'click', '.notice[data-notice] .notice-dismiss', function() {
            var notice = $( this ).closest( '.notice' ).data( 'notice' );

            $.post( ajaxurl, { _ajax_nonce: Estatik.nonces.dismiss_notice_nonce,
                notice: notice, action: 'es_dismiss_notices' } );
        } );
    } );

    window.Estatik_Admin = Estatik_Admin;
    window.esLoadFieldLocations = loadFieldLocations;
} )( jQuery );
