<?php

/**
 * Base class for Estatik plugin entities Es_Entity.
 */
abstract class Es_Entity {

	/**
	 * Entity ID.
	 *
	 * @var int Post ID.
	 */
	protected $_id;

	/**
	 * @var WP_Post|WP_User
	 */
	protected $_wp_entity;

	/**
	 * Entity fields array.
	 *
	 * @var array
	 */
	public static $entity_fields;

	/**
	 * Entity construct.
	 *
	 * @param $id
	 */
	public function __construct( $id = null ) {
		$this->_id = $id;
	}


	/**
	 * Return entity field value.
	 *
	 * @param $name
	 *
	 * @return mixed
	 */
	public function __get( $name ) {
		$field = static::get_field_info( $name );
		$value = null;

		if ( ! empty( $field ) ) {
			if ( ! empty( $field['system'] ) ) {
				$entity = $this->get_wp_entity();
				$value = ! empty( $entity ) && isset( $entity->{$name} ) ? $entity->{$name} : null;
			} else {
				$value = $this->get_field_value( $name );
			}
		}

		return apply_filters( "es_get_" . static::get_entity_name() . "_field_value", $value, $name, $this );
	}

	/**
	 * Magic method for empty and isset methods.
	 *
	 * @param $name
	 *
	 * @return bool
	 */
	public function __isset( $name ) {
		$value = $this->__get( $name );
		return ! empty( $value );
	}

	/**
	 * Return entity ID.
	 *
	 * @return int|null
	 */
	public function get_id() {
		return $this->_id;
	}

	/**
	 * Return entity custom fields array.
	 *
	 * @return mixed
	 */
	public static function get_fields() {

		if ( ! static::$entity_fields ) {
			$entity = static::get_entity_name();
			static::$entity_fields = apply_filters( "es_get_{$entity}_fields", static::get_default_fields() );
		}

		return static::$entity_fields;
	}

    /**
     * Return field values.
     *
     * @param $field
     *
     * @return mixed|void
     */
	public static function get_field_values( $field ) {
	    $field_info = static::get_field_info( $field );
	    $values = ! empty( $field_info['options'] ) ? $field_info['options'] : array();
	    return apply_filters( 'es_' . static::get_entity_name() . '_get_field_values', $values, $field );
    }

	/**
	 * Return entities fields list.
	 *
	 * @return array
	 */
	public static function get_default_fields() {
		return array();
	}

	/**
	 * Return field label
	 *
	 * @param $field
	 *
	 * @return string
	 */
	public static function get_field_label( $field ) {
		$info = static::get_field_info( $field );

		return ! empty( $info['label'] ) ? $info['label'] : __( ucfirst( $field ), 'es' );
	}

	/**
	 * Return field info data.
	 *
	 * @param $field
	 *
	 * @return array
	 */
	public static function get_field_info( $field ) {
	    $fields_builder = es_get_fields_builder_instance();
		$entity = static::get_entity_name();
        $fields = $fields_builder::get_items( $entity );
		$field_info = isset( $fields[ $field ] ) ? $fields[ $field ] : array();

        if ( empty( $field_info ) && ( ( stristr( $field, 'min_' ) || stristr( $field, 'max_' ) || stristr( $field, 'from_' ) ) ) ) {
            $base_field = str_replace( 'min_', '', $field );
            $base_field = str_replace( 'from_', '', $base_field );
            $base_field = str_replace( 'max_', '', $base_field );
            $field_info = isset( $fields[ $base_field ] ) ? $fields[ $base_field ] : array();
        }

		if ( is_array( $field_info ) && ! empty( $field_info ) ) {
			$field_info = wp_parse_args( $field_info, array(
				'is_single_meta' => true,
				'use_prefix' => true,
                'base_field_key' => ! empty( $base_field ) ? $base_field : $field,
                'formatter' => 'default',
			) );
		}

		return apply_filters( "es_{$entity}_field_info", $field_info, $field );
	}

	/**
	 * Save meta for entity.
	 *
	 * @param $data
	 *
	 * @return mixed
	 */
	public function save_fields( $data ) {
		$entity = static::get_entity_name();

		if ( ! empty( $data ) ) {
		    $fields = static::get_fields();
			$data = apply_filters( "es_{$entity}_before_save_data", $data, $this->get_id(), $this );

			// Save another fields.
			foreach ( $fields as $key => $field ) {
				$value = isset( $data[ $key ] ) ? $data[ $key ] : null;

				if ( ! isset( $data[ $key ] ) && ( ! isset( $field['default_value'] ) || ! empty( $field['skip_empty_save'] ) ) ) continue;

				if ( ! isset( $data[ $key ] ) && isset( $field['default_value'] ) ) {
					$value = $field['default_value'];
				}

				if ( isset( $data[ $key ] ) && ! $value && ! isset( $field['default_value'] ) ) {
					$this->delete_field_value( $key );
				}

				if ( ( is_string( $value ) && strlen( $value ) ) || ! is_string( $value ) ) {
					$this->save_field_value( $key, $value );
				}
			}
		}

		do_action( "es_{$entity}_after_save_fields", $data, $this );
	}

	/**
	 * Return entity field value.
	 *
	 * @param $field
	 *
	 * @return mixed
	 */
	public function get_field_value( $field ) {

		$f_info = static::get_field_info( $field );
		$value = null;

		if ( ! empty( $f_info['value_callback'] ) ) {
			if ( ! empty( $f_info['value_callback'][0] ) && function_exists( $f_info['value_callback'][0] ) ) {
				if ( ! empty( $f_info['value_callback'][1] ) ) {
					$value = call_user_func_array( $f_info['value_callback'][0], $f_info['value_callback'][1] );
				} else {
					$value = call_user_func( $f_info['value_callback'][0] );
				}
			}
		} else if ( ! empty( $f_info['system'] ) ) {
            $value = $this->get_wp_entity()->{$field};
        } else {
            $type = static::get_entity_type();
            $single = ! empty( $f_info['is_single_meta'] ) || ! isset( $f_info['is_single_meta'] );

            // get_user_meta, get_post_meta functions call.
            $value = call_user_func(
                "get_{$type}_meta",
                $this->get_id(),
                $this->get_entity_prefix() . $field,
                $single
            );

			$value = ( ( is_string( $value ) && ! strlen( $value ) ) || ( ! is_string( $value ) && empty( $value ) ) ) && isset( $f_info['default_value'] ) ? $f_info['default_value'] : $value;
        }

		$entity = static::get_entity_name();

		return apply_filters( "es_{$entity}_get_field_value", $value, $field, $this );
	}

	/**
	 * Save entity field value.
	 *
	 * @param $field string
	 * @param $value mixed
	 *
	 * @return void
	 */
	public function save_field_value( $field, $value ) {
		$entity = static::get_entity_name();
		$type = static::get_entity_type();

		$f_info = static::get_field_info( $field );

		$key = ! empty( $f_info['use_prefix'] ) || ! isset( $f_info['use_prefix'] ) ? $this->get_entity_prefix() . $field : $field;
		$value = es_clean( $value );
		$value = apply_filters( "es_{$entity}_save_field_value", $value, $field, $this );

		do_action( "es_{$entity}_before_save_field_value", $value, $field, $this );

		if ( ! empty( $f_info['is_single_meta'] ) || ! isset( $f_info['is_single_meta'] ) ) {
			call_user_func( "update_{$type}_meta", $this->get_id(), $key, $value );
		} else {
			if ( ! empty( $value ) && is_array( $value ) ) {
				call_user_func( "delete_{$type}_meta", $this->get_id(), $key );
				foreach ( $value as $val ) {
					call_user_func( "add_{$type}_meta", $this->get_id(), $key, $val );
				}
			}
		}

		do_action( "es_{$entity}_after_save_field_value", $value, $field, $this );
	}

	/**
	 * @param $field
	 * @param string $value
	 */
	public function delete_field_value( $field, $value = '' ) {
		$entity = static::get_entity_name();
		$type = static::get_entity_type();

		do_action( "es_{$entity}_before_delete_field_value", $field, $value, $this );
		call_user_func( "delete_{$type}_meta", $this->get_id(), $this->get_entity_prefix() . $field, $value );
		do_action( "es_{$entity}_after_delete_field_value", $field, $value, $this );
	}

	/**
	 * Return entity prefix string.
	 *
	 * @return string
	 */
	abstract public function get_entity_prefix();

	/**
	 * Return entity object like WP_Post or WP_User.
	 *
	 * @return array|null|WP_Post|WP_User
	 */
	abstract public function get_wp_entity();

	/**
	 * Delete entity method.
	 *
	 * @param bool $force
	 * @return mixed
	 */
	abstract public function delete( $force = false );

	/**
	 * Unpublish / deactivate entity method.
	 *
	 * @return void
	 */
	abstract public function deactivate();

	/**
	 * Return entity name.
	 *
	 * @return string
	 */
	public static function get_entity_name() {
		return null;
	}

	/**
	 * Return entity type.
	 *
	 * @return string Return wp entity type like post or user.
	 */
	public static function get_entity_type() {
		return null;
	}
}
