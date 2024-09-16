<?php

/**
 * Class Es_User
 *
 * @property $avatar_id
 */
class Es_User extends Es_Entity {

	/**
	 * User active status.
	 */
	const STATUS_ACTIVE = 1;
	const STATUS_DISABLED = 0;

	/**
	 * @return array|mixed|void
	 */
	public static function get_default_fields() {
		return apply_filters( 'es_user_default_fields', array(
			'avatar_id' => array(
				'is_single_meta' => true,
			),
			'user_email' => array(
				'system' => true,
				'label' => __( 'Email', 'es' ),
			),
			'user_login' => array(
				'label' => __( 'Login', 'es' ),
				'system' => true,
			),
			'user_password' => array(
				'label' => __( 'Password', 'es' ),
				'system' => true,
			),
			'first_name' => array(
				'label' => __( 'First name', 'es' ),
				'system' => true,
			),
			'last_name' => array(
				'label' => __( 'Last name', 'es' ),
				'system' => true,
			),
			'name' => array(
				'type' => 'text',
				'label' => __( 'Name', 'es' ),
			),
			'status' => array(
				'default_value' => static::STATUS_ACTIVE,
			),
		) );
	}

	/**
	 * @param string $field
	 * @param mixed $value
	 */
	public function save_field_value( $field, $value ) {
		$finfo = static::get_field_info( $field );

		if ( ! empty( $finfo['system'] ) ) {
			if ( $field == 'user_password' ) {
				$value = wp_hash_password( $value );
			}
			wp_update_user( array( 'ID' => $this->get_id(), $field => $value ) );
		} else {
			parent::save_field_value( $field, $value );
		}
	}

	/**
	 * @inheritdoc
	 */
	public function get_wp_entity() {
		if ( empty( $this->_wp_entity ) ) {
			$this->_wp_entity = new WP_User( $this->get_id() );
		}

		return $this->_wp_entity;
	}

	/**
	 * Return user first name.
	 *
	 * @return string
	 */
	public function get_full_name() {
		$user = $this->get_wp_entity();
		$name = array();

		if ( ! empty( $user->first_name ) ) {
			$name[] = $user->first_name;
		}

		if ( ! empty( $user->last_name ) ) {
			$name[] = $user->last_name;
		}

		$name = ! empty( $name ) ? implode( ' ', $name ) : $user->user_login;

		return apply_filters( 'es_user_get_full_name', $name, $this );
	}

	/**
	 * @return mixed|string
	 */
	public function get_email() {
		return $this->get_wp_entity()->user_email;
	}

	/**
	 * @inheritdoc
	 */
	public function delete( $force = false ) {
		if ( ! function_exists( 'wp_delete_user' ) ) {
			require_once(ABSPATH.'wp-admin/includes/user.php');
		}

		wp_delete_user( $this->get_id() );
	}

	/**
	 * Change user status (active / disabled).
	 *
	 * @param $status
	 */
	public function change_status( $status ) {
		$old_status = $this->status;
		$this->save_field_value( 'status', $status );
		do_action( 'es_' . static::get_entity_name() . '_after_change_status', $this, $status, $old_status );
	}

	/**
	 * Deactivate an agent
	 *
	 * @return void
	 */
	public function activate() {
		$this->change_status( self::STATUS_ACTIVE );
	}

	/**
	 * Deactivate an agent
	 *
	 * @return void
	 */
	public function deactivate() {
		$this->change_status( self::STATUS_DISABLED );
	}

	/**
	 * Return entity type.
	 *
	 * @return string Return wp entity type like post or user.
	 */
	public static function get_entity_type() {
		return 'user';
	}

	/**
	 * @return string
	 */
	public function get_entity_prefix() {
		return 'es_';
	}

	/**
	 * @return string|null
	 */
	public static function get_entity_name() {
		return 'user';
	}
}
