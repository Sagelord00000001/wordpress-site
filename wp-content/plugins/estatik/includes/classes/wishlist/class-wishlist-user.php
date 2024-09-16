<?php

/**
 * Class Es_Wishlist_User.
 */
class Es_Wishlist_User extends Es_User_Container {

	/**
	 * @var mixed|string
	 */
    protected $_entity_name;

	/**
	 * Es_Wishlist_User constructor.
	 *
	 * @param $user_id
	 * @param $entity_name
	 */
    public function __construct( $user_id, $entity_name = 'property' ) {
		parent::__construct( $user_id );

        $this->_entity_name = $entity_name;
    }

	/**
	 * @return string
	 */
    protected function get_meta_item_name() {
    	return 'es_wishlist_' . $this->_entity_name . '_item';
    }

	/**
	 * Return array of entities ids.
	 *
	 * @return array
	 */
	public function get_items_ids() {
		$data = parent::get_items_ids();

		if ( ! empty( $data ) ) {
			foreach ( $data as $key => $post_id ) {
				if ( get_post_status( $post_id ) != 'publish' ) {
					unset( $data[ $key ] );
					$this->remove( $post_id );
				}
			}
		}

		return $data;
	}
}
