<?php

/**
 * Class Es_Plugin_Migration.
 */
class Es_Plugin_Migration {

	/**
	 * @return void
	 */
	public static function migrate_pages() {
		global $wpdb;
		$replace_pages = $wpdb->get_results( "SELECT `post_content`, `ID` FROM {$wpdb->posts} WHERE `post_content` LIKE '%[es_search %' or `post_content` LIKE '%[es_property_slideshow%'" );

		if ( ! empty( $replace_pages ) ) {
			foreach ( $replace_pages as $page ) {
				$post_content = strtr( $page->post_content, array(
					'[es_property_slideshow ' => '[es_properties_slider ',
					'[es_search ' => '[es_my_listings ',
				) );

				$wpdb->update( $wpdb->posts, array( 'post_content' => $post_content ), array( 'ID' => $page->ID ) );
			}
		}
	}

	/**
	 * @return false|void
	 */
	public static function migrate_mls() {
		global $wpdb;

		$minor_table_exists = $wpdb->get_var( "SHOW TABLES LIKE '{$wpdb->prefix}es_mls_active_fields'" );
		$major_table_exists = $wpdb->get_var( "SHOW TABLES LIKE '{$wpdb->prefix}estatik_mls_active_fields'" );

		if ( ! $minor_table_exists || ! $major_table_exists || get_option( 'es_mls_fields_migrated' ) ) return false;

		$copy_query = $wpdb->query( "INSERT INTO {$wpdb->prefix}estatik_mls_active_fields SELECT * FROM {$wpdb->prefix}es_mls_active_fields;" );

		if ( $copy_query !== false ) {
			update_option( 'es_mls_fields_migrated', 1 );
		}
	}

	/**
	 * Migrate plugin settings handler.
	 *
	 * @return mixed|void
	 */
	public static function migrate_settings() {
		$settings_map = apply_filters( 'es_get_available_settings', array(
			'country' => 'country',
			'area_unit' => 'unit',
			'lot_size_unit' => 'unit',
			'properties_per_page' => 'properties_per_page',
			'is_price_enabled' => 'show_price',
			'is_rest_support_enabled' => 'is_rest_support_enabled',
			'is_single_listing_map_enabled' => 'hide_map',
			'is_auto_tags_enabled' => 'is_tags_enabled',
			'is_clickable_tags_enabled' => 'is_tags_clickable',
			'is_dynamic_content_enabled' => 'is_dynamic_content_enabled',
			'dynamic_content' => 'dynamic_content',
			'is_pdf_map_enabled' => 'is_pdf_map_enabled',
			'admin_listing_added_by_agent_email_subject' => 'agent_draft_email_subject',
			'admin_listing_added_by_agent_email_content' => 'agent_draft_email_body',
			'is_listing_address_enabled' => 'show_address',
			'is_date_added_enabled' => 'date_added',
			'is_clusters_enabled' => 'cluster_enabled',
			'listings_layout' => 'listing_layout',
			'is_labels_enabled' => 'show_labels',
			'currency' => 'currency',
			'price_format' => 'price_format',
			'currency_position' => 'currency_position',
			'title_mode' => 'title_address',
			'date_format' => 'date_format',
			'google_api_key' => 'google_api_key',
			'mls_google_api_key' => 'mls_google_api_key',
			'default_property_image_id' => 'thumbnail_attachment_id',
			'social_networks' => array( 'share_twitter', 'share_facebook', 'share_linkedin' ),
			'is_pdf_enabled' => 'use_pdf',
			'pdf_flyer_layout' => 'pdf_flyer_layout',
			'pdf_map_zoom' => 'pdf_map_zoom',
			'logo_attachment_id' => 'logo_attachment_id',
			'map_marker_icon' => 'markers',
			'map_marker_color' => 'marker_color',
			'default_agent_avatar_attachment_id' => 'thumbnail_attachment_agent_id',
			'currency_sign' => 'currency',

//			'pdf_qr' => array(
//				'default_value' => 1,
//				'values' => array(
//					0 => __( 'Disable', 'es-plugin' ),
//					1 => __( 'Enable', 'es-plugin' ),
//				),
//			),
//
//			'pdf_phone' => array(
//				'default_value' => '',
//			),
//
//			'pdf_email' => array(
//				'default_value' => '',
//			),
//
//			'pdf_address' => array(
//				'default_value' => '',
//			),
			'manual_listing_approve' => 'listings_publishing',
			'login_page_id' => 'login_page_id',
			'property_management_page_id' => 'prop_management_page_id',
			'reset_password_page_id' => 'reset_password_page_id',
			'recaptcha_site_key' => 'recaptcha_site_key',
			'recaptcha_secret_key' => 'recaptcha_secret_key',
			'recaptcha_version' => 'recaptcha_version',
			'fb_property_deleted_fields' => 'property_removed_fields',
			'map_zoom' => 'map_zoom',
			'is_white_label_enabled' => 'enable_white_label',
			'main_color' => 'main_color',
			'secondary_color' => 'secondary_color',
			'property_slug' => 'property_slug',
			'post_type_name' => 'property_name',
			'is_agent_rating_enabled' => 'hide_agent_rating',
			'terms_conditions_page_id' => 'term_of_use_page_id',
			'privacy_policy_page_id' => 'privacy_policy_page_id',

//			'email_logo_attachment_id' => array(
//				'default_value' => '',
//				'validate_callback' => 'intval',
//			),

			'search_results_page_id' => 'search_page_id',
			'profile_page_id' => 'user_profile_page_id',
			'is_properties_wishlist_enabled' => 'is_wishlist_enabled',
			'is_agents_wishlist_enabled' => 'is_wishlist_enabled',
			'is_agencies_wishlist_enabled' => 'is_wishlist_enabled',
			'listing_meta_icons' => 'property_fields_icons',

//			'country_component_types' => array(
//				'default_value' => array( 'country' ),
//				'validate_type' => 'array_strings'
//			),
//
//			'state_component_types' => array(
//				'default_value' => array( 'administrative_area_level_1', 'administrative_area_level_3' ),
//				'validate_type' => 'array_strings'
//			),
//
//			'province_component_types' => array(
//				'default_value' => array( 'administrative_area_level_2' ),
//				'validate_type' => 'array_strings'
//			),
//
//			'city_component_types' => array(
//				'default_value' => array( 'locality', 'postal_town' ),
//				'validate_type' => 'array_strings'
//			),
//
//			'neighborhood_component_types' => array(
//				'default_value' => array( 'neighborhood' ),
//				'validate_type' => 'array_strings'
//			),
//
//			'street_component_types' => array(
//				'default_value' => array( 'street_address', 'route' ),
//				'validate_type' => 'array_strings'
//			),

			'facebook_app_id' => 'facebook_app_id',
			'facebook_app_secret' => 'facebook_app_secret',
			'google_client_key' => 'google_client_key',
			'google_client_secret' => 'google_client_secret',
			'is_login_facebook_enabled' => 'enable_facebook_auth',
			'is_login_google_enabled' => 'enable_google_auth',
			'is_lightbox_disabled' => 'is_lightbox_disabled',
		) );

		foreach ( $settings_map as $major_option => $minor_option ) {
			if ( is_array( $minor_option ) ) continue;

			$minor_option_value = get_option( 'es_' . $minor_option );

			if ( ( is_string( $minor_option_value ) && strlen( $minor_option_value ) ) || ! empty( $minor_option_value ) ) {
				switch ( $major_option ) {
					case 'listings_layout':
						switch( $minor_option_value ) {
							case '2_col';
								ests_save_option( $major_option, 'grid-2' );
								break;
							case '3_col';
								ests_save_option( $major_option, 'grid-3' );
								break;
							default:
								ests_save_option( $major_option, 'list' );
						}
						break;

					case 'is_agent_rating_enabled':
					case 'is_single_listing_map_enabled':
						ests_save_option( $major_option, ! ((bool)$minor_option_value) );
						break;

					case 'price_format':
						$sup = substr( $minor_option_value, 0 ,1 );
						$dec = substr( $minor_option_value, 1 ,1 );
						ests_save_option( 'currency_dec', $dec );
						ests_save_option( 'currency_sup', $sup );
						break;

					case 'social_networks':
						break;

					case 'listing_meta_icons':
						break;

					case 'manual_listing_approve':
						ests_save_option( $major_option, $minor_option_value == 'manual' ? 1 : 0 );
						break;

					default:
						ests_save_option( $major_option, $minor_option_value );
				}
			}
		}
	}

	public static function migrate_labels() {
		$labels = get_terms( array(
			'taxonomy' => 'es_labels',
			'hide_empty' => false,
		) );

		if ( ! empty( $labels ) && ! is_wp_error( $labels ) ) {
			foreach ( $labels as $label ) {
				if ( ! ( $term = term_exists( $label->name, 'es_label' ) ) ) {
					$term = wp_insert_term( $label->name, 'es_label' );
				}

				if ( ! is_wp_error( $term ) && ! empty( $term['term_id'] ) && ( $term_color = get_term_meta( $label->term_id, 'es_color', true ) ) ) {
					update_term_meta( $term['term_id'], 'es_color', $term_color );
				}
			}
		}
	}

	/**
	 * Migrate fields builder handler
	 */
	public static function migrate_fb() {
		global $wpdb;

		$minor_table_exists = $wpdb->get_var( "SHOW TABLES LIKE '{$wpdb->prefix}fbuilder_fields'" );

		if ( $minor_table_exists ) {
			$minor_fields_list = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}fbuilder_fields
														INNER JOIN {$wpdb->prefix}fbuilder_fields_order 
														ON field_machine_name=machine_name" );

			if ( ! empty( $minor_fields_list ) ) {
				$fbuilder_instance = es_get_fields_builder_instance();

				foreach ( $minor_fields_list as $minor_field ) {
					$visible_for = static::prepare_visible_permissions( $minor_field->visible_permission );

					$fbuilder_instance::save_field( array(
						'label' => $minor_field->label,
						'machine_name' => $minor_field->machine_name,
						'type' => static::prepare_field_type( $minor_field->type ),
						'options' => unserialize( $minor_field->options ),
						'values' => static::prepare_field_values( maybe_unserialize( $minor_field->values ), $minor_field ),
						'section_machine_name' => static::prepare_section_name( $minor_field->section ),
						'tab_machine_name' => static::prepare_section_name( $minor_field->section ),
						'is_visible' => 1,
						'is_visible_for' => $visible_for,
						'search_support' => (int)$minor_field->search_support,
						'order' => ! empty( $minor_field->order ) ? $minor_field->order : 0,
						'mls_import_support' => ! empty( $minor_field->mls_support ) ? 1 : 0,
						'entity_name' => 'property',
					) );
				}
			}

			$minor_sections_list = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}fbuilder_sections
														INNER JOIN {$wpdb->prefix}fbuilder_sections_order 
														ON section_machine_name=machine_name" );

			if ( ! empty( $minor_sections_list ) ) {
				$sbuilder_instance = es_get_sections_builder_instance();

				foreach ( $minor_sections_list as $minor_section ) {
					$visible_for = static::prepare_visible_permissions( $minor_section->visible_permission );

					$sbuilder_instance::save_section( array(
						'machine_name' => $minor_section->machine_name,
						'is_visible' => 1,
						'is_visible_for' => $visible_for,
						'label' => $minor_section->label,
						'order' => $minor_section->order,
					) );
				}
			}
		}
	}

	/**
	 * @param $listing_id
	 */
	public static function migrate_listing( $listing_id ) {
		global $wpdb;
		$property = es_get_property( $listing_id );

		$minor_table_exists = $wpdb->get_var( "SHOW TABLES LIKE '{$wpdb->prefix}fbuilder_fields'" );

		if ( $minor_table_exists ) {
			$files_fields = $wpdb->get_col( "SELECT machine_name FROM {$wpdb->prefix}fbuilder_fields WHERE type='file'" );
			$files_fields = $files_fields ? array_merge( $files_fields, array( 'gallery' ) ) : array( 'gallery' );

			foreach ( $files_fields as $field ) {
				$value = get_post_meta( $listing_id, 'es_property_' . $field, true );
				if ( empty( $value ) ) continue;
				$value = is_string( $value ) ? array( $value ) : $value;

				if ( is_array( $value ) && ( $value = array_filter( $value ) ) ) {
					foreach ( $value as $order => $attachment_id ) {
						if ( ! function_exists( 'PLL' ) ) {
							$attachment_id = wp_update_post( array(
								'ID' => $attachment_id,
								'post_parent' => $listing_id,
							), true );
						}

						if ( ! is_wp_error( $attachment_id ) ) {
							update_post_meta( $attachment_id, 'es_attachment_order', $order ) ;
							update_post_meta( $attachment_id, 'es_attachment_type', $field ) ;
						}
					}
				}
			}
		}

		$labels = get_terms( array( 'taxonomy' => 'es_label', 'hide_empty' => false ) );

		if ( $labels && ! is_wp_error( $labels ) ) {
			foreach ( $labels as $label ) {
				if ( get_post_meta( $property->get_id(), 'es_property_' . $label->slug, true ) ) {
					wp_set_object_terms( $listing_id, $label->slug, 'es_label', true );
				}
			}
		}

		if ( $address_components = get_post_meta( $listing_id, 'es_property_address_components', true ) ) {
			$property->save_field_value( 'address_components', $address_components );
		}

		do_action( 'save_post', $listing_id, $property->get_wp_entity(), true );
	}

	/**
	 * @param $user_id
	 */
	public static function migrate_buyer( $user_id ) {
		$user = get_user_by( 'id', $user_id );

		if ( $user instanceof WP_User ) {
			$user->add_role( 'subscriber' );

			$name = get_user_meta( $user_id, 'es_buyer_name', true );

			if ( $name ) {
				update_user_meta( $user_id, 'es_name', $name );
			}

			$profile_attachment_id = get_user_meta( $user_id, 'es_buyer_profile_attachment_id', true );

			if ( $profile_attachment_id ) {
				update_user_meta( $user_id, 'es_avatar_id', $profile_attachment_id );
			}
		}
	}

	/**
	 * @param $name
	 *
	 * @return array|mixed|string|string[]
	 */
	public static function prepare_section_name( $name ) {
		switch ( $name ) {
			case 'es-info':
				$name = 'basic-facts';
				break;

			case 'es-map':
				$name = 'location';
				break;

			case 'es-features':
			case 'es-description':
			case 'es-video':
				$name = str_replace( 'es-', '', $name );
				break;
		}

		return $name;
	}

	/**
	 * @param $visible_permission
	 *
	 * @return string[]
	 */
	public static function prepare_visible_permissions( $visible_permission ) {
		$visible_for = array( 'all_users' );

		if ( $visible_permission == 'es_fb_admins_field_visible' ) {
			$visible_for = array( 'admin' );
		}

		if ( $visible_permission == 'es_fb_agents_field_visible' ) {
			$visible_for = array( 'agents', 'admin' );
		}

		return $visible_for;
	}

	/**
	 * @param $type
	 *
	 * @return mixed|string
	 */
	public static function prepare_field_type( $type ) {
		switch ( $type ) {
			case 'file':
				$type = 'media';
				break;

			case 'list':
				$type = 'select';
				break;

			case 'url':
				$type = 'link';
				break;

			case 'wp_editor':
				$type = 'editor';
				break;
		}

		return $type;
	}

	/**
	 * @param $values
	 * @param $minor_field
	 *
	 * @return mixed
	 */
	public static function prepare_field_values( $values, $minor_field ) {
		if ( ! empty( $values ) && $minor_field->type == 'list' ) {
			$res_values = array();
			foreach ( $values as $value ) {
				$res_values[] = array( 'id' => '', 'value' => $value );
			}

			return $res_values;
		}

		return $values;
	}
}