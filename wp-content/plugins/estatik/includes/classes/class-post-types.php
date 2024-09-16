<?php

/**
 * Class Es_Post_Types.
 *
 * Register custom plugin post types.
 */
class Es_Post_Types {

	/**
	 * @return void
	 */
	public static function init() {
		add_action( 'init', array( 'Es_Post_Types', 'register_post_types' ) );
	}

	/**
	 * Register plugin custom post types.
	 *
	 * @return void
	 */
	public static function register_post_types() {
		$args = array(
			'label' => __( ests( 'post_type_name' ), 'es' ),
			'labels' => array(
				'name' => __( ests( 'post_type_name' ), 'es' ),
			),
			'public' => true,
			'show_in_menu' => false,
			'has_archive' => ests( 'is_properties_archive_enabled' ),
			'supports' => array( 'title', 'editor', 'author', 'excerpt', 'thumbnail', 'elementor' ),
			'rewrite' => array(
				'slug' => ests( 'property_slug' ),
				'with_front' => false,
				'pages'      => true,
				'feeds'      => true,
				'ep_mask'    => EP_PERMALINK,
			),
            'map_meta_cap' => true,
            'capability_type'     => array( 'es_property', 'es_properties' ),
            'capabilities' => array(
                'create_posts' => 'create_es_properties',
            ),
			'show_in_rest' => (bool) ests( 'is_rest_support_enabled' ),
			'rest_base' => 'properties',
		);

		register_post_type( 'properties', $args );

        $args = array(
            'public' => false,
            'show_in_menu' => false,
            'has_archive' => false,
            'supports' => array( 'title', 'author' ),
            'show_in_rest' => (bool) ests( 'is_rest_support_enabled' ),
            'rest_base' => 'saved_search',
        );

        register_post_type( 'saved_search', $args );
	}
}

Es_Post_Types::init();
