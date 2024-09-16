<?php

/**
 * Class Es_Post.
 */
abstract class Es_Post extends Es_Entity {

	/**
	 * @var int
	 */
	public static $post_count = 0;

	/**
	 * @inheritdoc
	 */
	public function get_wp_entity() {

		if ( empty( $this->_wp_entity ) ) {
			$this->_wp_entity = get_post( $this->get_id() );
		}

		return $this->_wp_entity;
	}

	/**
	 * @inheritdoc
	 */
	public function get_field_value( $field ) {
		$entity = static::get_entity_name();
        $f_info = static::get_field_info( $field );

		if ( ! empty( $f_info['taxonomy'] ) ) {
			$value = wp_get_object_terms( $this->get_id(), $field, array(
			    'fields' => 'ids'
            ) );
		} else {
			return parent::get_field_value( $field );
		}

		return apply_filters( "es_{$entity}_get_field_value", $value, $field, $this );
	}

	/**
	 * @param $field
	 * @param string $value
	 */
	public function delete_field_value( $field, $value = '' ) {
		$entity = static::get_entity_name();
		$field_info = static::get_field_info( $field );

		if ( ! empty( $field_info['taxonomy'] ) ) {
			do_action( "es_{$entity}_before_delete_field_value", $field, $value, $this );
			wp_set_object_terms( $this->get_id(), array(), $field );
			do_action( "es_{$entity}_after_delete_field_value", $field, $value, $this );
		} else {
			parent::delete_field_value( $field, $value );
		}
	}

	/**
	 * Get entity author.
	 *
	 * @return false|WP_User
	 */
	public function get_author() {
		return get_user_by( 'ID', $this->get_wp_entity()->post_author );
	}

	/**
	 * @inheritdoc
	 */
	public function delete( $force = false ) {
		$entity = static::get_entity_name();
		do_action( "es_{$entity}_before_delete", $this );
		wp_delete_post( $this->get_id(), $force );
		do_action( "es_{$entity}_after_delete", $this );
	}

	/**
	 * @inheritdoc
	 */
	public function deactivate() {
		wp_update_post( array(
			'post_status' => 'draft',
			'ID' => $this->get_id(),
		) );
	}

	/**
	 * @return mixed
	 */
	public static function get_post_type_name() {
		return 'post';
	}

	/**
	 * Return entity type.
	 *
	 * @return string Return wp entity type like post or user.
	 */
	public static function get_entity_type() {
		return 'post';
	}

	/**
	 * @param string $field
	 * @param mixed $value
	 */
	public function save_field_value( $field, $value ) {
		$field_info = static::get_field_info( $field );

		if ( ! empty( $field_info['taxonomy'] ) ) {
			$value = $value ? $value : array();

			if ( is_string( $value ) ) {
				$value = array( $value );
			}

			$value_ids = array();

			foreach ( $value as $val ) {
				$value_ids[] = is_numeric( $val ) ? intval( $val ) : sanitize_text_field( $val );
			}

			$value_ids = array_filter( $value_ids );
			$value = ! empty( $value_ids ) ? $value_ids : $value;
			wp_set_post_terms( $this->get_id(), $value, $field );
		} else {
			parent::save_field_value( $field, $value );
		}
	}

	/**
	 * @return int
	 */
	public static function count() {
		if ( static::$post_count ) {
			return static::$post_count;
		}

		global $wpdb;
		static::$post_count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type='%s'", static::get_post_type_name() ) );
		return static::$post_count;
	}
}
