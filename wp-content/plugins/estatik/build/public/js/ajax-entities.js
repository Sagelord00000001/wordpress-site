( function( $ ) {
    'use strict';

    var Entities = {

        /**
         * Initialize listings ajax search.
         */
        initSearch: function() {
            $( document ).find( '[data-search-form-selector]' ).each( function() {
                var $search_forms = $( $( this ).data( 'search-form-selector' ) ).not( '.js-es-event-added' );
                var $search_container = $( this );
                var xhr;

                if ( $search_forms.length ) {
                    $search_forms.each( function() {
                        $( this ).addClass( 'js-es-search--ajax' ).addClass( 'js-es-event-added' );
                        var $search_form = $( this ).find( 'form' );

                        $search_form.on( 'change', 'input:not([type=text]):not([type=search]),select', function() {
                            var $field = $( this );

                            var hash = new EntitiesHash( $search_container.find( '.js-es-entities' ).data( 'hash' ) );
                            var field_type = $field.attr( 'type' );

                            if ( 'checkbox' === field_type ) {
                                hash.delete( $field.attr( 'name' ) );
                            }

                            $search_form.submit();
                        } );

                        $search_form.on( 'focusout', 'input[type=text], input[type=number]', function() {
                            $search_form.submit();
                        } );

                        $search_form.on( 'submit', function() {
                            var $wrapper = $search_container.find( '.js-es-entities__wrap_inner' );
                            var serialized_data = $search_form.serializeArray();
                            var $entities = $wrapper.closest( '.js-es-entities-wrap' ).find( '.js-es-entities' );

                            var hash = new EntitiesHash( $entities.data( 'hash' ) );

                            hash.clearQueryArguments();

                            if ( serialized_data.length ) {
                                var counter_list = {};

                                serialized_data.forEach( function( item ) {
                                    var cleaned_list = {};

                                    if ( item.value ) {
                                        if ( item.name.indexOf( '[]' ) !== -1 ) {
                                            counter_list[item.name] = counter_list[item.name] ? counter_list[item.name] : 0;
                                            item.name = item.name.replace( '[]', '[' + counter_list[item.name]++ + ']' );
                                        }
                                        hash.setValue( item.name, item.value );
                                    } else {
                                        hash.delete( item.name );
                                    }
                                } );
                                hash.delete( 'prop_id' );
                                hash.setValue( 'page_num', 1 );
                                hash.setValue( 'paged-' + hash.getValue( 'loop_uid' ), 1 );
                                hash.setHistoryQuery();
                            }

                            $entities.data( 'hash', hash.getHash() );

                            if ( xhr ) {
                                xhr.abort();
                            }

                            $wrapper.find( '.js-es-entities' ).addClass( 'es-entities--loading' );

                            var entity_type = $entities.data( 'entity' );
                            var builder = entitiesBuilderFactory( entity_type );

                            xhr = $.post( Estatik.settings.ajaxurl, {action: 'get_' + entity_type, hash: hash.getHash(), reload_map: 1 }, function( response ) {
                                response = response || {};

                                if ( 'success' === response.status ) {
                                    builder.append( response, $wrapper );
                                }
                            }, 'json' );

                            return false;
                        } );
                    } );
                }
            } );
        },

        /**
         * Add entities from ajax response to the DOM.
         *
         * @param ajaxResponse
         * @param $wrapper
         * @param need_scroll
         */
        append: function( ajaxResponse, $wrapper, need_scroll ) {
            if ( ajaxResponse.status === 'success' ) {
                var scroll_offset = $wrapper.offset().top - ( +Estatik.settings.listings_offset_corrector );
                var $new_wrapper = $wrapper.replaceWith( ajaxResponse.message );
                var $entities_wrapper = $new_wrapper.find( '.js-es-entities' );
                var entity = $entities_wrapper.data( 'entity' );

                var builder = entitiesBuilderFactory( entity );

                builder.init( $new_wrapper.closest( '.js-es-entities-wrap' ) );

                need_scroll = need_scroll === 'undefined' ? true : need_scroll;

                if ( need_scroll ) {
                    $( [document.documentElement, document.body] ).animate( {
                        scrollTop: scroll_offset || 0
                    }, 500 );
                }

                $wrapper.find( '.js-es-entities' ).removeClass( 'es-entities--loading' );

                $( window ).trigger( 'resize' );

                return $new_wrapper;
            }
        },
    };

    /**
     * Manage properties archive hash string.
     *
     * @param hash
     * @param is_temp
     * @constructor
     */
    var EntitiesHash = function( hash, is_temp ) {
        is_temp = is_temp || false;
        var _this = this;
        this.hash = hash;
        this.attributes = new URLSearchParams( decodeURIComponent( escape(window.atob( this.hash ) ) ) );

        if ( ! is_temp ) {
            this.tempEntitiesHash = new EntitiesHash( hash, true );
        }

        this.shortcode_attributes = [
            'layout',
            'posts_per_page',
            'disable_navbar',
            'show_sort',
            'show_total',
            'show_page_title',
            'show_layouts',
            // 'sort',
            'limit',
            'page_num',
            'loop_uid',
            'page_title',
            'fields',
            'collapsed_fields',
            'main_fields',
            'ignore_search',
            'search_form_selector',
            'enable_search',
            'search_type',
            'view_all_link_name',
            'wishlist_confirm',
            'view_all_page_id' ,
            'disable_pagination',
            '_ajax_mode',
            '_ignore_coordinates',
            'reload_map',
            'hfm_full_width',
            'ajax_response_mode',
            'fields_delimiter',
            'action',
            'hash',
            'prop_id',
            'enable_ajax',
            'strict_address',
            'map_show',
            'authors'
        ];

        // this.ignore_attributes = [
        //     'disable_pagination',
        //     'view_all_page_id',
        //     '_ajax_mode',
        //     'view_all_link_name',
        //     'search_type',
        //     'enable_search',
        //     'search_form_selector',
        //     'ignore_search',
        //     'page_title',
        //     'limit',
        //     'show_layouts',
        //     'show_page_title',
        //     'show_total',
        //     'show_sort',
        //     'disable_navbar',
        //     'posts_per_page',
        //     'action',
        //     'hash',
        //     'page_num',
        //     'sort',
        //     'reload_map',
        //     '_ignore_coordinates',
        //     'loop_uid',
        //     'hfm_full_width',
        //     'wishlist_confirm',
        //     'ajax_response_mode',
        //     'prop_id',
        // ];

        EntitiesHash.prototype.clearQueryArguments = function() {
            var _this = this;
            var temp_hash = this.tempEntitiesHash;
            var search_fields = Estatik.search.fields;

            this.tempEntitiesHash.getAttributes().forEach( function( value, key ) {
                if ( ! temp_hash.shortcode_attributes.includes( key ) && ! search_fields.includes( key ) ) {
                    _this.delete( key );
                }
            } );
        };

        /**
         * Get attribute value from hash string.
         *
         * @param key
         * @returns {*}
         */
        EntitiesHash.prototype.getValue = function( key ) {
            return _this.attributes.get( key );
        };

        /**
         * Get attribute value from hash string.
         *
         * @param key
         * @returns {*}
         */
        EntitiesHash.prototype.getAllValues = function( key ) {
            return _this.attributes.getAll( key );
        };

        /**
         * Set attribute in hash string.
         *
         * @param key
         * @param value
         */
        EntitiesHash.prototype.setValue = function( key, value ) {
            _this.attributes.set( key, value );
            _this.hash = btoa( _this.attributes.toString() );

            return _this;
        };

        /**
         *
         * @param key
         * @returns {EntitiesHash}
         */
        EntitiesHash.prototype.delete = function( key ) {
            _this.attributes.delete( key );
            _this.hash = btoa( _this.attributes.toString() );

            return _this;
        };

        /**
         * Return hash string.
         *
         * @returns {string}
         */
        EntitiesHash.prototype.getHash = function() {
            return _this.hash;
        };

        /**
         * Return decoded hash.
         *
         * @returns {any}
         */
        EntitiesHash.prototype.getAttributes = function() {
            return this.attributes;
        };

        /**
         * toString implementation
         *
         * @returns {string}
         */
        EntitiesHash.prototype.toString = function() {
            return this.getHash();
        };

        /**
         * Append query args to the url.
         */
        EntitiesHash.prototype.setHistoryQuery = function() {
            var data = this.getAttributes();
            var temp_hash = this.tempEntitiesHash;

            temp_hash.getAttributes().forEach( function( value, key ) {
                temp_hash.shortcode_attributes.forEach( function( shortcode_key, shortcode_value ) {
                    if ( key.includes( shortcode_key ) && key != 'layout' ) {
                        data.delete(key);
                    }
                } );
            } );

            var query = decodeURIComponent( window.location.pathname + '?' + data.toString() );
            window.history.replaceState( {}, "", query );
        };

        return this;
    };

    window.EstatikEntitiesHash = EntitiesHash;

    /**
     * Properties object with helper functions.
     */
    var Properties = {

        halfMapInstances: [],

        /**
         * Initialize properties functionality.
         */
        init: function() {
            // Initialize properties GRID using responsive breakpoints.
            // EstatikResponsinator.init( 'listings' );

            setTimeout( function() {
                Properties.initCarousel();
            }, 10 );
        },

        /**
         *
         * @returns {string}
         */
        getLayoutEntityType: function() {
            return 'listings';
        },

        /**
         * Add listings from ajax response to the DOM.
         *
         * @param ajaxResponse
         * @param $wrapper
         * @param need_scroll
         */
        append: function( ajaxResponse, $wrapper, need_scroll ) {
            if ( ajaxResponse.status === 'success' ) {
                Entities.append( ajaxResponse, $wrapper, need_scroll );

                if ( +ajaxResponse.reload_map && ajaxResponse.loop_uid ) {
                    var map_instance = Properties.halfMapInstances[ ajaxResponse.loop_uid ];

                    if ( map_instance ) {
                        if ( ajaxResponse.coordinates ) {
                            map_instance.setMarkers( ajaxResponse.coordinates );
                        } else {
                            map_instance.deleteMarkers();
                        }
                    }
                }
            }
        },

        /**
         * Return num of cols && grid css class.
         *
         * @param $listings_wrapper
         * @returns string
         */
        getLayoutClass: function( $listings_wrapper ) {
            for ( var i = 1; i < 10; i++ ) {
                if ( $listings_wrapper.hasClass( 'es-listings--grid-' + i ) ) {
                    return 'es-listings--grid-' + i;
                }

                if ( $listings_wrapper.hasClass( 'es-listings--grid-' + i ) ) {
                    return 'es-listings--grid-' + i;
                }
            }

            return 'es-listings--list';
        },

        /**
         * Initialize properties items carousels.
         *
         * @param e
         * @param $context
         */
        initCarousel: function( e, $context ) {
            $context = $context || $( document );

            $( '.js-es-slick.slick-initialized', $context ).slick( 'unslick' );

            $( '.js-es-slick', $context ).each( function() {
                var config = $( this ).data( 'slick' ) || {};
                var slidesToShow = config.slidesToShow || 1;
                var is_vertical = config.vertical || false;
                var $slider = false;

                if ( typeof config.infinite !== 'undefined' ) {
                    config.infinite = Boolean( config.infinite );
                }

                config.rtl = Estatik.settings.is_rtl;

                if ( is_vertical ) {
                    if ( ! $( this ).hasClass( 'slick-initialized' ) ) {
                        $slider = $( this );
                    }
                } else {
                    if ( ! $( this ).hasClass( 'slick-initialized' ) ) {
                        var item_width = 230;
                        var container_width = $(this).width();
                        var items_count = parseInt( container_width / item_width ) || 1;
                        $slider = $( this );

                        config.slidesToShow = slidesToShow <= items_count ? slidesToShow : items_count;
                    }
                }

                if ( $slider ) {
                    $slider.on( 'init', function() {
                        $slider.removeClass( 'slick-hidden' );
                    } ).slick( config );

                    if ( $slider.find( '.js-es-slick' ).length ) {
                        $slider.on( 'beforeChange', function( event, slick, currentSlide, nextSlide ) {
                            var $inner_sliders = $( slick.$list ).find( '.js-es-slick:not(.slick-initialized)' );
                            if ( $inner_sliders.length ) {
                                Properties.initCarousel( e, $slider );
                            }
                        } );
                    }
                }
            } );
        },
    };

    window.EstatikProperties = Properties;

    /**
     * Half map helper class.
     *
     * @param $wrapper
     * @constructor
     */
    var HalfMap = function( $wrapper ) {
        this.$wrapper = $wrapper;
        this.mapInstance = null;
        this.markers = [];
        this.clusters = [];
        this.load_first_time = true;
        this.ignoreLoadListingsByBounds = true;
        var _this = this;

        /**
         * Initialize half map layout.
         */
        HalfMap.prototype.init = function() {
            this.setFullWidth();

            if ( typeof google === 'undefined' ) return false;

            this.$map = $( '.js-es-map', _this.$wrapper );
            this.map = _this.$map[0];

            // EstatikResponsinator.init( 'half_map' );

            this.mapInstance = new google.maps.Map( _this.map , {
                draggable: true,
                zoom: 16
            } );

            this.setMarkers( _this.$map.data( 'listings' ) );

            this.mapInstance.addListener( 'click', function() {
                _this.close_popups();
            } );

            $( window ).on( 'resize', function() {
                _this.setFullWidth();
            } );

            /**
             * Load listings in ap visible area on zoom changed.
             */
            _this.mapInstance.addListener( 'zoom_changed', function() {
                if ( _this.load_first_time || _this.ignoreZoomHandler ) {
                    _this.load_first_time = false;
                    return false;
                }
                // Close all map popups.
                _this.close_popups();

                _this.loadListingsFromVisibleBounds();
            } );

            /**
             * Load listings in ap visible area on zoom changed.
             */
            _this.mapInstance.addListener( 'dragend', function() {
                _this.loadListingsFromVisibleBounds();
            } );
        };

        HalfMap.prototype.setFullWidth = function() {
            if ( this.$wrapper.hasClass( 'es-properties--hfm' ) && this.$wrapper.hasClass( 'es-properties--hfm--full-width' ) ) {
                this.$wrapper.css( 'margin-left', 0 );
                var bounds = this.$wrapper[0].getBoundingClientRect();
                var left = bounds.left;
                this.$wrapper.css( 'margin-left', -left + 'px' );
            }
        };

        /**
         * Load listings in visible map area.
         */
        HalfMap.prototype.loadListingsFromVisibleBounds = function() {
            if ( _this.ignoreLoadListingsByBounds ) return false;
            if ( typeof google === 'undefined' ) return false;

            // List of listings from map visible area.
            var properties_ids = [];
            // Map bounds.
            var bounds = _this.mapInstance.getBounds();

            if ( _this.markers.length ) {
                for ( var i in _this.markers ) {
                    if ( _this.markers.hasOwnProperty( i ) ) {
                        var position = _this.markers[i].getPosition();

                        if ( bounds !== undefined && bounds.contains( position ) ) {
                            properties_ids.push( _this.markers[i].post_id );
                        }
                    }
                }

                if ( ! properties_ids.length ) {
                    properties_ids.push( -1 );
                }

                var hash = new EntitiesHash( _this.$wrapper.find( '.js-es-listings' ).data( 'hash' ) );
                hash.setValue( 'prop_id', properties_ids.join( ',' ) );
                hash.setValue( 'page_num', 1 );
                hash.setValue( 'paged-' + hash.getValue( 'loop_uid' ), 1 );

                if ( typeof _this.xhr !== 'undefined' ) {
                    _this.xhr.abort();
                }

                _this.$wrapper.find( '.js-es-listings' ).addClass( 'es-listings--loading' );

                _this.xhr = $.post( Estatik.settings.ajaxurl, { reload_map: 0, hash: hash.getHash(), action: 'get_listings' }, function( response ) {
                    Properties.append( response, _this.$wrapper.find( '.js-es-entities__wrap_inner' ), false );
                }, 'json' );
            }
        };

        /**
         * Markers hover state active.
         */
        $( _this.$wrapper ).on( 'mouseenter', '.js-es-listing', function() {
            var post_id = $( this ).data( 'post-id' );

            if ( typeof _this.markers[ post_id ] !== 'undefined' ) {
                var marker = _this.markers[ post_id ];
                var icon = {};
                if ( Estatik.settings.map_marker_type !== 'price' ) {
                    icon = marker.getIcon();
                }
                var marker_svg = marker.marker_svg;
                if ( marker_svg ) {
                    marker_svg = marker_svg.replaceAll( 'data-color', 'style="fill: ' + Estatik.settings.main_color + '"' );
                    icon.url = 'data:image/svg+xml;charset=UTF-8;base64,' + window.btoa( marker_svg );
                    marker.setIcon( icon );
                }
            }
        } );

        /**
         * Markers hover state inactive.
         */
        $( _this.$wrapper ).on( 'mouseleave', '.js-es-listing', function() {
            var post_id = $( this ).data( 'post-id' );

            if ( typeof _this.markers[ post_id ] !== 'undefined' ) {
                var marker = _this.markers[ post_id ];
                var icon = {};
                if ( Estatik.settings.map_marker_type !== 'price' ) {
                    icon = marker.getIcon();
                }
                var marker_svg = marker.marker_svg;
                if ( marker_svg ) {
                    marker_svg = marker_svg.replaceAll( 'data-color', 'style="fill: ' + marker.marker_color + '"' );
                    icon.url = 'data:image/svg+xml;charset=UTF-8;base64,' + window.btoa( marker_svg );
                    marker.setIcon( icon );
                }
            }
        } );

        /**
         * Delete markers from map.
         *
         * @return void
         */
        HalfMap.prototype.deleteMarkers = function() {
            if ( typeof google === 'undefined' ) return false;

            if ( _this.clusters.length ) {
                for ( var j in _this.clusters ) {
                    if ( _this.clusters.hasOwnProperty( j ) ) {
                        _this.clusters[j].clearMarkers();
                    }
                }
            }

            if ( _this.markers.length ) {
                for ( var i in _this.markers ) {
                    if ( _this.markers.hasOwnProperty( i ) ) {
                        _this.markers[i].setMap( null );
                    }
                }
            }

            _this.markers = null;
            _this.markers = [];
        };

        /**
         * Set markers on the map.
         */
        HalfMap.prototype.setMarkers = function( coordinates ) {
            if ( typeof google === 'undefined' ) return false;
            _this.ignoreLoadListingsByBounds = true;

            var marker;
            var bounds = new google.maps.LatLngBounds();
            var oms = new OverlappingMarkerSpiderfier( _this.mapInstance, {
                markersWontMove: true,
                markersWontHide: true,
                basicFormatEvents: true,
                keepSpiderfied: true
            } );

            _this.$map.data( 'listings', coordinates );

            _this.deleteMarkers();

            if ( coordinates ) {
                for ( var j in coordinates ) {
                    if ( ! coordinates.hasOwnProperty( j ) ) continue;

                    var location =  new google.maps.LatLng(
                        parseFloat( coordinates[j].lat ),
                        parseFloat( coordinates[j].lng )
                    );

                    bounds.extend( location );

                    marker = {
                        position: location,
                        map: _this.mapInstance,
                        optimized: false,
                        zIndex: 99
                    };

                    var marker_svg = null;
                    var marker_color = null;

                    if ( typeof coordinates[j].marker !== 'undefined' ) {
                        marker_svg = Estatik.settings.map_marker_icons[coordinates[j].marker];
                        marker_color = coordinates[j].marker_color;
                    } else {
                        marker_svg = Estatik.settings.map_marker_icons[Estatik.settings.map_marker_icon];
                        marker_color = Estatik.settings.map_marker_color;
                    }

                    marker.marker_svg = marker_svg;
                    marker.marker_color = marker_color;

                    var $svg = $( marker_svg );
                    var svg_width = $svg.attr( 'width' );
                    var svg_height = +$svg.attr( 'height' );
                    marker_svg = marker_svg.replaceAll( 'data-color', 'style="fill: ' + marker_color + '"' );

                    marker.icon = {
                        url: 'data:image/svg+xml;charset=UTF-8;base64,' + window.btoa( marker_svg ),
                        scaledSize: new google.maps.Size( svg_width, svg_height ),
                        size: new google.maps.Size( svg_width, svg_height ),
                        origin: new google.maps.Point(0, 0),
                        anchor: new google.maps.Point( svg_width / 2, svg_height / 2 ),
                        optimized: false
                    };

                    marker = new google.maps.Marker( marker );
                    marker.post_id = coordinates[j].post_id;
                    google.maps.event.addListener( marker, 'click', ( _this.propertyPopup )( location, coordinates[j] ) );

                    _this.markers[ coordinates[j].post_id ] = marker;
                    oms.addMarker( marker );
                }

                if ( typeof Estatik.settings.default_lat_lng !== 'undefined' ) {
                    var center = Estatik.settings.default_lat_lng;
                    _this.mapInstance.setCenter({  lat: +center[0], lng: +center[1] } );

                    if ( Estatik.settings.map_zoom ) {
                        _this.mapInstance.setZoom( +Estatik.settings.map_zoom );
                    }
                } else {
                    if ( typeof bounds !== 'undefined' ) {
                        _this.mapInstance.fitBounds( bounds );
                        _this.mapInstance.panToBounds( bounds );
                    }
                }

                if ( _this.markers ) {
                    if ( Estatik.settings.is_cluster_enabled ) {
                        var cluster_styles = [{
                            width: 44,
                            height: 44,
                            url: HalfMap.getClusterIcon(),
                            textColor: 'white',
                            textSize: 10,
                            anchorText: [16],
                            anchorIcon: [38,22]
                        }];

                        if ( 'cluster3' === Estatik.settings.map_cluster_icon ) {
                            cluster_styles[0].textColor = Estatik.settings.map_cluster_color;
                        }

                        if ( +Estatik.settings.is_cluster_enabled && Estatik.settings.map_marker_type !== 'price' ) {
                            _this.clusters.push( new MarkerClusterer( _this.mapInstance, _this.markers, {
                                maxZoom: 12,
                                styles: cluster_styles
                            } ) );
                        }
                    }
                }
            }

            _this.ignoreLoadListingsByBounds = false;
        };

        /**
         * Load properties popup.
         *
         * @param location
         * @param property_config
         * @returns {function(): void}
         */
        HalfMap.prototype.propertyPopup = function( location, property_config ) {
            return function() {
                var request_data = {
                    post_id: property_config.post_id,
                    action: 'es_get_property_item',
                };

                if ( typeof _this.xhr !== 'undefined' ) {
                    _this.xhr.abort();
                }

                _this.close_popups();

                _this.xhr = $.post( Estatik.settings.ajaxurl, request_data, function( response ) {
                    response = response || {};
                    property_config.content = response.content;
                    property_config.position = new google.maps.LatLng( property_config.lat , property_config.lng );

                    if ( response.status === 'success' ) {
                        var popup = new EsGoogleMapPopup( property_config );
                        _this.mapInstance.setCenter( property_config.position );
                        popup.setMap( _this.mapInstance );
                        popup.getMap().panToWithOffset( property_config.position, 0, 120 );

                        setTimeout( function() {
                            Properties.initCarousel( $( _this.mapInstance.getDiv() ) );
                        }, 100 );
                    }
                }, 'json' );
            };
        };

        /**
         * Close map popups.
         */
        HalfMap.prototype.close_popups = function() {
            var popupContainer = _this.mapInstance.getDiv().querySelector( '.es-map-popup' );

            if ( popupContainer ) {
                popupContainer.remove();
            }
        };

        /**
         * Return cluster inline svg icon.
         *
         * @returns {string}
         */
        HalfMap.getClusterIcon = function( color ) {
            color = color || Estatik.settings.map_cluster_color;
            var cluster = Estatik.settings.map_cluster_icons[Estatik.settings.map_cluster_icon];
            cluster = cluster.replaceAll( 'data-color', 'style="fill:' + color + '"' ).replaceAll('data-hide', 'style="fill:#ffffff"');
            var encoded = window.btoa( cluster );

            return ('data:image/svg+xml;base64,' + encoded);
        };
    };

    window.EstatikHalfMap = HalfMap;

    /**
     * Return entity builder class.
     *
     * @param entity_type
     */
    function entitiesBuilderFactory( entity_type ) {
        switch ( entity_type ) {
            case 'listings':
                return Properties;
        }
    }

    function toggleSidebar(layout, entity_type) {
        if ( entity_type === 'listings' ) {
            $( Estatik.settings.hfm_toggle_sidebar_selector ).toggleClass( 'es-hidden', layout === 'half_map' );
            $( document ).trigger( 'listings_toggle_sidebar', {
                layout: layout,
                entity_type: entity_type
            } );
        }
    }

    $( function() {

        /**
         * Initialize properties functionality on page load.
         */
        Properties.init();
        Entities.initSearch();

        /**
         * Initialize half map layout.
         */
        $( '.js-es-properties__map.es-properties__map--visible' ).each( function() {
            var $properties_wrap = $( this ).closest( '.js-es-properties' );
            var map_instance = new HalfMap( $properties_wrap );
            var $listings_wrapper = $properties_wrap.find( '.js-es-listings' );
            var hash = new EntitiesHash( $listings_wrapper.data( 'hash' ) );

            Properties.halfMapInstances[ hash.getValue( 'loop_uid' ) ] = map_instance;

            if ( map_instance ) {
                map_instance.init();
                toggleSidebar( 'half_map', 'listings' );
            }
        } );

        $( document ).on( 'click', '.js-es-entities-filter-item', function(e) {
            var $link = $( this );
            var query = $link.data( 'query' );

            if ( query && Object.keys( query ).length ) {
                var $entities_wrapper = $link.closest( '.js-es-entities-wrap' ).find( '.js-es-entities' );
                var $wrapper = $link.closest( '.js-es-entities-wrap' ).find( '.js-es-entities__wrap_inner' );
                var entity_type = $entities_wrapper.data( 'entity' );
                var builder = entitiesBuilderFactory( entity_type );

                var hash = new EntitiesHash( $entities_wrapper.data( 'hash' ) );

                Object.entries(query).forEach( function( item ) {
                    hash.setValue( item[0], item[1] );
                } );

                hash.setHistoryQuery();

                var data = {
                    action: 'get_' + entity_type,
                    hash: hash.getHash(),
                    reload_map: 1
                };

                $.post( Estatik.settings.ajaxurl, data, function( response ) {
                    builder.append( response, $wrapper );
                }, 'json' );
            }

            e.preventDefault();
            return false;
        } );

        /**
         * Change layout action.
         */
        $( document ).on( 'click', '.js-es-change-layout', function() {
            if ( ! $( this ).hasClass( 'es-btn--active' ) ) {
                var layout = $( this ).data( 'layout' );
                var $control_wrapper = $( this ).closest( '.js-es-control--layouts' );
                var $entities_wrap = $( this ).closest( '.js-es-entities-wrap' );
                var $entities_wrapper = $entities_wrap.find( '.js-es-entities' );
                var entity_type = $entities_wrapper.data( 'entity' );
                var builder = entitiesBuilderFactory( entity_type );
                var current_layout = builder.getLayoutClass( $entities_wrapper );
                $entities_wrap.removeClass( 'es-properties--hfm' );

                var hash = new EntitiesHash( $entities_wrapper.data( 'hash' ) );
                var loop_id = hash.getValue( 'loop_uid' );

                hash.setValue( 'layout', layout );
                hash.setHistoryQuery();

                $control_wrapper.find( '.js-es-change-layout' ).removeClass( 'es-btn--active' );

                $( this ).addClass( 'es-btn--active' );

                var temp_layout = layout;

                if ( 'half_map' === layout ) {
                    $entities_wrap.find( '.js-es-properties__map' ).addClass( 'es-properties__map--visible' );
                    $entities_wrap.addClass( 'es-properties--hfm' );
                    var map_instance = new HalfMap( $entities_wrap );

                    Properties.halfMapInstances[ loop_id ] = map_instance;

                    if ( map_instance ) {
                        map_instance.init();
                    }

                    layout = Estatik.settings.grid_layout;
                } else {
                    var $map = $entities_wrap.find( '.js-es-properties__map' );

                    if ( $map.length ) {
                        $map.removeClass( 'es-properties__map--visible' );
                        $entities_wrap.css( { 'margin-left': '-15px' } );
                    }
                }

                if ( Estatik.settings.hfm_toggle_sidebar && Estatik.settings.hfm_toggle_sidebar_selector ) {
                    toggleSidebar(temp_layout, entity_type);
                }

                var entity_css_type = builder.getLayoutEntityType();

                $entities_wrapper.removeClass( current_layout )
                    .addClass( 'es-' + entity_css_type + '--' + layout )
                    .data( 'layout', 'es-' + entity_css_type + '--' + layout );

                $entities_wrapper.data( 'hash', hash.getHash() );

                builder.init();
                $( window ).trigger( 'resize' );
            }

            return false;
        } );

        /**
         * Listings ajax pagination event.
         */
        $( document ).on( 'click', '.js-es-pagination a.page-numbers', function() {
            var $pagination_wrapper = $( this ).closest( '.es-pagination' );

            if ( ! $pagination_wrapper.find( '.page-numbers--preload' ).length ) {
                var reload_map = $pagination_wrapper.closest( '.es-properties--hfm' ).length;
                var page = $( this ).data( 'page-number' );
                var $wrapper = $( this ).closest( '.js-es-entities__wrap_inner' );
                var $entities_wrapper = $wrapper.find( '.js-es-entities' );
                $entities_wrapper.addClass( 'es-entities--loading' );
                var hash = new EntitiesHash( $entities_wrapper.data( 'hash' ) );
                var entity_type = $entities_wrapper.data( 'entity' );
                var builder = entitiesBuilderFactory( entity_type );
                var data = { reload_map: reload_map, action: 'get_' + entity_type, hash: hash.setValue( 'page_num', page ).getHash() };
                var loop_id = hash.getValue( 'loop_uid' );
                var sort = $wrapper.find( '.js-es-sort' ).val();
                hash.setValue( 'paged-' + loop_id, page );

                if ( sort ) {
                    hash.setValue( 'sort-' + loop_id, sort );
                    hash.setValue( 'sort', sort );
                }

                hash.setHistoryQuery();

                $( this ).addClass( 'page-numbers--preload' );
                $pagination_wrapper.addClass( 'es-pagination--disabled' );

                $.post( Estatik.settings.ajaxurl, data, function( response ) {
                    builder.append( response, $wrapper, true );
                }, 'json' );
            }

            return false;
        } );

        /**
         * Listings sorting via ajax event.
         */
        $( document ).on( 'change', '.js-es-sort', function() {
            var $el = $( this );
            var $wrapper = $el.closest( '.js-es-entities__wrap_inner' );

            if ( ! $wrapper.length ) return;

            $el.prop( 'disabled', 'disabled' );

            var $entities_wrapper = $wrapper.find( '.js-es-entities' );

            $entities_wrapper.addClass( 'es-entities--loading' );

            var hash = new EntitiesHash( $entities_wrapper.data( 'hash' ) );
            var loop_id = hash.getValue( 'loop_uid' );
            var sort = $el.val();

            if ( sort ) {
                hash.setValue( 'sort-' + loop_id, sort );
                hash.setValue( 'sort', sort );
            }

            hash.setValue( 'page_num', 1 );
            hash.setValue( 'paged-' + loop_id, 1  );

            hash.setHistoryQuery();
            var entity_name = $entities_wrapper.data( 'entity' );
            var builder = entitiesBuilderFactory( entity_name );

            var data = {
                action: 'get_' + entity_name,
                hash: hash.getHash(),
                reload_map: 1
            };

            $.post( Estatik.settings.ajaxurl, data, function( response ) {
                builder.append( response, $wrapper );
            }, 'json' ).always( function() {
                $el.removeProp( 'disabled' ).removeAttr( 'disabled' );
            } );
        } );

        /**
         * Reset all search filters using reset btn.
         */
        $( document ).on( 'click', '.js-es-remove-filters', function() {
            var $btn = $( this );
            $btn.addClass( 'es-btn--preload' ).attr( 'disabled', 'disabled' ).prop( 'disabled', 'disabled' );
            var $wrapper = $( this ).closest( '.js-es-entities-wrap' );
            if ($wrapper.length === 0) {
                $wrapper = $(this).closest('.es-search-nav--dropdowns');
            }
            $wrapper.find( '.js-es-address' ).val( '' );
            // $wrapper.find( '.js-es-search-nav__reset' ).trigger( 'click' );
            
            $wrapper.find( '.js-es-search-nav__item' ).find( 'input[type!="reset"][type!="button"][type!="submit"],select' ).each( function() {
                var $field = $( this );
                var type = $( this ).prop( 'type' );
                if ( type === 'radio' || type === 'checkbox' ) {
                    $field.removeProp( 'checked' ).removeAttr( 'checked' );
                    var $any_field = $( this ).closest( '.js-search-field-container' ).find( 'input[value=""]' );

                    if ( $any_field.length ) {
                        $any_field.prop( 'checked', 'checked' );
                    }
                } else {
                    if ( $( this ).hasClass( 'select2-hidden-accessible' ) ) {
                        if ( type === 'select-one' ) {
                            $( this ).val('').trigger( 'change' );
                        } else {
                            $( this ).val([]).trigger( 'change' );
                        }
                    } else {
                        $( this ).val('');
                    } 
                }
            } );

            $wrapper.find( '.js-es-search form' ).trigger( 'submit' );

            return false;
        } );
    } );

} )( jQuery );
