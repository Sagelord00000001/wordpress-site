( function( $ ) {
    'use strict';

    var $sortableFieldsXHR;

    /**
     * Sortable fields initialize method.
     *
     * @return void
     */
    function es_fields_builder_sortable_init() {
        var $lists = $( document ).find( '.js-es-fields-builder-fields-list, .js-es-sections-list' );

        $lists.sortable( {
            cancel: '.disable-order',
            change: function(event, ui) {
                var $ul = $( event.target );

                // Disable drag on first position for sections.
                if ( $ul.hasClass( 'js-es-sections-list' ) ) {
                    if ( ui.placeholder.index() < 1 ) {
                        $ul.find( '.disable-order' ).after( ui.placeholder );
                    }
                }
            },
            update: function( event, ui ) {
                var $el = $( event.target );
                var machine_name;

                var data = {
                    items: [],
                    action: 'es_fields_builder_change_items_order',
                    field_builder_nonce: Estatik_Fields_Builder.nonce.update_items_order
                };

                $el.find( 'li' ).each( function() {
                    var $li = $( this );
                    machine_name = $li.data( 'machine-name' );
                    data.items.push( machine_name );
                } );

                if ( $el.hasClass( 'js-es-sections-list' ) ) {
                    data.type = 'sections';
                } else {
                    data.type = 'fields';
                }

                if ( $sortableFieldsXHR ) $sortableFieldsXHR.abort();

                $sortableFieldsXHR = $.post( ajaxurl, data, function( response ) {
                    response = response || {};
                    if ( response.message ) {
                        Estatik_Admin.renderNotification( response.message );
                    }
                }, 'json' ).fail( function() {

                } ).always( function() {

                } );
            }
        } );

        $lists.disableSelection();
    }

    /**
     * Load fields tab.
     *
     * @param section_machine_name
     */
    function es_load_fields_tab( section_machine_name ) {
        var $section = $( '#' + section_machine_name );

        var data = {
            field_builder_nonce: Estatik_Fields_Builder.nonce.get_fields_tab,
            machine_name: section_machine_name,
            action: 'es_fields_builder_get_fields_tab'
        };

        $section.html( '' ).addClass( 'es-preload' );

        $.get( ajaxurl, data, function( response ) {
            if ( response.status === 'success' && response.content ) {
                $section.html( response.content );
                es_fields_builder_sortable_init();
            }
        }, 'json' ).fail( function() {

        } ).always( function() {
            $section.removeClass( 'es-preload' );
        } );
    }

    /**
     * Load sections.
     */
    function es_load_sections( active_section_machine_name ) {
        var $nav = $( '.js-es-sections-list' );
        var $tabs_nav = $( '.es-tabs__fields-builder .es-tabs__nav' );

        var data = {
            field_builder_nonce: Estatik_Fields_Builder.nonce.get_sections,
            action: 'es_fields_builder_get_sections'
        };

        $nav.html('');

        $.get( ajaxurl, data, function( response ) {
            if ( response.status === 'success' && response.content ) {
                $nav.replaceWith( response.content );
                es_fields_builder_sortable_init();

                if ( active_section_machine_name ) {
                    $tabs_nav.find( '.es-tabs__nav-link[href="#' + active_section_machine_name + '"]' ).trigger( 'click' );
                }
            }
        }, 'json' ).fail( function() {

        } ).always( function() {
            // $section.removeClass( 'es-preload' );
        } );
    }

    /**
     * Load section create / edit form.
     *
     * @param machine_name
     * @param $response_container
     */
    function es_load_section_form( machine_name, $response_container ) {
        var data = {
            field_builder_nonce: Estatik_Fields_Builder.nonce.get_section_form,
            action: 'es_fields_builder_get_section_form',
            machine_name: machine_name
        };

        $response_container.html('').addClass( 'es-preload' );

        $.get( ajaxurl, data, function( response ) {
            $response_container.html( response );
            $response_container.find( 'input' ).trigger( 'change' );
        } ).fail( function() {

        } ).always( function() {
            $response_container.removeClass( 'es-preload' );
        } );
    }

    /**
     * Load fields builder field form.
     *
     * @param machine_name
     * @param section_machine_name
     * @param $response_container
     */
    function es_load_field_form( machine_name, section_machine_name, $response_container ) {

        var data = {
            field_builder_nonce: Estatik_Fields_Builder.nonce.get_field_form,
            action: 'es_fields_builder_get_field_form',
            machine_name: machine_name,
            section_machine_name: section_machine_name
        };

        $response_container.html('').addClass( 'es-preload' );

        $.get( ajaxurl, data, function( response ) {
            $response_container.html( response );
            $response_container.find( 'input' ).trigger( 'change' );
        } ).fail( function() {

        } ).always( function() {
            $response_container.removeClass( 'es-preload' );
        } );
    }

    $( function() {
        var $form_response_container = $( '.js-es-field-builder__form' );

        $( document ).on( 'change input', '#es-fields-builder-form input, #es-fields-builder-form select, #es-fields-builder-form textarea', function() {
            var $form = $( this ).closest( 'form' );
            var serialized = $form.data( 'serialized' );

            if ( serialized && serialized !== $form.serialize() ) {
                $form.find( '[type=submit]' ).removeProp( 'disabled' ).removeAttr( 'disabled' );
            } else {
                $form.data( 'serialized', $form.serialize() );
            }
        } );

        // Translations list.
        var tr = Estatik_Fields_Builder.tr;

        // Get field settings by field type.
        $( document ).on( 'change', '.js-es-field__input-type', function() {
            var $field = $( this );
            var machine_name = $( this ).closest( 'form' ).find( '#es-field-machine_name' ).val();

            var data = {
                machine_name: machine_name,
                type: $field.val(),
                action: 'es_fields_builder_get_field_settings',
                field_builder_nonce: Estatik_Fields_Builder.nonce.get_field_settings
            };

            $( '.js-es-fields-builder__field-settings' ).html( '' );

            $.get( ajaxurl, data, function( response ) {
                response = response || {};

                if ( response.message ) {
                    Estatik_Admin.renderNotification( response.message );
                }

                if ( response.content ) {
                    $( '.js-es-fields-builder__field-settings' ).html( response.content )
                        .find( 'input' )
                        .trigger( 'change' );
                }
            }, 'json' ).fail( function() {

            } );
        } );

        // Save / Create fields / sections action.
        $( document ).on( 'submit', '#es-fields-builder-form', function() {
            var $form = $( this );
            var $submit_btn = $form.find( '[type=submit]' );

            $submit_btn.prop( 'disabled', 'disabled' ).addClass( 'es-preload' );

            $.post( ajaxurl, $form.serialize(), function( response ) {
                response = response || {};

                if ( response.status === 'success' ) {
                    if ( response.field ) {
                        var field_new_section = response.field.section_machine_name;
                        // Reload fields tab.
                        es_load_fields_tab( field_new_section );
                        // Load field create form.
                        es_load_field_form( false, response.field.section_machine_name, $form_response_container );
                        // Set new field tab as current.
                        $( '.es-tabs__nav .es-tabs__nav-link[href="#' + field_new_section + '"]' ).trigger( 'click' );
                    }

                    if ( response.section ) {
                        if ( ! $( '#' + response.section.machine_name ).length ) {

                            $( '.js-es-field-builder__form' ).before("<div class='js-es-tabs__content es-tabs__content' id='" + response.section.machine_name + "'></div>");
                        }

                        es_load_sections( response.section.machine_name );
                        es_load_fields_tab( response.section.machine_name );
                        es_load_section_form( false, $form_response_container );
                    }

                    // Reinit fields sortable for dynamic content.
                    es_fields_builder_sortable_init();

                    $( 'html,body' ).animate( { scrollTop: 0 }, 'slow' );
                }

                if ( response.message ) {
                    Estatik_Admin.renderNotification( response.message );
                }

            }, 'json' ).fail( function() {

            } ).always( function() {
                $submit_btn.removeProp( 'disabled' ).removeAttr( 'disabled' ).removeClass( 'es-preload' );
            } );

            return false;
        } );

        $( document ).on( 'click', '.js-es-fields-list__item:not(.disable-edit)', function() {
            var $el = $( this );

            $el.closest( '.js-es-fields-list' ).find( '.es-fields-list__item' ).removeClass( 'es-fields-list__item--active' );
            $el.addClass( 'es-fields-list__item--active' );

            es_load_field_form( $el.data( 'machine-name' ), $el.data( 'section-machine-name' ), $form_response_container );

            return false;
        } );

        $( document ).on( 'click', '.js-es-section-item', function() {
            var $el = $( this );

            $el.closest( 'li' ).find( '.es-tabs__nav-link' ).trigger( 'click' );

            es_load_section_form( $el.data( 'machine-name' ), $form_response_container );

            return false;
        } );

        $( document ).on( 'click', '.es-fields-list__item-drag', function( e ) {
            e.stopPropagation();
            return false;
        } );

        $( document ).on( 'click', '.js-es-fields-builder-section-delete-confirm', function( e ) {
            var $link = $( this );
            var section_machine_name = $link.data( 'machine-name' );
            var field_label = $link.data( 'section-label' );

            var message = tr.delete_section.replace( '%s', field_label );

            $.estatikPopup( {
                inline_html: "<p class='es-center es-popup-text'>" + message + "</p>" +
                "<div class='es-popup__buttons es-center'>" +
                "<button class='js-es-popup__close es-btn es-btn es-btn--link'>" + tr.cancel + "</button>" +
                "<button data-machine-name='" + section_machine_name + "' class='js-es-fields-builder-remove-section es-btn es-btn--secondary'>" +
                tr.remove  + "" +
                "</button>" +
                "</div>"
            } ).open();

            e.stopPropagation();

            return false;
        } );

        $( document ).on( 'click', '.js-es-fields-builder-remove-section', function() {
            $( this ).addClass( 'preload' );
            var machine_name = $( this ).data( 'machine-name' );

            var data = {
                machine_name: machine_name,
                action: 'es_fields_builder_delete_section',
                field_builder_nonce: Estatik_Fields_Builder.nonce.delete_section
            };

            $.post( ajaxurl, data, function( response ) {
                response = response || {};

                if ( response.status === 'success' ) {
                    es_load_sections();
                    es_load_fields_tab( machine_name );
                    es_load_section_form( false, $form_response_container );
                    $( 'html,body' ).animate( { scrollTop: 0 }, 'slow' );
                }

                if ( response.message ) {
                    Estatik_Admin.renderNotification( response.message );
                }

            }, 'json' ).fail( function() {

            } ).always( function() {
                $.estatikPopup().close();
            } );
        } );

        $( document ).on( 'click', '.js-es-fields-list__item-delete', function( e ) {
            var $link = $( this );
            var field_machine_name = $link.data( 'machine-name' );
            var field_label = $link.data( 'field-label' );
            var section_machine_name = $link.data( 'section-machine-name' );

            var message = tr.delete_field.replace( '%s', field_label );

            $.estatikPopup( {
                inline_html: "<p class='es-center es-popup-text'>" + message + "</p>" +
                             "<div class='es-popup__buttons es-center'>" +
                                 "<button class='js-es-popup__close es-btn es-btn--link'>" + tr.cancel + "</button>" +
                                 "<button data-id='" + $link.data( 'id' ) + "' data-section-machine-name='" + section_machine_name + "' data-machine-name='" + field_machine_name + "' class='js-es-fields-builder-remove-field es-btn es-btn--secondary'>" +
                                    tr.remove  + "" +
                                 "</button>" +
                             "</div>"
            } ).open();

            e.stopPropagation();

            return false;
        } );

        $( document ).on( 'click', '.js-es-section-item-restore', function() {
            var $link = $( this );
            var data = {
                action: 'es_fields_builder_restore_section',
                field_builder_nonce: Estatik_Fields_Builder.nonce.restore_section,
                machine_name: $link.data( 'machine-name' )
            };

            $.post( ajaxurl, data, function( response ) {
                if ( response.status === 'success' ) {
                    es_load_sections();
                    $( 'html,body' ).animate( { scrollTop: 0 }, 'slow' );
                }

                if ( response.message ) {
                    Estatik_Admin.renderNotification( response.message );
                }
            }, 'json' );

            return false;
        } );

        $( document ).on( 'click', '.js-es-fields-list__item-add', function() {
            var $link = $( this );
            var data = {
                action: 'es_fields_builder_restore_field',
                field_builder_nonce: Estatik_Fields_Builder.nonce.restore_field,
                machine_name: $link.data( 'machine-name' )
            };

            $.post( ajaxurl, data, function( response ) {
                if ( response.status === 'success' ) {
                    es_load_fields_tab( $link.data( 'section-machine-name' ) );
                    $( 'html,body' ).animate( { scrollTop: 0 }, 'slow' );
                }

                if ( response.message ) {
                    Estatik_Admin.renderNotification( response.message );
                }
            }, 'json' );

            return false;
        } );

        // Remove fields after confirmation popup.
        $( document ).on( 'click', '.js-es-fields-builder-remove-field', function() {

            $( this ).addClass( 'preload' );
            var machine_name = $( this ).data( 'machine-name' );
            var id = $( this ).data( 'id' );
            var section_machine_name = $( this ).data( 'section-machine-name' );

            var data = {
                machine_name: machine_name,
                id: id,
                action: 'es_fields_builder_delete_field',
                field_builder_nonce: Estatik_Fields_Builder.nonce.delete_field
            };

            $.post( ajaxurl, data, function( response ) {
                response = response || {};

                if ( response.status === 'success' ) {
                    es_load_field_form( false, 'basic-facts', $form_response_container );
                    es_load_fields_tab( section_machine_name );
                    $( 'html,body' ).animate( { scrollTop: 0 }, 'slow' );
                }

                if ( response.message ) {
                    Estatik_Admin.renderNotification( response.message );
                }

            }, 'json' ).fail( function() {

            } ).always( function() {
                $.estatikPopup().close();
            } );
        } );


        $( document ).on( 'click', '.js-es-fields-builder-add-field', function() {
            es_load_field_form( false, $( this ).data( 'section-machine-name' ), $form_response_container );
        } );

        new ClipboardJS('.js-es-fields-list__copy');

        $( document ).on( 'click', '.js-es-fields-list__copy', function( e ) {
            e.stopPropagation();
            e.preventDefault();
            return false;
        } );

        $( document ).on( 'click', '.js-es-fields-builder-add-section', function() {
            es_load_section_form( false, $form_response_container );
        } );

        es_load_field_form( false, 'basic-facts', $form_response_container );
        es_fields_builder_sortable_init();
    } );
} )( jQuery );
