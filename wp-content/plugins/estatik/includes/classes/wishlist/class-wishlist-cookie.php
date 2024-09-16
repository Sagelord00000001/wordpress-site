<?php

/**
 * Class Es_Wishlist_Cookie.
 */
class Es_Wishlist_Cookie extends Es_Cookies_Container {

	/**
	 * @var
	 */
	protected $_entity_name;

	/**
	 * Es_Wishlist_Cookie constructor.
	 *
	 * @param $entity_name
	 */
	public function __construct( $entity_name = 'property' ) {
		parent::__construct( 'es_wishlist' );
		$this->_entity_name = $entity_name;
	}

	/**
	 * @param $post_id
	 *
	 * @return void
	 */
	public function add( $post_id ) {
		if ( ! $this->has( $post_id ) ) {
			$data = array();
			$data[ $this->_entity_name ] = $this->get_items_ids();
			$data[ $this->_entity_name ][] = $post_id;
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

			$wishlist = unserialize( $_COOKIE[ $this->get_key_name() ], array( 'allowed_classes' => false ) );
			$wishlist[ $this->_entity_name ] = $data;

			es_setcookie( $this->get_key_name(), serialize( $wishlist ), time() + (31449600) );
		}
	}

	/**
	 * Return array of entities ids.
	 *
	 * @return array
	 */
	public function get_items_ids() {
		$result = array();

		if ( ! empty( $_COOKIE[ $this->get_key_name() ] ) ) {
			$data = unserialize( wp_unslash( $_COOKIE[ $this->get_key_name() ] ), array( 'allowed_classes' => false ) );

			if ( ! empty( $data[ $this->_entity_name ] ) ) {
				$result = $data[ $this->_entity_name ];
			}
		}

		return $result;
	}
}
