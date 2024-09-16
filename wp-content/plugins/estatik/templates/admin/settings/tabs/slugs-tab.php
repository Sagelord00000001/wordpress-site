<h2><?php _e( 'URL Slug' ); ?></h2>

<div class="es-settings-fields es-settings-fields--slugs es-settings-fields--max-width">
    <?php es_settings_field_render( 'property_slug', array(
        'label' => __( 'Property slug', 'es' ),
        'type' => 'text',
    ) );

    es_settings_field_render( 'category_slug', array(
        'label' => __( 'Property category slug', 'es' ),
        'type' => 'text',
    ) );

    es_settings_field_render( 'type_slug', array(
        'label' => __( 'Property type slug', 'es' ),
        'type' => 'text',
    ) );

    es_settings_field_render( 'status_slug', array(
        'label' => __( 'Property status slug', 'es' ),
        'type' => 'text',
    ) );

    es_settings_field_render( 'label_slug', array(
        'label' => __( 'Property label slug', 'es' ),
        'type' => 'text',
    ) );

    es_settings_field_render( 'tag_slug', array(
        'label' => __( 'Property tag slug', 'es' ),
        'type' => 'text',
    ) );

    es_settings_field_render( 'neighborhood_slug', array(
	    'label' => __( 'Property neighborhood slug', 'es' ),
	    'type' => 'text',
    ) );

    es_settings_field_render( 'state_slug', array(
        'label' => __( 'Province / state slug', 'es' ),
        'type' => 'text',
    ) );

    es_settings_field_render( 'city_slug', array(
        'label' => __( 'City slug', 'es' ),
        'type' => 'text',
    ) ); ?>
</div>
