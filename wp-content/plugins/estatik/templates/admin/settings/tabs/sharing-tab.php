<h2><?php _e( 'Sharing', 'es' ); ?></h2>

<div class="es-settings-fields es-settings-fields--max-width">
    <?php es_settings_field_render( 'is_link_sharing_enabled', array(
        'label' => __( 'Enable sharing with link', 'es' ),
        'type' => 'switcher',
    ) );

    es_settings_field_render( 'is_social_sharing_enabled', array(
        'label' => __( 'Enable sharing via social networks', 'es' ),
        'type' => 'switcher',
        'attributes' => array(
            'data-toggle-container' => '#es-sharing-container',
        )
    ) ); ?>

    <div id="es-sharing-container">
        <?php es_settings_field_render( 'social_networks', array(
            'label' => __( 'Select options', 'es' ),
            'type' => 'checkboxes',
        ) ); ?>
    </div>

    <?php es_settings_field_render( 'is_pdf_enabled', array(
        'label' => __( 'Enable sharing with PDF', 'es' ),
        'type' => 'switcher',
        'pro' => true,
    ) ); ?>
</div>
