<?php

/**
 * Class Es_Taxonomies.
 *
 * Register plugin custom taxonomies.
 */
class Es_Taxonomies {

	/**
	 * @return void
	 */
	public static function init() {
		add_action( 'init', array( 'Es_Taxonomies', 'register_taxonomies' ) );
	}

	/**
	 * Register custom plugin taxonomies.
	 *
	 * @return void
	 */
	public static function register_taxonomies() {
		$args = apply_filters( 'es_location_taxonomy_args', array(
            'labels' => array(
                'name' => __( 'Locations', 'es' ),
                'singular_name' => __( 'Location', 'es' ),
            ),
            'rewrite' => array( 'slug' => 'location', 'with_front' => false ),
            'show_ui' => true,
            'meta_box_cb' => false,
            'show_in_rest' => (bool) ests( 'is_rest_support_enabled' ),
            'rest_base' => 'es_locations',
            'query_var' => empty( $_GET['es'] ),
        ) );

		register_taxonomy( 'es_location', 'properties', $args );

		$args = apply_filters( 'es_category_taxonomy_args', array(
			'labels' => array(
				'name' => __( 'Categories', 'es' ),
				'singular_name' => __( 'Category', 'es' ),
			),
			'rewrite' => array( 'slug' => ests( 'category_slug' ), 'with_front' => false ),
			'show_in_rest' => (bool) ests( 'is_rest_support_enabled' ),
			'rest_base' => 'es_categories',
			'query_var' => empty( $_GET['es'] ),
		) );

		register_taxonomy( 'es_category', 'properties', $args );

		$args = apply_filters( 'es_type_taxonomy_args', array(
			'labels' => array(
				'name' => __( 'Types', 'es' ),
				'singular_name' => __( 'Type', 'es' ),
			),
			'rewrite' => array( 'slug' => ests( 'type_slug'), 'with_front' => false ),
			'show_in_rest' => (bool) ests( 'is_rest_support_enabled' ),
			'rest_base' => 'es_types',
			'query_var' => empty( $_GET['es'] ),
		) );

		register_taxonomy( 'es_type', 'properties', $args );

		$args = apply_filters( 'es_rent_period_taxonomy_args', array(
			'labels' => array(
				'name' => __( 'Rent Periods', 'es' ),
				'singular_name' => __( 'Rent Period', 'es' ),
			),
			'show_in_rest' => (bool) ests( 'is_rest_support_enabled' ),
			'rest_base' => 'es_rent_periods',
			'query_var' => empty( $_GET['es'] ),
		) );

		register_taxonomy( 'es_rent_period', 'properties', $args );

		$args = apply_filters( 'es_label_taxonomy_args', array(
			'labels' => array(
				'name' => __( 'Labels', 'es' ),
				'singular_name' => __( 'Label', 'es' ),
			),
			'meta_box_cb' => false,
			'rewrite' => array( 'slug' => ests( 'label_slug' ), 'with_front' => false ),
			'show_in_rest' => (bool) ests( 'is_rest_support_enabled' ),
			'rest_base' => 'es_labels',
			'query_var' => empty( $_GET['es'] ),
		) );

		register_taxonomy( 'es_label', 'properties', $args );

		$statuses_args = apply_filters( 'es_status_taxonomy_args', array(
			'labels' => array(
				'name' => __( 'Statuses', 'es' ),
				'singular_name' => __( 'Status', 'es' ),
			),
			'rewrite' => array( 'slug' => ests( 'status_slug' ), 'with_front' => false ),
			'show_in_rest' => (bool) ests( 'is_rest_support_enabled' ),
			'rest_base' => 'es_statuses',
			'query_var' => empty( $_GET['es'] ),
		) );

		register_taxonomy( 'es_status', 'properties', $statuses_args );

		$args = apply_filters( 'es_parking_taxonomy_args', array(
			'labels' => array(
				'name' => __( 'Parking', 'es' ),
				'singular_name' => __( 'Parking', 'es' ),
			),
			'show_in_rest' => (bool) ests( 'is_rest_support_enabled' ),
			'rest_base' => 'es_parkings',
			'query_var' => empty( $_GET['es'] ),
		) );

		register_taxonomy( 'es_parking', 'properties', $args );

		$args = apply_filters( 'es_roof_taxonomy_args', array(
			'labels' => array(
				'name' => __( 'Roof', 'es' ),
				'singular_name' => __( 'Roof', 'es' ),
			),
			'show_in_rest' => (bool) ests( 'is_rest_support_enabled' ),
			'rest_base' => 'es_roofs',
			'query_var' => empty( $_GET['es'] ),
		) );

		register_taxonomy( 'es_roof', 'properties', $args );

		$args = apply_filters( 'es_exterior_material_taxonomy_args', array(
			'labels' => array(
				'name' => __( 'Exterior Material', 'es' ),
				'singular_name' => __( 'Exterior Material', 'es' ),
			),
			'show_in_rest' => (bool) ests( 'is_rest_support_enabled' ),
			'rest_base' => 'es_exterior_materials',
			'query_var' => empty( $_GET['es'] ),
		) );

		register_taxonomy( 'es_exterior_material', 'properties', $args );

		$args = apply_filters( 'es_basement_taxonomy_args', array(
			'labels' => array(
				'name' => __( 'Basement', 'es' ),
				'singular_name' => __( 'Basement', 'es' ),
			),
			'show_in_rest' => (bool) ests( 'is_rest_support_enabled' ),
			'rest_base' => 'es_basements',
			'query_var' => empty( $_GET['es'] ),
		) );

		register_taxonomy( 'es_basement', 'properties', $args );

		$args = apply_filters( 'es_floor_covering_args', array(
			'labels' => array(
				'name' => __( 'Floor covering', 'es' ),
				'singular_name' => __( 'Floor covering', 'es' ),
			),
			'show_in_rest' => (bool) ests( 'is_rest_support_enabled' ),
			'rest_base' => 'es_floor_coverings',
			'query_var' => empty( $_GET['es'] ),
		) );

		register_taxonomy( 'es_floor_covering', 'properties', $args );

		$args = apply_filters( 'es_feature_args', array(
			'labels' => array(
				'name' => __( 'Features', 'es' ),
				'singular_name' => __( 'Feature', 'es' ),
			),
			'show_in_rest' => (bool) ests( 'is_rest_support_enabled' ),
			'rest_base' => 'es_features',
			'query_var' => empty( $_GET['es'] ),
		) );

		register_taxonomy( 'es_feature', 'properties', $args );

		$args = apply_filters( 'es_amenity_args', array(
			'labels' => array(
				'name' => __( 'Amenities', 'es' ),
				'singular_name' => __( 'Amenity', 'es' ),
			),
			'show_in_rest' => (bool) ests( 'is_rest_support_enabled' ),
			'rest_base' => 'es_amenities',
			'query_var' => empty( $_GET['es'] ),
		) );

		register_taxonomy( 'es_amenity', 'properties', $args );

        $args = apply_filters( 'es_neighborhood_taxonomy_args', array(
            'labels' => array(
                'name' => __( 'Neighborhoods', 'es' ),
                'singular_name' => __( 'Neighborhood', 'es' ),
            ),
            'rewrite' => array( 'slug' => ests( 'neighborhood_slug' ), 'with_front' => false ),
            'show_in_rest' => (bool) ests( 'is_rest_support_enabled' ),
            'rest_base' => 'es_neighborhoods',
            'query_var' => empty( $_GET['es'] ),
        ) );

        register_taxonomy( 'es_neighborhood', 'properties', $args );

        $args = apply_filters( 'es_tag_taxonomy_args', array(
            'labels' => array(
                'name' => __( 'Tags', 'es' ),
                'singular_name' => __( 'Tag', 'es' ),
            ),
            'rewrite' => array( 'slug' => ests( 'tag_slug' ), 'with_front' => false ),
            'show_in_rest' => (bool) ests( 'is_rest_support_enabled' ),
            'rest_base' => 'es_tags',
            'query_var' => empty( $_GET['es'] ),
        ) );

        register_taxonomy( 'es_tag', 'properties', $args );
	}
}

Es_Taxonomies::init();
