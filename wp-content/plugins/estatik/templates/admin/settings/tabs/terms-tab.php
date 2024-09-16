<h2><?php echo _x( 'Privacy policy & Terms of use', 'plugin settings', 'es' ); ?></h2>

<?php es_settings_field_render( 'terms_forms', array(
	'label' => __( 'Enable information about Privacy policy & Terms of use to submit forms', 'es' ),
	'type' => 'checkboxes',
) );

es_settings_field_render( 'terms_input_type', array(
	'label' => __( 'What to use as accepting Privacy policy & Terms of use?', 'es' ),
	'type' => 'radio-text',
	'texts' => array(
		'text' => __( 'By clicking the «BUTTON» button you agree to the Terms of Use and Privacy Policy', 'es' ),
		'checkbox' => __( 'I agree to the Terms of Use and Privacy Policy', 'es' ),
	),
    'size' => 6
) ); ?>

<div class="es-settings-fields es-settings-fields--max-width">
    <?php es_settings_recommended_page_render( 'terms_conditions_page_id', array(
        'page_name'         => __( 'Terms & conditions', 'es' ),
        'page_display_name' => __( 'Terms & conditions page', 'es' ),
        'page_content'      => 'Terms & Conditions',
    ) );

    es_settings_recommended_page_render( 'privacy_policy_page_id', array(
        'page_name'         => __( 'Privacy policy', 'es' ),
        'page_display_name' => __( 'Privacy policy page', 'es' ),
        'page_content'      => 'Privacy policy',
    ) ); ?>
</div>
