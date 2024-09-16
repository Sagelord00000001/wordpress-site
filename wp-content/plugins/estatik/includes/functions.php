<?php

if ( ! function_exists( 'es_format_values' ) ) {

	/**
	 * @param $values
	 * @param $format
	 * @param array $args
	 *
	 * @return mixed
	 */
	function es_format_values( $values, $format, $args = array() ) {
		if ( ! empty( $values ) ) {
			foreach ( $values as $key => $value ) {
				$values[ $key ] = es_format_value( $value, $format, $args );
			}
		}
		return $values;
	}
}

if ( ! function_exists( 'es_format_value' ) ) {

	/**
	 * Format value by formatter type.
	 *
	 * @param $value string|array
	 * @param $format string
	 * @param array $args
	 *
	 * @return mixed
	 */
	function es_format_value( $value, $format, $args = array() ) {
		$formatted_value = $value;

		switch ( $format ) {
			case 'url':
				if ( ! empty( $value['url'] ) && ! empty( $value['label'] ) ) {
					$formatted_value = sprintf( "<a href='%s' target='_blank'>%s</a>", esc_url( $value['url'] ), $value['label'] );
				} else if ( is_string( $value ) ) {
					$formatted_value = sprintf( "<a href='%s' target='_blank'>%s</a>", esc_url( $value ), $value );
				}
				break;

			case 'link':
				if ( is_array( $value ) && ! empty( $value['url'] ) ) {
					$label = ! empty( $value['label'] ) ? $value['label'] : $value['url'];
					$formatted_value = sprintf( "<a href='%s'>%s</a>", esc_url( $value['url'] ), $label );
				}
				break;

            case 'date_added':
                $date = human_time_diff( $value, current_time( 'U' ) );
	            /* translators: %s: num of days */
                $formatted_value = sprintf( __( 'Added %s ago', 'es' ), $date );
                break;
            case 'price':
            case 'price-area':
                if ( $value ) {
                    $dec = stripslashes( ests( 'currency_dec' ) );
                    $dec_num = stripslashes( ests( 'currency_dec_num' ) );
                    $sup = stripslashes( ests( 'currency_sup' ) );
                    $price_format = $sup . $dec;
                    $position = ests( 'currency_position' );
                    $sign = ests_label( 'currency_sign' );
                    $currency = $sign ? $sign : ests( 'currency' );
                    $space = empty( $sign ) ? ' ' : '';

					if ( in_array( $position, array( 'before_space', 'after_space' ) ) ) {
						$space = ' ';
					}

					if ( ! strlen( $dec_num ) ) {
						$dec_num = $sup == ' ' || $sup == ',' || $sup == '.' || '\'' || empty( $sup ) ? 0 : 2;
						$dec_num = $price_format == ',.' || $price_format == '.,' ? 2 : $dec_num;
					}

					if ( ! es_is_decimal( $value ) ) {
						$dec_num = 0;
					}

                    $price_temp = floatval( $value );
                    $price_temp = number_format( $price_temp, $dec_num, $dec, $sup );

                    if ( $format == 'price-area' ) {
                        $price_temp .= '/' . __( 'sq ft', 'es' );
                    }
                    $formatted_value = $position == 'after' || $position == 'after_space' ? $price_temp . $space . $currency : $currency . $space . $price_temp;
                }
                break;

			case 'beds':
			    $formatted_value = sprintf( '<b>%s</b> <span>%s</span>', $value, _n( 'bed', 'beds', $value, 'es' ) );
				break;

            case 'baths':
                $formatted_value = sprintf( '<b>%s</b> <span>%s</span>', $value, _n( 'bath', 'baths', $value, 'es' ) );
				break;

            case 'floors':
                $formatted_value = sprintf( '<b>%s</b> <span>%s</span>', $value, _n( 'floor', 'floors', $value, 'es' ) );
                break;

            case 'half_baths':
                $formatted_value = sprintf( '<b>%s</b> <span>%s</span>', $value, _n( 'half bath', 'half baths', $value, 'es' ) );
                break;

            case 'area':
            case 'lot_size':
				if ( ! empty( $args['unit'] ) ) {
					$values = ests_values( $format . '_unit' );
					$unit = ! empty( $values[ $args['unit'] ] ) ? $values[ $args['unit'] ] :
						ests_label( $format . '_unit' );
				} else {
					$unit = ests_label( $format . '_unit' );
				}

                $formatted_value = sprintf( '<b>%s</b> <span>%s</span>', $value, $unit );
                break;

            case 'document':
                if ( ! empty( $value ) ) {
                    ob_start();
                    es_load_template( 'front/property/partials/documents.php', array(
                        'attachments_ids' => $value
                    ) );
                    $formatted_value = ob_get_clean();
                }
                break;

            case 'image':
                if ( ! empty( $value ) ) {
                    ob_start();
                    es_load_template( 'front/property/partials/images.php', array(
                        'attachments_ids' => $value
                    ) );
                    $formatted_value = ob_get_clean();
                }
                break;

            case 'country':
            case 'city':
            case 'state':
            case 'province':
				$term = get_term( $value, 'es_location' );
				if ( $term ) {
					if ( es_get_search_page_url() ) {
						$formatted_value = "<a href='" . esc_url( get_term_link( $term ) ) . "'>{$term->name}</a>";
					} else {
						$formatted_value = $term->name;
					}
				}

				break;

            case 'appointments':
                ob_start();
                echo "<ul class='es-appointments'>";
                if ( is_array( $value ) ) {
					$time_format = ests( 'time_format' );

                    foreach ( $value as $appointment ) {
	                    if ( $time_format == 'h' ) {
		                    foreach ( array( 'start_time', 'end_time' ) as $field ) {
			                    if ( ! empty( $appointment[ $field ] ) ) {
									$dt = new DateTime( $appointment[ $field ] );
				                    $appointment[ $field ] = $dt->format( 'h:i A ' );
			                    }
		                    }
	                    }

                        printf( "<li><b>%s</b><span>%s to %s</span></li>", $appointment['date'], $appointment['start_time'], $appointment['end_time'] );
                    }
                }
                echo "</ul>";
                $formatted_value = ob_get_clean();
                break;

			case 'video':
				if ( $value ) {
					$formatted_value = '';
					if ( ! empty( $value['video_url'] ) ) {
						$formatted_value = wp_oembed_get( esc_url( $value['video_url'] ) );
					}
					if ( ! empty( $value['video_iframe'] ) ) {
						$formatted_value .= html_entity_decode( $value['video_iframe'] );
					}
					if ( ! empty( $value['video_file'] ) ) {
						$formatted_value .= wp_video_shortcode( array(
							'src' => wp_get_attachment_url( $value['video_file'] ),
						) );
					}
				}
				break;

			case 'switcher':
				if ( ! $value ) {
					$formatted_value = _x( 'No', 'switcher field value', 'es' );
				} else {
					$formatted_value = _x( 'Yes', 'switcher field value', 'es' );
				}
				break;

            default:
                if ( is_array( $value ) ) {
                    $formatted_value = implode( ', ', $value );
                }
		}

		return apply_filters( 'es_format_value', $formatted_value, $value, $format, $args );
	}
}

if ( ! function_exists( 'es_locate_template' ) ) {

	/**
	 * Return plugin template path.
	 *
	 * @param $template_path string
	 *
	 * @return string
	 */
	function es_locate_template( $template_path ) {

		$find = array();
		$path = $template_path;
		$context = ES_PLUGIN_PATH . DS . 'templates' . DS;
		$base = $template_path;

		$find[] = 'estatik4/' . $template_path;
		$find[] = $context . $template_path;

		$template_path = locate_template( array_unique( $find ) );

		if ( ! $template_path ) {
			$template_path = $context . $base;
		}

		return apply_filters( 'es_locate_template', $template_path, $path );
	}
}

if ( ! function_exists( 'es_load_template' ) ) {

	/**
	 * Include template by provided path.
	 *
	 * @see es_locate_template
	 *
	 * @param $template_path string
	 *    Template path.
	 *
	 * @param array $args
	 *    Template variables list.
	 */
	function es_load_template( $template_path, $args = array() ) {

		global $posts, $post, $wp_did_header, $wp_query, $wp_rewrite, $wpdb, $wp_version, $wp, $id, $comment, $user_ID;
		$args = apply_filters( 'es_template_args', $args, $template_path );
		extract( $args );

		include es_locate_template( $template_path );
	}
}

if ( ! function_exists( 'es_get_settings_container' ) ) {

	/**
	 * Return plugin settings container.
	 *
	 * @return Es_Settings_Container
	 */
	function es_get_settings_container() {
		return apply_filters( 'es_settings_container', new Es_Settings_Container() );
	}
}

if ( ! function_exists( 'es_get_available_search_fields' ) ) {

	/**
	 * Return available search fields for main and collapsed filters.
	 *
	 * @return array
	 */
	function es_get_available_search_fields() {
	    $fields = es_get_entity_fields( 'property' );
	    $fields = array_filter( $fields, 'es_filter_search_fields' );

	    if ( ! empty( $fields ) ) {
	        $fields = wp_list_pluck( $fields, 'label' );
			asort( $fields );
        }

		return apply_filters( 'es_get_available_search_fields', $fields );
	}
}

if ( ! function_exists( 'es_get_default_sections' ) ) {

	/**
	 * Return entity default sections.
	 *
	 * @param string $entity
	 *
	 * @return array
	 */
	function es_get_default_sections( $entity = 'property' ) {

		$sections = apply_filters( 'es_get_default_sections', array(
			'property' => array(
				'basic-facts' => array(
					'label' => __( 'Basics', 'es' ),
					'fb_settings' => array(
						'disable_order' => true,
						'disable_deletion' => true,
					),
					'order' => 10,
					'is_visible' => true,
					'is_visible_for' => array( 'all_users' ),
				),
				'description' => array(
					'label' => __( 'Description', 'es' ),
					'order' => 20,
					'is_visible' => true,
					'is_visible_for' => array( 'all_users' ),
				),
				'open-house' => array(
                    'label' => __( 'Open House', 'es' ),
                    'order' => 30,
                    'is_visible' => true,
                    'is_visible_for' => array( 'all_users' ),
                ),
				'location' => array(
					'label' => __( 'Location', 'es' ),
					'order' => 40,
					'is_visible' => true,
					'is_visible_for' => array( 'all_users' ),
				),
				'media' => array(
					'label' => __( 'Photos & Media', 'es' ),
					'order' => 50,
				),
                'building-details' => array(
                    'label' => __( 'Building Details', 'es' ),
                    'order' => 60,
                    'is_visible' => true,
                    'is_visible_for' => array( 'all_users' ),
                ),
                'video' => array(
                    'label' => __( 'Video', 'es' ),
                    'order' => 70,
                    'is_visible' => true,
                    'is_visible_for' => array( 'all_users' ),
                ),
                'documents' => array(
                    'label' => __( 'Documents', 'es' ),
                    'order' => 80,
                    'is_visible' => true,
                    'is_visible_for' => array( 'all_users' ),
                ),
                'floors_plans' => array(
                    'label' => __( 'Floor Plans', 'es' ),
                    'order' => 90,
                    'is_visible' => true,
                    'is_visible_for' => array( 'all_users' ),
                ),
				'features' => array(
					'label' => __( 'Amenities & Features', 'es' ),
					'order' => 100,
					'is_visible' => true,
					'is_visible_for' => array( 'all_users' ),
				),
				'request_form' => array(
					'label' => __( 'Ask an Agent About This Home', 'es' ),
					'section_name' => __( 'Request form', 'es' ),
					'order' => 110,
					'is_visible' => true,
					'is_visible_for' => array( 'all_users' ),
					'options' => array(
						'background_color' => '#ffffff',
						'text_color' => '#263238',
					),
				),
			),
		) );

		return apply_filters( 'es_get_entity_default_sections',
			! empty( $sections[ $entity ] ) ? $sections[ $entity ] : array() );
	}
}

if ( ! function_exists( 'es_get_property' ) ) {

	/**
	 * Return property instance.
	 *
	 * @param null $id
	 *
	 * @return Es_Property
	 */
	function es_get_property( $id = null ) {
		return apply_filters( 'es_get_property', new Es_Property( $id ), $id );
	}
}

if ( ! function_exists( 'es_get_saved_search' ) ) {

    /**
     * Return property instance.
     *
     * @param null $id
     *
     * @return Es_Saved_Search
     */
    function es_get_saved_search( $id = null ) {
        return apply_filters( 'es_get_saved_search', new Es_Saved_Search( $id ), $id );
    }
}

if ( ! function_exists( 'es_get_term_color' ) ) {

	/**
	 * Return term color. Function used for es_label taxonomy.
	 *
	 * @param $term_id .
	 * @param string $default
	 *
	 * @return mixed|string
	 */
	function es_get_term_color( $term_id, $default = '' ) {
		$default = $default ? $default : ests( 'default_label_color' );
		$term_color = get_term_meta( $term_id, 'es_color', true );
		$term_color = $term_color ? $term_color : $default;

		return es_strtolower( $term_color );
	}
}

if ( ! function_exists( 'es_search_get_field_config' ) ) {

    /**
     * Return field config for search widget.
     *
     * @param $field string
     *
     * @return array
     */
	function es_search_get_field_config( $field, $entity_name = 'property' ) {
		$entity = es_get_entity( $entity_name );
		$field = trim( $field );

		$field_config = $entity::get_field_info( $field );

        if ( ! empty( $field_config ) ) {
            $field_config = es_parse_args( $field_config, array(
                'search_settings' => array(
                    'type' => ! empty( $field_config['type'] ) ? $field_config['type'] : null,
	                'wrapper_class' => '',
	                'attributes' => array(
                        'data-single_unit' => '',
                        'data-plural_unit' => '',
                        'multiple' => false,
                    ),
                    'values' => array(),
                ),
                'formatter' => 'default'
            ) );

	        $field_config['type'] = ! empty( $field_config['type'] ) ? $field_config['type'] : $field_config['search_settings']['type'];

            if ( ! empty( $field_config['options'] ) ) {
                $field_config['search_settings']['values'] = $field_config['options'];
            }

			if ( 'select' == $field_config['search_settings']['type'] && empty( $field_config['search_settings']['attributes']['data-placeholder'] ) ) {
				if ( !empty ( $field_config['frontend_visible_name'] ) ) {
					$label = es_mulultilingual_translate_string( $field_config['frontend_visible_name'] );
				} else {
					$label = $field_config['label'];
				}
				$field_config['search_settings']['attributes']['data-placeholder'] = __( 'Choose value', 'es' );
				$field_config['search_settings']['attributes']['search-placeholder'] = ! empty( $field_config['label'] ) ? $label : '';
			}

            if ( ! empty( $field_config['search_settings']['values_callback'] ) ) {
                $callback = $field_config['search_settings']['values_callback'];
                $args = ! empty( $callback['args'] ) ? $callback['args'] : array();
                $values = call_user_func_array( $callback['callback'], $args );
                $field_config['search_settings']['values'] = $values;
	            /** @var Es_Entity $entity_fields */
	            $entity::$entity_fields[ $field ]['search_settings']['values'] = $values;
            }
        }

        return $field_config;
    }
}

/**
 * Return shortcode class name by shortcode name.
 *
 * @param $shortcode_name
 *
 * @return bool|string
 */
function es_get_shortcode_classname( $shortcode_name ) {
    $class_name = ! empty( Es_Shortcodes_List::$_shortcodes[ $shortcode_name ] ) ?
        Es_Shortcodes_List::$_shortcodes[ $shortcode_name ] : false;

    return apply_filters( 'es_get_shortcode_classname', $class_name, $shortcode_name );
}

/**
 * Return shortcode instance.
 *
 * @param $shortcode_name
 * @param array $attributes
 *
 * @return null|Es_Shortcode
 */
function es_get_shortcode_instance( $shortcode_name, $attributes = array() ) {
    $shortcode_classname = es_get_shortcode_classname( $shortcode_name );
    $instance = null;

    if ( ! empty( $shortcode_classname ) ) {
        $instance = new $shortcode_classname( $attributes );
    }

    return apply_filters( 'es_get_shortcode_instance', $instance, $shortcode_name, $attributes );
}

if ( ! function_exists( 'es_get_wishlist_instance' ) ) {

	/**
	 * Return wishlist instance.
	 *
	 * @param $entity_name
	 *
	 * @return Es_Wishlist_Cookie|Es_Wishlist_User
	 */
    function es_get_wishlist_instance( $entity_name = 'property' ) {

        if ( is_user_logged_in() ) {
            $instance =  new Es_Wishlist_User( get_current_user_id(), $entity_name );
        } else {
            $instance = new Es_Wishlist_Cookie( $entity_name );
        }

        return apply_filters( 'es_get_wishlist_instance', $instance );
    }
}

/**
 * @return mixed|void
 */
function es_get_auth_networks_list() {
    return apply_filters( 'es_get_auth_networks_list', array( 'facebook', 'google' ) );
}

/**
 * Return social network auth class instance.
 *
 * @param $network
 * @param array $config
 *
 * @return Es_Authentication
 */
function es_get_auth_instance( $network, $config = array() ) {
    $instance = null;

    switch ( $network ) {
        case 'facebook':
            $instance = new Es_Facebook_Authentication( $config );
            break;

        case 'google':
            $instance = new Es_Google_Authentication( $config );
            break;
    }

    return apply_filters( 'es_get_auth_instance', $instance, $network, $config );
}

/**
 * Return redirect url after success auth.
 *
 * @return mixed|void
 */
function es_get_success_auth_redirect_url() {
	$profile_page_id = ests( 'profile_page_id' );

    if ( $profile_page_id && get_post_status( $profile_page_id ) ) {
        $url = get_permalink( $profile_page_id );
    } else {
        $url = home_url();
    }

    return apply_filters( 'es_get_success_auth_redirect_url', $url );
}

if ( ! function_exists( 'es_get_user_entity' ) ) {

    /**
     * Return user entity.
     *
     * @param null $user_id
     *
     * @return Es_User|null
     */
    function es_get_user_entity( $user_id = null ) {
        $user_id = $user_id ? $user_id : get_current_user_id();

        return $user_id ? new Es_User( $user_id ) : null;
    }
}

/**
 * @return mixed|void
 */
function es_get_search_page_url() {
	$url = null;
	$map_search_page_id = ests( 'map_search_page_id' );
	$search_results_page_id = ests( 'search_results_page_id' );

	if ( $search_results_page_id && get_post_status( $search_results_page_id ) == 'publish' ) {
		$url = es_get_permalink( $search_results_page_id );
	} else if ( $map_search_page_id && get_post_status( $map_search_page_id ) == 'publish' ) {
		$url = es_get_permalink( $map_search_page_id );
	}

	return apply_filters( 'es_get_search_page_url', $url );
}

/**
 * @param null $user_id
 *
 * @return string
 */
if ( ! function_exists( 'es_user_get_default_image_url' ) ) {

	/**
	 * @param null $user_id
	 *
	 * @return string
	 */
	function es_user_get_default_image_url( $user_id = null ) {
		$def_image = ES_PLUGIN_URL . 'public/img/avatar.svg';
		$def_image = apply_filters( 'es_user_get_default_image_url_avatar', $def_image );

		return apply_filters( 'es_user_get_default_image_url', $def_image, $user_id );
	}
}

if ( ! function_exists( 'es_get_entity_tabs' ) ) {

	function es_get_entity_tabs( $entity_name ) {
		$sections_builder = es_get_sections_builder_instance();
		$tabs = array();

		if ( $sections = $sections_builder::get_items( $entity_name ) ) {
			foreach ( $sections as $section_id => $section ) {
				if ( es_can_render_tab( $entity_name, $section_id ) )
					$tabs[ $section_id ] = array(
						'label' => $section['label'],
						'template' => es_locate_template( sprintf( 'admin/' . $entity_name . '/tabs/%s.php', $section_id ) ),
						'action' => 'es_' . $entity_name .  '_metabox_tab',
					);
			}
		}

		return apply_filters( 'es_get_entity_tabs', $tabs, $entity_name );
	}
}

if ( ! function_exists( 'es_can_render_tab' ) ) {

	function es_can_render_tab( $entity_name, $tab_id ) {
		$fields_builder = es_get_fields_builder_instance();
		$fields = $fields_builder::get_tab_fields( $tab_id, $entity_name );
		$can_render = ! empty( $fields ) && es_is_tab_active( $tab_id );

		return apply_filters( 'es_can_render_tab', $can_render, $tab_id, $entity_name );
	}
}

/**
 * @param $post_id
 *
 * @return false|mixed
 */
function et_builder_es_get_property_layout( $post_id ) {
	$post = get_post( $post_id );

	if ( ! $post ) {
		return false;
	}

	return get_post_meta( $post_id, '_et_pb_property_page_layout', true );
}

/**
 * @param $post_id
 *
 * @return bool
 */
function et_builder_is_enabled( $post_id ) {
	return get_post_meta( $post_id, '_et_pb_use_builder', true ) == 'on';
}

/**
 * @return string
 */
function es_et_builder_estatik_get_initial_property_content() {
	$content = '[et_pb_section admin_label="section"]
			[et_pb_row admin_label="row"]
				[et_pb_column type="4_4"][es_single_entity_page][/et_pb_column]
			[/et_pb_row]
		[/et_pb_section]';

	if ( ! empty( $args['existing_shortcode'] ) ) {
		$content = $content . $args['existing_shortcode'];
	}

	return $content;
}

/**
 * @return mixed|void
 */
function es_get_profile_tabs() {
	$tabs = array(
		'saved-homes' => array(
			'template' => es_locate_template( 'front/shortcodes/profile/tabs/saved-homes.php' ),
			'label' => __( 'Saved homes', 'es' ),
			'icon' => "<span class='es-icon es-icon_heart'></span>",
			'id' => 'saved-homes',
		),
		'saved-searches' => array(
			'template' => es_locate_template( 'front/shortcodes/profile/tabs/saved-searches.php' ),
			'label' => __( 'Saved searches', 'es' ),
			'icon' => "<span class='es-icon es-icon_search'></span>",
			'id' => 'saved-searches',
		),
	);

	if ( ! ests( 'is_saved_search_enabled' ) ) {
		unset( $tabs['saved-searches'] );
	}

	if ( ! ests( 'is_properties_wishlist_enabled' ) ) {
		unset( $tabs['saved-homes'] );
	}

	return apply_filters( 'es_profile_get_tabs', $tabs );
}

/**
 * @param $entity_name string
 *
 * @return bool
 */
function es_is_collapsed_description( $entity_name ) {
	$option_name = $entity_name == 'property' ? 'is_collapsed_description_enabled' : sprintf( 'is_%s_collapsed_description_enabled', $entity_name );

	return (bool) ests( $option_name );
}

/**
 * @return mixed|void
 */
function es_get_location_fields() {
	return apply_filters( 'es_get_location_fields', array( 'country', 'state', 'province', 'city' ) );
}