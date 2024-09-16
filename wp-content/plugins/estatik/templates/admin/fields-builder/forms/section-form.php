<?php

/**
 * @var $section stdClass
 * @var $machine_name string
 * @var $sections array
 * @var $sections_builder Es_Sections_Builder
 */

$is_new = empty( $section );
$title = $is_new ? __( 'Create section', 'es' ) : __( 'Edit section', 'es' );

if ( $is_new ) {
	$section = array(
		'is_visible' => 1,
		'is_visible_for' => array( 'all_users' ),
        'message' => __( "Hello, I'd like more information about this home. Thank you!", 'es' ),
	);
} ?>

<h2><?php echo $title; ?></h2>

<form action="" id="es-fields-builder-form">
	<?php es_field_builder_field_render( 'label', array(
		'type' => 'text',
		'label' => __( 'Section name', 'es' ),
	), $section ); ?>

	<?php do_action( 'es_fields_builder_section_settings', $section ); ?>

	<?php es_field_builder_field_render( 'is_visible', array(
		'type' => 'switcher',
		'label' => __( 'Visible on frontend', 'es' ),
		'attributes' => array(
			'data-toggle-container' => '#es-visible-for-container'
		)
	), $section ); ?>

	<?php es_field_builder_field_render( 'is_visible_for', array(
		'before' => "<div id='es-visible-for-container'>",
		'type' => 'checkboxes',
		'label' => __( 'Visible for', 'es' ),
		'options' => $sections_builder::get_visible_for(),
		'pro' => array( 'agents', 'buyers', 'authenticated_users' ),
		'after' => "</div>",
	), $section ); ?>

	<?php if ( ! empty( $section['machine_name'] ) ) : ?>
		<?php es_field_builder_field_render( 'machine_name', array(
			'type' => 'hidden',
		), $section ); ?>
	<?php endif; ?>

	<?php if ( ! empty( $section['order'] ) ) : ?>
		<?php es_field_builder_field_render( 'order', array(
			'type' => 'hidden',
		), $section ); ?>
	<?php endif; ?>

    <input type="hidden" name="action" value="es_fields_builder_save_section"/>
	<?php wp_nonce_field( 'es_fields_builder_save_section' ); ?>

    <div style="text-align: center;">
        <button type="submit" disabled class="es-btn es-btn--primary es-btn--large"><?php _e( 'Save changes', 'es' ); ?></button>
    </div>

	<?php if ( ! empty( $section['machine_name'] ) ) : ?>
        <div class="es-row es-fields-builder-form__manage-item" style="margin-top: 50px;">
			<?php if ( empty( $section['fb_settings']['disable_deletion'] ) ) : ?>
                <div class="es-col-6 es-center">
                    <a href="#" data-section-label="<?php echo $section['label']; ?>" data-machine-name="<?php echo $section['machine_name']; ?>" class="js-es-fields-builder-section-delete-confirm">
						<?php _e( 'Delete section', 'es' ); ?>
                    </a>
                </div>
			<?php endif; ?>
        </div>
	<?php endif; ?>
</form>
