<?php

/**
 * @var $field stdClass
 * @var $machine_name string
 * @var $section_machine_name string
 * @var $sections array
 * @var $fields_builder Es_Fields_Builder
 */

unset( $sections['request_form'] );

$is_new = empty( $field );
$title = $is_new ? __( 'Create field', 'es' ) : __( 'Edit field', 'es' );

if ( $is_new ) {
	$field = array(
		'is_visible' => 1,
		'is_visible_for' => array( 'all_users' ),
		'section_machine_name' => $section_machine_name,
		'options' => array(),
	);
}

$name_disabled = ! empty( $field['fb_settings']['disable_name_edit'] ); ?>

<h2><?php echo $title; ?></h2>

<form action="" id="es-fields-builder-form">
	<?php es_field_builder_field_render( 'label', array(
		'type' => 'text',
		'label' => __( 'Field name', 'es' ),
		'attributes' => array(
			'required' => $name_disabled ? false : 'required',
			'readonly' => $name_disabled ? 'readonly' : false,
			'disabled' => $name_disabled ? 'disabled' : false,
		)
	), $field );

	es_field_builder_field_render( 'frontend_form_name', array(
		'type' => 'text',
		'label' => __( 'Label for frontend form', 'es' ),
	), $field );

	es_field_builder_field_render( 'frontend_visible_name', array(
		'type' => 'text',
		'label' => __( 'Visible on the front page (optional, helpful for translating custom field)', 'es' ),
	), $field );

	es_field_builder_field_render( 'section_machine_name', array(
		'type' => 'select',
		'label' => __( 'Single Property Section', 'es' ),
		'options' => $sections,
	), $field );

    if ( empty( $field['fb_settings']['disable_tab_field'] ) ) {
	    es_field_builder_field_render( 'tab_machine_name', array(
		    'type' => 'select',
		    'label' => __( 'Admin Tab', 'es' ),
		    'options' => $sections,
	    ), $field );
    }

	es_field_builder_field_render( 'mandatory', array(
		'type' => 'switcher',
		'label' => __( 'Required field', 'es' ),
	), $field );

    $type_disabled = ! empty( $field['fb_settings']['disable_type_edit'] ) ? true : false;

	es_field_builder_field_render( 'type', array(
		'type' => 'select',
		'label' => __( 'Type', 'es' ),
		'attributes' => array(
			'class' => 'es-field__input js-es-field__input-type',
			'disabled' => $type_disabled ? 'disabled' : false,
		),
		'options' => $fields_builder::get_types_list(),
	), $field );

	es_field_builder_field_render( 'is_full_width', array(
		'type' => 'switcher',
		'label' => __( 'Full width on single page', 'es' ),
	), $field );

	if ( $type_disabled ) {
		es_field_builder_field_render( 'type', array(
			'type' => 'hidden',
		), $field );
	} ?>

    <div class='js-es-fields-builder__field-settings'>
		<?php if ( ! empty( $field['type'] ) ) : ?>
			<?php do_action( 'es_fields_builder_field_settings', $field['type'], $field ); ?>
		<?php endif; ?>
    </div>

	<?php if ( ! empty( $field['is_address_field'] ) ) : ?>
		<?php es_field_builder_field_render( 'address_component', array(
            'description' => __( 'See', 'es' ) . " <a href='https://developers.google.com/maps/documentation/javascript/geocoding#GeocodingAddressTypes' target='_blank'>" . __( 'Address Types and Address Component Types', 'es' ) . "</a>",
            'label' => __( 'Address component', 'es' ),
			'type' => 'select',
			'options' => array(
				'street_address' => __( 'street_address', 'es' ),
				'route' => __( 'route', 'es' ),
				'intersection' => __( 'intersection', 'es' ),
				'political' => __( 'political', 'es' ),
				'country' => __( 'country', 'es' ),
				'administrative_area_level_1' => __( 'administrative_area_level_1', 'es' ),
				'administrative_area_level_2' => __( 'administrative_area_level_2', 'es' ),
				'administrative_area_level_3' => __( 'administrative_area_level_3', 'es' ),
				'administrative_area_level_4' => __( 'administrative_area_level_4', 'es' ),
				'administrative_area_level_5' => __( 'administrative_area_level_5', 'es' ),
				'colloquial_area' => __( 'colloquial_area', 'es' ),
				'locality' => __( 'locality', 'es' ),
				'neighborhood' => __( 'neighborhood', 'es' ),
				'sublocality_level_1' => __( 'Sublocality level 1', 'es' ),
				'sublocality_level_2' => __( 'Sublocality level 2', 'es' ),
				'sublocality_level_3' => __( 'Sublocality level 3', 'es' ),
				'sublocality_level_4' => __( 'Sublocality level 4', 'es' ),
				'sublocality_level_5' => __( 'Sublocality level 5', 'es' ),
				'sublocality' => __( 'sublocality', 'es' ),
				'premise' => __( 'premise', 'es' ),
				'subpremise' => __( 'subpremise', 'es' ),
				'establishment' => __( 'establishment', 'es' ),
				'landmark' => __( 'landmark', 'es' ),
				'postal_town' => __( 'postal_town', 'es' ),
				'postal_code' => __( 'Postal Code', 'es' ),
			)
		), $field ); ?>
	<?php endif; ?>

	<?php es_field_builder_field_render( 'is_visible', array(
		'type' => 'switcher',
		'label' => __( 'Visible on frontend', 'es' ),
		'attributes' => array(
			'data-toggle-container' => '#es-visible-for-container'
		),
	), $field );

	es_field_builder_field_render( 'is_visible_for', array(
		'before' => "<div id='es-visible-for-container'>",
		'type' => 'checkboxes',
		'label' => __( 'Visible for', 'es' ),
		'options' => $fields_builder::get_visible_for(),
		'pro' => array( 'agents', 'buyers', 'authenticated_users' ),
		'after' => "</div>",
	), $field );

	es_field_builder_field_render( 'search_support', array(
		'type' => 'switcher',
		'label' => __( 'Search support', 'es' ),
	), $field );

	es_field_builder_field_render( 'compare_support', array(
		'type' => 'switcher',
		'label' => __( 'Compare support', 'es' ),
		'pro' => true,
	), $field );

	es_field_builder_field_render( 'mls_import_support', array(
		'type' => 'switcher',
		'label' => __( 'MLS import support', 'es' ),
		'premium' => true,
	), $field ); ?>

    <div style="text-align: center;">
        <button type="submit" disabled class="es-btn es-btn--primary es-btn--large"><?php _e( 'Save changes', 'es' ); ?></button>
    </div>

	<?php if ( ! empty( $field['machine_name'] ) ) : ?>
		<?php es_field_builder_field_render( 'machine_name', array(
			'type' => 'hidden',
		), $field ); ?>
	<?php endif;

	if ( ! empty( $field['order'] ) ) : ?>
		<?php es_field_builder_field_render( 'order', array(
			'type' => 'hidden',
		), $field ); ?>
	<?php endif;

	wp_nonce_field( 'es_fields_builder_save_field' ); ?>

    <input type="hidden" name="action" value="es_fields_builder_save_field"/>

	<?php if ( ! empty( $field['machine_name'] ) ) : ?>
        <div class="es-row es-fields-builder-form__manage-item" style="margin-top: 50px;">
			<?php if ( empty( $field['fb_settings']['disable_deletion'] ) ) : ?>
                <div class="es-col-6 es-center">
                    <a href="#" data-section-machine-name="<?php echo $field['section_machine_name']; ?>" data-field-label="<?php echo $field['label']; ?>" data-machine-name="<?php echo $field['machine_name']; ?>" class="js-es-fields-list__item-delete">
						<?php _e( 'Delete field', 'es' ); ?>
                    </a>
                </div>
			<?php endif; ?>
        </div>
	<?php endif; ?>
</form>
