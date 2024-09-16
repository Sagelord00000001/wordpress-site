<?php

/**
 * @var $id integer Section ID.
 * @var $this Es_Property_Fields_Meta_Box
 */

$property = es_get_the_property();
$fields_builder = es_get_fields_builder_instance();
$google_api_key = ests( 'google_api_key' );
$disabled_config = $google_api_key ? array() : array( 'attributes' => array( 'disabled' => 'disabled' ) );

if ( ! $google_api_key ) : ?>
    <?php echo es_get_notification_markup( sprintf( __( '<a href="%s">Set up your Google Maps API Key</a> for adding property location and using Google maps on listing pages.', 'es' ), admin_url( 'admin.php?page=es_settings#map' ) ), 'warning' ); ?>
<?php endif; ?>

<?php es_property_field_render( 'is_address_disabled' ); ?>
<?php es_property_field_render( 'address', array(
        'attributes' => array(
            'class' => ! $google_api_key ? 'disable-autocomplete' : 'js-es-autocomplete-address',
        ),
) ); ?>

<?php if ( $google_api_key ) : ?>
    <?php if ( ! $property->is_manual_address ) : ?>
        <a href="#" class="es-manual-address js-es-manual-address" data-toggle-label="<?php _e( 'Hide manual address entry', 'es' ); ?>"><?php _e( 'Add address manually', 'es' ); ?></a>
    <?php else : ?>
        <a href="#" class="es-manual-address js-es-manual-address" data-toggle-label="<?php _e( 'Add address manually', 'es' ); ?>"><?php _e( 'Hide manual address entry', 'es' ); ?></a>
    <?php endif; ?>
<?php endif; ?>

<div class="js-es-location-fields <?php echo $property->is_manual_address ? '' : 'es-hidden'; ?>">
    <div class="es-field-row es-field-row--2">
        <?php foreach ( es_get_address_fields_list() as $field ) : ?>
            <?php es_property_field_render( $field, $disabled_config ); ?>
        <?php endforeach; ?>
    </div>
    <div class="es-field-row es-field-row--2" style="margin-top: 0; margin-bottom: 36px;">
        <?php es_property_field_render( 'latitude', $disabled_config ); ?>
        <?php es_property_field_render( 'longitude', $disabled_config ); ?>
    </div>
</div>

<?php if ( $fields = $fields_builder::get_tab_fields( $id ) ) : ?>
    <div class="es-field-row">
		<?php foreach ( $fields as $field_key => $field_config ) :
			if ( ! in_array( $field_key, Es_Property_Fields_Meta_Box::$static_fields ) ) : ?>
                <div class="es-col-5">
					<?php es_property_field_render( $field_key, $field_config ); ?>
                </div>
			<?php endif; ?>
		<?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="js-es-form-map es-form-map es-hidden"></div>

<?php es_property_field_render( 'address_components' ); ?>
<?php es_property_field_render( 'is_manual_address', array(
    'attributes' => array(
        'class' => 'js-es-manual-address-input'
    )
) );
