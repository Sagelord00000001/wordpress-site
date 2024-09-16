<?php

/**
 * Class Es_Assets
 */
class Es_Assets {

	/**
	 * Init plugin assets.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'wp_enqueue_scripts', array( 'Es_Assets', 'register_global_assets' ) );
		add_action( 'admin_enqueue_scripts', array( 'Es_Assets', 'register_global_assets' ) );

		add_action( 'wp_enqueue_scripts', array( 'Es_Assets', 'frontend_assets' ) );
		add_action( 'admin_enqueue_scripts', array( 'Es_Assets', 'admin_assets' ), 9 );

		add_filter( 'script_loader_src', array( 'Es_Assets', 'alter_asset_url' ), 10, 2 );

		//if ( is_admin() && empty( $_GET['legacy-widget-preview'] ) ) return;
	}

	public static function admin_assets() {
		wp_register_script( 'es-admin', ES_PLUGIN_URL . 'admin/js/admin.min.js', array( 'jquery', 'clipboard', 'es-select2' ), Estatik::get_version() );
		wp_register_style( 'es-admin', ES_PLUGIN_URL . 'admin/css/admin.min.css', array(), Estatik::get_version() );

		wp_localize_script( 'es-admin', 'Estatik', array(
			'nonces' => array(
				'dismiss_notice_nonce' => wp_create_nonce( 'es_dismiss_notices' ),
				'save_field_nonce' => wp_create_nonce( 'es_save_field' ),
				'get_terms_creator_nonce' => wp_create_nonce( 'es_get_terms_creator' ),
				'quick_edit_nonce' => wp_create_nonce( 'es_property_quick_edit_form' ),
				'quick_edit_bulk_nonce' => wp_create_nonce( 'es_property_quick_edit_bulk_form' ),
				'nonce_locations' => wp_create_nonce( 'es_get_locations' ),
			),
		) );

		$deps = array( 'jquery', 'es-select2', 'es-admin' );

		if ( ests( 'google_api_key' ) ) {
			$deps[] = 'es-googlemap-api';
		}

		wp_register_script( 'es-property-metabox', ES_PLUGIN_URL . 'admin/js/property-metabox.min.js', $deps, Estatik::get_version() );

		global $pagenow;

		if ( 'widgets.php' === $pagenow || ( $pagenow == 'edit-tags.php' && ! empty( $_GET['taxonomy'] ) && $_GET['taxonomy'] == 'es_location' ) ) {
			$f = es_framework_instance(); $f->load_assets();
			wp_enqueue_style( 'es-admin' );
			wp_enqueue_style( 'es-select2' );
			wp_enqueue_script( 'es-admin' );

			wp_enqueue_style( 'es-locations', ES_PLUGIN_URL . 'admin/css/locations.min.css', array(), Estatik::get_version() );
		} else {
			wp_enqueue_script( 'es-admin' );
		}
	}

	/**
	 * Register global plugin assets.
	 *
	 * @return void
	 */
	public static function register_global_assets() {
		$public = ES_PLUGIN_URL . 'public';
		$common = ES_PLUGIN_URL . 'common';

		// Select2
		wp_register_script( 'es-select2', $common . '/select2/select2.full.min.js', array( 'jquery' ), Estatik::get_version() );
		wp_register_style( 'es-select2', $common . '/select2/select2.min.css', array(), Estatik::get_version()  );

		wp_register_style( 'estatik-popup', $common . '/estatik-popup/estatik-popup.css', array(), Estatik::get_version() );
		wp_register_script( 'estatik-popup', $common . '/estatik-popup/estatik-popup.js', array( 'jquery' ), Estatik::get_version() );

		wp_register_style( 'estatik-progress', $common . '/estatik-progress/estatik-progress.css', array(), Estatik::get_version() );
		wp_register_script( 'estatik-progress', $common . '/estatik-progress/estatik-progress.js', array( 'jquery' ), Estatik::get_version() );

		wp_register_script( 'es-slick', $common . '/slick/slick-fixed.min.js', array( 'jquery' ), Estatik::get_version() );
		wp_register_style( 'es-slick', $common . '/slick/slick.min.css', array(), Estatik::get_version() );

		es_framework_instance()->load_scripts();

		// Init google map API script.
		if ( ests( 'google_api_key' ) ) {
			$api_deps = array();

			wp_register_script( 'es-googlemap-osm', $public . '/js/osm.min.js', array(), Estatik::get_version() );
			wp_register_script( 'es-googlemap-popup', $public . '/js/gm-popup.min.js', array(), Estatik::get_version() );
			wp_register_script( 'es-googlemap-clusters-api', $public . '/js/markerclustererplus.min.js', array(), Estatik::get_version() );

			if ( ests( 'is_clusters_enabled' ) ) {
				$api_deps[] = 'es-googlemap-clusters-api';
			}

			// Google map API.
			wp_register_script(
				'es-googlemap-api',
				'https://maps.googleapis.com/maps/api/js?key=' . ests( 'google_api_key' ) . '&libraries=places&callback=Function.prototype&language=' . es_get_gmap_locale(),
				$api_deps
			);
		}
	}

	public static function frontend_assets() {
		$public = ES_PLUGIN_URL . 'public';
		$common = ES_PLUGIN_URL . 'common';

		wp_register_script( 'es-share-script', 'https://static.addtoany.com/menu/page.js', array(), null, true );
		wp_register_script( 'es-magnific', $common . '/magnific-popup/jquery.magnific-popup.min.js', array( 'jquery' ), Estatik::get_version() );
		wp_register_style( 'es-magnific', $common . '/magnific-popup/magnific-popup.min.css', array(), Estatik::get_version() );

		if ( ests( 'google_api_key' ) ) {
			$public_deps[] = 'es-googlemap-osm';
			$public_deps[] = 'es-googlemap-api';
		}

		$public_deps[] = 'es-magnific';
		$public_deps[] = 'es-select2';
		$public_deps[] = 'es-slick';

		if ( ests( 'google_api_key' ) ) {
			$public_deps[] = 'es-googlemap-popup';
		}

		if ( ests( 'is_link_sharing_enabled' ) ) {
			$public_deps[] = 'clipboard';
		}

		wp_register_script( 'es-frontend', $public . '/js/public.min.js', $public_deps, Estatik::get_version() );
		wp_enqueue_script( 'es-frontend' );

		wp_register_script( 'es-properties', ES_PLUGIN_URL . 'public/js/ajax-entities.min.js', array( 'es-frontend' ), Estatik::get_version() );
		wp_enqueue_script( 'es-properties' );
		wp_enqueue_script( 'es-share-script' );

		$localize = array(
			'tr' => es_js_get_translations(),
			'nonce' => array(
				'saved_search' => wp_create_nonce( 'es_remove_saved_search' ),
				'get_locations' => wp_create_nonce( 'es_get_locations' ),
				'delete_property_popup' => wp_create_nonce( 'es_management_delete_property_popup' ),
				'nonce_locations' => wp_create_nonce( 'es_get_locations' ),
			),
			'settings' => array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'recaptcha_version' => ests( 'recaptcha_version' ),
				'recaptcha_site_key' => ests( 'recaptcha_site_key' ),
				'is_cluster_enabled' => ests( 'is_clusters_enabled' ),
				'map_cluster_icon' => ests( 'map_cluster_icon' ),
				'map_cluster_icons' => ests_values( 'map_cluster_icon' ),
				'map_cluster_color' => ests( 'map_cluster_color' ),
				'map_marker_color' => ests( 'map_marker_color' ),
				'map_marker_icon' => ests( 'map_marker_icon' ),
				'map_marker_icons' => ests_values( 'map_marker_icon' ),
				'address_autocomplete_enabled' => ests( 'is_locations_autocomplete_enabled' ),
				'map_zoom' => ests( 'map_zoom' ),
				'single_property_map_zoom' => ests( 'single_property_map_zoom' ),
				'responsive_breakpoints' => ests( 'responsive_breakpoints' ),
				'listings_offset_corrector' => ests( 'listings_offset_corrector' ),
				'main_color' => ests( 'main_color' ),
				'search_locations_init_priority' => es_get_location_fields(),
				'request_form_geolocation_enabled' => ests( 'is_request_form_geolocation_enabled' ),
				'country' => ests( 'country' ),
				'grid_layout' => es_get_active_grid_layout(),
				'currency' => ests( 'currency' ),
				'currency_dec' => ests( 'currency_dec' ),
				'currency_sup' => ests( 'currency_sup' ),
				'currency_position' => ests( 'currency_position' ),
				'currency_sign' => ests_label( 'currency_sign' ),
				'map_marker_type' => ests( 'map_marker_type' ),
				'is_lightbox_disabled' => ests( 'is_lightbox_disabled' ),
				'hfm_toggle_sidebar' => true,
				'hfm_toggle_sidebar_selector' => '#right-sidebar, #left-sidebar, .sidebar, #sidebar, #secondary, .js-es-hfm-sidebar-toggle',
				'is_rtl' => is_rtl(),
			)
		);

		if ( ! empty( ests( 'default_lat_lng' ) ) ) {
			$lat_lng = array_map( 'trim', explode( ',', ests( 'default_lat_lng' ) ) );

			if ( isset( $lat_lng[0] ) && strlen( $lat_lng[0] ) && isset( $lat_lng[1] ) && strlen( $lat_lng[1] ) ) {
				$localize['settings']['default_lat_lng'] = $lat_lng;
			}
		}

		if (! empty( ests( 'is_request_form_geolocation_enabled' ) ) ) {
			if (! empty( $_SERVER["REMOTE_ADDR"] ) ) {
				$ip = $_SERVER["REMOTE_ADDR"];
				if (! empty( $ip ) ) {
					//$xml = simplexml_load_file("http://www.geoplugin.net/xml.gp?ip=".$ip);
					if (! empty ( $xml->geoplugin_countryCode ) ) {
						$localize['settings']['country'] = $xml->geoplugin_countryCode;
					}
				}
			}
		}

		ob_start();
		do_action( 'es_property_control', array(
			'show_sharing' => true,
			'is_full' => false,
			'icon_size' => 'medium'
		) );
		$control = ob_get_clean();
		$localize['single']['control'] = $control;
		$localize['search']['fields'] = array_keys( es_get_available_search_fields() );

		wp_localize_script( 'es-frontend', 'Estatik', apply_filters( 'es_front_script_localize', $localize ) );

		$fonts = es_get_google_fonts();
		$main_color = ests( 'main_color' );
		$secondary_color = ests( 'secondary_color' );

		list($sr, $sg, $sb) = sscanf( ests( 'secondary_color' ), "#%02x%02x%02x" );
		list($pr, $pg, $pb) = sscanf( ests( 'main_color' ), "#%02x%02x%02x" );

		$styles = "
        .es-btn.es-btn--primary, .es-btn--primary[type=submit], button.es-btn--primary, a.es-btn--primary {
            border-color: {$main_color};
        }
        
        .es-price-marker--active:after {
            border-top-color: {$main_color};
        }
        
        .es-btn.es-btn--primary:not(.es-btn--bordered):not(:hover):not(:active),
        .es-btn.es-btn--primary:not(.es-btn--bordered):hover,
        .es-price-marker--active,
        .xdsoft_datetimepicker .xdsoft_calendar td.xdsoft_default, .xdsoft_datetimepicker .xdsoft_calendar td.xdsoft_current, .xdsoft_datetimepicker .xdsoft_timepicker .xdsoft_time_box>div>div.xdsoft_current {
            background-color: {$main_color};
        }
        
        .es-btn.es-btn--primary.es-btn--bordered,
        .es-btn.es-btn--active .es-icon.es-icon_heart,
        .es-wishlist-link.es-wishlist-link--active .es-icon {
            color: {$main_color};
        }
        
        button.es-btn--secondary:disabled, .es-btn.es-btn--secondary, .es-btn--secondary[type=submit], button.es-btn--secondary, a.es-btn--secondary {
            border-color: {$secondary_color};
        }
        
        .es-secondary-bg {
            background-color: {$secondary_color};
        }
        
        .es-primary-bg {
            background-color: {$main_color};
        }
        
        
        .es-btn.es-btn--secondary:not(.es-btn--bordered), .es-field .select2 .select2-selection__choice {
            background-color: {$secondary_color};
        }
        
        .xdsoft_datetimepicker .xdsoft_calendar td:hover, .xdsoft_datetimepicker .xdsoft_timepicker .xdsoft_time_box>div>div:hover {
            background-color: {$secondary_color}!important;
        }
        
        .es-btn.es-btn--secondary.es-btn--bordered, 
        .es-btn.es-btn--default:hover:not([disabled]):not(.es-btn--disabled),
        .es-btn.es-btn--icon:hover:not([disabled]):not(.es-btn--disabled) .es-icon,
        .xdsoft_datetimepicker .xdsoft_calendar td.xdsoft_today,
        .es-property-field--post_content .es-property-field__value a,
        .es-dymanic-content a,
        .es-hit-limit a, button.es-slick-arrow:not(.slick-disabled):hover {
            color: {$secondary_color};
            background-color: transparent;
        }
        
        .es-btn:hover:not([disabled]):not(.es-btn--disabled) .es-icon.es-icon_heart, .entity-box__delete:hover {
            color: {$main_color}!important;
        }
        
        .es-select2__dropdown .select2-results__option--highlighted[aria-selected],
        .es-field.es-field--checkbox input:checked, .widget .es-field.es-field--checkbox input:checked,
        .es-field.es-field--radio input:checked, .es-bg-secondary,
        .es-property-management--form .es-tabs__nav li:hover .es-tabs__numeric,
        .es-property-management--form .es-tabs__nav li.active .es-tabs__numeric {
            background-color: {$secondary_color};
        }
        
        .es-pagination ul li a.page-numbers:hover {
            border: 2px solid {$secondary_color};
            color: {$secondary_color};
        }

        .es-field--radio-item-bordered:hover input + label, .es-field--checkbox-item-bordered:hover input + label {
            border-color:rgba($sr, $sg, $sb, 0.4);
        }

        .es-field--radio-item-bordered input:checked + label,
        .widget .es-field--radio-item-bordered input:checked + label,
        .es-field--checkbox-item-bordered input:checked + label,
        .widget .es-field--checkbox-item-bordered input:checked + label,
        .es-field.es-field--checkbox input:checked,
        .es-field.es-field--radio input:checked,
        body .es-field textarea:focus, body .es-field.es-field--select select:focus, body .es-field input[type=email]:focus, body .es-field input[type=text]:focus, body .es-field input[type=password]:focus, body .es-field input[type=number]:focus {
            border-color:{$secondary_color};
        }

        .es-field--radio-item-bordered input:checked + label .es-icon,
        .es-field--checkbox-item-bordered input:checked + label .es-icon,
        .es-field a.es-field__show-more,
        .es-section__content p a,
        .es-secondary-color,
        a.es-secondary-color:active,
        a.es-secondary-color:hover,
        a.es-secondary-color,
        .es-profile__menu a:hover,
        .widget .es-secondary-color,
         a.es-secondary-color,
         a.es-secondary-color.es-toggle-pwd,
         a.es-secondary-color-hover:hover,
        .es-property-field__value a:hover,
        .es-agent-field__value a,
        .es-privacy-policy-container a,
        .es-auth a:not(.es-btn),
        .es-powered a,
        .es-preferred-contact--whatsapp a {
            color:{$secondary_color};
        }";

		$fields = ests( 'listing_meta_icons' );
		if ( ! empty( $fields ) ) {
			foreach ( $fields as $field ) {
				if ( ! empty( $field['icon_color'] ) && ! empty( $field['enabled'] ) ) {
					$styles .= ".es-listing__meta-{$field['field']} path {fill: {$field['icon_color']}}";
				}
			}
		}

		if ( $fonts ) {
			$fonts_style_css = '';
			foreach ( array( ests( 'headings_font' ), ests( 'content_font' ) ) as $font ) {
				if ( $font = wp_list_filter( $fonts, array( 'family' => $font ) ) ) {
					/** @var stdClass $font */
					$font = reset( $font );
					$font = (array) $font;
					$font_url = "https://fonts.googleapis.com/css2?family={$font['family']}:wght@300;400;700&display=swap";

					if ( ests( 'is_download_font' ) ) {
						// If font is not downloaded yet.
						if ( ! ( $fonts_attachments = static::get_fonts_attachments_ids( $font['family'] ) ) ) {
							if ( ! function_exists( 'download_url' ) ) {
								require_once ABSPATH . 'wp-admin/includes/file.php';
							}

							foreach ( array( 300, 400, 'regular', 700 ) as $style ) {
								$style_font_url = ! empty( $font['files'][ $style ] ) ? $font['files'][ $style ] : false;

								if ( ! $style_font_url ) continue;

								$file = array();
								$file['name'] = basename( $style_font_url );
								$file['tmp_name'] = download_url( $style_font_url );

								if ( ! is_wp_error( $file['tmp_name'] ) ) {
									$attachment_id = media_handle_sideload( $file );

									if ( ! is_wp_error( $attachment_id ) ) {
										update_post_meta( $attachment_id, 'es_font_family', $font['family'] );
										update_post_meta( $attachment_id, 'es_font_style', $style );
									}
								}
							}

							$fonts_attachments = static::get_fonts_attachments_ids( $font['family'] );
						}

						// Load and enqueue downloaded fonts
						if ( ! empty( $fonts_attachments ) ) {
							foreach ( $fonts_attachments as $font_attachment ) {
								$weight = get_post_meta( $font_attachment, 'es_font_style', true );
								$weight = $weight == 'regular' ? 400 : $weight;
								$url = wp_get_attachment_url( $font_attachment );
								$fonts_style_css .= "@font-face {font-family: '{$font['family']}'; font-style: normal; font-weight: {$weight}; src: url({$url}) format('woff2');}";
							}
						}
					} else {
						wp_enqueue_style( "es-font-{$font['family']}", $font_url );
					}
				}
			}

			$content_font = ests( 'content_font' );
			$headings_font = ests( 'headings_font' );

			$styles .= ".es-media, .es-file, .es-listing, .es-agent-single, .es-agency-single, .es-widget-wrap *:not(.es-icon):not(.fa):not(.heading-font),
            .es-select2__dropdown, .es-single, .es-btn, button.es-btn[disabled]:hover .mfp-wrap.es-property-magnific,
            .es-field input, .es-field select, .es-field textarea, .es-magnific-popup:not(.es-icon),
            .es-magnific-popup:not(.fa), .es-listings-filter, .es-search, .content-font, .es-profile,
            .es-property-magnific .mfp-counter, .es-property-magnific .mfp-title,
            .xdsoft_datetimepicker, .es-component, .es-auth, .es-entity, .es-entities--grid .es-entity .es-entity__title,
            .es-review-form, .es-review-form .es-field__label, .es-field .es-field__strlen, .es-entities-list {
                font-family: '{$content_font}', sans-serif;
            }
            .es-listing h1, .es-listing h2, .es-listing h3, .es-listing h4, .es-listing h5, .es-listing h6,
            .es-search h2, .es-search h3, .es-search h4, .es-search h5, .es-search h6, .heading-font,
            .es-price, .es-property-section .es-property-section__title,
            .es-entity-section__title,
            .widget .es-widget-wrap .es-widget__title, .es-widget__title,
            .es-magnific-popup h1, .es-magnific-popup h2, .es-magnific-popup h3, .es-magnific-popup h4,
            .es-magnific-popup h5, .es-magnific-popup h6, .es-entity .es-entity__title,
            .es-review-form h3.es-review-form__title {
                font-family: '$headings_font';
            }
            ";
		}

		wp_enqueue_style( 'es-frontend', $public . '/css/public.min.css', array( 'es-select2', 'es-slick', 'es-magnific' ), Estatik::get_version() );
		if ( ! empty( $fonts_style_css ) ) {
			wp_add_inline_style( 'es-frontend', $fonts_style_css );
		}
		wp_add_inline_style( 'es-frontend', $styles );
	}

	/**
	 * @param $src
	 * @param $handle
	 *
	 * @return mixed|string
	 */
	public static function alter_asset_url( $src, $handle ) {
		$common = ES_PLUGIN_URL . 'common';
		// Only filter the specific script we want.
		if ( 'slick' === $handle ) {
			// Add the argument to the exisitng URL.
			$src = $common . '/slick/slick-fixed.min.js';
		}

		// Return the filtered URL.
		return $src;
	}

	/**
	 * @param $font
	 *
	 * @return array
	 */
	public static function get_fonts_attachments_ids( $font_family ) {
		global $wpdb;

		return $wpdb->get_col( $wpdb->prepare( "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key='es_font_family' AND meta_value='%s' GROUP BY post_id", $font_family ) );
	}
}

Es_Assets::init();
