<?php

/**
 * Class Es_Settings_Page
 */
class Es_Settings_Page {

	/**
	 * Initialize settings page.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'admin_menu', array( 'Es_Settings_Page', 'save_settings_handler' ) );
		add_action( 'wp_ajax_es_settings_create_page', array( 'Es_Settings_Page', 'create_recommended_page' ) );
		add_action( 'wp_ajax_es_save_settings', array( 'Es_Settings_Page', 'ajax_save_settings' ) );
	}

	/**
	 * Create recommended page via ajax.
	 *
	 * @return void
	 */
	public static function create_recommended_page() {

		if ( check_ajax_referer( 'es_settings_create_page', '_wpnonce' ) ) {
			if ( current_user_can( 'manage_options' ) ) {
			    $title = sanitize_text_field( $_POST['page_name'] );
				$post_id = wp_insert_post( array(
					'post_title' => $title,
					'post_content' => sanitize_text_field( $_POST['page_content'] ),
					'post_type' => 'page',
					'post_status' => 'publish',
				), true );

				if ( ! is_wp_error( $post_id ) ) {
					ests_save_option( sanitize_text_field( $_POST['field'] ), $post_id );
					/* translators: %s: page name. */
					$response = es_notification_ajax_response( sprintf( __( 'Page <b>%s</b> successfully created.', 'es' ), $title ), 'success' );
				} else {
					$response = es_notification_ajax_response(  $post_id->get_error_message(), 'error' );
				}
			} else {
				$response = es_notification_ajax_response( __( 'You have no permissions for this action', 'es' ), 'error' );
			}
		} else {
		    $response = es_ajax_invalid_nonce_response();
        }

        wp_die( json_encode( $response ) );
	}

	/**
	 * Render settings page.
	 *
	 * @return void
	 */
	public static function render() {
        $f = es_framework_instance();
        $f->load_assets();

		wp_enqueue_style( 'wp-color-picker' );

		wp_enqueue_style( 'es-admin-settings', plugin_dir_url( ES_FILE ) . 'admin/css/settings.min.css', array( 'es-admin', 'es-select2' ), Estatik::get_version() );
		wp_enqueue_script( 'es-admin-settings', plugin_dir_url( ES_FILE ) . 'admin/js/settings.min.js', array( 'jquery', 'es-admin' ), Estatik::get_version() );

		wp_localize_script( 'es-admin-settings', 'Estatik_Settings', array(
			'save_nonce' => wp_create_nonce( 'es_save_settings' )
		) );

		$tabs = apply_filters( 'es_settings_page_tabs', array(
			'general' => array(
				'label' => _x( 'General', 'plugin settings', 'es' ),
				'template' => es_locate_template( 'admin/settings/tabs/general-tab.php' ),
			),
			'map' => array(
				'label' => _x( 'Map', 'plugin settings', 'es' ),
				'template' => es_locate_template( 'admin/settings/tabs/map-tab.php' )
			),
			'google-recaptcha' => array(
				'label' => _x( 'Google reCAPTCHA', 'plugin settings', 'es' ),
				'template' => es_locate_template( 'admin/settings/tabs/recaptcha-tab.php' )
			),
			'listings' => array(
				'label' => _x( 'Listings', 'plugin settings', 'es' ),
				'template' => es_locate_template( 'admin/settings/tabs/listings-tab.php' )
			),
			'listing-search' => array(
				'label' => __( 'Listing search', 'es' ),
				'template' => es_locate_template( 'admin/settings/tabs/listing-search-tab.php' )
			),
			'profile' => array(
				'label' => _x( 'User profile', 'plugin settings', 'es' ),
				'template' => es_locate_template( 'admin/settings/tabs/profile-tab.php' )
			),
			'auth' => array(
				'label' => _x( 'Log in & Sign up', 'plugin settings', 'es' ),
				'template' => es_locate_template( 'admin/settings/tabs/auth-tab.php' )
			),
            'seo' => array(
                'label' => _x( 'SEO', 'plugin settings', 'es' ),
                'template' => es_locate_template( 'admin/settings/tabs/seo-tab.php' )
            ),
			'sharing' => array(
				'label' => _x( 'Sharing', 'plugin settings', 'es' ),
				'template' => es_locate_template( 'admin/settings/tabs/sharing-tab.php' )
			),
			'slugs' => array(
				'label' => _x( 'URL slug', 'plugin settings', 'es' ),
				'template' => es_locate_template( 'admin/settings/tabs/slugs-tab.php' )
			),

			'terms' => array(
				'label' => _x( 'Privacy policy & Terms of use', 'plugin settings', 'es' ),
				'template' => es_locate_template( 'admin/settings/tabs/terms-tab.php' )
			),
		) );

		es_load_template( 'admin/settings/index.php', array(
			'tabs' => $tabs,
		) );
	}

	/**
	 * Save settings via ajax
	 *
	 * @return void
	 */
	public static function ajax_save_settings() {
		if ( check_ajax_referer( 'es_save_settings', '_wpnonce', false ) ) {

			if ( current_user_can( 'manage_options' ) ) {
				$settings = new Es_Settings_Container();
				$settings->save( $_POST['es_settings'] );

				$response = array(
					'status' => 'success',
					'message' => es_get_notification_markup( __( 'All changes saved.', 'es' ) ),
				);
			} else {
				$response = array(
					'status' => 'error',
					'message' => es_get_notification_markup(
						__( "You don't have permissions for this action.", 'es' ),
						'error'
					),
				);
			}
		} else {
			$response = array(
				'status' => 'error',
				'message' => es_get_notification_markup(
					__( "Settings didn\'t saved. Please, reload the page and try again.", 'es' ),
					'error'
				),
			);
		}

		wp_die( json_encode( $response ) );
	}

	/**
	 * Save settings page handler.
	 *
	 * @return void
	 */
	public static function save_settings_handler() {

		if ( wp_verify_nonce( es_get_nonce(), 'es_save_settings' ) && current_user_can( 'manage_options' ) ) {
			$settings = new Es_Settings_Container();
			$settings->save( $_POST['es_settings'] );
		}
	}
}

Es_Settings_Page::init();
