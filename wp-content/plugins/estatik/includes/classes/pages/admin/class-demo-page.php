<?php

/**
 * Class Es_Demo_Page.
 */
class Es_Demo_Page {

    /**
     * @return void
     */
    public static function init() {
        add_action( 'init', array( 'Es_Demo_Page', 'setup_demo' ) );
    }

    /**
     * Setup demo content handler.
     *
     * @return void
     */
    public static function setup_demo() {
        $nonce = filter_input( INPUT_POST, 'es_demo_content' );

        if ( wp_verify_nonce( $nonce, 'es_demo_content' ) ) {
            $settings_container = es_get_settings_container();
            $settings = es_clean( $_POST['es_settings'] );
            $country = es_clean( filter_input( INPUT_POST, 'country' ) );
            $pages = es_clean( $_POST['pages'] );
            $import_listings = es_clean( filter_input( INPUT_POST, 'import_listings' ) );
            $settings['country'] = $country;

            $country_data = ests_values( 'country_settings' );
            $settings_data = ! empty( $country_data[ $country ] ) ? $country_data[ $country ] : array();

            if ( $settings_data ) {
            	$settings = array_merge( $settings_data, $settings );
            } else {
	            unset( $settings['country'] );
            }

            if ( ! empty( $pages ) ) {
                $pages_list = static::get_demo_pages();

                foreach ( $pages as $page ) {
                    if ( ! empty( $pages_list[ $page ] ) ) {
                        $page_id = wp_insert_post( array(
                            'post_title' => $pages_list[ $page ]['title'],
                            'post_content' => $pages_list[ $page ]['content'],
                            'post_type' => 'page',
                            'post_status' => 'publish'
                        ), true );

                        if ( $page_id && ! is_wp_error( $page_id ) ) {
                            if ( ! empty( $pages_list[ $page ]['setting'] ) ) {
                                $key = $pages_list[ $page ]['setting'];
                                $settings[ $key ] = $page_id;
                            }
                        }
                    }
                }
            }

            // Save plugin provided settings.
            if ( ! empty( $settings ) && is_array( $settings ) ) {
                $settings_container->save( $settings );
            }

            if ( ! empty( $import_listings ) ) {
	            $fbs_instance = es_get_sections_builder_instance();

	            $fbs_instance::save_section( array(
		            'label' => 'Nearby Schools',
		            'machine_name' => 'nearby-schools',
		            'entity_name' => 'property',
		            'is_visible' => 1,
		            'order' => 120,
		            'is_visible_for' => array( 'all_users' ),
	            ) );

	            $fb_instance = es_get_fields_builder_instance();

	            $fb_fields = array(
		            array(
			            'label' => 'Cooling features',
			            'machine_name' => 'cooling',
			            'type' => 'text',
			            'section_machine_name' => 'building-details',
			            'tab_machine_name' => 'building-details',
			            'is_visible' => 1,
			            'is_visible_for' => array( 'all_users' ),
			            'order' => 10
		            ),
		            array(
			            'label' => 'Heating features',
			            'machine_name' => 'heating-features',
			            'type' => 'text',
			            'section_machine_name' => 'building-details',
			            'tab_machine_name' => 'building-details',
			            'is_visible' => 1,
			            'is_visible_for' => array( 'all_users' ),
			            'order' => 11
		            ),
		            array(
			            'label' => 'Garage spaces',
			            'machine_name' => 'garage-spaces',
			            'type' => 'number',
			            'section_machine_name' => 'building-details',
			            'tab_machine_name' => 'building-details',
			            'is_visible' => 1,
			            'is_visible_for' => array( 'all_users' ),
			            'order' => 12
		            ),
		            array(
			            'label' => 'Elementary School',
			            'machine_name' => 'elementary-school',
			            'type' => 'text',
			            'section_machine_name' => 'nearby-schools',
			            'tab_machine_name' => 'nearby-schools',
			            'is_visible' => 1,
			            'is_visible_for' => array( 'all_users' ),
			            'order' => 20
		            ),
		            array(
			            'label' => 'Secondary School',
			            'machine_name' => 'secondary-school',
			            'type' => 'text',
			            'section_machine_name' => 'nearby-schools',
			            'tab_machine_name' => 'nearby-schools',
			            'is_visible' => 1,
			            'is_visible_for' => array( 'all_users' ),
			            'order' => 20
		            ),
		            array(
			            'label' => 'High School',
			            'machine_name' => 'high-school',
			            'type' => 'text',
			            'section_machine_name' => 'nearby-schools',
			            'tab_machine_name' => 'nearby-schools',
			            'is_visible' => 1,
			            'is_visible_for' => array( 'all_users' ),
			            'order' => 20
		            ),
	            );

	            foreach ( $fb_fields as $field ) {
		            $fb_instance::save_field( $field );
	            }

                $listings_data = include ES_PLUGIN_INCLUDES . 'demo-content.php';

                if ( ! empty( $listings_data ) ) {
                    foreach ( $listings_data as $listing ) {
                        $post_data = wp_parse_args( $listing['system'], array(
                            'post_type' => 'properties',
                            'post_status' => 'publish',
                        ) );

                        $post_id = wp_insert_post( $post_data, true );

                        if ( ! is_wp_error( $post_id ) ) {
                            $property = es_get_property( $post_id );
                            $property->save_fields( $listing['fields'] );
                        }
                    }
                }
            }

			es_set_demo_as_executed();

            wp_safe_redirect( admin_url( 'edit.php?post_type=properties' ) );
            exit;
        }
    }

    /**
     * Render demo page.
     *
     * @return void
     */
    public static function render() {
        $f = es_framework_instance();
        $f->load_assets();
        wp_enqueue_script( 'es-admin' );
        wp_enqueue_script( 'es-demo', ES_PLUGIN_URL . 'admin/js/demo.min.js', array( 'jquery' ), Estatik::get_version() );
        wp_localize_script( 'es-demo', 'Estatik', array(
        	'country_data' => ests_values( 'country_settings' ),
        ) );
        wp_enqueue_style( 'es-demo', ES_PLUGIN_URL . 'admin/css/demo.min.css', array( 'es-admin' ), Estatik::get_version() );

        es_load_template( 'admin/demo/index.php', array(
            'features' => array(
                __( 'Unlimited download access', 'es' ) => array(
                    'simple' => true,
                    'pro' => true,
                    'premium' => true,
                ),
                __( 'Social sharing', 'es' ) => array(
                    'pro' => true,
                    'premium' => true,
                    'simple' => true,
                ),
                __( 'Wishlists & Saved searches', 'es' ) => array(
                    'pro' => true,
                    'premium' => true,
                    'simple' => true,
                ),
                __( '1 year FREE support for 1 website', 'es' ) => array(
                    'premium' => true,
                ),
                __( '6 months FREE support for 1 website', 'es' ) => array(
                    'pro' => true,
                ),
                __( 'PDF feature', 'es' ) => array(
                    'pro' => true,
                    'premium' => true,
                ),
                __( 'Compare feature', 'es' ) => array(
                    'pro' => true,
                    'premium' => true,
                ),
                __( 'CSV import', 'es' ) => array(
                    'pro' => true,
                    'premium' => true,
                ),
                __( 'Nearby & Walkscore integration', 'es' ) => array(
                    'pro' => true,
                    'premium' => true,
                ),
                __( 'WhatsApp integration', 'es' ) => array(
                    'pro' => true,
                    'premium' => true,
                ),
                __( 'White label', 'es' ) => array(
                    'pro' => true,
                    'premium' => true,
                ),
                __( 'Unlocked Fields Builder', 'es' ) => array(
                    'pro' => true,
                    'premium' => true,
                ),
                __( 'Subscriptions', 'es' ) => array(
                    'pro' => true,
                    'premium' => true,
                ),
                __( 'Agents & Agencies support', 'es' ) => array(
                    'pro' => true,
                    'premium' => true,
                ),
                __( '10+ advanced widgets', 'es' ) => array(
                    'pro' => true,
                    'premium' => true,
                ),
                __( 'MLS import via RETS', 'es' ) => array(
                    'premium' => true,
                ),
            ),
            'products' => array(
                'simple' => array(
                    'label' => _x( 'Simple', 'estatik product', 'es' ),
                    'price' => _x( 'Free', 'estatik simple price', 'es' ),
	                'icon' => '<span class="es-icon es-icon_simple es-icon--rounded es-icon--light"></span>',
                ),
                'pro' => array(
                    'label' => _x( 'PRO', 'estatik product', 'es' ),
                    'link' => 'https://estatik.net/choose-your-version/',
                    'price' => '$89',
	                'icon' => '<span class="es-icon es-icon_pro es-icon--rounded es-icon--light"></span>',
                ),
                'premium' => array(
                    'label' => _x( 'Premium', 'estatik product', 'es' ),
                    'link' => 'https://estatik.net/choose-your-version/',
                    'price' => '$649',
		            'icon' => '<span class="es-icon es-icon_premium es-icon--rounded es-icon--light"></span>',
                ),
            )
        ) );
    }

    /**
     * Return demo content pages list.
     *
     * @return array[]
     */
    public static function get_demo_pages() {
        return array(
            'list-layout' => array(
                'title' => __( 'List layout', 'es' ),
                'content' => '[es_my_listing enable_search="1" layout="list"]',
            ),
            'grid-layout' => array(
                'title' => __( 'Grid layout', 'es' ),
                'content' => '[es_my_listing enable_search="1" layout="grid"]',
            ),
            'map-view' => array(
                'title' => __( 'Map view', 'es' ),
                'content' => '[es_my_listing enable_search="1" layout="half_map"]',
                'setting' => 'map_search_page_id',
            ),
            'search-results' => array(
                'title' => __( 'Search results', 'es' ),
                'content' => '[es_my_listing enable_search="1" ignore_search="0"]',
                'setting' => 'search_results_page_id',
            ),
            'sign-up' => array(
                'title' => __( 'Sign up', 'es' ),
                'content' => '[es_authentication auth_item="buyer-register-buttons"]',
                'setting' => 'buyer_register_page_id',
            ),
            'log-in' => array(
                'title' => __( 'Log in', 'es' ),
                'content' => '[es_authentication auth_item="login-buttons"]',
                'setting' => 'login_page_id',
            ),
            'reset-password' => array(
                'title' => __( 'Reset password', 'es' ),
                'content' => '[es_authentication auth_item="reset-form"]',
                'setting' => 'reset_password_page_id',
            ),
            'profile' => array(
                'title' => __( 'Profile', 'es' ),
                'content' => '[es_profile]',
            ),
        );
    }

}

Es_Demo_Page::init();
