<?php

/**
 * Class Es_Fbuilder.
 */
class Es_Fields_Builder extends Es_Fields_Builder_Item {

    /**
     * Es_Fields_Builder constructor.
     * @param bool $force_reload
     * @param string $entity
     */
    public function __construct( $force_reload = false, $entity = 'property' ) {
        if ( $force_reload ) {
            static::$fields = array();
            static::get_items( $entity );
        }
    }

    /**
	 * Entities fields array.
	 *
	 * @var array
	 */
	public static $fields = array();

	/**
	 * Return custom fields types.
	 *
	 * @return array
	 */
	public static function get_types_list() {
		return apply_filters( 'es_fields_builder_get_types_list', array(
			'text' => _x( 'Text', 'field type', 'es' ),
			'number' => _x( 'Number', 'field type', 'es' ),
			'price' => _x( 'Price', 'field type', 'es' ),
			'area' => _x( 'Area', 'field type', 'es' ),
			'media' => _x( 'File Upload', 'field type', 'es' ),
			'date' => _x( 'Date picker', 'field type', 'es' ),
			'date-time' => _x( 'Date & time picker', 'field type', 'es' ),
			'email' => _x( 'Email', 'field type', 'es' ),
			'tel' => _x( 'Phone number', 'field type', 'es' ),
			'url' => _x( 'URL', 'field type', 'es' ),
			'link' => _x( 'Link', 'field type', 'es' ),
			'textarea' => _x( 'Textarea', 'field type', 'es' ),
			'select' => _x( 'Dropdown', 'field type', 'es' ),
			'radio-bordered' => _x( 'Select - buttons', 'field type', 'es' ),
			'switcher' => _x( 'Switch', 'field type', 'es' ),
			'checkboxes' => _x( 'Checkboxes', 'field type', 'es' ),
			'radio' => _x( 'Radio buttons', 'field type', 'es' ),
		) );
	}

	/**
	 * Return sections table name.
	 *
	 * @return string
	 */
	public static function get_table_name() {
		global $wpdb;
		return apply_filters( 'es_fields_builder_get_table_name', $wpdb->prefix . 'estatik_fb_fields' );
	}

	/**
	 * Return fields builder fields.
	 *
	 * @param string $entity
	 *
	 * @return stdClass[]
	 */
	public static function get_items( $entity = 'property' ) {

		if ( ! empty( $entity ) && empty( static::$fields[ $entity ] ) ) {
			global $wpdb;
			$fields = array();
			$table_name = static::get_table_name();
			$fields_data = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table_name WHERE entity_name='%s' ORDER BY `order` ASC", $entity ), ARRAY_A );

			if ( ! empty( $fields_data ) ) {
				foreach ( $fields_data as $field ) {
					$fields[ $field['entity_name'] ][ $field['machine_name'] ] = $field;
				}
			}

			if ( $default_fields = es_get_entity_default_fields( $entity ) ) {
				foreach ( $default_fields as $field_key => $field_config ) {
					$default_fields[ $field_key ]['machine_name'] = $field_key;
				}
			}

			$fields[ $entity ] = es_parse_args( $fields[ $entity ], $default_fields );
			$fields[ $entity ] = wp_list_sort( $fields[ $entity ], 'order', 'ASC', true );

			foreach ( $fields[ $entity ] as $key => $field ) {
				if ( ! empty( $field['options'] ) && is_string( $field['options'] ) ) {
					$options = maybe_unserialize( $field['options'] );
					$fields[ $entity ][ $key ] = es_parse_args( $options, $fields[ $entity ][ $key ] );
					unset( $fields[ $entity ][ $key ]['options'] );
				}

				if ( ! empty( $field['type'] ) ) {
					if ( in_array( $field['type'], array( 'switcher', 'date', 'date-time', 'url', 'price', 'area' ) ) ) {
						$fields[ $entity ][ $key ]['formatter'] = $field['type'];
					}

					if ( 'media' == $field['type'] && empty( $fields[ $entity ][ $key ]['formatter'] ) ) {
						$fields[ $entity ][ $key ]['formatter'] = 'document';
					}

					if ( in_array( $field['type'], array( 'date', 'date-time' ) ) ) {
						$format = ests( 'date_format' );
						$format .= $field['type'] == 'date-time' ? ' ' . ests( 'time_format' ) . ':i A' : '';
						$fields[ $entity ][ $key ]['attributes']['data-date-format'] = $format;
					}

					if ( $field['type'] == 'link' ) {
						$fields[ $entity ][ $key ]['formatter'] = $field['type'];
					}

					if ( in_array( $field['type'], array( 'price', 'area' ) ) ) {
						$fields[ $entity ][ $key ]['search_settings']['type'] = $field['type'] == 'area' ? 'range' : $field['type'];
						$fields[ $entity ][ $key ]['search_settings']['range'] = true;
					}

					if ( ! empty( $default_fields[ $key ]['fb_settings']['disable_type_edit'] ) && ! empty( $default_fields[ $key ]['type'] ) ) {
						$fields[ $entity ][ $key ]['type'] = $default_fields[ $key ]['type'];
					}

					if ( ! empty( $default_fields[ $key ]['fb_settings']['disable_tab_field'] ) ) {
						unset( $fields[ $entity ][ $key ]['tab_machine_name'] );
					}
				}

			    if ( ! empty( $field['values'] ) && is_string( $field['values'] ) ) {
			        $serialized_value = maybe_unserialize( $field['values'] );
			        if ( isset( $serialized_value[0]['value'] ) ) {
			            $values_unserialized = wp_list_pluck( $serialized_value, 'value' );
						if ( ! empty ( $values_unserialized ) ) {
							foreach ( $values_unserialized as $unkey => $untranslated_val ) {
								$values_unserialized[ $unkey ] = __( $untranslated_val, 'es' );
							}
						}
                        $fields[ $entity ][ $key ]['options'] = array_combine( $values_unserialized, $values_unserialized );
                    }
			        if ( $field['type'] == 'checkboxes' ) {
                        $fields[ $entity ][ $key ]['is_single_meta'] = false;
                    }
                }

				if ( ! empty( $field['address_component'] ) ) {
					$fields[ $entity ][ $key ]['attributes']['data-address-components'] = es_esc_json_attr( array( $field['address_component'] ) );
					$fields[ $entity ][ $key ]['search_settings']['attributes']['data-address-components'] = es_esc_json_attr( array( $field['address_component'] ) );
				}

			    if ( ! empty( $field['type'] ) && $field['type'] == 'select' && ! isset( $field['attributes']['placeholder'] ) ) {
                    $fields[ $entity ][ $key ]['attributes']['placeholder'] = __( 'Choose value', 'es' );
                    $fields[ $entity ][ $key ]['attributes']['data-placeholder'] = __( 'Choose value', 'es' );
                }

				if ( ! empty( $field['mandatory'] ) ) {
					if ( ( $field['type'] !== 'checkboxes' ) ) {
						$fields[ $entity ][ $key ]['attributes']['required'] = 'required';
						$fields[ $entity ][ $key ]['wrapper_class'] = 'es-is-required';
					} else {
						$fields[ $entity ][ $key ]['wrapper_class'] = 'es-is-required js-es-is-required';
					} 
				}

				if ( ! empty( $field['id'] ) && ! empty( $field['label'] ) ) {
					$fields[ $entity ][ $key ]['label'] = __( $field['label'], 'es' );
					if ( !empty ( $fields[ $entity ][ $key ]['frontend_visible_name'] ) ) {
						$fields[ $entity ][ $key ]['frontend_visible_name'] = __( $field['frontend_visible_name'], 'es' );
					}
					
					if ( ! empty( $field['type'] ) && $field['type'] == 'select' ) {
						/* translators: %s: Field name. */
						$fields[ $entity ][ $key ]['search_settings']['attributes']['data-placeholder'] = sprintf( __( 'Choose %s', 'es' ), $field['label'] );
					}
				}

				if ( empty( $fields[ $entity ][ $key ]['label'] ) ) {
					if ( ! empty( $default_fields[ $key ]['label'] ) ) {
						$fields[ $entity ][ $key ]['label'] = $default_fields[ $key ]['label'];
					}
				}
            }
			
			static::$fields[ $entity ] = apply_filters( 'es_fields_builder_set_fields', $fields[ $entity ], $entity );
		}

		if ( ! empty( $entity ) ) {
			$fields = ! empty( static::$fields[ $entity ] ) ? static::$fields[ $entity ] : array();
		} else {
			$fields = static::$fields;
		}

		return apply_filters( 'es_fields_builder_get_fields', $fields, $entity );
	}

	/**
	 * Return fields by section ID.
	 *
	 * @param $section_machine_name string
	 * @param $entity string
	 *
	 * @return array
	 */
	public static function get_section_fields( $section_machine_name, $entity = 'property' ) {
		$fields = static::get_items( $entity );

		if ( ! empty( $fields ) ) {
			$fields = wp_filter_object_list( $fields, array(
			    'section_machine_name' => $section_machine_name
            ) );
		}

		return $fields;
	}

	/**
	 * Return fields by section ID.
	 *
	 * @param $section_machine_name string
	 * @param $entity string
	 *
	 * @return stdClass[]
	 */
	public static function get_tab_fields( $section_machine_name, $entity = 'property' ) {
		$fields = static::get_items( $entity );

		if ( ! empty( $fields ) ) {
			$fields = wp_filter_object_list( $fields, array(
			    'tab_machine_name' => $section_machine_name
            ) );
		}

		return $fields;
	}

	/**
	 * @param $save_data
	 *
	 * @return false|string|void
	 */
	public static function save_item( $save_data ) {
		return static::save_field( $save_data );
	}

	/**
	 * @param $section_data
	 *
	 * @return mixed
	 */
	public static function prepare_item_data( $section_data ) {
		$valid_cols = array( 'id', 'label', 'frontend_form_name', 'frontend_machine_name', 'frontend_visible_name', 'type', 'values', 'options',
			'machine_name', 'section_machine_name', 'tab_machine_name', 'entity_name', 'mandatory', 'search_support',
			'mls_import_support', 'is_visible', 'is_visible_for', 'order', 'address_component', 'is_full_width' );
		$valid_cols = apply_filters( 'es_sections_prepare_item_data', $valid_cols, $section_data );

		if ( ! empty( $section_data ) ) {
			foreach ( $section_data as $key => $value ) {
				if ( ! in_array( $key, $valid_cols ) ) {
					unset( $section_data[ $key ] );
				}
			}
		}

		return $section_data;
	}

	/**
	 * Save fields builder field.
	 *
	 * @param $field_data
	 *
	 * @return string
	 */
	public static function save_field( $field_data ) {

		global $wpdb;
		$is_new_field = false;
		$table_name = static::get_table_name();

		$field_data = static::prepare_item_data( $field_data );
		$exists = false;

		if ( ! empty( $field_data['machine_name'] ) ) {
			$exists = static::exists( $field_data['machine_name'] );
		}

		$field_data = es_parse_args( $field_data, array(
			'entity_name' => 'property',
		) );

		if ( ! $exists ) {
			$field_data = es_parse_args( $field_data, array(
				'section_machine_name' => 'basic-facts',
				'values' => '',
			) );
		}

		if ( ! isset( $field_data['order'] ) && ! $exists ) {
			$field_data['order'] = static::get_next_field_order( $field_data['section_machine_name'], $field_data['entity_name'] );
		}

		if ( ! empty( $field_data['is_visible_for'] ) ) {
			$field_data['is_visible_for'] = maybe_serialize( $field_data['is_visible_for'] );
		}

		if ( ! empty( $field_data['machine_name'] ) ) {
			$field_info = es_property_get_field_info( $field_data['machine_name'] );
		}

		if ( ! empty( $field_data['values'] ) ) {
            $field_data['values'] = array_filter( $field_data['values'] );
        }

		if ( ! empty( $field_info['taxonomy'] ) ) {

            if ( ! empty( $field_data['values'] ) ) {
                $terms = get_terms( array(
                    'taxonomy' => $field_data['machine_name'],
                    'hide_empty' => false,
                    'fields' => 'ids',
                ) );

                $terms = $terms ? $terms : array();
                $terms_ids = wp_list_pluck( $field_data['values'], 'id' );

                if ( ! empty( $terms ) ) {
                    foreach ( $terms as $term ) {
                        if ( ! in_array( $term, $terms_ids ) ) {
                            wp_delete_term( intval( $term ), $field_data['machine_name'] );
                        }
                    }
                }

                foreach ( $field_data['values'] as $term ) {
                    if ( ! empty( $term['id'] ) && term_exists( intval( $term['id'] ), $field_data['machine_name'] ) ) {
                        wp_update_term( intval( $term['id'] ), $field_data['machine_name'], array(
                            'name' => $term['value']
                        ) );
                    } else {
                        wp_insert_term( $term['value'], $field_data['machine_name'] );
                    }
                }

                $field_data['values'] = array();
            } else {
                /** @var Int[] $terms */
                $terms = get_terms( $field_data['machine_name'], array( 'fields' => 'ids', 'hide_empty' => false ) );
                foreach ( $terms as $value ) {
                    wp_delete_term( $value, $field_data['machine_name'] );
                }
                $field_data['values'] = array();
            }
        } else {
            if ( ! empty( $field_data['values'] ) ) {
                $field_data['values'] = maybe_serialize( $field_data['values'] );
            } else {
                $field_data['values'] = '';
            }
        }

		if ( ! empty( $field_data['options'] ) ) {
			$field_data['options'] = maybe_serialize( $field_data['options'] );
		} else {
			$field_data['options'] = '';
		}

		if ( empty( $field_data['machine_name'] ) ) {
			$field_data['machine_name'] = static::get_unique_machine_name( $field_data['label'] );
		}

		if ( ! empty( $field_data['frontend_visible_name'] && isset ( $field_data['machine_name'] )) ) {
			// Checking the presence and activation of Polylang 
			if ( function_exists('pll_register_string') ) {
				// Registering translation strings in Polylang
					pll_register_string($field_data['machine_name'], $field_data['frontend_visible_name']);
			}
			// Checking the presence and activation of WPML
			if ( function_exists('icl_register_string') ) {
				// Registering translation strings in WPML
				icl_register_string('Estatik', $field_data['machine_name'], $field_data['frontend_visible_name']);
			}
		}

		$field_data = apply_filters( 'es_fields_builder_save_field', $field_data, $is_new_field );

		if ( empty( $field_data['machine_name'] ) ) {
			return false;
		}

		$field_data['machine_name'] = sanitize_title( $field_data['machine_name'] );

		if ( ! static::exists( $field_data['machine_name'] ) ) {
			$is_executed = $wpdb->insert( $table_name, $field_data );
		} else {
			unset( $field_data['id'] );
			$is_executed = $wpdb->update( $table_name, $field_data, array( 'machine_name' => $field_data['machine_name'] ) );
		}

		if ( $is_executed !== false ) {
		    // Rebuild static fields array.
            static::$fields = array();
            static::get_items( $field_data['entity_name'] );
        }

		return $is_executed !== false ? $field_data['machine_name'] : false;
	}

	/**
	 * Generate max field order by section name.
	 *
	 * @param $section_machine_name
	 * @param string $entity_name
	 *
	 * @return int
	 */
	public static function get_next_field_order( $section_machine_name, $entity_name = 'property' ) {
		global $wpdb;
		$table_name = static::get_table_name();

		$order = $wpdb->get_var( $wpdb->prepare( "SELECT MAX(`order`)
														 FROM $table_name
														 WHERE section_machine_name='%s'
														 AND entity_name='%s'", $section_machine_name, $entity_name ) );

		return ! $order ? 300 : $order + 10;
	}
}
