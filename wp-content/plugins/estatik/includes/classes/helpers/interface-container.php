<?php

/**
 * Interface Es_Container
 */
interface Es_Container {

	/**
	 * @param $post_id
	 *
	 * @return mixed
	 */
	public function add( $post_id );

	/**
	 * @param $post_id
	 *
	 * @return mixed
	 */
	public function remove( $post_id );

	/**
	 * Check post in container.
	 *
	 * @param $post_id
	 *
	 * @return mixed
	 */
	public function has( $post_id );

	/**
	 * Return posts ids.
	 *
	 * @return mixed
	 */
	public function get_items_ids();
}
