<?php
/**
 * @var $property Es_Property
 * @var $post WP_Post
 */
?>

<div class="es-property-quick-edit">
    <?php do_action( 'es_property_before_quick_bulk_edit' ); ?>

    <a href="#" class="es-close js-es-cancel-quick-save">
        <span class="es-icon es-icon_close"></span>
    </a>
    <h2><?php _e( 'Quick edit', 'es' ); ?></h2>
    <div class="es-selected">
        <span class="js-es-selected-num es-num">0</span>
        <b><?php _e( 'selected listings', 'es' ); ?></b>
    </div>

    <?php do_action( 'es_property_before_quick_edit_bulk_fields' ); ?>

    <div class="es-row es-row-fields">
        <div class="es-col-lg-4 es-col-md-6">
            <?php $values = es_get_terms_list( 'es_category' );
            $values = $values && ! is_wp_error( $values ) ? array_combine(  $values, $values ) : array();

            es_framework_field_render( 'es_category', array(
	            'type' => 'hidden',
	            'value' => '',
	            'attributes' => array(
		            'name' => 'tax_input[es_category]',
	            ),
            ) );

            es_framework_field_render( 'es_category', array(
                'type' => 'select',
                'label' => __( 'Category', 'es' ),
                'attributes' => array(
                    'name' => 'tax_input[es_category]',
                    'data-placeholder' => __( 'Choose category', 'es' ),
                    'multiple' => 'multiple',
                    'class' => 'js-es-select2'
                ),
                'options' => $values,
                'wrapper_class' => 'es-field--small'
            ) ); ?>
        </div>
        <div class="es-col-lg-4 es-col-md-6">
            <?php $values = es_get_terms_list( 'es_type' );
            $values = $values && ! is_wp_error( $values ) ? array_combine(  $values, $values ) : array();

            es_framework_field_render( 'es_type', array(
	            'type' => 'hidden',
	            'value' => '',
	            'attributes' => array(
		            'name' => 'tax_input[es_type]',
	            ),
            ) );

            es_framework_field_render( 'es_type', array(
                'type' => 'select',
                'label' => __( 'Type', 'es' ),
                'attributes' => array(
                    'name' => 'tax_input[es_type]',
                    'data-placeholder' => __( 'Choose type', 'es' ),
                    'multiple' => 'multiple',
                    'class' => 'js-es-select2'
                ),
                'options' => $values,
                'wrapper_class' => 'es-field--small'
            ) ); ?>
        </div>
        <div class="es-col-lg-4 es-col-md-6">
            <?php $values = es_get_terms_list( 'es_status' );
            $values = $values && ! is_wp_error( $values ) ? array_combine(  $values, $values ) : array();

            es_framework_field_render( 'es_status', array(
	            'type' => 'hidden',
	            'value' => '',
	            'attributes' => array(
		            'name' => 'tax_input[es_status]',
	            ),
            ) );

            es_framework_field_render( 'es_status', array(
                'type' => 'select',
                'label' => __( 'Status', 'es' ),
                'attributes' => array(
                    'name' => 'tax_input[es_status]',
                    'data-placeholder' => __( 'Choose status', 'es' ),
                    'multiple' => 'multiple',
                    'class' => 'js-es-select2'
                ),
                'options' => $values,
                'wrapper_class' => 'es-field--small'
            ) ); ?>
        </div>
    </div>

    <?php do_action( 'es_property_before_quick_edit_bulk_fields' ); ?>

    <div class="es-control">
        <div class="es-control-left">
            <ul class="es-actions-buttons">
                <li><a href="#" class="js-es-delete-bulk" data-action="delete" data-nonce="<?php echo wp_create_nonce( 'es_entities_actions' ); ?>"><span class="es-icon es-icon_trash"></span><?php _e( 'Delete properties', 'es' ); ?></a></li>
                <li><a href="#" class="js-es-duplicate-bulk" data-action="copy" data-nonce="<?php echo wp_create_nonce( 'es_entities_actions' ); ?>"><span class="es-icon es-icon_copy"></span><?php _e( 'Copy', 'es' ); ?></a></li>
            </ul>
        </div>
        <div class="es-control-right">
            <a href="#" class="es-btn es-btn--transparent js-es-cancel-quick-save"><?php _e( 'Cancel' ); ?></a>
            <a href="#" class="es-btn es-btn--secondary js-es-submit-quick-save-bulk"><?php _e( 'Save changes', 'es' ); ?></a>
        </div>
    </div>

    <?php do_action( 'es_property_after_quick_bulk_edit' ); ?>
</div>
