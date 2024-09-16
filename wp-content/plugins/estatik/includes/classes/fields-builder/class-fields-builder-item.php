<?php

/**
 * Class Es_Fields_Builder_Item.
 */
abstract class Es_Fields_Builder_Item {

    const FORCE_RELOAD_ITEMS = true;

	/**
	 * Return fields builder item entity table name.
	 *
	 * @return string
	 */
	public static function get_table_name() {}

	/**
	 * @param $save_data
	 */
	public static function save_item( $save_data ) {}

	/**
	 * Return items list.
	 *
	 * @param string $entity
	 *
	 * @return array
	 */
	public static function get_items( $entity = 'property' ) {}

	/**
	 * Return visible for list.
	 *
	 * @return array
	 */
	public static function get_visible_for() {
		return apply_filters( 'es_fields_builder_get_visible_for', array(
			'all_users' => _x( 'All users', 'field builder visible for', 'es' ),
			'admin' => _x( 'Admins', 'field builder visible for', 'es' ),
			'agents' => _x( 'Agents', 'field builder visible for', 'es' ),
			'buyers' => _x( 'Buyers', 'field builder visible for', 'es' ),
			'authenticated_users' => _x( 'Authenticated Users', 'field builder visible for', 'es' ),
		) );
	}

	/**
	 * Return items by machine name.
	 *
	 * @param $machine_name
	 * @param string $entity
	 *
	 * @return mixed|null
	 */
	public static function get_item_by_id( $id, $entity = 'property' ) {

		if ( empty( $id ) ) return null;

		$items = static::get_items( $entity );

		if ( ! empty( $items ) ) {
			foreach ( $items as $item ) {
				if ( ! empty( $item['id'] ) && $item['id'] == $id ) {
					return $item;
				}
			}
		}

		return null;
	}

	/**
	 * Return items by machine name.
	 *
	 * @param $machine_name
	 * @param string $entity
	 *
	 * @return mixed|null
	 */
	public static function get_item_by_machine_name( $machine_name, $entity = 'property' ) {

		if ( empty( $machine_name ) ) return null;

		$items = static::get_items( $entity );

		if ( ! empty( $items ) ) {
			foreach ( $items as $item ) {
				if ( $item['machine_name'] == $machine_name ) {
					return $item;
				}
			}
		}

		return null;
	}

    /**
     * @param $save_data
     */
    public static function save_item_order( $save_data ) {
        $save_data = wp_parse_args( $save_data, array(
            'order' => '10',
            'id' => '',
        ) );

        global $wpdb;

        $wpdb->update( static::get_table_name(), array( 'order' => $save_data['order'] ), array( 'id' => $save_data['id'] ) );
    }

	/**
	 * @param $title
	 * @param $raw_title
	 * @param $context
	 *
	 * @return array|string|string[]
	 */
	public static function sanitize_title_custom( $title, $raw_title ) {
		$title = str_replace( " ","-", $raw_title );
		return $title;
	}

	/**
	 * Generate and return unique field machine name.
	 *
	 * @param $item_name
	 * @param int $index
	 *
	 * @return string
	 */
	public static function get_unique_machine_name( $item_name, $index = 0 ) {
		$item_name = strtolower( sanitize_title( es_create_slug_transliterator ($item_name) ) );
		$unique_item_name = $item_name;

		global $wpdb;
		$table_name = static::get_table_name();

		if ( $index ) {
			$unique_item_name .= '-' . $index;
		}

		$count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $table_name WHERE machine_name='%s'", $unique_item_name ) );

		if ( ! $count ) {
			if ( get_called_class() === 'Es_Fields_Builder' ) {
				$def_items = es_get_entity_default_fields( 'property' );
			} else {
				$def_items = es_get_default_sections( 'property' );
			}

			if ( ! empty( $def_items[ $unique_item_name ] ) ) {
				$index++;
				$unique_item_name = $item_name . '-' . $index;
				return ! static::exists( $unique_item_name ) ? $unique_item_name : static::get_unique_machine_name( $item_name, ++$index );
			}
		}

		return $count ? static::get_unique_machine_name( $item_name, ++$index ) : $unique_item_name;
	}

	/**
	 * Delete fields builder item from database.
	 *
	 * @param $machine_name
	 *
	 * @return false|int
	 */
	public static function delete_item_by_id( $id ) {
		global $wpdb;

		return $wpdb->delete( static::get_table_name(), array( 'id' => $id ) );
	}

	/**
	 * Delete fields builder item from database.
	 *
	 * @param $machine_name
	 *
	 * @return false|int
	 */
	public static function delete_item( $machine_name ) {
		global $wpdb;

		return $wpdb->delete( static::get_table_name(), array( 'machine_name' => $machine_name ) );
	}

	/**
	 * @param $machine_name
	 *
	 * @return null|string
	 */
	public static function exists( $machine_name ) {
		global $wpdb;
		$table_name = static::get_table_name();

		return $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$table_name} WHERE machine_name='%s'", $machine_name ) );
	}
}
