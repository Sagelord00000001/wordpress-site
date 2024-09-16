<h2><?php _e( 'SEO', 'es' ); ?></h2>

<div class="es-settings-fields es-settings-fields--general es-settings-fields--max-width">
    <?php es_settings_field_render( 'is_auto_tags_enabled', array(
        'label' => __( 'Enable auto tags', 'es' ),
        'type' => 'switcher',
        'attributes' => array(
            'data-toggle-container' => '#es-clickable-tags'
        )
    ) );

    es_settings_field_render( 'is_clickable_tags_enabled', array(
        'before' => '<div id="es-clickable-tags">',
        'label' => __( 'Enable clickable tags', 'es' ),
        'type' => 'switcher',
        'after' => '</div>'
    ) );

    es_settings_field_render( 'is_dynamic_content_enabled', array(
        'label' => __( 'Enable dynamic content', 'es' ),
        'type' => 'switcher',
        'attributes' => array(
            'data-toggle-container' => '#es-dynamic-content'
        )
    ) ); ?>
</div>

<?php es_settings_field_render( 'dynamic_content', array(
    'label' => __( 'Dynamic content', 'es' ),
    'type' => 'editor',
    'before' => '<div id="es-dynamic-content">',
    'after' => '</div>',
) ); ?>
