<?php

/**
 * Class Es_Fields_Builder_Page
 */
class Es_Fields_Builder_Page {

	/**
	 * Initialize fields builder actions.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'es_fields_builder_fields_tab', array( 'Es_Fields_Builder_Page', 'section_fields_tab_render' ), 10, 3 );

		add_action( 'wp_ajax_es_fields_builder_get_field_form', array( 'Es_Fields_Builder_Page', 'ajax_get_field_form' ) );
		add_action( 'wp_ajax_es_fields_builder_get_section_form', array( 'Es_Fields_Builder_Page', 'ajax_get_section_form' ) );

		add_action( 'wp_ajax_es_fields_builder_save_field', array( 'Es_Fields_Builder_Page', 'ajax_save_field' ) );
		add_action( 'wp_ajax_es_fields_builder_save_section', array( 'Es_Fields_Builder_Page', 'ajax_save_section' ) );

		add_action( 'wp_ajax_es_fields_builder_delete_field', array( 'Es_Fields_Builder_Page', 'ajax_delete_field' ) );
		add_action( 'wp_ajax_es_fields_builder_delete_section', array( 'Es_Fields_Builder_Page', 'ajax_delete_section' ) );

		add_action( 'wp_ajax_es_fields_builder_change_items_order', array( 'Es_Fields_Builder_Page', 'ajax_change_items_order' ) );

		add_action( 'wp_ajax_es_fields_builder_get_field_settings', array( 'Es_Fields_Builder_Page', 'ajax_get_field_settings' ) );
		add_action( 'wp_ajax_es_fields_builder_get_fields_tab', array( 'Es_Fields_Builder_Page', 'ajax_get_fields_tab' ) );
		add_action( 'wp_ajax_es_fields_builder_get_sections', array( 'Es_Fields_Builder_Page', 'ajax_get_sections' ) );
		add_action( 'wp_ajax_es_fields_builder_restore_field', array( 'Es_Fields_Builder_Page', 'ajax_restore_field' ) );
		add_action( 'wp_ajax_es_fields_builder_restore_section', array( 'Es_Fields_Builder_Page', 'ajax_restore_section' ) );

		add_action( 'es_fields_builder_field_settings', array( 'Es_Fields_Builder_Page', 'render_field_settings' ), 10, 2 );
		add_action( 'es_fields_builder_section_settings', array( 'Es_Fields_Builder_Page', 'render_section_settings' ) );
	}

	/**
	 * Restore default field via ajax.
	 */
	public static function ajax_restore_field() {
		$action = 'es_fields_builder_restore_field';

		if ( check_ajax_referer( $action, 'field_builder_nonce', false ) ) {
		    es_activate_default_property_field( filter_input( INPUT_POST, 'machine_name' ) );
			$response = es_notification_ajax_response( __( 'Field successfully restored.', 'es' ), 'success' );
		} else {
			$response = es_ajax_invalid_nonce_response();
		}

		wp_die( json_encode( $response ) );
    }

	/**
	 * Restore default field via ajax.
	 */
	public static function ajax_restore_section() {
		$action = 'es_fields_builder_restore_section';

		if ( check_ajax_referer( $action, 'field_builder_nonce', false ) ) {
			es_activate_default_property_section( filter_input( INPUT_POST, 'machine_name' ) );
			$response = es_notification_ajax_response( __( 'Section successfully restored.', 'es' ), 'success' );
		} else {
			$response = es_ajax_invalid_nonce_response();
		}

		wp_die( json_encode( $response ) );
	}

	/**
	 * Load fields tab by section machine name via ajax.
	 *
	 * @return void
	 */
	public static function ajax_get_sections() {
		$action = 'es_fields_builder_get_sections';

		if ( check_ajax_referer( $action, 'field_builder_nonce', false ) ) {
		    /** @var Es_Tabs_View $tabs */
		    $tabs = es_framework_get_view( 'tabs', static::get_tabs_config() );
			ob_start();
            $tabs->render_nav();
			$content = ob_get_clean();

			$response = array(
				'status' => 'success',
				'content' => $content
			);
		} else {
			$response = es_ajax_invalid_nonce_response();
		}

		wp_die( json_encode( $response ) );
	}

	/**
	 * Load fields tab by section machine name via ajax.
     *
     * @return void
	 */
	public static function ajax_get_fields_tab() {
	    $action = 'es_fields_builder_get_fields_tab';

		if ( check_ajax_referer( $action, 'field_builder_nonce', false ) ) {
		    ob_start();
            static::section_fields_tab_render( null, filter_input( INPUT_GET, 'machine_name' ), array() );
            $content = ob_get_clean();

            $response = array(
                'status' => 'success',
                'content' => $content
            );
		} else {
		    $response = es_ajax_invalid_nonce_response();
        }

        wp_die( json_encode( $response ) );
    }

	/**
	 * Render field settings.
	 *
	 * @param $type string
	 * @param $field array
	 */
	public static function render_field_settings( $type, $field ) {
		$machine_name = ! empty( $field['machine_name'] ) ? $field['machine_name']  : false;
		if ( in_array( $type, array( 'select', 'checkboxes', 'radio', 'radio-bordered' ) ) && empty( $field['is_address_field'] ) ) {

		    $config = array(
                'type' => 'repeater',
                'fields' => array(
                    'id' => array(
                        'type' => 'hidden',
                    ),
                    'value' => array(
                        'type' => 'text',
                        'label' => _x( 'Option {#index}', 'fields builder options', 'es' ),
                        'attributes' => array(
                            'required' => 'required'
                        )
                    ),
                ),
                'add_button_label' => __( 'Add option', 'es' ),
                'delete_button' => "<span class='js-es-repeater__delete-item es-icon es-icon_trash'></span>",
            );

		    if ( ! empty( $field['taxonomy'] ) ) {
		        $terms = get_terms( array(
		            'taxonomy' => $field['machine_name'],
                    'hide_empty' => false,
                    'fields' => 'id=>name',
                ) );

		        if ( ! empty( $terms ) ) {
		            foreach ( $terms as $id => $term ) {
                        $config['value'][] = array( 'value' => $term, 'id' => $id );
                    }
                }
            }

			es_field_builder_field_render( 'values', $config, $field );

			if ( 'checkboxes' == $type ) {
				es_field_builder_field_option_render( 'relation', false, array(
					'type' => 'select',
					'label' => __( 'Logical operators', 'es' ),
					'options' => array(
						'and' => 'AND',
						'or' => 'OR',
					),
				), $field );
			}
		}

		if ( 'number' == $type || 'incrementer' == $type ) {
			es_field_builder_field_option_render( 'min', true, array(
				'type' => 'number',
				'label' => __( 'Min', 'es' ),
				'attributes' => array(
					'step' => 'any'
				),
			), $field );

			es_field_builder_field_option_render( 'max', true, array(
				'type' => 'number',
				'label' => __( 'Max', 'es' ),
				'attributes' => array(
					'step' => 'any'
				),
			), $field );

			es_field_builder_field_option_render( 'step', true, array(
				'type' => 'number',
				'label' => __( 'Step', 'es' ),
				'attributes' => array(
					'step' => 'any'
				),
			), $field );
		}

		if ( 'price' == $type ) {
			es_field_builder_field_option_render( 'unit', false, array(
				'type' => 'select',
				'label' => __( 'Default currency', 'es' ),
				'options' => ests_values( 'currency' )
			), $field );

			es_field_builder_field_option_render( 'step', true, array(
				'type' => 'number',
				'label' => __( 'Step', 'es' ),
				'attributes' => array(
					'min' => 0,
					'step' => 0.000001
				),
			), $field );
		}

		if ( 'area' == $type && ! in_array( $machine_name, array( 'area', 'lot_size' ) ) ) {
			es_field_builder_field_option_render( 'unit', false, array(
				'type' => 'select',
				'label' => __( 'Default unit', 'es' ),
				'options' => ests_values( 'area_unit' )
			), $field );
		}

		if ( 'tel' == $type ) {
			es_field_builder_field_option_render( 'pattern', true,  array(
				'type' => 'text',
				'label' => __( 'Pattern', 'es' ),
				'description' => '<a target="_blank" href="https://developer.mozilla.org/ru/docs/Web/HTML/Element/Input/tel">https://developer.mozilla.org/ru/docs/Web/HTML/Element/Input/tel</a>',
			), $field );
		}
	}

	/**
	 * Add section settings fields.
	 *
	 * @param $section_config
	 */
	public static function render_section_settings( $section_config ) {
		if ( ! empty( $section_config['machine_name'] ) && 'request_form' == $section_config['machine_name'] ) {
			es_field_builder_field_option_render( 'background_color', false, array(
				'type' => 'color',
				'label' => _x( 'Background color', 'request form bg color field', 'es' ),
				'wrapper_class' => 'es-field--color--break-label',
			), $section_config );

			es_field_builder_field_option_render( 'text_color', false, array(
				'type' => 'color',
				'label' => _x( 'Text color', 'request form text color field', 'es' ),
				'wrapper_class' => 'es-field--color--break-label',
			), $section_config );

			es_field_builder_field_option_render( 'message', false, array(
				'type' => 'textarea',
				'label' => _x( 'Message', 'request form message field', 'es' ),
			), $section_config );
		}
	}

	/**
	 * Return field settings via ajax.
	 *
	 * @return void
	 */
	public static function ajax_get_field_settings() {

		$action = 'es_fields_builder_get_field_settings';

		if ( check_ajax_referer( $action, 'field_builder_nonce', true ) ) {
			$type = filter_input( INPUT_GET, 'type' );
			$builder = es_get_fields_builder_instance();
			$types = $builder::get_types_list();
			$machine_name = es_get( 'machine_name' );

			if ( ! empty( $types[ $type ] ) ) {
				ob_start();

				do_action( 'es_fields_builder_field_settings', $type, es_property_get_field_info( $machine_name ) );

				$response = array(
					'status' => 'success',
					'content' => ob_get_clean(),
				);
			} else {
				$response = es_notification_ajax_response( __( 'Invalid input type', 'es' ), 'error' );
			}
		} else {
			$response = es_ajax_invalid_nonce_response();
		}

		wp_die( json_encode( $response ) );
	}

	/**
	 * Change fields order via ajax.
	 *
	 * @return void
	 */
	public static function ajax_change_items_order() {

		$action = 'es_fields_builder_change_items_order';

		if ( check_ajax_referer( $action, 'field_builder_nonce', false ) ) {
			if ( ! empty( $_POST['items'] ) && is_array( $_POST['items'] ) ) {
			    $items = es_clean( $_POST['items'] );
				$order = 10;
				$type = es_clean( filter_input( INPUT_POST, 'type' ) );
				$fb_instance = false;

				if ( 'fields' == $type ) {
					$fb_instance = es_get_fields_builder_instance();
				} else if ( 'sections' == $type ) {
					$fb_instance = es_get_sections_builder_instance();
				}

				$fb_instance = apply_filters( 'es_fields_builder_order_builder', $fb_instance );

				foreach ( $items as $item ) {
				    $machine_name = sanitize_text_field( $item );

					if ( $fb_instance ) {
						$def_item = $fb_instance::get_item_by_machine_name( $machine_name, 'property' );

						$save_data = $def_item;
						$save_data['order'] = $order;

						if ( empty( $save_data['id'] ) ) {
							$machine_name = $fb_instance::save_item( $save_data );

							if ( $machine_name ) {
								$def_item = $fb_instance::get_item_by_machine_name( $machine_name, 'property' );
								$save_data['id'] = $def_item['id'];
							}
						}

						if ( ! empty( $save_data['id'] ) ) {
							$save_data = array(
								'id' => $def_item['id'],
								'machine_name' => $machine_name,
								'order' => $order
							);
						}

						$fb_instance::save_item_order( $save_data );

						$order += 10;
					}
				}

				$response = es_notification_ajax_response( __( 'Items order was changed successfully.', 'es' ), 'success' );
			} else {
			    $response = es_notification_ajax_response( __( 'Nothing to sort.', 'es' ), 'error' );
			}
		} else {
		    $response = es_ajax_invalid_nonce_response();
		}

		wp_die( json_encode( $response ) );
	}

	/**
	 * Delete section action.
	 *
	 * @return void
	 */
	public static function ajax_delete_section() {

		$action = 'es_fields_builder_delete_section';

		if ( check_ajax_referer( $action, 'field_builder_nonce', false ) ) {
			$machine_name = es_clean( filter_input( INPUT_POST, 'machine_name' ) );

			if ( $machine_name ) {
				$builder = es_get_sections_builder_instance();
				$item = $builder::get_item_by_machine_name( $machine_name );
				$section_name = $item['label'];

				if ( es_is_property_default_section( $machine_name ) ) {
				    es_deactivate_default_property_section( $machine_name );
                } else {
					$builder::delete_item( $machine_name );
                }

				global $wpdb;
				$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}estatik_fb_fields WHERE section_machine_name='%s' OR tab_machine_name='%s'", $machine_name, $machine_name ) );

				/* translators: %s: section name. */
				$response = es_notification_ajax_response( sprintf( __( 'Section <b>%s</b> was deleted successfully.', 'es' ), $section_name ), 'success' );
			} else {
			    $response = es_notification_ajax_response( __( 'Section machine name is empty.', 'es' ), 'error' );
			}
		} else {
			$response = es_ajax_invalid_nonce_response();
		}

		wp_die( json_encode( $response ) );
	}

	/**
	 * Delete fields builder field via ajax.
	 *
	 * @return void
	 */
	public static function ajax_delete_field() {

		$action = 'es_fields_builder_delete_field';

		if ( check_ajax_referer( $action, 'field_builder_nonce', false ) ) {
			$machine_name = es_clean( filter_input( INPUT_POST, 'machine_name' ) );
			$id = es_clean( filter_input( INPUT_POST, 'id' ) );

			if ( $machine_name || $id ) {
				$fields_builder = es_get_fields_builder_instance();
				if ( ! empty( $id ) ) {
					$item = $fields_builder::get_item_by_id( $id );
				} else {
					$item = $fields_builder::get_item_by_machine_name( $machine_name );
				}

				$field_name = $item['label'];

				if ( es_is_property_default_field( $machine_name ) ) {
                    es_deactivate_default_property_field( $machine_name );
                } else {
					if ( ! empty( $id ) ) {
						$fields_builder::delete_item_by_id( $id );
					} else {
						$fields_builder::delete_item( $machine_name );
					}
                }

				/* translators: %s: field name. */
				$response = es_notification_ajax_response( sprintf( __( 'Field <b>%s</b> was deleted successfully.', 'es' ), $field_name ), 'success' );
			} else {
			    $response = es_notification_ajax_response( __( 'Field machine name is empty.', 'es' ), 'error' );
			}
		} else {
			$response = es_ajax_invalid_nonce_response();
		}

		wp_die( json_encode( $response ) );
	}

	/**
	 * Create / Update fields builder field.
	 *
	 * @return void
	 */
	public static function ajax_save_field() {

		$action = 'es_fields_builder_save_field';

		if ( check_ajax_referer( $action, '_wpnonce', false ) && ! empty( $_POST['es_fields_builder'] ) ) {
			$field_data = es_clean( $_POST['es_fields_builder'] );
			$field_data = wp_unslash( $field_data );
			$field_builder = es_get_fields_builder_instance();

			if ( $field_machine_name = $field_builder::save_field( $field_data ) ) {
				$field = $field_builder::get_item_by_machine_name( $field_machine_name );

				$response = array(
					'status' => 'success',
					'message' => es_get_notification_markup( __( 'All changes saved.', 'es' ) ),
					'field' => (array) $field
				);
			} else {
				$response = es_notification_ajax_response( __( "Field didn't saved.", 'es' ), 'error' );
			}
		} else {
			$response = es_ajax_invalid_nonce_response();
		}

		wp_die( json_encode( $response ) );
	}

	/**
	 * Create / Update fields builder field.
	 *
	 * @return void
	 */
	public static function ajax_save_section() {

		$action = 'es_fields_builder_save_section';

		if ( check_ajax_referer( $action, '_wpnonce', false ) && ! empty( $_POST['es_fields_builder'] ) ) {
			$section_data = es_clean( $_POST['es_fields_builder'] );
			$section_data = wp_unslash( $section_data );
			$sections_builder = es_get_sections_builder_instance();

			if ( $section_machine_name = $sections_builder::save_section( $section_data ) ) {
				$section = $sections_builder::get_item_by_machine_name( $section_machine_name );

				$response = array(
					'status' => 'success',
					'message' => es_get_notification_markup( __( 'All changes saved.', 'es' ) ),
					'section' => (array) $section,
				);
			} else {
			    $response = es_notification_ajax_response( __( "Section didn't saved.", 'es' ), 'error' );
			}
		} else {
			$response = es_ajax_invalid_nonce_response();
		}

		wp_die( json_encode( $response ) );
	}

	/**
	 * Return create field form.
	 *
	 * @return void
	 */
	public static function ajax_get_field_form() {

		$action = 'es_fields_builder_get_field_form';

		if ( check_ajax_referer( $action , 'field_builder_nonce', false ) ) {
			$machine_name = filter_input( INPUT_GET, 'machine_name' );
			$section_machine_name = filter_input( INPUT_GET, 'section_machine_name' );
			$field_builder = es_get_fields_builder_instance();
			$sections_builder = es_get_sections_builder_instance();
			$sections = $sections_builder::get_items( 'property' );
			$sections = $sections ? wp_list_pluck( $sections, 'label', 'machine_name' ) : array();

			es_load_template( 'admin/fields-builder/forms/field-form.php', array(
				'machine_name' => $machine_name,
				'section_machine_name' => $section_machine_name,
				'field' => $field_builder::get_item_by_machine_name( $machine_name ),
				'sections' => $sections,
				'fields_builder' => $field_builder,
			) );
		} else {
			$response = es_ajax_invalid_nonce_response();
			echo $response['message'];
		}

		wp_die();
	}

	/**
	 * Return create field form.
	 *
	 * @return void
	 */
	public static function ajax_get_section_form() {

		$action = 'es_fields_builder_get_section_form';

		if ( check_ajax_referer( $action , 'field_builder_nonce', false ) ) {
			$machine_name = filter_input( INPUT_GET, 'machine_name' );
			$sections_builder = es_get_sections_builder_instance();

			es_load_template( 'admin/fields-builder/forms/section-form.php', array(
				'machine_name' => $machine_name,
				'section' => $sections_builder::get_item_by_machine_name( $machine_name ),
				'sections_builder' => $sections_builder,
			) );
		} else {
			$response = es_ajax_invalid_nonce_response();
			echo $response['message'];
		}

		wp_die();
	}

	/**
	 * Build tabs array for tabs view/
	 *
	 * @param $sections
	 *
	 * @return array
	 */
	protected static function build_tabs( $sections ) {
		$prepared_sections = array();

		if ( ! empty( $sections ) ) {
			foreach ( $sections as $section ) {

				$li_disable_drag = empty( $section['fb_settings']['disable_order'] ) ? '' : 'disable-order';
				$deactivated = es_is_property_default_section_deactivated( $section['machine_name'] ) ?
                    'disable-edit' : '';

				$prepared_sections[ $section['machine_name'] ] = array(
					'label' => ! empty( $section['section_name'] ) ? $section['section_name'] : $section['label'],
					'action' => 'es_fields_builder_fields_tab',
					'link_html' => es_field_builder_get_tab_link_markup( $section ),
					'li_attributes' => "class='{$li_disable_drag} {$deactivated}' data-machine-name='{$section['machine_name']}'",
				);
			}
		}

		return $prepared_sections;
	}

	/**
     * Return tabs view config.
     *
	 * @return array
	 */
	public static function get_tabs_config() {
		$sections_builder = es_get_sections_builder_instance();
		$sections = $sections_builder::get_items( 'property' );

	    return array(
		    'tabs' => static::build_tabs( $sections ),
		    'nav_title' => __( 'Listing sections', 'es' ),
		    'wrapper_class' => 'es-tabs es-tabs--vertical es-tabs__fields-builder',
		    'after_content_tabs' => "<div class='es-field-builder__form js-es-field-builder__form'></div>",
		    'after_nav' => '<button class="es-btn es-btn--third js-es-fields-builder-add-section">
                            <span class="es-icon es-icon_plus"></span>
                            ' . __( 'Add section', 'es' ) . '
                        </button>',
		    'ul_class' => 'js-es-sections-list',
	    );
    }

	public static function enqueue_assets() {
		wp_enqueue_style( 'es-fields-builder', plugin_dir_url( ES_FILE ) . 'admin/css/fields-builder.min.css', array( 'es-admin', 'estatik-popup' ), Estatik::get_version() );
		wp_enqueue_script( 'es-fields-builder', plugin_dir_url( ES_FILE ) . 'admin/js/fields-builder.min.js', array( 'jquery', 'es-admin', 'jquery-ui-sortable', 'estatik-popup', 'clipboard' ), Estatik::get_version() );

		wp_localize_script( 'es-fields-builder', 'Estatik_Fields_Builder', array(
			'nonce' => array(
				'get_field_form' => wp_create_nonce(  'es_fields_builder_get_field_form' ),
				'get_section_form' => wp_create_nonce(  'es_fields_builder_get_section_form' ),
				'delete_field' => wp_create_nonce(  'es_fields_builder_delete_field' ),
				'delete_section' => wp_create_nonce(  'es_fields_builder_delete_section' ),
				'update_items_order' => wp_create_nonce(  'es_fields_builder_change_items_order' ),
				'get_field_settings' => wp_create_nonce(  'es_fields_builder_get_field_settings' ),
				'get_fields_tab' => wp_create_nonce(  'es_fields_builder_get_fields_tab' ),
				'restore_field' => wp_create_nonce(  'es_fields_builder_restore_field' ),
				'get_sections' => wp_create_nonce(  'es_fields_builder_get_sections' ),
				'restore_section' => wp_create_nonce(  'es_fields_builder_restore_section' ),
			),
			'tr' => es_js_get_translations(),
		) );
	}

	/**
	 * Render fields builder page.
	 *
	 * @return void
	 */
	public static function render() {
        $f = es_framework_instance();
        $f->load_assets();
		static::enqueue_assets();

		es_load_template( 'admin/fields-builder/index.php', array(
			'tabs_config' => static::get_tabs_config(),
		) );
	}

	/**
	 * Render section fields tab.
	 *
	 * @param $item array
	 * @param $section_machine_name string
	 * @param $config array
	 */
	public static function section_fields_tab_render( $item, $section_machine_name, $config ) {

		$fields_builder = es_get_fields_builder_instance();
		$section_fields = $fields_builder::get_section_fields( $section_machine_name );

		es_load_template( 'admin/fields-builder/partials/fields-tab.php', array(
			'section_fields' => $section_fields,
			'item' => $item,
			'section_machine_name' => $section_machine_name,
			'config' => $config,
		) );
	}
}

Es_Fields_Builder_Page::init();
