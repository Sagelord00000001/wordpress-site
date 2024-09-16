<h2><?php _e( 'Listing search', 'es' ); ?></h2>

<div class="es-settings-fields es-settings-fields--max-width">
    <?php global $wp_taxonomies;
    $currency = ests( 'currency' );

    es_settings_field_render( 'address_search_placeholder', array(
        'label' => __( 'Address field search placeholder', 'es' ),
        'type' => 'text',
    ) );

    es_settings_recommended_page_render( 'search_results_page_id', array(
        'page_name'         => __( 'Search results', 'es' ),
        'page_display_name' => __( 'Default Search results', 'es' ),
        'page_content'      => '[es_my_listing]',
    ) );

    $price_desc = __( 'Allow only comma separated numbers. Do not add decimal points, dashes, spaces, currency signs.Ex: 5000,7000', 'es' );
    $comma_desc = __( 'Allow only comma separated numbres.', 'es' );

    es_settings_field_render( 'is_saved_search_enabled', array(
        'label' => __( 'Enable saved search', 'es' ),
        'type' => 'switcher',
    ) );

    es_settings_field_render( 'is_update_search_results_enabled', array(
        'label' => __( 'Enable auto-update search results for Simple and Advanced Estatik search', 'es' ),
        'type' => 'switcher',
    ) );

    es_settings_field_render( 'is_locations_autocomplete_enabled', array(
        'label' => __( 'Enable autocomplete locations for search', 'es' ),
        'type' => 'switcher',
        'description' => __( 'Autocomplete will be done with data used on your database', 'es' ),
    ) );

//    es_settings_field_render( 'is_geolocation_search_enabled', array(
//	    'label' => __( 'Enable geolocation search - «Homes near me»', 'es' ),
//	    'type' => 'switcher',
//	    'description' => __( "Listings search is based on a user's current geolocation. Note: Google Geolocation doesn't work in Chrome without SSL (https://). You can enable IPINFO location for Chrome browser below if you don't have SSL.", 'es' ),
//        'pro' => true,
//    ) ); ?>

    <h2><?php _e( 'Filters options', 'es' ); ?></h2>

    <div class='es-accordion js-es-accordion'>
        <div class='es-accordion__head'>
            <h3><?php echo _x( 'Price', 'plugin options', 'es' ); ?></h3>
            <button type='button' class='es-accordion__toggle js-es-accordion__toggle'>
                <span class='es-icon es-icon_chevron-bottom'></span>
            </button>
        </div>
        <div class='es-accordion__body'>
            <?php 
            es_settings_field_render( 'price_input_type', array(
                'type' => 'radio-bordered',
                'label' => __( 'Price Input Type', 'es' ),
                'attributes' => array(
                    'class' => 'js-es-price-input-type-container',
                ),
            ) );

            es_settings_field_render( 'is_same_price_for_categories_enabled', array(
                'type' => 'switcher',
                'label' => __( 'Use the same price list for all categories and types', 'es' ),
                'attributes' => array(
                    'data-toggle-container' => '',
                    'data-active-container' => '#es-same-price-list-container',
                    'data-inactive-container' => '#es-diff-price-list-container',
                ),
            ) );

            es_settings_field_render( 'min_prices_list', array(
                'label' => sprintf( __( 'Minimum prices, %s', 'es' ), $currency ),
                'type' => 'textarea',
                'description' => $price_desc,
                'attributes' => array(
                    'rows' => 6,
                ),
                'before' => "<div id='es-same-price-list-container'>",
            ) );

            es_settings_field_render( 'max_prices_list', array(
                'label' => sprintf( __( 'Maximum prices, %s', 'es' ), $currency ),
                'type' => 'textarea',
                'description' => $price_desc,
                'attributes' => array(
                    'rows' => 6,
                ),
                'after' => '</div>',
            ) );

            es_settings_field_render( 'custom_prices_list', array(
                'before' => "<div id='es-diff-price-list-container'>",
                'after' => "</div>",
                'type' => 'repeater',
                'add_button_label' => __( 'Add price list', 'es' ),
                'fields' => array(
                    'category' => array(
	                    'label' => __( 'Category', 'es' ),
                        'type' => 'select',
                        'options' => es_get_terms_list( 'es_category' ),
                        'attributes' => array(
                            'placeholder' => _x( 'All', 'plugin settings taxonomy placeholder', 'es' ),
                        ),
                    ),
                    'type' => array(
                        'label' => __( 'Type', 'es' ),
                        'type' => 'select',
                        'options' => es_get_terms_list( 'es_type' ),
                        'attributes' => array(
	                        'placeholder' => _x( 'All', 'plugin settings taxonomy placeholder', 'es' ),
                        ),
                    ),
                    'min_prices_list' => array(
                        'label' => sprintf( __( 'Minimum prices, %s', 'es' ), $currency ),
                        'type' => 'textarea',
                        'description' => $price_desc,
                        'attributes' => array(
                            'rows' => 5,
                        ),
                        'default_value' => ests_default( 'min_prices_list' ),
                    ),
                    'max_prices_list' => array(
                        'label' => sprintf( __( 'Maximum prices, %s', 'es' ), $currency ),
                        'type' => 'textarea',
                        'description' => $price_desc,
                        'attributes' => array(
                            'rows' => 5,
                        ),
                        'default_value' => ests_default( 'max_prices_list' ),
                    ),
                ),
            ) );?>
        </div>
    </div>


    <?php $link = "<a href='#' target='_blank'>" . _x( 'Data manager', 'plugin options', 'es' ) . "</a>";

    foreach ( array( 'es_category', 'es_type', 'es_rent_period' ) as $taxonomy ) {
        if ( ! empty( $wp_taxonomies[ $taxonomy ] ) ) : ?>
            <?php es_settings_field_render( 'search_' . $taxonomy . '_field_mode', array(
                'before' => "<div class='es-accordion js-es-accordion'>
                                <div class='es-accordion__head'>
                                    <h3>{$wp_taxonomies[ $taxonomy ]->label}</h3>
                                    <p class='es-subtitle'>" . sprintf( __( 'Go to %s to edit options.', 'es' ), $link ) . "</p>
                                    <button type='button' class='es-accordion__toggle js-es-accordion__toggle'>
                                        <span class='es-icon es-icon_chevron-bottom'></span>
                                    </button>
                                </div>
                                <div class='es-accordion__body'>",
                'label' => __( 'Select field type' ),
                'type' => 'radio-bordered',
                'after' => "</div></div>",
            ) );
        endif;
    }

    $fields = array( 'bedrooms', 'bathrooms' );

    // Bedrooms & bathrooms fields settings.
    foreach ( $fields as $field ) : $label = ucfirst( $field ); ?>
        <div class='es-accordion js-es-accordion'>
            <div class='es-accordion__head'>
                <h3><?php echo $label; ?></h3>
                <button type='button' class='es-accordion__toggle js-es-accordion__toggle'>
                    <span class='es-icon es-icon_chevron-bottom'></span>
                </button>
            </div>
            <div class='es-accordion__body'>
                <?php es_settings_field_render( "search_{$field}_list", array(
	                /* translators: %s: fields list */
                    'label' => sprintf( __( "%s list", 'es' ), $label ),
                    'type' => 'text',
                    'description' => $comma_desc
                ) );

                es_settings_field_render( "is_search_{$field}_range_enabled", array(
	                /* translators: %s: min - max */
                    'label' => sprintf( __( "Enable %s range", 'es' ), $field ),
                    'type' => 'switcher',
                    'attributes' => array(
	                    'data-toggle-container' => sprintf( '#es-%s-container', $field )
                    ),
                ) );

                es_settings_field_render( "search_min_{$field}_list", array(
	                /* translators: %s: min list */
                    'label' => sprintf( __( "Minimum %s list", 'es' ), $field ),
                    'type' => 'text',
                    'description' => $comma_desc,
                    'before' => "<div id='es-{$field}-container'>",
                ) );

                es_settings_field_render( "search_max_{$field}_list", array(
	                /* translators: %s: max list */
	                'label' => sprintf( __( "Maximum %s list", 'es' ), $field ),
                    'type' => 'text',
                    'description' => $comma_desc,
                    'after' => '</div>',
                ) ); ?>
            </div></div>
    <?php endforeach; ?>

    <div class='es-accordion js-es-accordion'>
        <div class='es-accordion__head'>
            <h3><?php _e( 'Half baths' ); ?></h3>
            <button type='button' class='es-accordion__toggle js-es-accordion__toggle'>
                <span class="es-icon es-icon_chevron-bottom"></span>
            </button>
        </div>
        <div class='es-accordion__body'>
            <?php es_settings_field_render( "search_half_baths_list", array(
                'label' => __( "Half baths list", 'es' ),
                'type' => 'text',
                'description' => $comma_desc,
            ) ); ?>
        </div>
    </div>

    <?php foreach ( array( 'amenities', 'features' ) as $field ) : $label = ucfirst( $field ); ?>
        <div class='es-accordion js-es-accordion'>
            <div class='es-accordion__head'>
                <h3><?php echo _x( $label, 'plugin options', 'es' ); ?></h3>
                <p class='es-subtitle'><?php printf( __( 'Go to %s to edit options.', 'es' ), "<a href='#' target='_blank'>" . _x( 'Data manager', 'plugin options', 'es' ) . "</a>" ); ?></p>
                <button type='button' class='es-accordion__toggle js-es-accordion__toggle'>
                    <span class="es-icon es-icon_chevron-bottom"></span>
                </button>
            </div>
            <div class='es-accordion__body'>
	            <?php es_settings_field_render( "is_{$field}_collapse_enabled", array(
		            'label' => __( 'Enable collapse list of more than 6 items', 'es' ),
		            'type' => 'switcher',
	            ) ); ?>
            </div>
        </div>
    <?php endforeach;

    foreach ( array( 'area', 'lot_size' ) as $field ) : $label = ucfirst( str_replace( '_', ' ', $field ) ); ?>
        <div class='es-accordion js-es-accordion'>
            <div class='es-accordion__head'>
                <h3><?php echo $label; ?></h3>
                <button type='button' class='es-accordion__toggle js-es-accordion__toggle'>
                    <span class="es-icon es-icon_chevron-bottom"></span>
                </button>
            </div>
            <div class='es-accordion__body'>
	            <?php es_settings_field_render( "search_min_{$field}_list", array(
		            'label' => sprintf( __( 'Minimum %1$s list, %2$s', 'es' ), $label, ests_label( 'area_unit' ) ),
		            'type' => 'textarea',
		            'caption' => $price_desc,
                    'attributes' => array(
                        'rows' => 5,
                    ),
	            ) );

	            es_settings_field_render( "search_max_{$field}_list", array(
		            'label' => sprintf( __( 'Maximum %1$s list, %2$s', 'es' ), $label, ests_label( 'lot_size_unit' ) ),
		            'type' => 'textarea',
		            'caption' => $price_desc,
		            'attributes' => array(
			            'rows' => 5,
		            ),
	            ) ); ?>
            </div>
        </div>
    <?php endforeach; ?>

    <div class='es-accordion js-es-accordion'>
        <div class='es-accordion__head'>
            <h3><?php _e( 'Floors', 'es' ); ?></h3>
            <button type='button' class='es-accordion__toggle js-es-accordion__toggle'>
                <span class="es-icon es-icon_chevron-bottom"></span>
            </button>
        </div>
        <div class='es-accordion__body'>
            <div class="es-field-row">
                <?php es_settings_field_render( "search_min_floor", array(
                    'label' => __( 'Minimum floor', 'es' ),
                    'type' => 'number',
                ) ); ?>

                <?php es_settings_field_render( "search_max_floor", array(
                    'label' => __( 'Maximum floor', 'es' ),
                    'type' => 'number',
                ) ); ?>
            </div>
        </div>
    </div>

    <div class='es-accordion js-es-accordion'>
        <div class='es-accordion__head'>
            <h3><?php _e( 'Floor Level', 'es' ); ?></h3>
            <button type='button' class='es-accordion__toggle js-es-accordion__toggle'>
                <span class="es-icon es-icon_chevron-bottom"></span>
            </button>
        </div>
        <div class='es-accordion__body'>
            <div class="es-field-row">
                <?php es_settings_field_render( "search_min_floor_level", array(
                    'label' => __( 'Minimum floor level', 'es' ),
                    'type' => 'number',
                ) ); ?>

                <?php es_settings_field_render( "search_max_floor_level", array(
                    'label' => __( 'Maximum floor level', 'es' ),
                    'type' => 'number',
                ) ); ?>
            </div>
        </div>
    </div>
</div>
