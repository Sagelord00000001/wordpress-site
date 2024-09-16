<h2><?php echo _x( 'General', 'plugin settings', 'es' ); ?></h2>

<div class="es-settings-fields es-settings-fields--general es-settings-fields--max-width">
    <?php if ( current_user_can( 'install_languages' ) ) {
        if ( ! function_exists( 'wp_get_available_translations' ) ) {
            /** WordPress Translation Installation API */
            require_once( ABSPATH . 'wp-admin/includes/translation-install.php' );
        }

        if ( wp_can_install_language_pack() ) {
            es_settings_field_render( 'language', array(
                'label' => __( 'Language', 'es' ),
                'type' => 'select',
                'attributes' => array(
                    'placeholder' => __( 'Choose language', 'es' ),
                ),
                'options' => wp_list_pluck( wp_get_available_translations(), 'native_name', 'language' ),
            ) );
        }
    } ?>

    <div class="es-field-row">
        <?php es_settings_field_render( 'area_unit', array(
            'label' => __( 'Area unit', 'es' ),
            'type' => 'select',
        ) ); ?>

        <?php es_settings_field_render( 'lot_size_unit', array(
            'label' => __( 'Lot size unit', 'es' ),
            'type' => 'select',
        ) ); ?>
    </div>

    <?php es_settings_field_render( 'currency', array(
        'label' => __( 'Currency', 'es' ),
        'type' => 'select',
    ) );

    es_settings_field_render( 'date_format', array(
        'label' => __( 'Date format', 'es' ),
        'type' => 'select',
    ) );

    es_settings_field_render( 'time_format', array(
        'label' => __( 'Time format', 'es' ),
        'type' => 'radio-bordered',
    ) );

    es_settings_field_render( 'is_white_label_enabled', array(
	    'label' => __( 'Enable white label', 'es' ),
	    'type' => 'switcher',
	    'pro' => true,
	    'description' => __( "Enable this option if you want to remove Estatik logo and 'Powered by' link on Estatik pages.", 'es' ),
    ) );

    es_settings_field_render( 'is_rest_support_enabled', array(
	    'label' => __( 'Enable REST Support', 'es' ),
	    'type' => 'switcher',
	    'description' => __( "Please enable REST support if you need to use Gutenberg or pull your listings via wp api.", 'es' ),
    ) );

    es_settings_field_render( 'logo_attachment_id', array(
        'label' => __( 'Logo image for admin login page', 'es' ),
        'type' => 'images',
        'description' => __( 'Maximum file size - 2MB.<br>Allowed file types: JPG, PNG, GIF.', 'es' ),
        'button_label' => __( 'Upload image', 'es' ),
    ) );

    es_settings_field_render( 'main_color', array(
        'label' => __( 'Main color', 'es' ),
        'type' => 'color',
        'description' => __( 'For large buttons like Search, Request info, etc.', 'es' ),
        'reset_button' => true,
        'reset_value' => ests_default( 'main_color' ),
    ) );

    es_settings_field_render( 'secondary_color', array(
        'label' => __( 'Secondary color', 'es' ),
        'type' => 'color',
        'description' => __( 'For smaller buttons like Search on results page, Contact, etc.', 'es' ),
        'reset_button' => true,
        'reset_value' => ests_default( 'secondary_color' ),
    ) );

    es_settings_field_render( 'is_tel_code_disabled', array(
        'label' => __( 'Disable tel country code', 'es' ),
        'type' => 'switcher',
    ) ); ?>
</div>
