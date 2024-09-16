<?php

if ( ! function_exists( 'es_the_property_gallery' ) ) {

    /**
     * Display single property gallery \ slider.
     *
     * @param int $post_id
     */
    function es_the_property_gallery( $post_id = 0 ) {
        $property = es_get_the_property( $post_id );

        es_load_template( '/front/property/gallery/gallery.php', array(
            'images' => $property->gallery,
        ) );
    }
}

if ( ! function_exists( 'es_the_mobile_slider' ) ) {

	/**
	 * @param int $post_id
	 */
    function es_the_mobile_slider( $post_id = 0 ) {
	    $property = es_get_the_property( $post_id );

	    es_load_template( '/front/property/gallery/mobile.php', array(
		    'images' => $property->gallery,
	    ) );
    }
}

if ( ! function_exists( 'es_the_property_slider' ) ) {

    /**
     * Display single property gallery \ slider.
     *
     * @param int $post_id
     */
    function es_the_property_slider( $post_id = 0 ) {
        $property = es_get_the_property( $post_id );

        es_load_template( '/front/property/gallery/slider.php', array(
            'images' => $property->gallery,
        ) );
    }
}

if ( ! function_exists( 'es_the_property_section_content' ) ) {

    /**
     * Return section content.
     *
     * @param $section
     * @param int $post_id
     *
     * @return mixed|void
     */
    function es_the_property_section_content( $section, $post_id = 0 ) {
        $property = es_get_the_property( $post_id );
        $content = null;

        if ( ! empty( $section['machine_name'] ) ) {

            switch ( $section['machine_name'] ) {
                case 'location':
                    if ( $property->latitude && $property->longitude && ests( 'is_single_listing_map_enabled' ) && ests( 'google_api_key' ) ) {
                        $content = "<div class='es-property-map js-es-property-map'
                            data-latitude='" . esc_attr( $property->latitude ) . "'
                            data-longitude='" . esc_attr( $property->longitude ) . "'></div>";
                    }
                    $content .= es_get_the_section_fields_html( $section, $post_id );
                    break;

                case 'request_form':
                    $config = array(
	                    'layout' => 'section',
	                    'title' => '',
	                    'subject' => ests( 'request_property_info_email_subject' )
                    );
                    if ( ! empty( $section['options']['background_color'] ) ) {
                        $config['background'] = $section['options']['background_color'];
                    }
	                if ( ! empty( $section['options']['text_color'] ) ) {
		                $config['color'] = $section['options']['text_color'];
	                }

	                $config['message'] = isset( $section['options']['message'] ) ?
                        $section['options']['message'] : '';

                    $shortcode = es_get_shortcode_instance( 'es_request_form', $config );
                    if ( $shortcode instanceof Es_Shortcode ) {
                        $content = $shortcode->get_content();
                    }
                    $content .= es_get_the_section_fields_html( $section, $post_id );
                    break;

                default:
                    $content = es_get_the_section_fields_html( $section, $post_id );
                    break;
            }
        }

        return apply_filters( 'es_the_property_section_content', $content, $section, $post_id );
    }
}

if ( ! function_exists( 'es_the_entity_section' ) ) {

    /**
     * Return generated section.
     *
     * @param $section
     * @param int $post_id
     */
    function es_the_entity_section( $section, $post_id = 0 ) {
        $entity = es_get_entity_by_id( $post_id );
        $entity_name = $entity::get_entity_name();
        if ( es_is_visible( $section, $entity_name, 'section' ) ) {
            $section[ $entity_name . '_id' ] = $post_id;
	        $section['content'] = '';

            if ( ! empty( $section['render_action'] ) ) {
                ob_start();
                do_action( $section['render_action'], $section, $post_id );
                $section['content'] = ob_get_clean();
            } else if ( function_exists( 'es_the_' . $entity_name . '_section_content' ) ) {
	            $section['content'] = call_user_func( 'es_the_' . $entity_name . '_section_content', $section, $post_id );
            }

	        es_load_template( 'front/'. $entity_name .'/section.php', $section );
        }
    }
}
add_action( 'es_single_property_section', 'es_the_entity_section' );

if ( ! function_exists( 'es_the_property_badges' ) ) {

    /**
     * @return void
     */
    function es_the_property_badges() {
        if ( ests( 'is_labels_enabled' ) )
            es_load_template( 'front/property/partials/property-badges.php' );
    }
}
add_action( 'es_property_badges', 'es_the_property_badges' );

if ( ! function_exists( 'es_the_property_meta' ) ) {

    /**
     * @param array $config
     * @return void
     */
    function es_the_property_meta( $config = array() ) {
        es_load_template( 'front/property/partials/property-meta.php', $config );
    }
}
add_action( 'es_property_meta', 'es_the_property_meta' );

if ( ! function_exists( 'es_the_property_control' ) ) {

	/**
	 * @param array $args
	 *
	 * @return void
	 */
    function es_the_property_control( $args = array() ) {
        $args = es_parse_args( $args, array(
            'show_sharing' => true,
            'is_full' => true,
            'wishlist_confirm' => false,
            'entity' => 'property',
            'entity_plural' => 'properties',
        ) );
        extract( $args );
        include es_locate_template( 'front/partials/entity-control.php' );
    }
}
add_action( 'es_property_control', 'es_the_property_control', 10, 2 );

if ( ! function_exists( 'es_the_single_property_layout' ) ) {

    /**
     * @param $post_id
     */
    function es_the_single_property_layout( $post_id = 0 ) {
        $layout = apply_filters( 'es_single_property_current_layout', ests( 'single_layout' ), $post_id );

        es_load_template( sprintf( 'front/property/layout/%s.php', $layout ), array(
            'property' => es_get_the_property( $post_id )
        ) );
    }
}
add_action( 'es_single_property_layout', 'es_the_single_property_layout' );

if ( ! function_exists( 'es_the_property_share_popup' ) ) {

    /**
     * Render shares popup.
     *
     * @return void
     */
	function es_the_property_share_popup( $popup_id = 'es-share-popup' ) {
		es_load_template( 'front/popup/share.php', array(
			'entity_plural' => 'properties',
			'title' => __( 'Share this home', 'es' ),
			'popup_id' => $popup_id,
		) );
	}
}
add_action( 'es_single_property_layout', 'es_the_property_share_popup', 10, 0 );

if ( ! function_exists( 'es_sort_dropdown' ) ) {

    /**
     * Display sorting dropdown.
     *
     * @param $sort
     * @return void
     */
    function es_sort_dropdown( $sort ) {
        $sorting = ests( 'properties_sorting_options' );

        if ( ! empty( $sorting ) ) : ?>
            <div class="es-form">
                <?php es_framework_field_render( 'sort', array(
                    'type' => 'select',
                    'value' => $sort,
                    'options' => ests_selected( 'properties_sorting_options' ),
                    'label' => __( 'Sort by', 'es' ),
                    'attributes' => array(
                        'class' => 'js-es-sort'
                    )
                ) ); ?>
            </div>
        <?php endif;
    }
}
add_action( 'es_sort_dropdown', 'es_sort_dropdown' );

if ( ! function_exists( 'es_layouts' ) ) {

    /**
     * Display layouts buttons on listings page.
     *
     * @return void
     */
    function es_layouts( $args ) {
        es_load_template( 'front/property/partials/layout-buttons.php', $args );
    }
}
add_action( 'es_layouts', 'es_layouts' );

if ( ! function_exists( 'es_listings_navbar' ) ) {

	/**
	 * Display properties navbar.
	 *
	 * @param $args
	 */
    function es_listings_navbar( $args ) {
        $query = ! empty( $args['query'] ) ? $args['query'] : null;
	    if ( $query && $query->have_posts() && empty( $args['disable_navbar'] ) && ( ! empty( $args['show_sort'] ) || ! empty( $args['show_total'] ) || ! empty( $args['show_layouts'] ) ) ) {
		    es_load_template( 'front/property/partials/properties-navbar.php', $args );
        }
    }
}
add_action( 'es_before_listings', 'es_listings_navbar', 10 );

if ( ! function_exists( 'es_the_pagination' ) ) {

    /**
     * @param $wp_query WP_Query
     * @param array $args
     *
     * @return array|void
     */
    function es_the_pagination( $wp_query, $args = array() ) {
        global $wp_rewrite;
        $page_num = null;

        if ( $loop_uid = $wp_query->get( 'loop_uid' ) ) {
            $format = '?paged-' . $loop_uid . '=%#%';
            $page_num = filter_input( INPUT_GET, 'paged-' . $loop_uid );
        } else {
            $format = '?paged=%#%';
        }

        if ( is_tax() ) {
            $current_taxonomy = get_queried_object();
            $taxonomy_name = $current_taxonomy->taxonomy;

            if ($taxonomy_name == 'es_location') {
                $format = '?paged-=%#%';
            }
        }

        if ( ! $page_num ) {
            if ( $wp_query->get( 'paged' ) ) {
                $page_num = $wp_query->get( 'paged' );
            } else if ( get_query_var( 'paged' ) ) {
                $page_num = get_query_var( 'paged' );
            } elseif ( get_query_var( 'page' ) ) {
                $page_num = get_query_var( 'page' );
            } else {
                $page_num = 1;
            }
        }

        $limit = $wp_query->get( 'posts_per_page' );
        $page_num = intval( $page_num );

        $args = es_parse_args( $args, array(
            'format'  => $format,
            'show_all'           => false,
            'end_size'           => 1,
            'prev_next' => true,
            'prev_text' => '<span class="es-icon es-icon_chevron-left"></span>',
            'next_text' => '<span class="es-icon es-icon_chevron-right"></span>',
            'type' => 'list',
            'mid_size'           => 2,
            'screen_reader_text' => ' ',
            'total'              => $wp_query->max_num_pages,
            'current' => $page_num,
        ) );

        // Setting up default values based on the current URL.
        $pagenum_link = html_entity_decode( get_pagenum_link() );
        $url_parts    = explode( '?', $pagenum_link );

        // Get max pages and current page out of the current query, if available.
        $total   = isset( $wp_query->max_num_pages ) ? $wp_query->max_num_pages : 1;
        $current = get_query_var( 'paged' ) ? intval( get_query_var( 'paged' ) ) : 1;

        // Append the format placeholder to the base URL.
        $pagenum_link = trailingslashit( $url_parts[0] ) . '%_%';

        // URL base depends on permalink settings.
        $format  = $wp_rewrite->using_index_permalinks() && ! strpos( $pagenum_link, 'index.php' ) ? 'index.php/' : '';
        $format .= $wp_rewrite->using_permalinks() ? user_trailingslashit( $wp_rewrite->pagination_base . '/%#%', 'paged' ) : '?paged=%#%';

        $defaults = array(
            'base'               => $pagenum_link, // http://example.com/all_posts.php%_% : %_% is replaced by format (below)
            'format'             => $format, // ?page=%#% : %#% is replaced by the page number
            'total'              => $total,
            'current'            => $current,
            'aria_current'       => 'page',
            'show_all'           => false,
            'prev_next'          => true,
            'prev_text'          => __( '&laquo; Previous' ),
            'next_text'          => __( 'Next &raquo;' ),
            'end_size'           => 1,
            'mid_size'           => 2,
            'type'               => 'plain',
            'add_args'           => array(), // array of query args to add
            'add_fragment'       => '',
            'before_page_number' => '',
            'after_page_number'  => '',
        );

        $args = wp_parse_args( $args, $defaults );

        if ( ! is_array( $args['add_args'] ) ) {
            $args['add_args'] = array();
        }

        // Merge additional query vars found in the original URL into 'add_args' array.
        if ( isset( $url_parts[1] ) ) {
            // Find the format argument.
            $format       = explode( '?', str_replace( '%_%', $args['format'], $args['base'] ) );
            $format_query = isset( $format[1] ) ? $format[1] : '';
            wp_parse_str( $format_query, $format_args );

            // Find the query args of the requested URL.
            wp_parse_str( $url_parts[1], $url_query_args );

            // Remove the format argument from the array of query arguments, to avoid overwriting custom format.
            foreach ( $format_args as $format_arg => $format_arg_value ) {
                unset( $url_query_args[ $format_arg ] );
            }

            $args['add_args'] = array_merge( $args['add_args'], urlencode_deep( $url_query_args ) );
        }

        // Who knows what else people pass in $args
        $total = (int) $args['total'];
        if ( $total < 2 ) {
            return;
        }
        $current  = (int) $args['current'];
        $end_size = (int) $args['end_size']; // Out of bounds?  Make it the default.
        if ( $end_size < 1 ) {
            $end_size = 1;
        }
        $mid_size = (int) $args['mid_size'];
        if ( $mid_size < 0 ) {
            $mid_size = 2;
        }

        $add_args   = $args['add_args'];
        $r          = '';
        $page_links = array();
        $dots       = false;

        if ( $args['prev_next'] && $current && 1 < $current ) :
            $link = str_replace( '%_%', 2 == $current ? '' : $args['format'], $args['base'] );
            $link = str_replace( '%#%', $current - 1, $link );
            if ( $add_args ) {
                $link = add_query_arg( $add_args, $link );
            }
            $link .= $args['add_fragment'];

            $page_links[] = sprintf(
                '<a class="prev page-numbers" data-page-number="%s" href="%s"><span class="page-numbers__num">%s</span></a>',
                /**
                 * Filters the paginated links for the given archive pages.
                 *
                 * @since 3.0.0
                 *
                 * @param string $link The paginated link URL.
                 */
                $current - 1, esc_url( apply_filters( 'paginate_links', $link ) ),
                $args['prev_text']
            );
        endif;

        for ( $n = 1; $n <= $total; $n++ ) :
            if ( $n == $current ) :
                $page_links[] = sprintf(
                    '<span aria-current="%s" class="page-numbers current">%s</span>',
                    esc_attr( $args['aria_current'] ),
                    $args['before_page_number'] . number_format_i18n( $n ) . $args['after_page_number']
                );

                $dots = true;
            else :
                if ( $args['show_all'] || ( $n <= $end_size || ( $current && $n >= $current - $mid_size && $n <= $current + $mid_size ) || $n > $total - $end_size ) ) :
                    $link = str_replace( '%_%', 1 == $n ? '' : $args['format'], $args['base'] );
                    $link = str_replace( '%#%', $n, $link );
                    if ( $add_args ) {
                        $link = add_query_arg( $add_args, $link );
                    }
                    $link .= $args['add_fragment'];

                    $page_links[] = sprintf(
                        '<a class="page-numbers" data-page-number="%s" href="%s"><span class="page-numbers__num">%s</span></a>',
                        /** This filter is documented in wp-includes/general-template.php */
                        $n, esc_url( apply_filters( 'paginate_links', $link ) ),
                        $args['before_page_number'] . number_format_i18n( $n ) . $args['after_page_number']
                    );

                    $dots = true;
                elseif ( $dots && ! $args['show_all'] ) :
                    $page_links[] = '<span class="page-numbers dots">' . __( '&hellip;' ) . '</span>';

                    $dots = false;
                endif;
            endif;
        endfor;

        if ( $args['prev_next'] && $current && $current < $total ) :
            $link = str_replace( '%_%', $args['format'], $args['base'] );
            $link = str_replace( '%#%', $current + 1, $link );
            if ( $add_args ) {
                $link = add_query_arg( $add_args, $link );
            }
            $link .= $args['add_fragment'];

            $page_links[] = sprintf(
                '<a class="next page-numbers" data-page-number="%s" href="%s"><span class="page-numbers__num">%s</span></a>',
                /** This filter is documented in wp-includes/general-template.php */
                $current + 1, esc_url( apply_filters( 'paginate_links', $link ) ),
                $args['next_text']
            );
        endif;

        switch ( $args['type'] ) {
            case 'array':
                return $page_links;

            case 'list':
                $r .= "<ul class='page-numbers'>\n\t<li>";
                $r .= join( "</li>\n\t<li>", $page_links );
                $r .= "</li>\n</ul>\n";
                break;

            default:
                $r = join( "\n", $page_links );
                break;
        }

        if ( $wp_query->max_num_pages > 1 ) {
            echo "<div class='es-pagination js-es-pagination content-font'>";
            echo $r;
            $limit_start = ( $page_num * $limit ) - $limit + 1;
            $limit_end = $limit * $page_num;
            $limit_end = $wp_query->found_posts > $limit_end ? $limit_end : $wp_query->found_posts;
            echo "<span class='es-navigation'>";
	        /* translators: %1$s: limit start, %2$s: limit end, %3$s: found posts. */
            printf( __( '%1$s - %2$s of %3$s properties', 'es' ), $limit_start, $limit_end, $wp_query->found_posts );
            echo "</span></div>";
        }
    }
}

if ( ! function_exists( 'es_get_properties_query_args' ) ) {

    /**
     * Search properties method.
     *
     * @param array $args .
     *
     * @return mixed|void
     */
    function es_get_properties_query_args( $args = array() ) {
        $property = es_get_property();
        $args = apply_filters( 'es_get_properties_atts', es_parse_args( $args, array(
            'query' => array(
                'post_type' => 'properties',
                'posts_per_page' => 10,
                'post_status' => 'publish',
            ),
            'fields' => array(),
            'settings' => array(
                'fields_delimiter' => ',',
                'strict_address' => false,
            ),
        ) ) );

        $query_args = $args['query'];
        $meta_query = array();
        $tax_query = array();

	    $sort = ! empty( $args['fields']['sort'] ) ? $args['fields']['sort'] : ests( 'properties_default_sorting_option' );

        if ( $sort ) {

            $meta_query = apply_filters('es_add_meta_query_sort', [],  $sort);
            $query_args = apply_filters('es_add_query_args_sort', $query_args,  $sort);
            
		    switch ( $sort ) {
			    case 'newest':
				    $query_args['orderby'] = 'publish_date';
				    $query_args['order'] = 'DESC';
				    break;

			    case 'oldest':
				    $query_args['orderby'] = 'publish_date';
				    $query_args['order'] = 'ASC';
				    break;

			    case 'lowest_price':

                    $meta_query['price_exists'] = array(
                        'relation' => 'OR',
                        array(
                            'key' => 'es_property_price',
                            'compare' => 'EXISTS',
                        ),
                        array(
                            'key' => 'es_property_price',
                            'compare' => 'NOT EXISTS',
                        ),
                    );

                    $meta_query['call_for_price_exists'] = array(
                        'relation' => 'OR',
                        array(
                            'key' => 'es_property_call_for_price',
                            'compare' => 'NOT EXISTS',
                        ),
                        array(
                            'key' => 'es_property_call_for_price',
                            'compare' => 'EXISTS',
                        ),
                    );
                    $query_args['orderby'] = array(
					    'call_for_price_exists' => 'ASC',
					    'meta_value_num' => 'ASC',
                        'meta_value' => 'DESC',
				    );

				    break;

			    case 'highest_price':

                    $meta_query['price_exists'] = array(
                        'relation' => 'OR',
                        array(
                            'key' => 'es_property_price',
                            'compare' => 'NOT EXISTS',
                        ),
                        array(
                            'key' => 'es_property_price',
                            'compare' => 'EXISTS',
                        ),
                    );

                    $meta_query['call_for_price_exists'] = array(
                        'relation' => 'OR',
                        array(
                            'key' => 'es_property_call_for_price',
                            'compare' => 'NOT EXISTS',
                        ),
                        array(
                            'key' => 'es_property_call_for_price',
                            'compare' => 'EXISTS',
                        ),
                    );
                    $query_args['orderby'] = array(
					    'call_for_price_exists' => 'ASC',
					    'meta_value_num' => 'DESC',
                        'meta_value' => 'DESC',
				    );

				    break;

			    case 'largest_sq_ft':
                    $meta_query['property_area'] = array(
                        'relation' => 'OR',
                        array(
                            'key' => 'es_property_area',
                            'compare' => 'NOT EXISTS',
                        ),
                        array(
                            'key' => 'es_property_area',
                            'compare' => 'EXISTS',
                        ),
                    );
                    $query_args['orderby'] = array(
                        'meta_value_num' => 'DESC',
                        'meta_value' => 'ASC',
                    );

                    $query_args['order'] = 'DESC';
				    break;

			    case 'bedrooms':
			    case 'bathrooms':
                    $meta_query['exists_' . $sort] = array(
                        'relation' => 'OR',
                        array(
                            'key' => 'es_property_' . $sort,
                            'compare' => 'NOT EXISTS',
                        ),
                        array(
                            'key' => 'es_property_' . $sort,
                            'compare' => 'EXISTS',
                        ),
                    );
                    $query_args['orderby'] = array(
                        'meta_value_num' => 'DESC',
                        'meta_value' => 'ASC',
                    );

                    $query_args['order'] = 'DESC';

				    break;

			    default:
				    if ( term_exists( $sort, 'es_label' ) ) {

                        $query_args['meta_key'] = 'es_property_sort_' . $sort;
                        $query_args['orderby'] = array(
                            'meta_value_num' => 'DESC', // first posts with meta field = 1
                            'meta_value' => 'ASC' // Then posts with meta field = 0
                        );
                        $query_args['order'] = 'DESC';

					    break;
				    }
		    }
	    }

        $range_fields = apply_filters( 'es_get_properties_range_fields', array(
            'bedrooms', 'bathrooms', 'price', 'area', 'lot_size', 'floors', 'half_baths'
        ) );

        foreach ( $range_fields as $range_field ) {
            if ( ! es_property_get_field_info( $range_field ) ) continue;

	        if ( isset( $args['fields'][ 'from_' . $range_field ] ) && strlen( $args['fields'][ 'from_' . $range_field ] ) ) {
		        $meta_query[ $range_field ] = array(
			        'key' => $property->get_entity_prefix() . $range_field,
			        'value' => $args['fields'][ 'from_' . $range_field ],
			        'compare' => '>=',
			        'type' => 'NUMERIC',
		        );
	        }

            if ( isset( $args['fields'][ 'min_' . $range_field ] ) && strlen( $args['fields'][ 'min_' . $range_field ] ) && empty( $args['fields'][ 'max_' . $range_field ] ) ) {
                $meta_query[ $range_field ] = array(
                    'key' => $property->get_entity_prefix() . $range_field,
                    'value' => $args['fields'][ 'min_' . $range_field ],
                    'compare' => '>=',
                    'type' => 'NUMERIC',
                );
            }

            if ( empty( $args['fields'][ 'min_' . $range_field ] ) && isset( $args['fields'][ 'max_' . $range_field ] ) && strlen( $args['fields'][ 'max_' . $range_field ] ) ) {
                $meta_query[ $range_field ] = array(
                    'key' => $property->get_entity_prefix() . $range_field,
                    'value' => $args['fields'][ 'max_' . $range_field ],
                    'compare' => '<=',
                    'type' => 'NUMERIC',
                );
            }

            if ( ! empty( $args['fields'][ 'min_' . $range_field ] ) && ! empty( $args['fields'][ 'max_' . $range_field ] ) ) {
                $meta_query[ $range_field ] = array(
                    'key' => $property->get_entity_prefix() . $range_field,
                    'value' => array( $args['fields'][ 'min_' . $range_field ], $args['fields'][ 'max_' . $range_field ] ),
                    'compare' => 'BETWEEN',
                    'type' => 'NUMERIC',
                );
            }
        }

        if ( ! empty( $args['fields'] ) ) {
            foreach ( $args['fields'] as $field => $value ) {
                if ( 'id' == $field ) continue;
                $range_field = str_replace( 'min_', '', $field );
                $range_field = str_replace( 'max_', '', $range_field );
                $range_field = str_replace( 'from_', '', $range_field );
                if ( in_array( $range_field, $range_fields ) || in_array( $range_field, $range_fields ) ) continue;

                if ( 'authors' == $field ) {
                    $query_args['author'] = $value;
                }
                
                $finfo = es_property_get_field_info( $field );
                if ( ! $finfo ) continue;

                if ( is_array( $value ) ) {
                    $value = array_filter( $value );
                }

                if ( ! empty( $finfo['taxonomy'] ) && ! empty( $value ) ) {
                    $tax_query[ $field ] = array(
                        'taxonomy' => $field,
                        'terms' => is_array( $value ) ? $value : explode( $args['settings']['fields_delimiter'], $value ),
                        'field' => 'id'
                    );

                    if ( 'es_label' == $field ) {
	                    $tax_query[ $field ]['relation'] = 'OR';
                    }
                } else if ( ! empty( $finfo['system'] ) ) {
                    // Search by post fields.
                } else {
                    if ( empty( $finfo['taxonomy'] ) && empty( $finfo['system'] ) ) {
                        if ( is_string( $value ) ) {
                            if ( strlen( $value ) ) {
                                if ( ! empty( $finfo['type'] ) && in_array( $finfo['type'], array( 'date', 'date-time' ) ) ) {
                                    $format = $finfo['attributes']['data-date-format'];
                                    $value = DateTime::createFromFormat( $format, $value );

                                    if ( $finfo['type'] == 'date' ) {
                                        $value->setTime( 0, 0, 0 );
                                    }

                                    $value = $value instanceof DateTime ? $value->getTimestamp() : null;
                                }

                                if ( 'address' == $field ) {
                                    if ( empty( $args['settings']['strict_address'] ) ) {
	                                    $address_components = get_terms( array(
		                                    'taxonomy' => 'es_location',
		                                    'name__like' => $value,
		                                    'fields' => 'ids',
	                                    ) );

	                                    if ( $address_components ) {
		                                    $tax_query[] = array( 'taxonomy' => 'es_location', 'field' => 'id', 'terms' => $address_components );
	                                    } else {
		                                    $meta_query[ $field ] = array(
			                                    'key' => $property->get_entity_prefix() . $field,
			                                    'value' => $value,
			                                    'compare' => 'LIKE',
		                                    );
	                                    }
                                    } else {
	                                    $meta_query[ $field ] = array(
		                                    'key' => $property->get_entity_prefix() . $field,
		                                    'value' => $value,
		                                    'compare' => 'LIKE',
	                                    );
                                    }
                                } else if ( in_array( $field, es_get_location_fields() ) ) {
	                                $loc_arr = is_string( $value ) ? explode( ',', $value ) : $value;
	                                $tax_query['es_location'][] = array( 'taxonomy' => 'es_location', 'field' => 'id', 'terms' => $loc_arr );
                                } else {
		                            if ( is_string( $value ) || is_scalar( $value ) ) {
	                                    $value = array_map( 'trim', explode( $args['settings']['fields_delimiter'], $value ) );
                                    }

                                    if ( is_array( $value ) ) {
	                                    foreach ( $value as $field_value ) {
		                                    $meta_query[ $field ][] = array( 'key' => $property->get_entity_prefix() . $field, 'value' => $field_value );
	                                    }

                                        if ( ! empty( $meta_query[ $field ] ) && count( $meta_query[ $field ] ) > 1 ) {
	                                        $meta_query[ $field ]['relation'] = 'OR';
                                        }
                                    }
                                }
                            }
                        } else {
                            if ( $value ) {
                                if ( 'keywords' == $field ) {
                                    $meta_query[ $field ]['relation'] = 'OR';
                                    foreach ( $value as $keyword ) {
                                        $meta_query[ $field ][] = array(
                                            'key' => 'es_property_keywords',
                                            'value' => $keyword,
                                            'compare' => 'LIKE'
                                        );
                                    }
//                                    $query_args['s'] = implode( '+', $value );
                                } else if ( 'city' == $field ) {
	                                $tax_query[] = array( 'taxonomy' => 'es_location', 'field' => 'id', 'terms' => $value );
                                } else {
                                    $field_info = es_property_get_field_info( $field );
                                    if ( ! empty( $field_info[ 'relation' ] ) ) {
                                        $meta_query[ $field ]['relation'] = strtoupper( $field_info[ 'relation' ] );
                                    } else {
                                        $meta_query[ $field ]['relation'] = 'AND';
                                    }

                                    foreach ( $value as $single_value ) {
                                        $meta_query[ $field ][] = array(
                                            'key' => $property->get_entity_prefix() . $field,
                                            'value' => $single_value,
                                            'compare' => 'LIKE',
                                        );
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        if ( ! empty( $args['fields']['prop_id'] ) ) {
            $ids = $args['fields']['prop_id'];
	        $query_args['post__in'] = is_array( $ids ) ? $ids : explode( ',', $ids );
        }

        $query_args['meta_query'] = $meta_query;
        $query_args['tax_query'] = $tax_query;

        return apply_filters( 'es_get_properties_query_args', $query_args );
    }
}

if ( ! function_exists( 'es_properties_no_found_posts' ) ) {

	/**
	 * Render no found properties block
	 *
	 * @param array $args
	 *
	 * @return void
	 */
	function es_properties_no_found_posts( $args = array() ) {
        es_load_template( 'front/partials/no-found-posts.php', $args );
	}
}
add_action( 'es_properties_no_found_posts', 'es_properties_no_found_posts' );

if ( ! function_exists( 'es_property_back_search_button' ) ) {

	/**
	 * @return void
	 */
    function es_property_back_search_button() {
        if ( $url = filter_input( INPUT_GET, 'search_url' ) ) {
	        $label = __( 'Back to Search Results', 'es' );
	        echo "<a href='" . esc_url( $url ) . "' class='es-secondary-color es-back-search-btn'>
                <span class='es-icon es-icon_chevron-left'></span>{$label}</a>";
        }
    }
}
add_action( 'es_property_breadcrumbs', 'es_property_back_search_button' );

if ( ! function_exists( 'es_the_property_breadcrumbs' ) ) {

	/**
     * Display breadcrumbs on sigle property page.
     *
	 * @param int $post_id
	 */
	function es_the_property_breadcrumbs( $post_id = 0 ) {
		$query_args = array();
		$breadcrumbs = array();
		$search_url = es_get_search_page_url();
		$property = es_get_the_property( $post_id );
        $categories = wp_get_object_terms( $post_id, 'es_category', array( 'fields' => 'id=>name' ) );
        $types = wp_get_object_terms( $post_id, 'es_type', array( 'fields' => 'id=>name' ) );

        if ( ! empty( $categories ) ) {
            foreach ( $categories as $term_id => $label ) {
	            $query_args['es_category'][] = $term_id;
	            $args = array( 'es_category' => array( $term_id ) );
	            $breadcrumbs[] = "<a class='es-breadcrumbs__item es-secondary-color-hover' href='" . add_query_arg( $args, $search_url ) . "'>{$label}</a>";
            }
        }

        if ( ! empty( $types ) ) {
            foreach ( $types as $term_id => $label ) {
	            $args = $query_args;
	            $args['es_type'] = array( $term_id );
	            $query_args['es_type'][] = $term_id;
                $breadcrumbs[] = "<a class='es-breadcrumbs__item es-secondary-color-hover' href='" . add_query_arg( $args, $search_url ) . "'>{$label}</a>";
            }
        }

        $location_fields = apply_filters( 'es_property_breadcrumbs_location_fields', array( 'state', 'province', 'city' ) );

		if ( ests( 'is_listing_address_enabled' ) && ! es_get_the_field( 'is_address_disabled' ) ) {
            foreach ( $location_fields as $field ) {
                if ( ! es_is_property_default_field_deactivated( $field ) && $location_id = $property->{$field} ) {
                    $term = get_term_by( 'id', $location_id, 'es_location' );
                    if ( ! empty( $term->name ) ) {
	                    $query_args[ $field ] = $location_id;
                        $breadcrumbs[] = "<a class='es-breadcrumbs__item es-secondary-color-hover' href='" . add_query_arg( $query_args, $search_url ) . "'>" . $term->name . "</a>";
                    }
                }
            }

            if ( $postal_code = $property->postal_code ) {
                $query_args['postal_code'] = $postal_code;
	            $breadcrumbs[] = "<a class='es-breadcrumbs__item es-secondary-color-hover' href='" . add_query_arg( $query_args, $search_url ) . "'>{$postal_code}</a>";
            }

			if ( $address = es_get_the_field( 'address' ) ) {
				$breadcrumbs[] = "<span class='es-breadcrumbs__item'>" . $address . "</span>";
			} else {
				$breadcrumbs[] = "<span class='es-breadcrumbs__item'>" . get_the_title() . "</span>";
            }
        } else {
			$breadcrumbs[] = "<span class='es-breadcrumbs__item'>" . get_the_title() . "</span>";
        }

        ob_start();
        if ( ! empty( $breadcrumbs ) ) {
            $c = count( $breadcrumbs );
            $index = 0;
            echo "<div class='es-breadcrumbs'>";
                foreach ( $breadcrumbs as $item ) {
                    $index++;
                    echo $item;
                    echo $c != $index ? "<span class='es-icon es-icon_chevron-right'></span>" : '';
                }
            echo "</div>";
        }
		echo apply_filters( 'es_the_property_breadcrumbs', ob_get_clean(), $breadcrumbs, $post_id );
	}
}
add_action( 'es_property_breadcrumbs', 'es_the_property_breadcrumbs' );

function es_property_get_default_meta_icons() {
	$icons = array(
		'area.svg' => '<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M15.3334 10.6667V5.33333C15.3334 4.59695 14.7364 4 14 4H2.00002C1.26364 4 0.666687 4.59695 0.666687 5.33333V10.6667C0.666687 11.403 1.26364 12 2.00002 12H14C14.7364 12 15.3334 11.403 15.3334 10.6667ZM2.00002 5.33333H3.33335V7.33333H4.66669V5.33333H6.00002V8.66667H7.33335V5.33333H8.66669V7.33333H10V5.33333H11.3334V8.66667H12.6667V5.33333H14V10.6667H2.00002V5.33333Z" fill="#B0BEC5"/>
                        </svg>',
		'bathroom.svg' => '<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <g clip-path="url(#clip0)">
                                <path d="M15.3333 6.66667H14V2.33333C14 1.71449 13.7542 1.121 13.3166 0.683417C12.879 0.245833 12.2855 0 11.6667 0C11.0478 0 10.4543 0.245833 10.0168 0.683417C9.57917 1.121 9.33333 1.71449 9.33333 2.33333V2.66667H8.66667V4H11.3333V2.66667H10.6667V2.33333C10.6667 2.06812 10.772 1.81376 10.9596 1.62623C11.1471 1.43869 11.4015 1.33333 11.6667 1.33333C11.9319 1.33333 12.1862 1.43869 12.3738 1.62623C12.5613 1.81376 12.6667 2.06812 12.6667 2.33333V6.66667H3.33333V6H4V4.66667H2.66667C2.48986 4.66667 2.32029 4.7369 2.19526 4.86193C2.07024 4.98695 2 5.15652 2 5.33333V6.66667H0.666667C0.489856 6.66667 0.320286 6.7369 0.195262 6.86193C0.0702379 6.98695 0 7.15652 0 7.33333L0 10.6667C0.000712054 11.315 0.19023 11.9491 0.545401 12.4914C0.900573 13.0338 1.40602 13.461 2 13.7208V15.3333H3.33333V14H12.6667V15.3333H14V13.7208C14.594 13.461 15.0994 13.0338 15.4546 12.4914C15.8098 11.9491 15.9993 11.315 16 10.6667V7.33333C16 7.15652 15.9298 6.98695 15.8047 6.86193C15.6797 6.7369 15.5101 6.66667 15.3333 6.66667ZM3.33333 12.6667C3.33353 12.4899 3.40383 12.3205 3.52881 12.1955C3.6538 12.0705 3.82325 12.0002 4 12H12C12.1768 12.0002 12.3462 12.0705 12.4712 12.1955C12.5962 12.3205 12.6665 12.4899 12.6667 12.6667H3.33333ZM14.6667 10.6667C14.6667 10.9601 14.6021 11.2499 14.4774 11.5154C14.3527 11.781 14.171 12.0159 13.9453 12.2032C13.8405 11.766 13.5917 11.3766 13.2389 11.0979C12.886 10.8192 12.4497 10.6673 12 10.6667H4C3.55035 10.6673 3.11399 10.8192 2.76114 11.0979C2.40829 11.3766 2.15948 11.766 2.05475 12.2032C1.82899 12.0159 1.6473 11.781 1.52261 11.5154C1.39792 11.2499 1.3333 10.9601 1.33333 10.6667V8H14.6667V10.6667Z" fill="#B0BEC5"/>
                                </g>
                                <defs>
                                <clipPath id="clip0">
                                <rect width="16" height="16" fill="white"/>
                                </clipPath>
                                </defs>
                            </svg>',
		'bed.svg' => '<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <g clip-path="url(#clip0)">
                            <path d="M14 6.25H5.88533C5.74708 5.86037 5.49166 5.52307 5.15411 5.28435C4.81657 5.04563 4.41343 4.9172 4 4.91667H1.33333V3H0V12.9167H1.33333V11.5833H14.6667V12.9167H16V8.25C15.9994 7.71975 15.7885 7.21139 15.4136 6.83644C15.0386 6.4615 14.5303 6.2506 14 6.25ZM4 6.25C4.17675 6.2502 4.3462 6.3205 4.47119 6.44548C4.59617 6.57046 4.66647 6.73992 4.66667 6.91667V7.58333H1.33333V6.25H4ZM1.33333 8.91667H4.66667V10.25H1.33333V8.91667ZM6 7.58333H14C14.1768 7.58353 14.3462 7.65383 14.4712 7.77881C14.5962 7.9038 14.6665 8.07325 14.6667 8.25V10.25H6V7.58333Z" fill="#B0BEC5"/>
                            </g>
                            <defs>
                            <clipPath id="clip0">
                            <rect width="16" height="16" fill="white"/>
                            </clipPath>
                            </defs>
                        </svg>',
	);

	return apply_filters( 'es_property_get_default_meta_icons', $icons );
}

/**
 * @return mixed
 */
function es_property_get_meta_fields() {
	global $property_meta_fields;

	if ( empty( $property_meta_fields ) ) {
		$fields = ests( 'listing_meta_icons' );

		if ( ! empty( $fields ) ) {
			$cache = ests( 'listing_meta_icons_cache' );
			$cache = $cache ? $cache : array();
			$icons = es_property_get_default_meta_icons();

			foreach ( $fields as $key => $field ) {
				$property_meta_fields[ $key ] = $field;
				if ( empty( $field['icon'] ) ) continue;

				$fields[ $key ]['icon'] = str_replace( '{plugin_url}', ES_PLUGIN_URL, $field['icon'] );
				$fields[ $key ]['icon'] = untrailingslashit( $fields[ $key ]['icon'] );

				if ( ! empty( $fields[ $key ]['icon'] ) && stristr( $fields[ $key ]['icon'], '.svg' ) ) {
					$property_meta_fields[ $key ]['icon'] = $fields[ $key ]['icon'];

					if ( empty( $cache[ $field['icon'] ] ) ) {
						if ( stristr( $fields[ $key ]['icon'], ES_PLUGIN_URL ) !== FALSE ) {
							foreach ( array( 'area.svg', 'bathroom.svg', 'bed.svg' ) as $file ) {
								if ( stristr( $fields[ $key ]['icon'], $file ) && ! empty( $icons[ $file ] ) ) {
									$cache[ $field['icon'] ] = $icons[ $file ];
									break;
								}
							}
						} else {
							$cache[ $field['icon'] ] = file_get_contents( $fields[ $key ]['icon'] );
						}

						ests_save_option( 'listing_meta_icons_cache', $cache );
					}
                    
                    if ( !empty ($cache[ $field['icon'] ]) ) {
					    $property_meta_fields[ $key ]['svg'] = $cache[ $field['icon'] ];
                    }
				}
			}
		}
	}

	return $property_meta_fields;
}

/// //if ( ! function_exists( '' ) ) {
//
//    /**
//     *
//     */
//    function () {
//
//    }
//}
//add_action( '', '' );
