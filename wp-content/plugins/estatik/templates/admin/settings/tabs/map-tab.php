<?php $link = sprintf( "<a href='%s' target='_blank'>%s.</a>", 'https://developers.google.com/maps/documentation/javascript/get-api-key', _x( 'generate it', 'plugin settings link name', 'es' ) ); ?>
<h2><?php echo _x( 'Map', 'plugin settings', 'es' ); ?></h2>
<p class="es-setting-page-description">
    <?php _e( sprintf( 'To load Google Maps correctly you shoud enter Google API key.<br>If you don\'t have API key already then %s', $link ), 'es' ); ?>
</p>

<div class="es-settings-fields es-settings-fields--max-width">
    <?php es_settings_field_render( 'google_api_key', array(
        'label' => __( 'Google API key', 'es' ),
        'type' => 'text',
    ) );

    es_settings_field_render( 'default_lat_lng', array(
        'label' => __( 'Default latitude and longitude - center of the map', 'es' ),
        'type' => 'text',
        'attributes' => array(
            'placeholder' => __( 'ex: 12.381068,-1.492711', 'es' ),
        )
    ) );

    es_settings_field_render( 'map_zoom', array(
        'label' => __( 'Default zoom level', 'es' ),
        'type' => 'incrementer',
        'attributes' => array(
            'min' => 0,
            'max' => 18,
            'step' => 1,
        ),
        'caption' => __( 'Choose the zoom level for the map. 0 corresponds to a map of the earth fully zoomed out, and larger zoom levels zoom in at a higher resolution.' ),
    ) );

    es_settings_field_render( 'single_property_map_zoom', array(
	    'label' => __( 'Single property map zoom level', 'es' ),
	    'type' => 'incrementer',
	    'attributes' => array(
		    'min' => 0,
		    'max' => 18,
		    'step' => 1,
	    ),
	    'caption' => __( 'Choose the zoom level for the map on single property page. 0 corresponds to a map of the earth fully zoomed out, and larger zoom levels zoom in at a higher resolution.' ),
    ) ); ?>

    <div id="es-map-zoom-limit-container">
    <?php es_settings_field_render( 'map_zoom_min', array(
	    'label' => __( 'Min zoom level', 'es' ),
	    'type' => 'incrementer',
	    'value' => 1,
	    'attributes' => array(
		    'min' => 1,
		    'max' => 20,
		    'step' => 1,
	    ),
        'pro' => true,
    ) );

    es_settings_field_render( 'map_zoom_max', array(
	    'label' => __( 'Max zoom level', 'es' ),
	    'type' => 'incrementer',
	    'value' => 21,
	    'attributes' => array(
		    'min' => 1,
		    'max' => 21,
		    'step' => 1,
	    ),
	    'pro' => true,
    ) ); ?>
    </div>

    <?php es_settings_field_render( 'is_clusters_enabled', array(
        'label' => __( 'Enable markers cluster', 'es' ),
        'type' => 'switcher',
        'attributes' => array(
            'data-active-container' => '#es-cluster-container',
            'data-inactive-container' => '#es-map-zoom-limit-container',
            'data-toggle-container' => '',
        ),
    ) );

    es_settings_field_render( 'map_cluster_icon', array(
        'before' => "<div id='es-cluster-container'>",
        'label' => __( 'Select icon', 'es' ),
        'type' => 'radio-boxed',
        'item_class' => 'es-box--small es-box--marker',
        'size' => false,
    ) );

    es_settings_field_render( 'map_cluster_color', array(
        'label' => __( 'Icon color', 'es' ),
        'type' => 'color',
        'after' => '</div>'
    ) );

    es_settings_field_render( 'map_marker_type', array(
	    'label' => __( 'What to use as map marker?', 'es' ),
	    'type' => 'radio-bordered',
        'items_attributes' => array(
            'price' => array(
                'attributes' => array(
                    'disabled' => 'disabled'
                )
            )
        ),
    ) );

    es_settings_field_render( 'is_single_map_marker_enabled', array(
        'label' => __( 'Use single map marker?', 'es' ),
        'type' => 'switcher',
        'attributes' => array(
	        'data-toggle-container' => '',
	        'data-active-container' => '#es-single-marker-container',
	        'data-inactive-container' => '#es-multiple-markers-container',
        ),
    ) ); ?>

    <div id="es-single-marker-container">
	    <?php es_settings_field_render( 'map_marker_icon', array(
		    'label' => __( 'Select icon', 'es' ),
		    'type' => 'radio-boxed',
		    'options' => ests_values( 'map_marker_icon' ),
		    'item_class' => 'es-box--small es-box--marker',
		    'size' => false,
	    ) );

	    es_settings_field_render( 'map_marker_color', array(
		    'label' => __( 'Icon color', 'es' ),
		    'type' => 'color',
	    ) ); ?>
    </div>

    <div id="es-multiple-markers-container">
        <h4><?php _e( 'Set map markers', 'es' ); ?></h4>

        <div class='es-accordion js-es-accordion'>
            <div class='es-accordion__head'>
                <h3><?php _e( 'Default map marker icon', 'es' ); ?></h3>
                <button type='button' class='es-accordion__toggle js-es-accordion__toggle'>
                    <span class="es-icon es-icon_chevron-bottom"></span>
                </button>
            </div>
            <div class='es-accordion__body'>
			    <?php es_settings_field_render( 'map_marker_icon', array(
				    'label' => __( 'Select icon', 'es' ),
				    'type' => 'radio-boxed',
				    'options' => ests_values( 'map_marker_icon' ),
				    'item_class' => 'es-box--small es-box--marker',
				    'size' => false,
			    ) );

			    es_settings_field_render( 'map_marker_color', array(
				    'label' => __( 'Icon color', 'es' ),
				    'type' => 'color',
			    ) ); ?>
            </div>
        </div>

        <div id="es-multiple-map-marker-container">
            <div class='es-accordion js-es-accordion'>
                <div class='es-accordion__head'>
                    <h3><?php _e( 'Map markers for' ); ?></h3>
                    <button type='button' class='es-accordion__toggle js-es-accordion__toggle'>
                        <span class="es-icon es-icon_chevron-bottom"></span>
                    </button>
                </div>
                <div class='es-accordion__body'>
				    <?php es_settings_field_render( 'map_markers_list', array(
					    'type' => 'repeater',
					    'add_button_label' => __( 'Add map marker type', 'es' ),
					    'fields' => array(
						    'es_category' => array(
							    'label' => __( 'Property category', 'es' ),
							    'type' => 'select',
							    'options' => es_get_terms_list( 'es_category' ),
							    'attributes' => array(
								    'placeholder' => _x( 'All', 'plugin settings taxonomy placeholder', 'es' ),
							    ),
						    ),
						    'es_type' => array(
							    'label' => __( 'Property type', 'es' ),
							    'type' => 'select',
							    'options' => es_get_terms_list( 'es_type' ),
							    'attributes' => array(
								    'placeholder' => _x( 'All', 'plugin settings taxonomy placeholder', 'es' ),
							    ),
						    ),
						    'es_status' => array(
							    'label' => __( 'Property statuses', 'es' ),
							    'type' => 'select',
							    'options' => es_get_terms_list( 'es_status' ),
							    'attributes' => array(
								    'placeholder' => _x( 'All', 'plugin settings taxonomy placeholder', 'es' ),
							    ),
						    ),
						    'es_label' => array(
							    'label' => __( 'Property label', 'es' ),
							    'type' => 'select',
							    'options' => es_get_terms_list( 'es_label' ),
							    'attributes' => array(
								    'placeholder' => _x( 'All', 'plugin settings taxonomy placeholder', 'es' ),
							    ),
						    ),
						    'map_marker_icon' => array(
							    'label' => __( 'Select icon', 'es' ),
							    'type' => 'radio-boxed',
							    'options' => ests_values( 'map_marker_icon' ),
							    'default_value' => ests_default( 'map_marker_icon' ),
							    'item_class' => 'es-box--small es-box--marker',
							    'size' => false,
						    ),
						    'map_marker_color' => array(
							    'label' => __( 'Icon color', 'es' ),
							    'type' => 'color',
							    'default_value' => ests_values( 'map_marker_color' ),
						    ),
					    ),
				    ) ); ?>
                </div>
            </div>
        </div>
    </div>
</div>
