<?php

/**
 * Class Es_My_Listings_Shortcode.
 */
class Es_My_Listing_Shortcode extends Es_My_Entities_Shortcode {

	/**
	 * @return false|string
	 */
    public function get_content() {
	    ob_start();

	    $attr = $this->get_attributes();

	    if ( es_is_property_taxonomy() ) {
            global $wp_query;
		    $queried_object = get_queried_object();
		    $this->_attributes[ $queried_object->taxonomy ] = $queried_object->term_id;
			$this->_attributes['page_title'] = get_term( $queried_object->term_id )->name;
            if ( ! empty( $wp_query->get('posts_per_page') ) ) {
                $this->_attributes['posts_per_page'] = $wp_query->get('posts_per_page');
            }
	    }

	    $query = $this->get_query();
	    $query_args  = $this->get_query_args();
	    $map_query_args = $query_args;

        // Set template when load listings via ajax.
	    $template = $attr['_ajax_mode'] ? 'front/property/listings.php' : 'front/shortcodes/my-listings.php';

	    // Remove unused variables.
	    unset( $this->_attributes['action'],
		    $this->_attributes['_ajax_mode'],
		    $this->_attributes['hash'] );

		if ( empty( $attr['_ignore_coordinates'] ) && ! empty( $this->_attributes['map_show'] ) && $this->_attributes['map_show'] == 'all' ) {
			$map_query_args = $query_args;
			$map_query_args['posts_per_page'] = -1;
		}

	    $template_args = array(
            'query' => $query,
            'search_form' => empty( $attr['_ajax_mode'] ) ? $this->get_search_form_instance() : false,
            'args' => $this->get_attributes(),
            'hash' => es_encode( $this->get_attributes() ),
		    'coordinates' => empty( $attr['_ignore_coordinates'] ) ? es_properties_get_markers( $map_query_args ) : '',
		    'css_layout' => $this->get_listings_layout_class(),
		    'wrapper_class' => $this->get_wrapper_class(),
		    'wishlist_confirm' => $attr['wishlist_confirm'],
	    );

	    // Remove unused variables.
	    unset( $this->_attributes['search_type'],
		    $this->_attributes['search_form_selector'],
		    $this->_attributes['collapsed_fields'],
		    $this->_attributes['fields'],
		    $this->_attributes['main_fields'] );

	    $shortcode_name = static::get_shortcode_name();
	    $shortcode_name = $shortcode_name[0];

	    $template_args = apply_filters( $shortcode_name . '_template_args', $template_args, $this->_attributes, $this );
        es_load_template( $template, $template_args );

	    return ob_get_clean();
    }

	/**
	 * @return mixed|void
	 */
    public function get_wrapper_class() {
    	$classes = array( 'es-properties', 'js-es-properties', 'js-es-entities-wrap' );
    	$args = $this->get_attributes();
    	$layout = ! empty( $args['layout'] ) ? $args['layout'] : '';

    	if ( 'half_map' == $layout ) {
    	    $classes[] = 'es-properties--hfm';
    	}

    	if ( ! empty( $args['hfm_full_width'] ) ) {
    	    $classes[] = 'es-properties--hfm--full-width';
    	}

    	return apply_filters( 'es_my_listings_wrapper_classes', implode( ' ', $classes ), $classes, $this );
    }

	/**
	 * Return listings layout.
	 *
	 * @return mixed|string
	 */
    public function get_listings_layout_class() {
    	$attr = $this->get_attributes();
    	$layout = $attr['layout'] == 'half_map' ? es_get_active_grid_layout() : $attr['layout'];
        $layout_css_class = 'es-listings--list';

        for ( $i = 1; $i <= 6; $i++ ) {
            if ( in_array( $layout, array( "{$i}_col", "{$i}col", "grid-{$i}", "{$i}_cols", "{$i}cols" ) ) ) {
                $layout_css_class = "es-listings--grid-{$i}";
                break;
            }
        }

        $layout_css_class = $layout == 'grid' ? 'es-listings--grid-3' : $layout_css_class;
        $layout_css_class = $layout == 'list' ? 'es-listings--list' : $layout_css_class;

        return apply_filters( 'es_my_listings_layout_css_class', $layout_css_class, $layout );
    }

    /**
     * Return search form shortcode instance.
     *
     * @return mixed|void
     */
    public function get_search_form_instance() {
        if ( $this->_attributes['enable_search'] ) {
            $attributes = $this->_attributes;
            unset( $attributes['layout'] );

            $shortcode = es_get_shortcode_instance( 'es_search_form', $attributes );
        } else {
            $shortcode = null;
        }

	    $shortcode_name = static::get_shortcode_name();
	    $shortcode_name = $shortcode_name[0];

        return apply_filters( sprintf( '%s_search_form_shortcode_instance', $shortcode_name ), $shortcode, $this );
    }

    /**
     * @return mixed|void
     */
    public function get_query_args() {
        $page_num = ! empty( $_GET[ 'paged-' . $this->_attributes['loop_uid'] ] ) ? $_GET[ 'paged-' . $this->_attributes['loop_uid'] ] : $this->_attributes['page_num'];
        $page_num = intval( $page_num );

        if ( $sort = filter_input( INPUT_GET, 'sort-' . $this->_attributes['loop_uid'] ) ) {
            $this->_attributes['sort'] = $sort;
        }

        $args = array(
            'query' => array(
                'posts_per_page' => $this->_attributes['posts_per_page'],
                'paged' => $page_num,
            ),
            'fields' => $this->_attributes,
            'settings' => array(
	            'fields_delimiter' => $this->_attributes['fields_delimiter'],
	            'strict_address' => $this->_attributes['strict_address']
            ),
        );

        if ( ! empty( $this->_attributes['limit'] ) ) {
            unset( $args['query']['paged'] );
            $args['query']['no_found_rows'] = true;
            $args['query']['posts_per_page'] = $this->_attributes['limit'];
        }

        if ( ! empty( $this->_attributes['prop_id'] ) ) {
            $args['query']['post__in'] = array_map( 'trim', explode( ',', $this->_attributes['prop_id'] ) );
        }

        $query = apply_filters( 'es_properties_shortcode_query_args', es_get_properties_query_args( $args ), $this->_attributes, $this );
        
	    $shortcode_name = static::get_shortcode_name();
	    $shortcode_name = $shortcode_name[0];

        return apply_filters( sprintf( "es_%s_query_args", $shortcode_name ),
            $query, $this->_attributes, $this );
    }

    /**
     * Return shortcode default attributes.
     *
     * @return array|void
     */
    public function get_default_attributes() {
		$shortcode_name = static::get_shortcode_name();
		$shortcode_name = $shortcode_name[0];

        return apply_filters( sprintf( '%s_default_attributes', $shortcode_name ), array(
            'layout' => ests( 'listings_layout' ),
            'posts_per_page' => ests( 'properties_per_page' ),
            'disable_navbar' => false,
            'show_sort' => ests( 'is_properties_sorting_enabled' ),
            'show_total' => true,
            'show_page_title' => true,
            'show_layouts' => ests( 'is_layout_switcher_enabled' ),
            'sort' => ests( 'properties_default_sorting_option' ),
            'limit' => 0,
            'page_num' => null,
            'loop_uid' => '',
            'page_title' => get_the_title(),
            'ignore_search' => false,
            'search_form_selector' => ests( 'is_update_search_results_enabled') ? '.js-es-search:not(.es-search--ignore-ajax)' : null,
            'enable_search' => false,
            'search_type' => 'simple',
            'enable_ajax' => true,
            'view_all_link_name' => __( 'View all', 'es' ),
            'view_all_page_id' => null,
            'disable_pagination' => false,
            'wishlist_confirm' => false,
            'hfm_full_width' => 1,
	        '_ajax_mode' => false,
	        '_ignore_coordinates' => false,
	        'fields_delimiter' => ',',
	        'strict_address' => false,
	        'map_show' => false,
            'authors' => false, 
        ) );
    }

    /**
     * Return shortcode name.
     *
     * @return Exception|array
     */
    public static function get_shortcode_name() {
        return array( 'es_my_listing', 'listings' );
    }
}
