<div class="es-step es-step--hidden" id="step2">
    <h1><?php _e( 'Set up your Google Maps API Key', 'es' ); ?></h1>
    <p><?php _e( 'For using Google maps on listing pages.', 'es' ); ?></p>

    <div class="es-demo__field-container es-demo__field-container--agent">
        <?php es_settings_field_render( 'google_api_key', array(
            'label' => __( 'Google Maps API Key', 'es' ),
            'type' => 'text',
	        /* translators: %s: link */
            'description' => sprintf( __( 'To load Google Maps correctly you should enter Google API key. Read more information about %s.', 'es' ), "<a href='https://developers.google.com/maps/documentation/javascript/get-api-key' target='_blank'>" . __( 'getting the key', 'es' ) . "</a>" ),
        ) ); ?>
    </div>

    <a href="#step3" class="es-btn es-btn--step es-btn--large es-btn--primary es-btn--icon-right js-es-next-step"><?php _e( 'Continue', 'es' ); ?><span class="es-icon es-icon_arrow-right"></a>
</div>
