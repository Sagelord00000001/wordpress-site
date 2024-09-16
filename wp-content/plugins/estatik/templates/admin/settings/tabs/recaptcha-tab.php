<h2><?php echo _x( 'Google reCAPTCHA', 'plugin settings', 'es' ); ?></h2>
<?php $link = sprintf( "<a href='%s' target='_blank'>%s</a>", 'https://www.google.com/recaptcha/', _x( 'generate them', 'plugin settings', 'es' ) ); ?>
<p class="es-setting-page-description"><?php _e( sprintf( "If you don't have keys already then %s.", $link ), 'es' ); ?></p>

<div class="es-settings-fields es-settings-fields--max-width">
    <?php es_settings_field_render( 'recaptcha_version', array(
        'label' => __( 'reCAPTCHA version', 'es' ),
        'type' => 'radio-bordered',
    ) );

    es_settings_field_render( 'recaptcha_site_key', array(
        'label' => __( 'reCAPTCHA site key', 'es' ),
        'type' => 'text',
    ) );

    es_settings_field_render( 'recaptcha_secret_key', array(
        'label' => __( 'reCAPTCHA secret key', 'es' ),
        'type' => 'text',
    ) );

    es_settings_field_render( 'recaptcha_forms', array(
        'label' => __( 'Enable Google reCaptcha to submit forms:', 'es' ),
        'type' => 'checkboxes',
        'disable_hidden_input' => false,
    ) ); ?>
</div>
