( function( $ ) {
    'use strict';

    window.EstatikFramework = { initFields: initFields };

    /**
     * @param size
     * @returns {string}
     */
    function humanFileSize(size) {
        var i = Math.floor( Math.log(size) / Math.log(1024) );
        return ( size / Math.pow(1024, i) ).toFixed(2) * 1 + ' ' + ['B', 'kB', 'MB', 'GB', 'TB'][i];
    }

    var uploadFiles = [];

    /**
     * Upload and append files to the uploader area.
     *
     * @param $wrapper
     * @param files
     */
    function esUploadFiles( $wrapper, files ) {
        var item = $wrapper.find( '.js-es-hidden-item' ).html();
        var form_data;

        console.log(files);

        Array.prototype.forEach.call( files, function( file ) {
            var reader = new FileReader();
            var uniqid = Date.now() + Math.random();

            uploadFiles[ uniqid ] = file;

            reader.addEventListener( 'load', function( event ) {
                var html = item
                    .replaceAll( '{name}', file.name )
                    .replaceAll( '{caption}', '' )
                    .replaceAll( '{size}', humanFileSize( file.size ) );

                var $html = $( html ).addClass( 'es-media--uploading' );
                $html.find( '.es-media__image img' ).attr( 'src', event.target.result );
                $html.data( 'uniqid', uniqid );
                $wrapper.find( '.js-files-list' ).append( $html );

                form_data = new FormData();
                form_data.append( 'file', file );
                form_data.append( '_file-nonce', $wrapper.data( 'nonce' ) );
                form_data.append( 'action', $wrapper.data( 'ajax-action' ) );
                form_data.append( 'mime_allowed', $wrapper.data( 'mime-allowed' ) );

                $.ajax( {
                    type: 'POST',
                    url: Es_Framework.ajaxurl,
                    contentType: false,
                    processData: false,
                    dataType: 'json',
                    data: form_data,
                    success:function( response ) {
                        $html.removeClass( 'es-media--uploading' );
                        if ( response.status === 'success' ) {
                            $html.find( '.js-es-caption' ).data( 'attachment-id', response.attachment_id );
                            $html.find( '.js-es-media-field' ).val( response.attachment_id );
                        } else {
                            $html.addClass( 'es-media--failed' )
                                .find( '.js-es-file__msg' )
                                .addClass( 'es-file__msg--error' )
                                .html( Es_Framework.tr.failed );
                        }
                    },
                    xhr: function(){
                        // get the native XmlHttpRequest object
                        var xhr = $.ajaxSettings.xhr() ;
                        // set the onprogress event handler
                        xhr.upload.onprogress = function( evt ) {
                            var progress = evt.loaded / evt.total * 100;
                            $html.find( '.js-es-progress' ).animate( {width: progress + '%'}, 400 );
                        } ;
                        // set the onload event handler
                        xhr.upload.onload = function() {
                            $html.find( '.js-es-progress' ).animate( {width:'100%'}, 400 );
                        };
                        return xhr ;
                    }
                } ).fail( function() {
                    $html.addClass( 'es-media--failed' )
                        .find( '.js-es-file__msg' )
                        .addClass( 'es-file__msg--error' )
                        .html( Es_Framework.tr.failed );
                } );
            } );

            reader.readAsDataURL( file );
        } );
    }

    function initFields( $context ) {

        $context = $context || $( document );

        $context.find( '.js-es-field__date' ).each( function() {
            var format = $( this ).data( 'date-format' ) || 'm/d/y';
            if ( ! $( this ).is( ':disabled' ) ) {
                $( this ).datetimepicker( {
                    format: format,
                    dateFormat: format,
                    timepicker: false
                } );
            }
        } );

        $context.find( '.js-es-f-select2' ).each( function() {
            var config = {};
            var $el = $( this );

            $el.select2( config );
        } );

        $context.find( '.js-es-field__date-time' ).each( function() {
            var format = $( this ).data( 'date-format' ) || 'm/d/y';
            if ( ! $( this ).is( ':disabled' ) ) {
                $( this ).datetimepicker( {
                    format: format,
                    dateFormat: format,
                    timepicker: true
                } );
            }
        } );

        if ( typeof $.fn.wpColorPicker !== 'undefined' ) {
            $( '.js-es-iris-color-picker:not([disabled])', $context ).wpColorPicker();
        }

        $context.find( '.js-es-items' ).each( function() {
            var $list = $( this );

            $( this ).sortable( {
                change: function() {
                    $list.find( 'input' ).eq( 0 ).trigger( 'change' );
                }
            } );
            $( this ).disableSelection();
        } );

        $context.find( '.js-es-select2-search:visible:not(:disabled)' ).each( function() {
            var $field = $( this );

            if ( ! $field.hasClass( 'select2-hidden-accessible' ) ) {
                var config = {
                    width: '100%',
                };

                if ( $field.data( 'placeholder' ) ) {
                    config.placeholder = $field.data( 'placeholder' );
                }

                if ( $field.data( 'formatNoMatches' ) ) {
                    config.formatNoMatches = function () {
                        return $field.data( 'formatNoMatches' )
                    }
                }

                if ( $field.data( 'request' ) ) {
                    config.ajax = {
                        delay: 300,
                        url: Es_Framework.ajaxurl,
                        dataType: 'json',
                        data: function (params) {
                            var request_params = $field.data( 'request' );

                            if ( params.hasOwnProperty( 'term' ) ) {
                                request_params.s = params.term;
                            }

                            return request_params;
                        },
                    };
                }

                $field.select2( config );
            }
        } );

        $context.find( '.js-es-entities-items-search:visible:not(:disabled)' ).each( function() {
            var $field = $( this );

            if ( ! $field.hasClass( 'select2-hidden-accessible' ) ) {
                $field.select2( {
                    width: '100%',
                    dropdownCssClass: "es-select2__dropdown",
                    placeholder: $field.data( 'placeholder' ),
                    ajax: {
                        delay: 300,
                        url: Es_Framework.ajaxurl,
                        dataType: 'json',
                        data: function (params) {
                            var request_params = $field.data( 'request' );

                            if ( params.hasOwnProperty( 'term' ) ) {
                                request_params.s = params.term;
                            }

                            return request_params;
                        },
                        processResults: function (data) {
                            data = data || {};

                            if ( ! data.message ) {
                                data = [];
                                $field.find( 'option' ).each(function ( i, e ) {
                                    var object = { id: e.value, text: e.text };
                                    if ( $.inArray( object, data.items ) === -1 ) {
                                        data.push( object );
                                    }
                                } );
                            } else {
                                data = data.message;
                            }

                            // Transforms the top-level key of the response object from 'items' to 'results'
                            return {
                                results: data
                            };
                        }
                    }
                } ).change( function() {
                    var value = $( this ).val();
                    var item_markup = $( this ).data( 'item-markup' );
                    var $field = $( this );

                    if ( value ) {
                        $field.prop( 'disabled', 'disabled' ).attr( 'disabled', 'disabled' );

                        $.get( Es_Framework.ajaxurl, {value: value, action: $( this ).data( 'action' ) }, function( response ) {
                            response = response || {};

                            if ( response.id ) {
                                var $wrapper = $field.closest( '.js-es-entities-list-field' ).find( '.js-es-items' );
                                var $existing_item = $wrapper.find( '.entity-box--' + response.id );

                                if ( ! $existing_item.length ) {
                                    item_markup = item_markup
                                        .replace( '{value}', response.id )
                                        .replace( '{id}', response.id )
                                        .replace( '{title}', response.text )
                                        .replace( '{subtitle}', response.subtitle )
                                        .replace( '{image}', response.image );

                                    $wrapper.append( item_markup );

                                    $field.val( null ).trigger( 'change' );
                                } else {
                                    $existing_item.animate( {opacity: 0.5}, 300, function() {
                                        $existing_item.animate( {opacity: 1}, 300 );
                                    } );
                                }
                            }
                        }, 'json' ).always( function() {
                            $field.removeProp( 'disabled' ).removeAttr( 'disabled' );
                        } );
                    }
                } );
            }
        } );

        $context.find( '.js-es-phone-field:visible:not(:disabled)' ).each( function() {
            var $field = $( this );

            if ( ! $field.hasClass( 'select2-hidden-accessible' ) ) {

                $field.select2({
                    minimumResultsForSearch: -1,
                    dropdownCssClass: 'es-phone-dropdown content-font es-select2__dropdown--positioning',
                    escapeMarkup: function (m) {
                        return m;
                    },
                    templateSelection: function (option) {
                        var country_icons = $field.data('icons');
                        if (typeof country_icons[option.id] === 'undefined') {
                            return option.text;
                        } else {
                            return '<img src="' + country_icons[option.id] + '"/>';
                        }
                    },
                    templateResult: function (option) {
                        var country_icons = $field.data('icons');
                        var codes = $field.data('codes');
                        if (typeof country_icons[option.id] !== 'undefined') {
                            return '<img src="' + country_icons[option.id] + '"/>' +
                                option.text +
                                '<span class="es-country-code">' + codes[option.id] + '</span>';
                        } else {
                            return '<div class="es-flag-space"></div>' + option.text;
                        }
                    }
                } ).on( 'change', function () {
                    var codes = $field.data('codes');
                    var code = $field.val();
                    var tel_code = codes[ code ];
                    var $tel = $field.closest( '.es-field--phone' ).find( '.js-es-phone' );
                    var replaced = false;

                    Object.entries( codes ).forEach( function( codes_item ) {
                        if ( $tel.val().indexOf( codes_item[1] ) !== -1 && codes_item[1].length ) {
                            $tel.val( $tel.val().replace( codes_item[1], tel_code ) );
                            replaced = true;
                        }
                    } );

                    if ( ! replaced && tel_code.length ) {
                        $tel.val( tel_code );
                    }
                } ).trigger( 'change' );

            }

        } );

        $( 'input[data-toggle-container]:not([data-save-field]), input[data-inactive-container]:not(data-save-field), input[data-toggle-disabled]' ).trigger( 'change' );
    }

    $( function() {

        initFields();

        $( document ).on( 'widget-added', function ( $control ) {
            initFields();
        } );

        $( document ).on( 'click', '.es-field [type=radio]', function( e ) {
            if ( e.ctrlKey || e.metaKey ) {
                $( this ).removeProp( 'checked' ).removeAttr( 'checked' );
            }
        } );

        $( document ).on( 'change', '.js-es-upload-files', function(e) {
            if ( $( this )[0].files[0] === undefined ) return;

            var formData = new FormData();
            var $button = $( this ).next();
            var dataRequest = $( this ).data( 'request' );

            formData.append( "file", $( this )[0].files[0] );
            formData.append( "action", 'es_framework_upload_file' );
            formData.append( '_file-nonce', $( this ).data( 'nonce' ) );
            formData.append( 'mime_allowed', $( this ).data( 'mime-allowed' ) );

            if ( ! $.isEmptyObject( dataRequest ) ) {
                for ( const [key, value] of Object.entries( dataRequest ) ) {
                    formData.append( key, value );
                }
            }

            if ( $button.hasClass( 'es-btn' ) ) {
                $button.addClass( 'es-preload' );
            }

            $.ajax({
                type: 'POST',
                url: Es_Framework.ajaxurl,
                contentType: false,
                processData: false,
                dataType: 'json',
                data: formData,
                success: function (response) {
                    response = response || {}
                    if ( response.status === 'success' ) {
                        if ( dataRequest.file_type === 'icon-uploader' ) {
                            $( '.es-icons-overlay ul' ).each( function() {
                                $( this ).append( response.icon );
                            } );
                        }
                    } else if ( response.status === 'error' ) {
                        alert( response.message );
                    }
                },
            }).always( function() {
                $button.removeClass( 'es-preload' );
            } );

            e.preventDefault();
        } );

        $( '.js-files-list' ).sortable();

        $( document ).on( 'click', '.js-es-reload-media', function() {
            var $wrapper = $( this ).closest( '.js-es-file' );
            var uniqid = $wrapper.data( 'uniqid' );
            var file = uploadFiles[ uniqid ];

            console.log(file, uniqid, $wrapper, $wrapper.data());
            var files = [];
            files.push( file );

            esUploadFiles( $wrapper.closest( '.js-es-uploader-area' ), files );

            uploadFiles[ uniqid ] = null;
            $wrapper.remove();
            return false;
        } );

        $( document ).on( 'click', '.js-es-delete-media', function() {
            $( this ).closest( '.js-es-file' ).remove();
            return false;
        } );

        $( document ).on( 'drop', '.js-es-uploader-area', function(e) {
            e.preventDefault();
            var $wrapper = $( this );
            var files = e.originalEvent.dataTransfer.files;

            if ( files.length ) {
                esUploadFiles( $wrapper, files );
            }
        } );

        $( document ).on( 'change', '[data-value-container]', function() {
            var default_value = $( this ).data( 'value-default' ) || '';
            var suffix = $( this ).data( 'value-suffix' );
            var value = +$( this ).val() ? $( this ).val() + suffix : default_value;
            $( $( this ).data( 'value-container' ) ).html( value );
        } );

        $( '[data-value-container]' ).trigger( 'change' );

        $( document ).on( 'change', '.js-es-drag-files-field', function(e) {
            e.preventDefault();
            var files = $( this )[0].files;
            var $wrapper = $( this ).closest( '.js-es-uploader-area' );

            if ( files ) {
                esUploadFiles( $wrapper, files );
            }
        } );

        $( document ).on( 'dragenter dragover', '.js-es-uploader-area', function() {
            $( this ).addClass( 'es-uploader-area--focused' );
        } );

        $( document ).on( 'dragleave drop', '.js-es-uploader-area', function() {
            $( this ).removeClass( 'es-uploader-area--focused' );
        } );

        $( document ).on( 'dragover', '.js-es-uploader-area', function() {
            return false;
        } );

        $( '.js-es-tabs' ).on( 'tab_changed', function() {
            initFields( $( this ) );
        } );

        $( document ).on( 'change', '.js-es-password-field', function() {
            var val = $( this ).val();
            var email_val = $( this ).data( 'email' ) ?
                $( this ).data( 'email' ) : $( this ).closest( 'form' ).find( '[type="email"]' ).val();
            var $list = $( this ).closest( '.es-field, .js-es-field' ).find( '.es-field__validate-list' );
            var validate1 = false;
            var validate2 = false;
            var validate3 = false;

            if ( val && val.length ) {
                if ( email_val.length && email_val !== val ) {
                    $list.find( '.es-validate-item__contain' ).addClass( 'es-validate-item--active' );
                    validate1 = true;
                } else {
                    $list.find( '.es-validate-item__contain' ).removeClass( 'es-validate-item--active' );
                    validate1 = false;
                }

                if ( val.length >= 8 ) {
                    validate2 = true;
                    $list.find( '.es-validate-item__length' ).addClass( 'es-validate-item--active' );
                } else {
                    validate2 = false;
                    $list.find( '.es-validate-item__length' ).removeClass( 'es-validate-item--active' );
                }

                var regExp = /[a-zA-Z0-9]/g;

                if ( regExp.test( val ) ) {
                    validate3 = true;
                    $list.find( '.es-validate-item__char' ).addClass( 'es-validate-item--active' );
                } else {
                    validate3 = false;
                    $list.find( '.es-validate-item__char' ).removeClass( 'es-validate-item--active' );
                }
            }

            if ( validate1 && validate2 && validate3 ) {
                $( this ).closest( 'form' ).find( '[type=submit]' ).removeProp( 'disabled' ).removeAttr( 'disabled' );
            } else {
                $( this ).closest( 'form' ).find( '[type=submit]' ).prop( 'disabled', 'disabled' );
            }
        } );

        $( '.js-es-password-field' ).trigger( 'change' );

        $( document ).on( 'click', '.js-es-toggle-pwd', function() {
            var $field = $( this ).closest( '.es-field, .js-es-field' ).find( 'input' );
            $( this ).toggleClass( 'es-secondary-color' );
            if ( $field.attr( 'type' ) === 'text' ) {
                $field.prop( 'type', 'password' );
            } else {
                $field.prop( 'type', 'text' );
            }

            return false;
        } );

        $( document ).on( 'input', '.es-field__input[maxlength]', function() {
            var length = $( this ).val().length;

            $( this ).closest( '.es-field, .js-es-field' ).find( '.js-es-strlen' ).html( length );
        } );

        $( document ).on( 'click', '[data-trigger-click]', function() {
            $( $( this ).data( 'trigger-click' ) ).trigger( 'click' );

            return false;
        } );

        $( document ).on( 'click', '.js-es-delete-entity', function() {
            $( this ).closest( '.js-es-entity-box' ).remove();
            return false;
        } );

        // Input on register page.
        $( '.js-es-image-field' ).change( function() {
            var el = this;

            var reader = new FileReader();

            if ( el.files.length ) {
                reader.onload = function( e ) {
                    $( el ).closest( 'div' ).find( $( el ).data( 'img' ) )
                        .attr( 'src', e.target.result )
                        .attr( 'srcset', '' );

                    $( el ).closest( '.js-es-photo' ).find( '.js-es-delete-photo' ).removeClass( 'es-hidden' );
                };

                reader.readAsDataURL( el.files[0] );
            }
        } );

        $( '.js-es-delete-photo' ).click( function() {
            var $wrapper = $( this ).closest( '.js-es-photo' );
            $wrapper.find( '.js-es-image-field' ).val('').trigger( 'change' ).trigger( 'input' );
            $wrapper.find( '.js-es-avatar-field' ).val('').trigger( 'change' ).trigger( 'input' );
            var img = $wrapper.find( '.js-es-photo__image' ).data( 'photo' );
            $( this ).addClass( 'es-hidden' );

            $wrapper.find( '.js-es-photo__image img' ).replaceWith( img );
            return false;
        } );

        $( document ).on( 'click', '.js-es-reset-value', function() {
            $( this ).closest( '.es-field, .js-es-field' ).find( '.es-field__input' ).val( $( this ).data( 'value' ) ).trigger( 'change' );
            return false;
        } );

        $( document ).on( 'click', '.js-es-field__show-more', function() {
            $( this ).closest( '.es-field, .js-es-field' ).find( '.es-field--visibility' ).toggleClass( 'es-hidden' );
            return false;
        } );

        $( '.js-es-media-files' ).sortable();

        $( document ).on( 'click', '.js-es-delete-fields-item', function() {
            $( this ).closest( '.es-item' ).remove();
            return false;
        } );

        $( document ).on( 'click', '.js-es-field--rating .js-es-rating .es-star', function() {
            var $el = $( this );
            var value = $el.data( 'value' );
            var $rating_wrap = $( this ).closest( '.js-es-rating' );
            var $input = $( this ).closest( '.js-es-field--rating' ).find( '.es-field__input' );

            if ( $el.hasClass( 'es-star--active' ) ) {
                value -= 1;
            }

            $input.val( value );
            $rating_wrap.find( '.es-star' ).removeClass( 'es-star--active' );

            $rating_wrap.find( '.es-star:nth-child(-n+' + value +  ')' ).addClass( 'es-star--active' );
        } );

        $( document ).on( 'click', '.js-es-add-fields-item', function() {
            var $selector = $( this ).closest( '.es-fields-list__selector' ).find( '.js-es-items-selector' );
            var field = $selector.val();
            var item_html = $selector.data( 'item' );
            var $list = $( this ).closest( '.es-field--fields-list-selector' ).find( '.js-es-items' );
            var label = $selector.find( 'option:selected' ).html();

            if ( field && ! $list.find( '[data-item-id="' + field + '"]' ).length ) {
                $selector.val( '' ).trigger( 'change' );
                $list.append( item_html.replace( '{value}', field ).replace( '{field_name}', label ).replace( '{item-id}' ) );
            } else {
                $list.find( '[data-item-id="' + field + '"]' ).animate( {opacity: 0.5}, 300, function() {
                    $( this ).animate( {opacity: 1}, 300 );
                } );
                $selector.val( '' ).trigger( 'change' );
            }

            return false;
        } );

        $( document ).on( 'change', '.js-es-items-selector', function() {
            var $selector = $( this );
            var field = $selector.val();
            var $button = $selector.closest( '.es-fields-list__selector' ).find( '.js-es-add-fields-item' );
            $button.prop( 'disabled', ! field );
        } );

        $( document ).on( 'click', '.js-es-icon-field', function() {
            var $el = $( this );

            $el.closest( '.es-icon-field-wrap' ).toggleClass( 'es-icon-field--active' );
        } );

        $( document ).on( 'click', '.es-file__caption-container, .js-es-file__caption-field', function( e ) {
            e.stopPropagation();
        } );

        $( document ).on( 'click', '.js-es-icon-item', function() {
            var $el = $( this );
            var icon_config = $el.data( 'icon' );
            $el.closest( '.es-icon-field-wrap' )
                .removeClass( 'es-icon-field--active' )
                .find( '.js-es-icon-html' )
                .html( icon_config.icon );

            $el.closest( '.es-icon-field-wrap' ).find( '.es-field__input' ).val( JSON.stringify( icon_config ) );
        } );

        $( document ).click( function( e ) {
            if ( ! $( e.target ).closest( '.es-icon-field-wrap' ).length ) {
                $( '.es-icon-field-wrap' ).removeClass( 'es-icon-field--active' );
            }

            //if you click on anything except the modal itself or the "open modal" link, close the modal
            if ( ! $( e.target ).closest( ".es-file__caption-container").length ) {
                $( '.es-file__caption-container.es-file__caption--edit' ).each( function() {
                    var $el = $( this );
                    var $input = $el.find( '.js-es-file__caption-field' );

                    $.post( Es_Framework.ajaxurl, {
                        attachment_id: $input.data( 'attachment-id' ),
                        caption: $input.val(),
                        action: 'es_framework_attachment_save_caption',
                        nonce: Es_Framework.nonces.attachment_save_caption_nonce
                    } ).always( function() {
                        $el.removeClass( 'es-file__caption--edit' );

                        var $caption = $el.find( '.js-es-caption' );

                        if ( $input.val() ) {
                            if ( $caption.prop( 'tagName').toLowerCase() !== 'input' ) {
                                $el.find( '.js-es-caption' ).html( $input.val() ).removeClass( 'no-caption' );
                            }
                        } else {
                            if ( $caption.prop( 'tagName').toLowerCase() !== 'input' ) {
                                $el.find('.js-es-caption').html(Es_Framework.tr.add_caption).addClass('no-caption');
                            }
                        }
                    } );
                } );
            }
        });

        // $( '.es-icons-overlay' ).on( 'click', function(e) {
        //     e.stopPropagation();
        // } );

        // Add repeater item button handler.
        $( document ).on( 'click', '.js-es-repeater__add-item', function() {
            var $el = $( this );
            var $field_wrapper = $el.closest( '.es-field--repeater' );
            var $wrapper = $field_wrapper.find( '.js-es-repeater__wrapper' );
            var index = +$wrapper.data( 'index' );
            var $to_clone = $field_wrapper.find( '.js-es-to-clone .js-es-repeater-item' );

            var $cloned = $to_clone.clone();
            $cloned.data( 'item-index', index );

            $wrapper.find( '.js-es-repeater__items' ).append( $cloned );

            $cloned.find( '[disabled]' ).removeProp( 'disabled' ).removeAttr( 'disabled' );
            $cloned.find( '.es-field--disabled' ).removeClass( 'es-field--disabled' );

            $cloned.find( '[for]' ).each( function() {
                $( this ).prop( 'for', $( this ).prop( 'for' ).replaceAll( '{#index}', index ) );
            } );

            $cloned.find( '.es-field__label' ).each( function() {
                $( this ).text( $( this ).text().replaceAll( '{#index}', index + 1 ) );
            } );

            $cloned.find( '[name], [id], [for], [value]' ).each( function() {
                var $el = $( this );
                ['name', 'id', 'value', 'for'].forEach( function( selector ) {
                    if ( $el.prop( selector ) ) {
                        var modified = $el.prop( selector ).replaceAll( '{#index}', index );
                        $el.prop( selector, modified ).attr( selector, modified );
                    }
                } );
            } );

            $wrapper.data( 'index', index + 1 );

            $cloned.find('[disabled]').removeProp( 'disabled' ).removeAttr( 'disabled' );
            $cloned.find( '.es-switcher--disabled' ).removeClass( 'es-switcher--disabled' );

            $cloned.find( 'input:not([type=radio])' ).trigger( 'change' );

            if ( typeof $.fn.wpColorPicker !== 'undefined' ) {
                $cloned.find('.js-es-iris-color-picker:not([disabled])').wpColorPicker();
            }
            initFields( $cloned );

            return false;
        } );

        // Remove repeater item button handler.
        $( document ).on( 'click', '.js-es-repeater__delete-item', function() {
            var $el = $( this );
            var $wrapper = $el.closest( '.js-es-repeater__wrapper' );
            var index = $wrapper.data( 'index' );
            $el.closest( '.js-es-repeater-item' ).remove();

            $wrapper.data( 'index', index - 1 );

            $wrapper.find( 'input:not([type=radio])' ).trigger( 'change' );

            return false;
        } );

        // Upload file handler.
        if ( typeof wp !== 'undefined' && wp.media && wp.media.editor ) {

            $( document ).on( 'click', '.js-es-media-editor', function() {
                var frame = wp.media( { multiple: false } );
                var $field = $( this ).closest( '.es-field, .js-es-field' ).find( '.es-field__input' );

                frame.open();

                // When an image is selected in the media frame...
                frame.on( 'select', function() {

                    // Get media attachment details from the frame state
                    var attachments = frame.state().get('selection').toJSON();

                    // Attachment IDs array.
                    var ids = [];

                    if ( attachments.length ) {
                        var attachment = attachments[0];
                        $field.val( attachment.url );
                    }
                } );

                return false;
            } );

            $( document ).on( 'click', '.js-es-media-button', function () {
                var $button = $( this );
                var $wrapper = $button.parent();
                var $input = $wrapper.find( '.js-es-media-field' );
                var media_item_markup = $button.data( 'item-markup' );
                var is_multiple = $button.data( 'multiple' );
                var item;

                var frame = wp.media( { multiple: is_multiple } );

                frame.open();

                // When an image is selected in the media frame...
                frame.on( 'select', function() {

                    // Get media attachment details from the frame state
                    var attachments = frame.state().get('selection').toJSON();

                    // Attachment IDs array.
                    var ids = [];

                    if ( attachments.length ) {
                        var $files_container = $button.parent().find( '.js-es-media-files' );

                        for ( var i in attachments ) {
                            var file = attachments[i];
                            var filename = file.filename;
                            var url = file.url;
                            var caption = file.caption ? file.caption : false;

                            if ( typeof file.sizes !== 'undefined' ) {
                                url = file.url;
                            }

                            ids.push( attachments[i].id );

                            item = media_item_markup
                                .replaceAll( '{filename}', filename )
                                .replaceAll( '{attachment_id}', file.id )
                                .replaceAll( '{filesize}', file.filesizeHumanReadable )
                                .replaceAll( '{url}', url )
                                .replaceAll( '{caption}', caption ? caption : $button.data( 'caption' ) )
                                .replaceAll( '{input_caption}', caption ? caption : '' )
                                .replaceAll( '{no_caption}', caption ? '' : 'no-caption' );

                            var $repeater = $files_container.closest( '.js-es-repeater__wrapper' );

                            if ( $repeater.length ) {
                                var index = $files_container.closest( '.js-es-repeater-item' ).data( 'item-index' );
                                item = item.replaceAll( '{#index}', index );
                            }

                            if ( $button.data( 'multiple' ) ) {
                                $files_container.append( item );
                                $files_container.sortable();
                            } else {
                                $files_container.html( item );
                            }

                            $files_container.find( '[disabled]' ).removeProp( 'disabled' ).removeAttr( 'disabled' );
                        }
                    }
                } );

                return false;
            } );
        }

        $( document ).on( 'click', '.js-es-caption', function() {
            $( this ).closest( '.es-file__caption-container' ).addClass( 'es-file__caption--edit' );
            return false;
        } );

        $( document ).on( 'click', '.js-es-media-delete-attachment', function() {
            var $el = $( this );
            $el.closest( 'li' ).remove();

            return false;
        } );

        $( document ).on( 'click', '.js-es-tabs .js-es-tabs__nav a.es-tabs__nav-link', function( e ) {
            var $wrapper = $( this ).closest( '.js-es-tabs' );
            var tabContainer = $( this ).data( 'tab' ) || $( this ).attr( 'href' );

            if ( $( tabContainer ).length ) {
                $wrapper.find( '.js-es-tabs__wrapper .js-es-tabs__content' ).addClass( 'es-hidden' );
                $wrapper.find( tabContainer ).removeClass( 'es-hidden' );
                $wrapper.find( '.js-es-tabs__nav li' ).removeClass( 'active' );
                $( this ).closest( 'li' ).addClass( 'active' );

                if ( history.pushState ) {
                    history.pushState( null, null, tabContainer );
                }
                else {
                    location.hash = tabContainer;
                }

                $wrapper.trigger( 'tab_changed', [ tabContainer, $wrapper, $( this ) ] );
            }

            return false;
        } );

        try {
            if ( window.location.hash && $( window.location.hash ).length ) {
                if ( $( window.location.hash ).hasClass( 'js-es-tabs__content' ) ) {
                    var $link = $( 'a[href="' + window.location.hash + '"]' );
                    $link.trigger( 'click' );
                }
            }
        } catch( e ) {}

        $( '[data-toggle-label]' ).click( function( e ) {
            var temp_label = $( this ).html();
            $( this ).html( $( this ).data( 'toggle-label' ) ).data(  'toggle-label', temp_label );

            e.preventDefault();
        } );


        $( '.js-es-tabs' ).each( function() {
            if ( ! $( this ).find( '.js-es-tabs__nav li.active' ).length ) {
                var $link = $( this ).find( '.js-es-tabs__nav li:first-child a.es-tabs__nav-link' );
                $link.trigger( 'click' );
            }

            window.scrollTo( 0, 0 );

            setTimeout( function() {
                window.scrollTo( 0, 0 );
            }, 1 );
        } );

        $( document ).ajaxComplete( function() {
           initFields();
        } );

        $( document ).on( 'click', '.js-incrementer-button', function() {
            var method = $( this ).data( 'method' );
            var $input = $( this ).closest( '.js-es-incrementer-field' ).find( '.es-field__input' );
            var max = $input.prop( 'max' );
            var min = $input.prop( 'min' );
            var step = + $input.prop( 'step' );
            step = step || 1;
            var val = $input.val() ? $input.val() : 0;

            if ( 'increment' === method ) {
                $input.val( ( +max && +max > +val ) || ! max ? + val + step : val );
            } else {
                $input.val((min && min < val) || min === '' ? +val - step : val);
            }

            $input.trigger( 'change' );

            return false;
        } );

        $( document ).on( 'change', 'input[data-toggle-disabled]',  function() {
            var $el = $( this );
            var $field = $( $el.data( 'toggle-disabled' ) );
            var revert = $el.data( 'revert-toggler' );

            if ( $field.length ) {
                var checked = $el.is( ':checked' );
                checked = revert ? ! checked : checked;
                if ( checked ) {
                    $field.prop( 'disabled', 'disabled' ).closest( '.es-field, .js-es-field' ).addClass( 'es-field--disabled' );
                } else {
                    $field.removeProp( 'disabled' ).removeAttr( 'disabled' ).closest( '.es-field, .js-es-field' ).removeClass( 'es-field--disabled' );
                }
            }
        } );

        $( document ).on( 'change', 'input[data-toggle-container], input[data-inactive-container]',  function() {
            var $el = $( this );
            var container = $el.data( 'toggle-container' );

            if ( container && container.length ) {
                var $container = $('body').find( container );

                if ( $el.is( ':checked' ) ) {
                    $container.removeClass( 'es-hidden' );
                } else {
                    $container.addClass( 'es-hidden' );
                }
            } else {
                var $active_container = $( $el.data( 'active-container' ) );
                var $inactive_container = $( $el.data( 'inactive-container' ) );
                var fields_selector = 'input:not(.js-es-repeater-input),select:not(.js-es-repeater-input),textarea:not(.js-es-repeater-input)';

                if ( $el.is( ':checked' ) ) {
                    $active_container.removeClass( 'es-hidden' );
                    $inactive_container.addClass( 'es-hidden' );

                    $active_container.find( fields_selector ).removeProp( 'disabled' ).removeAttr( 'disabled' );
                    $inactive_container.find( fields_selector ).prop( 'disabled', 'disabled' );
                } else {
                    $active_container.addClass( 'es-hidden' );
                    $inactive_container.removeClass( 'es-hidden' );

                    $active_container.find( fields_selector ).prop( 'disabled', 'disabled' );
                    $inactive_container.find( fields_selector ).removeProp( 'disabled' ).removeAttr( 'disabled' );
                }
            }
        } );

        $( 'input[data-toggle-container], input[data-inactive-container], input[data-toggle-disabled]' ).trigger( 'change' );

        $( document ).on( 'click', '[data-toggle-container]:not(input)', function(e) {
            $( $( this ).data( 'toggle-container' ) ).toggleClass( 'es-hidden' );
            e.preventDefault();
        } );

        $( document ).on( 'click', '.js-es-add-field', function() {
            return false;
        } );

        $( document ).on( 'click', '.js-es-price-input-type-container', function() {
            var value = $(this).val();
            esPriceInputType( value );
        } );

        var esValuePriceInputType = $('.js-es-price-input-type-container:checked').val();

        esPriceInputType( esValuePriceInputType );

        function esPriceInputType( value ) {
            if(value == 'dropdown') {
                $( '.es-field__is_same_price_for_categories_enabled' ).removeClass( 'es-hidden' );
                $( '#es-same-price-list-container' ).show();
                $( '#es-diff-price-list-container' ).show();
                
            } else {
                $( '.es-field__is_same_price_for_categories_enabled' ).addClass( 'es-hidden' );
                $( '#es-same-price-list-container' ).hide();
                $( '#es-diff-price-list-container' ).hide();
            }
        }

    } );
} )( jQuery );
