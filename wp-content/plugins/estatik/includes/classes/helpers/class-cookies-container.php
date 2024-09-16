<?php

/**
 * Class Es_Cookies_Container.
 */
class Es_Cookies_Container implements Es_Container {

	/**
	 * @var
	 */
	protected $_key_name;

	/**
	 * @return string
	 */
	protected function get_key_name() {
		return $this->_key_name;
	}

	/**
	 * Es_Wishlist_Cookie constructor.
	 *
	 * @param $key_name
	 */
	public function __construct( $key_name ) {
		$this->_key_name = $key_name;
	}

	/**
	 * @param $post_id
	 *
	 * @return void
	 */
	public function add( $post_id ) {
		if ( ! $this->has( $post_id ) ) {
			$data = $this->get_items_ids();
			$data[] = (int) $post_id;
			es_setcookie( $this->get_key_name(), serialize( $data ), time() + (31449600) );
		}
	}

	/**
	 * @param $post_id
	 */
	public function remove( $post_id ) {
		$data = $this->get_items_ids();
		$key = array_search( $post_id, $data );

		if ( $key !== false ) {
			unset( $data[ $key ] );

			es_setcookie( $this->get_key_name(), serialize( $data ), time() + (31449600) );
		}
	}

	/**
	 * @return integer
	 */
	public function get_count() {
		return count( $this->get_items_ids() );
	}

	/**
	 * @param $post_id
	 *
	 * @return bool
	 */
	public function has( $post_id ) {
		return in_array( $post_id, $this->get_items_ids() );
	}

	/**
	 * Return array of entities ids.
	 *
	 * @return array
	 */
	public function get_items_ids() {
		$result = array();

		if ( ! empty( $_COOKIE[ $this->get_key_name() ] ) ) {
			$result = unserialize( $_COOKIE[ $this->get_key_name() ], array( 'allowed_classes' => false ) );
		}

		return $result;
	}
}
