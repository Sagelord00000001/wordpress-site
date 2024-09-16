<?php

/**
 * Class Es_Sections_Builder.
 */
class Es_Sections_Builder extends Es_Fields_Builder_Item {

	/**
	 * @var array Entity sections
	 */
	static $sections = array();

    /**
     * Es_Fields_Builder constructor.
     * @param bool $force_reload
     * @param string $entity
     */
    public function __construct( $force_reload = false, $entity = 'property' ) {
        if ( $force_reload ) {
            static::$sections = array();
            static::get_items( $entity );
        }
    }

	/**
	 * Return sections table name.
	 *
	 * @return string
	 */
	public static function get_table_name() {
		global $wpdb;
		return apply_filters( 'es_sections_builder_get_table_name', $wpdb->prefix . 'estatik_fb_sections' );
	}

	/**
	 * Return entity sections.
	 *
	 * @param bool $entity
	 *
	 * @return array
	 */
	public static function get_items( $entity = false ) {

		if ( empty( static::$sections ) ) {
			global $wpdb;
			$sections = array();
			$table_name = static::get_table_name();
			$sections_data = $wpdb->get_results( "SELECT * FROM $table_name ORDER BY `order` ASC", ARRAY_A );

			if ( ! empty( $sections_data ) ) {
				foreach ( $sections_data as $section ) {
					if ( ! empty( $section['options'] ) ) {
						$section['options'] = maybe_unserialize( $section['options'] );
					}
					$sections[ $section['entity_name'] ][ $section['machine_name'] ] = $section;
				}
			}

			if ( $default_sections = es_get_default_sections( $entity ) ) {
				foreach ( $default_sections as $section_key => $section ) {
					$default_sections[ $section_key ]['machine_name'] = $section_key;
				}
			}

			$sections[ $entity ] = es_parse_args( $sections[ $entity ], $default_sections );
			$sections[ $entity ] = wp_list_sort( $sections[ $entity ], 'order', 'ASC', true );

			static::$sections = apply_filters( 'es_sections_builder_set_sections', $sections );
		}

		if ( ! empty( $entity ) ) {
			$sections = ! empty( static::$sections[ $entity ] ) ? static::$sections[ $entity ] : array();
		} else {
			$sections = static::$sections;
		}

		return apply_filters( 'es_sections_builder_get_sections', $sections, $entity );
	}

	/**
	 * @param $save_data
	 *
	 * @return false|string|void
	 */
	public static function save_item( $save_data ) {
		return static::save_section( $save_data );
	}

	/**
	 * @param $section_data
	 *
	 * @return mixed
	 */
	public static function prepare_item_data( $section_data ) {
		$valid_cols = array( 'id', 'label', 'options', 'machine_name', 'entity_name', 'is_visible', 'is_visible_for', 'order' );
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
	 * Save field builder section.
	 *
	 * @param $section_data
	 *
	 * @return bool
	 */
	public static function save_section( $section_data ) {
		global $wpdb;
		$is_new_section = false;
		$table_name = static::get_table_name();

		$section_data = es_parse_args( $section_data, array(
			'entity_name' => 'property',
		) );

		$section_data = static::prepare_item_data( $section_data );

		if ( ! empty( $section_data['is_visible_for'] ) ) {
			$section_data['is_visible_for'] = maybe_serialize( $section_data['is_visible_for'] );
		}

		if ( ! isset( $section_data['order'] ) ) {
			$section_data['order'] = static::get_next_section_order( $section_data['entity_name'] );
		}

		if ( ! empty( $section_data['options'] ) ) {
			$section_data['options'] = maybe_serialize( $section_data['options'] );
		}

		if ( empty( $section_data['machine_name'] ) ) {
			$is_new_section = true;
			$section_data['machine_name'] = static::get_unique_machine_name( $section_data['label'] );
		}

		$section_data = apply_filters( 'es_fields_builder_save_section', $section_data, $is_new_section );

		if ( empty( $section_data['machine_name'] ) ) {
			return false;
		}

		$section_data['machine_name'] = sanitize_title( $section_data['machine_name'] );

		if ( ! static::exists( $section_data['machine_name'] ) ) {
			$is_executed = $wpdb->insert( $table_name, $section_data );
		} else {
			unset( $section_data['id'] );
			$is_executed = $wpdb->update( $table_name, $section_data, array( 'machine_name' => $section_data['machine_name'] ) );
		}

        if ( $is_executed !== false ) {
            // Rebuild static fields array.
            static::$sections = array();
            static::get_items( $section_data['entity_name'] );
        }

		return $is_executed ? $section_data['machine_name'] : false;
	}

	/**
	 * Generate max section order by section name.
	 *
	 * @param string $entity_name
	 *
	 * @return int|null|string
	 */
	public static function get_next_section_order( $entity_name = 'property' ) {
		global $wpdb;
		$table_name = static::get_table_name();

		$order = $wpdb->get_var( $wpdb->prepare( "SELECT MAX(`order`) 
														 FROM $table_name 
														 WHERE entity_name='%s'", $entity_name ) );

		return ! $order ? 10 : $order + 10;
	}
}
