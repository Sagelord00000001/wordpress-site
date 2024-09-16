<?php

/**
 * @var $property Es_Property
 * @var $post WP_Post
 */

do_action( 'es_property_before_quick_edit_fields', $post ); ?>

<div class="es-property-quick-edit">
    <h4><?php _e( 'Quick edit', 'es' ); ?></h4>

    <div class="es-row">
        <div class="es-col-lg-5">
            <?php es_framework_field_render( 'post_title', array(
                'type' => 'text',
                'label' => __( 'Title' ),
                'value' => $post->post_title,
                'wrapper_class' => 'es-field--small'
            ) ); ?>
        </div>
        <div class="es-col-lg-7 es-col-taxonomies">
            <div class="es-row">
                <div class="es-col-lg-4">
                    <?php $values = es_get_terms_list( 'es_category' );
                    $values = $values && ! is_wp_error( $values ) ? array_combine(  $values, $values ) : array();
                    $selected = wp_get_object_terms( $post->ID, 'es_category', array(
                        'fields' => 'id=>name',
                    ) );

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
                        'value' => $selected,
                        'options' => $values,
                        'wrapper_class' => 'es-field--small'
                    ) ); ?>
                </div>
                <div class="es-col-lg-4">
                    <?php $values = es_get_terms_list( 'es_type' );
                    $values = $values && ! is_wp_error( $values ) ? array_combine(  $values, $values ) : array();
                    $selected = wp_get_object_terms( $post->ID, 'es_type', array(
                        'fields' => 'id=>name',
                    ) );

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
                        'value' => $selected,
                        'options' => $values,
                        'wrapper_class' => 'es-field--small'
                    ) ); ?>
                </div>
                <div class="es-col-lg-4">
                    <?php $values = es_get_terms_list( 'es_status' );
                    $values = $values && ! is_wp_error( $values ) ? array_combine(  $values, $values ) : array();
                    $selected = wp_get_object_terms( $post->ID, 'es_status', array(
                        'fields' => 'id=>name',
                    ) );

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
                        'value' => $selected,
                        'options' => $values,
                        'wrapper_class' => 'es-field--small'
                    ) ); ?>
                </div>
            </div>
        </div>

        <div class="es-col-md-6 es-col-price">
            <div class="es-row">
                <div class="es-col-6">
                    <?php es_property_field_render( 'price', array(
                        'label' => __( 'Price', 'es' ),
                        'type' => 'text',
                        'wrapper_class' => 'es-field--small'
                    ) ); ?>
                </div>
                <div class="es-col-6">
                    <?php es_property_field_render( 'price_per_sqft', array(
                        'label' => __( 'Price per sqft', 'es' ),
                        'type' => 'text',
                        'value' => $property->price,
                        'wrapper_class' => 'es-field--small'
                    ) ); ?>
                </div>
            </div>
        </div>
        <div class="es-col-md-3" style="align-self: flex-end;">
            <div class="es-row">
                <div class="es-col-12">
                    <?php es_property_field_render( 'call_for_price', array(
                        'label' => __( 'Call for price', 'es' ),
                        'type' => 'switcher',
                        'value' => $property->price_per_sqft,
                        'wrapper_class' => 'es-field--small',
                    ) ); ?>
                </div>
                <div class="es-col-4 hide-900"></div>
                <div class="es-col-4 hide-900"></div>
            </div>
        </div>
    </div>

    <?php do_action( 'es_property_before_quick_edit_fields', $post ); ?>

    <div class="es-control">
        <div class="es-control-left">
            <ul class="es-actions-buttons">
                <li><a href="<?php echo get_delete_post_link( $post->ID, '', true ); ?>"><span class="es-icon es-icon_trash"></span><?php _e( 'Delete property', 'es' ); ?></a></li>
                <li><a href="<?php echo esc_url( es_get_action_post_link( $post->ID, 'copy' ) ); ?>"><span class="es-icon es-icon_copy"></span><?php _e( 'Copy', 'es' ); ?></a></li>
            </ul>
        </div>
        <div class="es-control-right">
            <a href="#" class="es-btn js-es-cancel-quick-save"><?php _e( 'Cancel' ); ?></a>
            <a href="#" data-post-id="<?php echo esc_attr( $post->ID ); ?>" class="es-btn es-btn--secondary js-es-submit-quick-save"><?php _e( 'Save changes', 'es' ); ?></a>
        </div>
    </div>

    <?php do_action( 'es_property_after_quick_edit', $post ); ?>
</div>
