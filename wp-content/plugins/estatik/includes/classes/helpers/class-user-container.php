<?php

/**
 * Class Es_Wishlist_User.
 */
abstract class Es_User_Container implements Es_Container {

	/**
	 * @var integer
	 */
	protected $user_id;

	/**
	 * Es_User_Container constructor.
	 *
	 * @param $user_id
	 */
	public function __construct( $user_id ) {
		$this->user_id = $user_id;
	}

	/**
	 * @return mixed
	 */
	abstract protected function get_meta_item_name();

	/**
	 * @param $post_id
	 *
	 * @return bool|false|int
	 */
	public function add( $post_id ) {
		if ( ! $this->has( $post_id ) ) {
			return add_user_meta( $this->user_id, $this->get_meta_item_name(), $post_id );
		}

		return true;
	}

	/**
	 * @param $post_id
	 *
	 * @return bool
	 */
	public function remove( $post_id ) {
		return delete_user_meta( $this->user_id, $this->get_meta_item_name(), $post_id );
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
		$data = $this->get_items_ids();

		return in_array( $post_id, $data );
	}

	/**
	 * Return array of entities ids.
	 *
	 * @return array
	 */
	public function get_items_ids() {
		$data = get_user_meta( $this->user_id, $this->get_meta_item_name() );

		return $data ? $data : array();
	}
}
