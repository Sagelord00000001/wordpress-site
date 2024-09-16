<?php

add_action( 'wp_ajax_es_save_field', 'es_ajax_save_field' );

/**
 * Save field via ajax.
 *
 * @return void
 */
function es_ajax_save_field() {
	if ( check_ajax_referer( 'es_save_field', 'save_field_nonce', false ) ) {
		if ( current_user_can( 'manage_options' ) ) {
			$field = sanitize_key( filter_input( INPUT_POST, 'field' ) );
			$value = es_clean( filter_input( INPUT_POST, 'value' ) );
			$container = filter_input( INPUT_POST, 'container' );

			if ( 'estatik-settings' == $container ) {
				ests_save_option( $field, $value );
			}
		}
	}

	wp_die();
}

add_action( 'wp_ajax_es_get_terms_creator', 'es_ajax_get_terms_creator' );

/**
 * Send terms creator markup via ajax.
 *
 * @return void
 */
function es_ajax_get_terms_creator() {

	if ( check_ajax_referer( 'es_get_terms_creator', 'nonce', false ) ) {
		if ( current_user_can( 'manage_options' ) ) {
			$taxonomy = sanitize_key( filter_input( INPUT_GET, 'taxonomy' ) );
			$type = es_clean( filter_input( INPUT_GET, 'type' ) );

			if ( $creator = es_get_terms_creator_factory( $taxonomy, $type ) ) {
				$creator->render();
			}
		}
	}

	wp_die();
}

/**
 * Return dependencies location fields values.
 *
 * @return void
 */
function es_ajax_get_locations() {
    if ( check_ajax_referer( 'es_get_locations', 'nonce', false ) ) {
        $parent_id = es_clean( filter_input( INPUT_GET, 'dependency_id' ) );
        $types = es_clean( filter_input( INPUT_GET, 'types', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY ) );
        $address_components = es_get_address_components_container();
        $components = $address_components::get_locations( $types, $parent_id );

        wp_die( json_encode( apply_filters( 'es_ajax_get_locations', $components, $parent_id, $types ) ) );
    }
}
add_action( 'wp_ajax_es_get_locations', 'es_ajax_get_locations' );
add_action( 'wp_ajax_nopriv_es_get_locations', 'es_ajax_get_locations' );

/**
 * Add / Delete item to / from wishlist via ajax.
 *
 * @return string
 */
function es_ajax_wishlist_action() {
    $post_id = es_clean( filter_input( INPUT_POST, 'post_id' ) );
    $entity = es_clean( filter_input( INPUT_POST, 'entity' ) );

    if ( $post_id ) {
        $wishlist = es_get_wishlist_instance( $entity );

        if ( $wishlist->has( $post_id ) ) {
            $wishlist->remove( $post_id );
            wp_die( json_encode( es_success_ajax_response( __( 'Item successfully removed from wishlist', 'es' ) ) ) );
        } else {
            $wishlist->add( $post_id );
            wp_die( json_encode( es_success_ajax_response( __( 'Item successfully added to wishlist', 'es' ) ) ) );
        }
    } else {
        wp_die( json_encode( es_error_ajax_response( __( 'Something wrong. Please contact the support.', 'es' ) ) ) );
    }
}
add_action( 'wp_ajax_es_wishlist_action', 'es_ajax_wishlist_action' );
add_action( 'wp_ajax_nopriv_es_wishlist_action', 'es_ajax_wishlist_action' );

/**
 * Return single property content-archive item via ajax.
 *
 * @return void
 */
function es_ajax_get_property_item() {
    $post_id = intval( es_post( 'post_id' ) );
    $response = array( 'status' => 'error' );

    if ( $post_id && get_post_status( $post_id ) == 'publish' && get_post_type( $post_id ) == 'properties' ) {
        $query = new WP_Query( array(
            'post_type' => 'properties',
            'p' => $post_id
        ) );

	    // Generate back to search link.
	    if ( ! empty( $_SERVER['HTTP_REFERER'] ) ) {
		    $GLOBALS['search_url'] = $_SERVER['HTTP_REFERER'];
	    }

        if ( $query->have_posts() ) {
            ob_start();
            echo '<div class="es-listings es-listings--hfm es-listings--grid-1">';
            while ( $query->have_posts() ) {
                $query->the_post();
                es_load_template( 'front/property/content-archive.php', array(
                    'ignore_wrapper' => true,
	                'target_blank' => 'target="_blank"',
                ) );
            }
            echo "</div>";
            wp_reset_postdata();
            $content = ob_get_clean();

            $response = array(
                'status' => 'success',
                'content' => $content,
            );
        }
    }

    wp_die( json_encode( apply_filters( 'es_ajax_get_property_item', $response, $post_id ) ) );
}
add_action( 'wp_ajax_es_get_property_item', 'es_ajax_get_property_item' );
add_action( 'wp_ajax_nopriv_es_get_property_item', 'es_ajax_get_property_item' );

if ( ! function_exists( 'es_ajax_search_address_components' ) ) {

    /**
     * Search address autocomplete ajax handler.
     *
     * @return void
     */
    function es_ajax_search_address_components() {
        $query = es_clean( filter_input( INPUT_GET, 'q' ) );
        if ( strlen( $query ) > 2 ) {
            $results_addresses_components = get_terms( array(
                'taxonomy' => 'es_location',
                'name__like' => $query,
                'fields' => 'id=>name',
                'number' => apply_filters( 'es_address_autocomplete_terms_number', 5 ),
            ) );

			$posts = get_posts( array(
				'posts_per_page' => 5,
				'post_status' => 'publish',
				'post_type' => 'properties',
				'meta_query' => array(
					array(
						'key' => 'es_property_address',
						'value' => $query,
						'compare' => 'LIKE',
					),
				),
			) );

            ob_start();
            es_load_template( 'front/shortcodes/search/partials/autocomplete.php', array(
                'addresses' => $results_addresses_components,
	            'posts' => $posts,
            ) );

            $response = array(
                'status' => 'success',
                'content' => ob_get_clean()
            );

            $response = apply_filters( 'es_address_autocomplete_response', json_encode( $response ) );

            wp_die( $response );
        }
    }
}
add_action( 'wp_ajax_es_search_address_components', 'es_ajax_search_address_components' );
add_action( 'wp_ajax_nopriv_es_search_address_components', 'es_ajax_search_address_components' );

/**
 * Save search actions.
 *
 * @return void
 */
function es_ajax_save_search() {
	$response = array(
		'status' => 'error',
		'message' => __( 'Invalid security nonce. Please, reload the page and try again.', 'es' ),
	);

	if ( check_ajax_referer( 'es_save_search', 'nonce' ) ) {
		$data = es_array_filter_recursive( es_clean( $_POST ), null, true );
		unset( $data['action'], $data['nonce'] );

		if ( ! empty( $data ) && is_array( $data ) ) {
			$data = apply_filters( 'es_save_search_saving_fields', $data );
			$data['update_type'] = 'none';

			$post_id = wp_insert_post( array(
				'post_type' => 'saved_search',
				'post_status' => 'private',
				'post_title' => '',
				'post_author' => get_current_user_id(),
			), true );

			if ( ! is_wp_error( $post_id ) ) {
				$saved_search = es_get_saved_search( $post_id );
				$saved_search->save_fields( $data );

				$response = es_simple_ajax_response( __( 'Search saved', 'es' ), 'success' );
			} else {
				$response = es_simple_ajax_response( $post_id->get_error_message(), 'error' );
			}
		} else {
			$response = es_simple_ajax_response( __( 'Search params are empty. Please fill search fields.', 'es' ), 'error' );
		}
	}

	wp_die( json_encode( $response ) );
}
add_action( 'wp_ajax_es_save_search', 'es_ajax_save_search' );

/**
 * Remove saved search via ajax.
 *
 * @return void
 */
function es_ajax_remove_saved_search() {
    if ( wp_verify_nonce( es_get_nonce( 'nonce' ), 'es_remove_saved_search' ) ) {
        $post_id = es_decode( es_clean( filter_input(INPUT_POST, 'hash' ) ) );
        if ( is_array( $post_id ) && ! empty( $post_id[0] ) ) {
        	$post_id = $post_id[0];
        }
        $saved_search = get_post( $post_id );
        if ( $post_id && $saved_search->post_author == get_current_user_id() ) {
            $saved_search = es_get_saved_search( $post_id );
            $saved_search->delete( true );
            $response = es_success_ajax_response( __( 'Successfully deleted.', 'es' ) );
        } else {
            $response = es_error_ajax_response( __( 'Invalid saved search.', 'es' ) );
        }
    } else {
        $response = es_error_ajax_response( __( 'Invalid security nonce. Please, reload the page and try again.', 'es' ) );
    }

    wp_die( json_encode( $response ) );
}
add_action( 'wp_ajax_es_remove_saved_search', 'es_ajax_remove_saved_search' );

/**
 * Return listings via ajax response.
 *
 * @return void
 */
function es_ajax_get_listings() {
	$attributes = es_get( 'hash', false ) ? es_get( 'hash', false ) : es_post( 'hash', false );
    $attributes = es_decode( $attributes );
    $need_reload_map = es_get( 'reload_map' ) ? es_get( 'reload_map' ) : es_post( 'reload_map' );
    $attributes['_ajax_mode'] = true;
    $attributes['_ignore_coordinates'] = ! $need_reload_map;

    // Generate back to search link.
	if ( ! empty( $_SERVER['HTTP_REFERER'] ) ) {
		$GLOBALS['search_url'] = $_SERVER['HTTP_REFERER'];
	}

    /** @var Es_My_Listing_Shortcode $shortcode */
    $shortcode = es_get_shortcode_instance( 'es_my_listing', $attributes );

    $response = array(
        'status' => 'success',
        'message' => $shortcode->get_content(),
    );

    $query_args = $shortcode->get_query_args();

    if ( $need_reload_map ) {
		if ( ! empty( $attributes['map_show'] ) && $attributes['map_show'] == 'all' ) {
			$query_args['posts_per_page'] = -1;
		}
	    $response['coordinates'] = es_properties_get_markers( $query_args );
    }

    $response['loop_uid'] = $attributes['loop_uid'];
    $response['reload_map'] = $need_reload_map;

    wp_die( json_encode( $response ) );
}
add_action( 'wp_ajax_get_listings', 'es_ajax_get_listings' );
add_action( 'wp_ajax_nopriv_get_listings', 'es_ajax_get_listings' );
