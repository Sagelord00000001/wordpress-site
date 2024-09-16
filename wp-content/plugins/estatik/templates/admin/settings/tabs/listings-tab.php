<h2><?php echo _x( 'Listings', 'plugin settings', 'es' ); ?></h2>

<div class="es-settings-fields es-settings-fields--max-width">
    <?php es_settings_field_render( 'post_type_name', array(
        'label' => __( 'General listing name', 'es' ),
        'type' => 'text',
    ) );

    es_settings_field_render( 'default_property_image_id', array(
        'label' => __( 'Default property image', 'es' ),
        'type' => 'images',
        'description' => __( 'Maximum file size - 2MB.<br>Allowed file types: JPG, PNG, GIF.', 'es' ),
    ) ); ?>
</div>

<?php es_settings_field_render( 'listings_layout', array(
	'label' => __( 'Default layout for listings pages', 'es' ),
	'type' => 'radio-image',
	'images' => array(
		'grid-3' => ES_PLUGIN_URL . 'admin/images/grid-layout.svg',
		'grid-2' => ES_PLUGIN_URL . 'admin/images/large-grid-layout.svg',
		'list' => ES_PLUGIN_URL . 'admin/images/list-layout.svg',
	),
) );

es_settings_field_render( 'is_layout_switcher_enabled', array(
    'before' => '<div class="es-settings-fields es-settings-fields--max-width">',
	'label' => __( 'Enable list view', 'es' ),
	'type' => 'switcher',
    'attributes' => array(
        'data-grid-3-label' => __( 'Enable grid view', 'es' ),
        'data-grid-2-label' => __( 'Enable grid view', 'es' ),
        'data-list-label' => __( 'Enable list view', 'es' ),
    ),
    'after' => '</div>',
) );

es_settings_field_render( 'single_layout', array(
	'label' => __( 'Default layout for single listing pages', 'es' ),
	'type' => 'radio-image',
	'images' => array(
		'single-slider' => ES_PLUGIN_URL . 'admin/images/single-listing-slider.svg',
//		'single-full-width-slider' => ES_PLUGIN_URL . 'admin/images/single-full-width-slider.svg',
		'single-tiled-gallery' => ES_PLUGIN_URL . 'admin/images/single-tiled-gallery.svg',
//		'single-full-width-tiled' => ES_PLUGIN_URL . 'admin/images/single-full-width-tiled.svg',
		'single-left-slider' => ES_PLUGIN_URL . 'admin/images/single-left-slider.svg',
	),
) ); ?>

<div class="es-settings-fields es-settings-fields--max-width">

<?php

es_settings_field_render( 'is_default_archive_template_enabled', array(
	'label' => __( 'Enable default archive template', 'es' ),
	'type' => 'switcher',
) );

es_settings_field_render( 'is_collapsed_description_enabled', array(
	'label' => __( 'Enable collapsed description', 'es' ),
	'type' => 'switcher',
) );

es_settings_field_render( 'is_lightbox_disabled', array(
	'label' => __( 'Disable lightBox on single page', 'es' ),
	'type' => 'switcher',
) );

es_settings_field_render( 'is_request_form_geolocation_enabled', array(
	'label' => __( 'Enable request form geolocation', 'es' ),
	'type' => 'switcher',
    'description' => __( 'This option uses for autofill tel code field by user location.', 'es' ),
) );

es_settings_field_render( 'is_request_form_button_disabled', array(
	'label' => __( 'Hide Request Info button', 'es' ),
	'type' => 'switcher',
) );

if ( $fonts = es_get_google_fonts() ) {
    es_settings_field_render( 'headings_font', array(
        'label' => __( 'Property headings font', 'es' ),
        'type' => 'select',
        'attributes' => array(
            'class' => 'js-es-select2',
        ),
        'options' => wp_list_pluck( $fonts, 'family', 'family' ),
        'reset_button' => true,
        'reset_value' => ests_default( 'headings_font' ),
    ) );

    es_settings_field_render( 'content_font', array(
        'label' => __( 'Property content font', 'es' ),
        'type' => 'select',
        'attributes' => array(
            'class' => 'js-es-select2',
        ),
        'options' => wp_list_pluck( $fonts, 'family', 'family' ),
        'reset_button' => true,
        'reset_value' => ests_default( 'content_font' ),
    ) );
}

es_settings_field_render( 'is_property_carousel_enabled', array(
    'type' => 'switcher',
    'label' => __( 'Enable property item carousel', 'es' )
) );

$image_sizes = es_get_image_sizes();

es_settings_field_render( 'property_item_image_size', array(
    'label' => __( 'Property item image size', 'es' ),
    'type' => 'select',
    'attributes' => array(
        'class' => 'js-es-select2',
    ),
    'options' => $image_sizes,
) );

es_settings_field_render( 'properties_per_page', array(
	'label' => __( 'Properties number per page', 'es' ),
	'type' => 'number',
) );

es_settings_field_render( 'is_properties_sorting_enabled', array(
	'label' => __( 'Enable sorting', 'es' ),
	'type' => 'switcher',
    'attributes' => array(
	    'data-toggle-container' => '#es-sorting-container',
    ),
) ); ?>

<div id="es-sorting-container" class="es-hidden">
    <?php es_settings_field_render( 'properties_sorting_options', array(
        'label' => __( 'Sort options', 'es' ),
        'type' => 'checkboxes',
    ) );

    es_settings_field_render( 'properties_default_sorting_option', array(
        'label' => __( 'Default sort options', 'es' ),
        'type' => 'select',
    ) ); ?>
</div>

<?php es_settings_field_render( 'is_price_enabled', array(
	'label' => __( 'Show price', 'es' ),
	'type' => 'switcher',
) );

es_settings_field_render( 'is_listing_address_enabled', array(
	'label' => __( 'Show listing address', 'es' ),
	'type' => 'switcher',
) );

es_settings_field_render( 'title_mode', array(
	'label' => __( 'What to show on the listing preview block?', 'es' ),
	'type' => 'radio-bordered',
) );

es_settings_field_render( 'is_listing_description_enabled', array(
	'label' => __( 'Show description in listing box', 'es' ),
	'type' => 'switcher',
) );

es_settings_field_render( 'is_single_listing_map_enabled', array(
	'label' => __( 'Enable map on single listing page', 'es' ),
	'type' => 'switcher',
) );

es_settings_field_render( 'is_properties_wishlist_enabled', array(
	'label' => __( 'Enable wishlist', 'es' ),
	'type' => 'switcher',
) );

es_settings_field_render( 'is_labels_enabled', array(
	'label' => __( 'Enable labels', 'es' ),
	'type' => 'switcher',
) );

es_settings_field_render( 'is_properties_sharing_enabled', array(
	'label' => __( 'Enable sharing', 'es' ),
	'type' => 'switcher',
) );

es_settings_field_render( 'is_date_added_enabled', array(
	'label' => __( 'Show date added', 'es' ),
	'type' => 'switcher',
) );

es_settings_field_render( 'is_open_collapsed_filter_mobile_enabled', array(
	'label' => __( 'Open collapsed filters on mobile devices', 'es' ),
	'type' => 'switcher',
) ); ?>
</div>
