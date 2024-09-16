<?php

if ( ! function_exists( 'es_get_the_property' ) ) {

	/**
	 * Return property instance in loop.
	 *
	 * @param int $post
	 *
	 * @return Es_Property
	 */
	function es_get_the_property( $post = 0 ) {
		$post = get_post( $post );

		return es_get_property( $post->ID );
	}
}

if ( ! function_exists( 'es_the_title' ) ) {

    /**
     * Render property title.
     *
     * @param string $before
     * @param string $after
     */
    function es_the_title( $before = '', $after = '' ) {
        $title_mode = ests( 'title_mode' );
        if ( $title_mode == 'title' ) {
            the_title( $before, $after );
        } else if ( $title_mode == 'address' ) {
            es_the_field( 'address', $before, $after );
        } else {
            do_action( 'es_the_title_custom_mode', $before, $after );
        }
    }
}

if ( ! function_exists( 'es_the_price' ) ) {

	/**
	 * Display property formatted price.
	 *
	 * @param string $before
	 * @param string $after
	 *
	 * @return void
	 */
	function es_the_price( $before = '', $after = '' ) {
		$property = es_get_the_property();
		if ( ests( 'is_price_enabled' ) ) {
		    if ( $property->call_for_price ) {
		        $result = '<span class="es-badge call-for-price">' . __( 'Call for price', 'es' ) . '</span>';
            } else {
		        $price = es_get_the_formatted_field( 'price' );
                $result = $price ? '<span class="es-price">' . $price . '</span>' : '';
            }

		    echo $result ? $before . $result . $after : '';
        }
	}
}

if ( ! function_exists( 'es_the_field' ) ) {

	/**
	 * Display property raw field.
	 *
	 * @param $field
	 * @param string $before
	 * @param string $after
	 */
	function es_the_field( $field, $before = '', $after = '' ) {
		$value = es_get_the_field( $field );
		echo $value ? apply_filters( 'es_the_field', $before . $value . $after ) : '';
	}
}

/**
 * @param $field
 * @param string $before
 * @param string $after
 */
function es_the_property_field( $field, $before = '', $after = '' ) {
    es_the_field( $field, $before, $after );
}

if ( ! function_exists( 'es_the_address' ) ) {

    /**
     * Render display address.
     *
     * @param string $before
     * @param string $after
     * @return void
     */
    function es_the_address( $before = '', $after = '' ) {
        if ( ests( 'is_listing_address_enabled' ) && ! es_get_the_field( 'is_address_disabled' ) ) {
            es_the_field( 'address', $before, $after );
        }
    }
}

if ( ! function_exists( 'es_get_the_field' ) ) {

	/**
	 * Return property field raw value.
	 *
	 * @param $field
	 * @param int $post
	 *
	 * @return mixed
	 */
	function es_get_the_field( $field, $post = 0 ) {
		$property = es_get_entity_by_id( $post );

		return apply_filters( 'es_get_the_field', $property->{$field}, $field, $post );
	}
}

if ( ! function_exists( 'es_get_the_featured_image_url' ) ) {

    /**
     * Return featured image url.
     *
     * @param $size
     * @param int $post
     * @return mixed|void
     */
    function es_get_the_featured_image_url( $size = 'thumbnail', $post = 0 ) {
        $featured_image = get_the_post_thumbnail_url( $post, $size );

        if ( ! $featured_image ) {
            $gallery = es_get_the_field( 'gallery', $post );
            $default_image_id = ests( 'default_property_image_id' );

            if ( ! empty( $gallery[0] ) ) {
                $featured_image = wp_get_attachment_image_url( $gallery[0], $size );
            } else if ( $default_image_id ) {
                $featured_image = wp_get_attachment_image_url( $default_image_id, $size );
            } else {
                $featured_image = ES_PLUGIN_URL . 'public/img/thumb.svg';
            }
        }

        return apply_filters( 'es_get_the_featured_image_url', $featured_image, $size, $post );
    }
}

if ( ! function_exists( 'es_get_the_formatted_field' ) ) {

	/**
	 * Return property formatted field.
	 *
	 * @param $field
	 * @param int $post
	 *
	 * @return string
	 */
	function es_get_the_formatted_field( $field, $post = 0 ) {
		$entity = es_get_entity_by_id( $post );
		$value = es_get_the_field( $field, $post );
		$field_info = $entity instanceof Es_Entity ? $entity::get_field_info( $field ) : array();

		if ( ( is_string( $value ) && strlen( $value ) ) || ! empty( $value ) ) {
		    if ( 'post_content' == $field ) {
                $value = es_get_the_content( $post );
            } else if ( ! empty( $field_info['taxonomy'] ) ) {
                $value = es_get_the_term_list( $post, $field, '', ', ' );
            } else {
                $value = ! empty( $field_info['formatter'] ) ?
	                es_format_value( $value, $field_info['formatter'], $field_info ) : $value;
            }
        }

		return apply_filters( 'es_get_the_formatted_field', $value, $field, $post );
	}
}

if ( ! function_exists( 'es_get_the_content' ) ) {

	/**
	 * @param int $post
	 * @param null $more_link_text
	 * @param bool $strip_teaser
	 * @return string
	 */
	function es_get_the_content( $post = 0, $more_link_text = null, $strip_teaser = false ) {
		$post = get_post( $post );
		$entity = es_get_entity_by_id( $post->ID );
		$elementor_editor_mode = es_is_elementor_builder_enabled( $post->ID );
		$divi_builder = function_exists( 'et_pb_is_pagebuilder_used' ) && et_pb_is_pagebuilder_used( $post->ID );
		return $elementor_editor_mode || $divi_builder ? $entity->alternative_description : wpautop( get_the_content( $more_link_text, $strip_teaser, $post ) );
	}
}

if ( ! function_exists( 'es_the_formatted_field' ) ) {

	/**
	 * Display property formatted field.
	 *
	 * @param $field
	 * @param string $before
	 * @param string $after
	 */
	function es_the_formatted_field( $field, $before = '', $after = '' ) {
		$value = es_get_the_formatted_field( $field );

		if ( is_string( $value ) && strlen( $value ) ) {
			echo apply_filters( 'es_the_formatted_field', $before . $value . $after, $field );
		}
	}
}

if ( ! function_exists( 'es_get_the_term_list' ) ) {

	/**
	 * Retrieve a post's terms as a list with specified format.
	 *
	 * @param int $id Post ID.
	 * @param string $taxonomy Taxonomy name.
	 * @param string $before Optional. Before list.
	 * @param string $sep Optional. Separate items using this.
	 * @param string $after Optional. After list.
	 *
	 * @return string|false|WP_Error A list of terms on success, false if there are no terms, WP_Error on failure.
	 */
    function es_get_the_term_list( $id, $taxonomy, $before = '', $sep = '', $after = '' ) {
        $terms = get_the_terms( $id, $taxonomy );

        if ( is_wp_error( $terms ) ) {
            return $terms;
        }

        if ( empty( $terms ) ) {
            return false;
        }

        $links = array();
        $icon_taxonomies = apply_filters( 'es_supported_icons_taxonomies', array( 'es_feature', 'es_amenity' ), $taxonomy, $id );
        $use_icon = ests( 'is_terms_icons_enabled' ) && in_array( $taxonomy, $icon_taxonomies );

        $i = 0;

        foreach ( $terms as $term ) {
            $icon = null;
            $link = get_term_link( $term, $taxonomy );

            if ( $use_icon ) {
                if ( ests( 'term_icon_type' ) == 'check' ) {
                    $icon = "<span class='es-icon es-icon_check-mark'></span>";
                } else if ( ests( 'term_icon_type' ) == 'icon' ) {
                    if ( $term_icon = get_term_meta( $term->term_id, 'es_icon', true ) ) {
                        $icon = apply_filters( 'es_term_icon', $term_icon->icon, $term_icon->type, $term, $taxonomy, $id );
                    } else {
                        $icon = apply_filters( 'es_term_icon', '<span class="es-icon es-icon_icon"></span>', 'default', $term, $taxonomy, $id );
                    }
                } else {
                    $icon = apply_filters( 'es_term_icon', null, 'custom', $term, $taxonomy, $id );
                }
            }

            if ( $link ) {
                $links[] = "<a href='" . esc_url( $link ) . "' rel='tag'>{$icon}{$term->name}</a>";
            } else {
                $links[] = "<span>{$icon}{$term->name}</span>";
            }

            $i++;
        }

        /**
         * Filters the term links for a given taxonomy.
         *
         * The dynamic portion of the filter name, `$taxonomy`, refers
         * to the taxonomy slug.
         *
         * @since 2.5.0
         *
         * @param string[] $links An array of term links.
         */
        $term_links = apply_filters( "term_links-{$taxonomy}", $links );  // phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores
	    $terms_html = join( $sep, $term_links );

        return $before . $terms_html . $after;
    }
}

if ( ! function_exists( 'es_get_the_permalink' ) ) {

	/**
	 * @param int $post_id
	 *
	 * @return bool|false|string|WP_Error
	 */
	function es_get_the_permalink( $post_id = 0 ) {
		$permalink = get_the_permalink( $post_id );
		$search_url = ! empty( $GLOBALS['search_url'] ) ? $GLOBALS['search_url'] : false;

		return $search_url ? add_query_arg( 'search_url', urlencode_deep( $search_url ), $permalink ) : $permalink;
	}
}

/**
 * @param $post_id
 *
 * @return false|string
 */
function es_get_permalink( $post_id ) {
	if ( function_exists( 'pll_get_post' ) ) {
		$post_tr_id = pll_get_post( $post_id );
		$post_id = $post_tr_id ? $post_tr_id : $post_id;
	}

	return get_permalink( $post_id );
}

/**
* Hook into Elementor widget before rendering content to modify query field.
*
* @param \Elementor\Widget_Base $widget The widget instance.
*/


add_action( 'plugins_loaded', function() {
	if ( class_exists( 'Elementor\Plugin' ) || class_exists( 'Elementor\Plugin_Base' ) ) {

		add_action( 'elementor/widget/before_render_content', function( $widget ) {

			if ( 'es-listings-widget' == $widget->get_name() || 'es-hfm-widget' == $widget->get_name() ) {

				$settings = $widget->get_settings_for_display();
		
				if ( isset( $settings['es_category'] ) && is_array( $settings['es_category'] ) ) {
					$settings['es_category'] = implode( ',', $settings['es_category'] );
					$widget->set_settings( $settings );
				}

				if ( isset( $settings['es_label'] ) && is_array( $settings['es_label'] ) ) {
					$settings['es_label'] = implode( ',', $settings['es_label'] );
					$widget->set_settings( $settings );
				}

				if ( isset( $settings['es_type'] ) && is_array( $settings['es_type'] ) ) {
					$settings['es_type'] = implode( ',', $settings['es_type'] );
					$widget->set_settings( $settings );
				}

				if ( isset( $settings['es_status'] ) && is_array( $settings['es_status'] ) ) {
					$settings['es_status'] = implode( ',', $settings['es_status'] );
					$widget->set_settings( $settings );
				}
			}

		} );
	}
}); 