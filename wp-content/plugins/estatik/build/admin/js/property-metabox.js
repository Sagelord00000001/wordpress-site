( function($) {
    'use strict';

    var map_triggered = false;

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

    function generateCoordinatesByAddressFields() {
        var address = [];
        address.push( $( '#es-field-city' ).find( 'option:selected' ).html() );
        address.push( $( '#es-field-state' ).find( 'option:selected' ).html() );
        address.push( $( '#es-field-country' ).find( 'option:selected' ).html() );
        address.push( $( '#es-field-address' ).val() );
        var map = $( '.js-es-form-map' ).get(0);

        address = address.filter( function( item ) {
            return !!(typeof item !== 'undefined' && item.length);
        } );

        if ( address ) {
            var geocoder = new google.maps.Geocoder();

            geocoder.geocode( { 'address': address.join( ' ' ) }, function( results, status ) {
                if (status === 'OK') {
                    var longitude = results[0].geometry.location.lng();
                    var latitude = results[0].geometry.location.lat();
                    initMap( longitude, latitude, map );
                    $( '.js-es-latitude' ).val( latitude );
                    $( '.js-es-longitude' ).val( longitude );
                }
            } );
        }
    }

    /**
     * Initialize google map.
     *
     * @param lon
     * @param lat
     * @param map
     */
    function initMap( lon, lat, map ) {
        if ( typeof google === 'undefined' || typeof google.maps === 'undefined' ) return;
        var set_pin_active = false;

        // Show map container.
        map.classList.remove( 'es-hidden' );

        // Initialize google map.
        var map_instance = new google.maps.Map( map , {
            center: {lat: +lat, lng: +lon},
            draggable: true,
            zoom: 16
        } );

        var controlDiv = document.createElement("div");
        controlDiv.innerHTML = EstatikMetabox.tr.set_pin;
        controlDiv.setAttribute( 'data-label', EstatikMetabox.tr.save_pin );
        controlDiv.style.margin = '10px';
        controlDiv.classList.add( 'es-btn', 'es-btn--third', 'es-btn--small' );
        controlDiv.addEventListener( 'click', function() {
            var toggle_label = this.getAttribute( 'data-label' );
            this.setAttribute( 'data-label', this.innerHTML );
            this.innerHTML = toggle_label;
            set_pin_active = ! set_pin_active;
            return false;
        } );

        map_instance.controls[ google.maps.ControlPosition.TOP_RIGHT ].push( controlDiv );

        var geocoder = new google.maps.Geocoder();

        google.maps.event.trigger( map_instance , 'resize' );

        // Add property marker.
        var marker = new google.maps.Marker( {
            position: map_instance.getCenter(),
            map: map_instance,
        } );

        // Set marker position always on map center on drag.
        window.google.maps.event.addListener( map_instance , 'drag', function (event) {
            if ( set_pin_active ) {
                marker.setPosition( map_instance.getCenter() );
            }
        } );

        // Load geodata on map stop drag.
        window.google.maps.event.addListener( map_instance , 'idle', function () {
            if ( map_triggered ) {
                if ( set_pin_active ) {
                    var position = map_instance.getCenter();
                    marker.setPosition( position );

                    if ( $( '.js-es-address-components' ).val() ) {
                        // Load google address components by coordinates.
                        geocoder.geocode( {'latLng': position }, function (results, status) {
                            if ( status !== google.maps.GeocoderStatus.OK ) return;

                            // Append address components data to the property address fields.
                            setAddressFieldsByGeoData( results[0] );
                        } );
                    } else {
                        $( '.js-es-latitude' ).val( position.lat() );
                        $( '.js-es-longitude' ).val( position.lng() );
                    }
                }
            } else {
                map_triggered = true;
            }
        } );

        return map_instance;
    }

    /**
     * Set addresses fields from google geo data.
     *
     * @param data
     */
    function setAddressFieldsByGeoData( data ) {
        var address_components = data.address_components;
        var location = data.geometry.location;
        var $fields = $( '.js-es-location' );

        // Set coordinates fields
        $( '.js-es-latitude' ).val( location.lat() );
        $( '.js-es-longitude' ).val( location.lng() );
        $( '.js-es-autocomplete-address' ).val( data.formatted_address );

        $( '.js-es-address-components' ).val( JSON.stringify( address_components ) );

        if ( typeof address_components !== 'undefined' ) {
            $fields.prop( 'disabled', true );
            address_components.forEach( function( item ) {
                item.types.forEach( function( type ) {
                    $fields.each( function() {
                        var value_types = $( this ).data( 'address-components' );
                        var $field = $( this );

                        if ( typeof value_types !== 'undefined' && value_types.includes( type ) ) {
                            if ( $field.prop( 'tagName' ).toLowerCase() === 'input' ) {
                                $field.val( item.long_name );
                            } else {
                                $field.html( new Option( item.long_name, item.long_name, false, true ) );
                            }
                        }
                    } );
                } );
            } );
        }
    }

    $( function() {
        $( document ).on( 'click', '.js-es-return-false', function() {
            return false;
        } );

        // Dont start the script if google maps is not loaded.
        // if ( typeof google === 'undefined' || typeof google.maps === 'undefined' ) return;

        var map = $( '.js-es-form-map' ).get(0);
        var $address_field = $( '.js-es-autocomplete-address:not(.disable-autocomplete)' );

        initBaseField();

        $( '.js-es-manual-address' ).click( function(e) {
            $( '.js-es-location-fields' ).toggleClass( 'es-hidden' );
            var $field =  $( '.js-es-manual-address-input' );
            var val = +$field.val() || 0;

            $field.val( +!val );
            e.preventDefault();
        } );

        $( '.js-es-select2-locations' ).select2( {
            tags: true,
            width: '100%',
        } );

        $( document ).on( 'change', '.js-es-location', function() {
            var $field = $( this );
            var dep_fields = $field.data( 'dependency-fields' );
            var $dep_field;

            if ( ! $field.is( ':disabled' ) ) {
                if ( dep_fields ) {
                    dep_fields.forEach( function( i ) {
                        $dep_field = $( '#es-field-' + i );
                        esLoadFieldLocations( $dep_field, $field );
                    } );
                }

                generateCoordinatesByAddressFields();
            }
        } );

        // Reload map on coordinates change.
        $( '.js-es-latitude, .js-es-latitude' ).change( function() {
            var lat = $( '.js-es-latitude' ).val();
            var lon = $( '.js-es-longitude' ).val();

            if ( lat && lon ) {
                initMap( lon, lat, map, !!$('.js-es-address-components').val() );
            }
        } ).trigger( 'change' );

        $address_field.on( 'keyup', function() {
            if ( ! $( this ).val().length ) {
                $( '.js-es-location' ).removeProp( 'disabled' ).removeAttr( 'disabled' ).html( false ).val( '' );
                initBaseField();
                $( '.js-es-address-components' ).val( '' ).data( 'value', false );
            }
        } );

        if ( typeof google !== 'undefined' && typeof google.maps !== 'undefined' && $address_field.length ) {
            var autocomplete = new google.maps.places.Autocomplete( $address_field.get(0) );

            // Reinit map when address changed.
            google.maps.event.addListener( autocomplete, 'place_changed', function() {
                var result = autocomplete.getPlace();

                if ( typeof result !== 'undefined' && typeof result.geometry !== 'undefined' ) {
                    var location = result.geometry.location;

                    if ( map ) {
                        map_triggered = false;
                        initMap( location.lng(), location.lat(), map );
                        setAddressFieldsByGeoData( result );
                    }
                }
            } );
        }
    } );

    $( '#publish' ).on('click', function(event) {
        var requiredDivs = $( '.js-es-is-required' );
        var isValid = true;
        var firstInvalidDiv = null;

        $( '.es-error-message' ).remove();
        
        requiredDivs.each(function( index ) {
            var checkboxes = $(this).find( 'input[type="checkbox"]' );
            var isChecked = checkboxes.is( ':checked' );

            if (!isChecked) {
                isValid = false;
                $( this ).prepend( '<p class="es-error-message" style="color: red;">Please select at least one option.</p>' );
                $( '#post-body-content' ).prepend( '<p class="es-error-message" style="color: red;">Please fill in all required fields.</p>' );
                if ( index == 0 ) {
                    firstInvalidDiv = $( this );
                }
            }
        } );

        if ( !isValid ) {
            event.preventDefault();
            if ( firstInvalidDiv !== null ) {
                $( 'html, body' ).animate({
                    scrollTop: firstInvalidDiv.offset().top - 60
                }, 1000);
            }
        }
    } ); 

} )( jQuery );
