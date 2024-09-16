<?php

/**
 * Class Es_Data_Manager_Page.
 */
class Es_Data_Manager_Page {

	static $nonce_arg = 'data_manager_nonce';

	/**
	 * Initialize data manager actions.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'es_data_manager_content', array( 'Es_Data_Manager_Page', 'render_tab_content' ) );
		add_action( 'wp_ajax_es_terms_creator_add_term', array( 'Es_Data_Manager_Page', 'ajax_save_term' ) );
		add_action( 'wp_ajax_es_data_manager_delete_terms', array( 'Es_Data_Manager_Page', 'ajax_delete_terms' ) );
		add_action( 'wp_ajax_es_data_manager_get_form', array( 'Es_Data_Manager_Page', 'ajax_get_form' ) );
		add_action( 'wp_ajax_es_data_manager_get_creator', array( 'Es_Data_Manager_Page', 'ajax_get_creator' ) );
		add_action( 'wp_ajax_es_data_manager_get_list', array( 'Es_Data_Manager_Page', 'ajax_get_list' ) );
		add_action( 'wp_ajax_es_data_manager_restore_term', array( 'Es_Data_Manager_Page', 'ajax_restore_term' ) );
	}

    /**
     * Return locations config for build terms creator and tabs.
     *
     * @return mixed|void
     */
	public static function get_locations_config() {
		$country = es_property_get_field_info( 'country' );
		$state = es_property_get_field_info( 'state' );
		$city = es_property_get_field_info( 'city' );
		$province = es_property_get_field_info( 'province' );

	    $fields = apply_filters( 'es_date_manager_get_locations_config', array(
	        'countries' => array(
                'id' => 'countries',
				'field' => 'country',
	            'label' => __( 'Countries', 'es' ),
	            'components' => array( $country['address_component'] ),
                'dependencies' => array( 'states', 'provinces' ),
                'taxonomy' => 'es_location',
            ),
            'states' => array(
                'id' => 'states',
				'field' => 'state',
                'label' => __( 'States', 'es' ),
                'components' => array( $state['address_component'] ),
                'dependencies' => array( 'provinces', 'cities' ),
                'taxonomy' => 'es_location',
            ),
            'provinces' => array(
                'id' => 'provinces',
				'field' => 'province',
                'label' => __( 'Provinces', 'es' ),
                'components' => array( $province['address_component'] ),
                'dependencies' => array( 'cities' ),
                'taxonomy' => 'es_location',
            ),
            'cities' => array(
                'id' => 'cities',
				'field' => 'city',
                'label' => __( 'Cities', 'es' ),
                'components' => array( $city['address_component'] ),
                'taxonomy' => 'es_location',
            ),
        ) );

		foreach ( $fields as $field => $config ) {
			if ( ! es_is_property_field_active( $config['field'] ) ) {
				unset( $fields[ $field ] );
			}
		}

		if ( ! empty( $fields ) ) {
			$firstKey = array_key_first( $fields );
			$fields[ $firstKey ]['initial'] = true;
		}

		return apply_filters( 'es_date_manager_get_locations_config', $fields );
    }

	/**
	 * Restore default term via ajax.
	 */
	public static function ajax_restore_term() {
		$action = 'es_data_manager_restore_term';

		if ( check_ajax_referer( $action, static::$nonce_arg, false ) ) {
			$term_id = intval( filter_input( INPUT_POST, 'term' ) );
			es_activate_term( $term_id );

			$response = array(
				'status' => 'success',
			);
		} else {
			$response = es_ajax_invalid_nonce_response();
		}

		wp_die( json_encode( $response ) );
	}

    /**
     * @return void
     */
	public static function ajax_get_creator() {
        $action = 'es_data_manager_get_creator';

        if ( check_ajax_referer( $action, static::$nonce_arg, false ) ) {
            $data = es_clean( $_GET );

            if ( $data['taxonomy'] == 'es_location' ) {
                $config = static::get_locations_config();
                $config = $config[ $data['dep'] ];
                $config['parent_id'] = $data['parent_id'];
                $term_creator = es_get_terms_creator_factory( $data['taxonomy'], $config );
            } else {
                $term_creator = es_get_terms_creator_factory( $data['taxonomy'] );
            }

            ob_start();
            $term_creator->render();

            $response = array(
                'status' => 'success',
                'content' => ob_get_clean(),
            );
        } else {
            $response = es_ajax_invalid_nonce_response();
        }

        wp_die( json_encode( $response ) );
    }

	/**
	 * Return terms list for creator component.
	 *
	 * @return void
	 */
	public static function ajax_get_list() {
		$action = 'es_data_manager_get_list';

		if ( check_ajax_referer( $action, static::$nonce_arg, false ) ) {
			$data = es_clean( $_GET );

			if ( $data['taxonomy'] == 'es_location' ) {
			    $config = static::get_locations_config();
			    $config = $config[ $data['dep'] ];
			    $config['parent_id'] = $data['parent_id'];
                $term_creator = es_get_terms_creator_factory( $data['taxonomy'], $config );
            } else {
                $term_creator = es_get_terms_creator_factory( $data['taxonomy'] );
            }

			ob_start();
			$term_creator->render_list();

			$response = array(
				'status' => 'success',
				'content' => ob_get_clean(),
			);
		} else {
			$response = es_ajax_invalid_nonce_response();
		}

		wp_die( json_encode( $response ) );
	}

	/**
	 * Render term creator form.
	 *
	 * @return void
	 */
	public static function ajax_get_form() {
		$action = 'es_data_manager_get_form';

		if ( check_ajax_referer( $action, static::$nonce_arg, false ) ) {
			$data = es_clean( $_GET );

			if ( $data['taxonomy'] == 'es_location' ) {
                $config = static::get_locations_config();
                $config = $config[ $data['type'] ];
                $config['parent_id'] = $data['parent_id'];
                $term_creator = es_get_terms_creator_factory( $data['taxonomy'], $config );
            } else {
                $term_creator = es_get_terms_creator_factory( $data['taxonomy'] );
            }


			if ( ! empty( $data['term'] ) ) {
				$term_creator->set_form_term_id( $data['term'] );
			}

			ob_start();
			$term_creator->render_form();

			$response = array(
				'status' => 'success',
				'content' => ob_get_clean(),
			);
		} else {
			$response = es_ajax_invalid_nonce_response();
		}

		wp_die( json_encode( $response ) );
	}

	/**
	 * Delete terms via ajax.
	 *
	 * @return void
	 */
	public static function ajax_delete_terms() {
		$action = 'es_data_manager_delete_terms';

		if ( check_ajax_referer( $action, static::$nonce_arg, false ) ) {
			$taxonomy = sanitize_text_field( filter_input( INPUT_POST, 'taxonomy' ) );
			$messages = array();
			$response = array();
            $response['children_terms_deleted'] = array();

			if ( ! empty( $_POST['terms_ids'] ) ) {
				foreach ( $_POST['terms_ids'] as $term_id ) {
					if ( $term_id = intval( $term_id ) ) {
						if ( es_is_default_term( $term_id ) ) {
							es_deactivate_term( $term_id );
							$deleted = true;
						} else {

						    if ( $taxonomy == 'es_location' ) {
						        if ( $children = es_get_children_locations( $term_id, $taxonomy ) ) {
                                    $response['children_terms_deleted'] = array_merge( $children, $response['children_terms_deleted'] );
                                }
                            }

							$deleted = wp_delete_term( $term_id, $taxonomy );
						}

						if ( is_wp_error( $deleted ) ) {
							$messages[] = $deleted->get_error_message();
						}
					}
				}

				$response['status'] = 'success';

				if ( $messages ) {
					$response['message'] = $messages;
					$response['status'] = 'warning';
				}
			}

		} else {
			$response = es_ajax_invalid_nonce_response();
		}

		wp_die( json_encode( $response ) );
	}

	/**
	 * Add term using term creator via ajax.
	 *
	 * @return void
	 */
	public static function ajax_save_term() {
		$action = 'es_terms_creator_add_term';

		if ( check_ajax_referer( $action, '_wpnonce', false ) ) {
			$term_name = sanitize_text_field( filter_input( INPUT_POST, 'term_name' ) );
			$taxonomy = filter_input( INPUT_POST, 'taxonomy' );
			$term_id = filter_input( INPUT_POST, 'term_id' );

			if ( ! empty( $term_name ) ) {
				if ( ! empty( $term_id ) ) {
					$term = wp_update_term( $term_id, $taxonomy, array(
						'name' => $term_name,
					) );
				} else {
				    $exist_term = get_term_by( 'name', $term_name, $taxonomy );
				    $slug = $exist_term instanceof WP_Term ? es_unique_term_slug( $exist_term, $term_name ) : null;

					$term = wp_insert_term( $term_name, $taxonomy, array(
					    'slug' => $slug,
                    ) );
				}

                if ( $taxonomy == 'es_location' ) {
                    $config = static::get_locations_config();
                    $config = $config[ filter_input( INPUT_POST, 'dep' ) ];
                    $creator = es_get_terms_creator_factory( $taxonomy, $config );
                } else {
                    $creator = es_get_terms_creator_factory( $taxonomy );
                }

				if ( ! is_wp_error( $term ) ) {

					if ( $color = filter_input( INPUT_POST, 'term_color' ) ) {
						update_term_meta( $term['term_id'], 'es_color', $color );
					}

					if ( $icon = filter_input( INPUT_POST, 'term_icon' ) ) {
						update_term_meta( $term['term_id'], 'es_icon', json_decode( $icon ) );
					}

                    if ( $type = filter_input( INPUT_POST, 'type' ) ) {
                        update_term_meta( $term['term_id'], 'type', $type );
                    }

                    if ( $parent_id = $_POST['parent_id'] ) {
                        $parent_id = es_clean( $_POST['parent_id'] );
                        if ( is_string( $parent_id ) ) {
                            $parent_id = array( $parent_id );
                        }

                        delete_term_meta( $term['term_id'], 'parent_component' );

                        foreach ( $parent_id as $pid ) {
                            add_term_meta( $term['term_id'], 'parent_component', $pid );
                        }
                    }

					do_action( 'es_data_manager_after_save_term', $term );

					ob_start();
					$creator->render_term_item( $term['term_id'], $term_name );
					$content = ob_get_clean();

					$response = array(
						'status' => 'success',
						'content' => $content,
						'is_new' => empty( $term_id ),
						'taxonomy' => $taxonomy,
						'term_id' => $term_id,
					);
				} else {
					$response = es_notification_ajax_response( $term->get_error_message(), 'error' );
				}
			} else {
				$response = es_notification_ajax_response( __( 'Term name is empty', 'es' ), 'error' );
			}
		} else {
			$response = es_ajax_invalid_nonce_response();
		}

		wp_die( json_encode( $response ) );
	}

	/**
	 * Render data manager tab content.
	 *
	 * @param $tab_id string
	 */
	public static function render_tab_content( $tab_id ) {
		es_load_template( "admin/data-manager/tabs/{$tab_id}-tab.php" );
	}

	/**
	 * Return nav items.
	 *
	 * @return array
	 */
	public static function get_nav_items() {

		$tabs = array(
			'parameters' => array(
				array(
					'label' => __( 'Parameters', 'es' ),
					'hash' => '#es-parameters',
				),
				array(
					'label' => __( 'Categories', 'es' ),
					'hash' => '#es-terms-es_category-creator',
				),
				array(
					'label' => __( 'Types', 'es' ),
					'hash' => '#es-terms-es_type-creator',
				),
				array(
					'label' => __( 'Statuses', 'es' ),
					'hash' => '#es-terms-es_status-creator',
				),
				array(
					'label' => __( 'Labels', 'es' ),
					'hash' => '#es-terms-es_label-creator',
				),
				array(
					'label' => __( 'Rent periods', 'es' ),
					'hash' => '#es-terms-es_rent_period-creator',
				),
				array(
					'label' => __( 'Amenities & Features', 'es' ),
					'hash' => '#es-features',
				),
				array(
					'label' => __( 'Interior', 'es' ),
					'hash' => '#es-terms-es_floor_covering-creator',
				),
				array(
					'label' => __( 'Exterior', 'es' ),
					'hash' => '#es-terms-es_exterior_material-creator',
				),
                array(
                    'label' => __( 'Tags', 'es' ),
                    'hash' => '#es-terms-es_tag-creator',
                ),
			),
			'units' => array(
				array(
					'label' => __( 'Units & Formats', 'es' ),
					'hash' => '#es-units',
				),
				array(
					'label' => __( 'Area units', 'es' ),
					'hash' => '#es-area-units',
				),
				array(
					'label' => __( 'Lot size units', 'es' ),
					'hash' => '#es-lot-size-units',
				),
				array(
					'label' => __( 'Currencies', 'es' ),
					'hash' => '#es-currencies',
				),
			),
            'locations' => array(
                array(
                    'label' => __( 'Locations', 'es' ),
                    'hash' => '#es-locations',
                ),
            ),
		);

		foreach ( static::get_locations_config() as $id => $config ) {
		    $tabs['locations'][] = array(
		        'label' => $config['label'],
                'hash' => '#es-' . $id
            );
        }

		$tabs['locations'][] = array(
			'label' => __( 'Neighborhoods', 'es' ),
			'hash' => '#es-neighborhood'
		);

        return apply_filters( 'es_date_manager_get_nav_items', $tabs );
	}

	/**
	 * Render data manager page.
	 *
	 * @return void
	 */
	public static function render() {
        $f = es_framework_instance();
        $f->load_assets();
		wp_enqueue_style( 'es-data-manager', plugin_dir_url( ES_FILE ) . 'admin/css/data-manager.min.css', array( 'es-admin', 'estatik-popup' ), Estatik::get_version() );
		wp_enqueue_script( 'es-data-manager', plugin_dir_url( ES_FILE ) . 'admin/js/data-manager.min.js', array( 'jquery', 'es-admin', 'estatik-popup' ), Estatik::get_version() );

		wp_localize_script( 'es-data-manager', 'Estatik_Data_Manager', array(
			'tr' => es_js_get_translations(),
			'nonce' => array(
				'delete_terms' => wp_create_nonce( 'es_data_manager_delete_terms' ),
				'get_form' => wp_create_nonce( 'es_data_manager_get_form' ),
				'get_list' => wp_create_nonce( 'es_data_manager_get_list' ),
				'get_creator' => wp_create_nonce( 'es_data_manager_get_creator' ),
				'restore_term' => wp_create_nonce( 'es_data_manager_restore_term' ),
			),
			'signs' => ests_values( 'currency_sign' ),
		) );

		es_load_template( 'admin/data-manager/index.php', array(
			'nav_items' => static::get_nav_items(),
		) );
	}
}

Es_Data_Manager_Page::init();
