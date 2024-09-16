<div class="es-wrap">
    <form action="">
        <div class="es-filter">
            <?php do_action( 'es_properties_filter_before' ); ?>

            <input type="hidden" name="post_type" value="properties"/>

            <?php if ( $order = filter_input( INPUT_GET, 'order' ) ) : ?>
                <input type="hidden" name="order" value="<?php echo esc_attr( $order ); ?>"/>
            <?php endif; ?>

            <?php if ( $orderby = filter_input( INPUT_GET, 'orderby' ) ) : ?>
                <input type="hidden" name="orderby" value="<?php echo esc_attr( $orderby ); ?>"/>
            <?php endif; ?>

            <div class="es-row" style="align-items: flex-end;">
                <div class="es-col-xl-3 es-col-md-6">
                    <?php es_entities_filter_field_render( 's', array(
                        'label' => __( 'Property search', 'es' ),
                        'type' => 'text',
                        'attributes' => array(
                            'placeholder' => __( 'Search by title, ID or address', 'es' ),
                        ),
                        'wrapper_class' => 'es-field--small'
                    ) ); ?>
                </div>

                <?php if ( es_is_property_field_active( 'es_category' ) ) : ?>
                    <div class="es-col-xl-3 es-col-md-6">
                        <?php es_entities_filter_field_render( 'es_category', array(
                            'label' => __( 'Category', 'es' ),
                            'type' => 'select',
                            'attributes' => array(
                                'placeholder' => _x( 'All', 'properties filter select', 'es' ),
                            ),
                            'options' => es_get_terms_list( 'es_category', true ),
                            'wrapper_class' => 'es-field--small'
                        ) ); ?>
                    </div>
                <?php endif; ?>

	            <?php if ( es_is_property_field_active( 'es_status' ) ) : ?>
                    <div class="es-col-xl-3 es-col-md-6">
                        <?php es_entities_filter_field_render( 'es_status', array(
                            'label' => __( 'Status', 'es' ),
                            'type' => 'select',
                            'attributes' => array(
                                'placeholder' => _x( 'All', 'properties filter select', 'es' ),
                            ),
                            'options' => es_get_terms_list( 'es_status', true ),
                            'wrapper_class' => 'es-field--small'
                        ) ); ?>
                    </div>
                <?php endif; ?>

                <div class="es-col-xl-3 es-col-md-6 es-form-manage es-form-manage--top">
	                <?php if ( ! empty( $_GET['entities_filter'] ) ) : ?>
                        <a href="<?php echo admin_url( 'edit.php?post_type=properties' ); ?>"><?php _e( 'Reset', 'es' ); ?></a>
	                <?php endif; ?>
                    <a href="#" class="es-filter__advanced" data-toggle-label="<?php _e( 'More filters', 'es' ); ?>" data-toggle-container=".js-es-filter__advanced"><?php _e( 'Less filters', 'es' ); ?></a>
                    <button type="submit" class="es-btn es-btn--third es-btn--icon es-btn--small">
                        <span class="es-icon es-icon_search"></span>
                        <?php _e( 'Search' ); ?>
                    </button>
                </div>
            </div>

            <div class="js-es-filter__advanced es-filter__advanced">
                <div class="es-row">
	                <?php if ( es_is_property_field_active( 'es_type' ) ) : ?>
                        <div class="es-col-xl-4 es-col-md-6">
                            <?php es_entities_filter_field_render( 'es_type', array(
                                'label' => __( 'Type', 'es' ),
                                'type' => 'select',
                                'attributes' => array(
                                    'placeholder' => _x( 'All', 'properties filter select', 'es' ),
                                ),
                                'options' => es_get_terms_list( 'es_type', true ),
                                'wrapper_class' => 'es-field--small'
                            ) ); ?>
                        </div>
                    <?php endif; ?>

	                <?php if ( es_is_property_field_active( 'price' ) ) : ?>
                        <div class="es-col-xl-4 es-col-md-6">
                            <div class="es-row">
                                <div class="es-col-6">
                                    <?php es_entities_filter_field_render( 'price_min', array(
                                        'label' => __( 'Price range', 'es' ),
                                        'type' => 'number',
                                        'attributes' => array(
                                            'placeholder' => __( 'No min', 'es' ),
                                        ),
                                        'wrapper_class' => 'es-field--small'
                                    ) ); ?>
                                </div>
                                <div class="es-col-6">
                                    <?php es_entities_filter_field_render( 'price_max', array(
                                        'type' => 'number',
                                        'label' => __( 'Price range', 'es' ),
                                        'attributes' => array(
                                            'placeholder' => __( 'No max', 'es' ),
                                        ),
                                        'wrapper_class' => 'es-field--small'
                                    ) ); ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="es-col-xl-4 es-col-md-6">
                        <?php es_entities_filter_field_render( 'date_added', array(
                            'label' => __( 'Date added', 'es' ),
                            'type' => 'date',
                            'wrapper_class' => 'es-field--small',
                            'attributes' => array(
                                'placeholder' => ests( 'date_format' ),
                                'data-date-format' => ests( 'date_format' ),
                            ),
                        ) ); ?>
                    </div>

	                <?php if ( es_is_property_field_active( 'country' ) ) : ?>
                        <div class="es-col-xl-4 es-col-md-6">
                            <?php es_entities_filter_field_render( 'country', array_merge( array(
                                'wrapper_class' => 'es-field--small'
                            ), es_property_get_field_info( 'country' ) ) ); ?>
                        </div>
                    <?php endif; ?>

	                <?php if ( es_is_property_field_active( 'state' ) ) : ?>
                        <div class="es-col-xl-4 es-col-md-6">
                            <?php es_entities_filter_field_render( 'state', array_merge( array(
                                'wrapper_class' => 'es-field--small'
                            ), es_property_get_field_info( 'state' ) ) ); ?>
                        </div>
                    <?php endif; ?>

	                <?php if ( es_is_property_field_active( 'city' ) ) : ?>
                        <div class="es-col-xl-4 es-col-md-6">
                            <?php es_entities_filter_field_render( 'city', array_merge( array(
                                'wrapper_class' => 'es-field--small'
                            ), es_property_get_field_info( 'city' ) ) ); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <?php do_action( 'es_properties_filter_after' ); ?>
        </div>
        <div class="es-sort-nav">
            <?php Es_Properties_Archive_Page::navigation_meta();
            Es_Properties_Archive_Page::sort_dropdown(); ?>
        </div>
    </form>
</div>
