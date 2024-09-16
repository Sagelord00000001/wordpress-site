<?php

/**
 * Class Es_My_Entities_Shortcode.
 */
abstract class Es_My_Entities_Shortcode extends Es_Shortcode {

	/**
	 * @var WP_Query
	 */
	protected $_query;

	/**
	 * Return query args array for WP_Query object.
	 *
	 * @return mixed
	 */
	abstract public function get_query_args();

	/**
	 * @param $attributes
	 */
	public function merge_shortcode_attr( $attributes ) {
		$request_attributes = empty( $attributes['ignore_search'] ) ? es_clean( $_GET ) : array();

		if ( ! empty( $request_attributes ) ) {
			$attributes = es_parse_args( $request_attributes, $attributes );
		}

		$default = $this->get_default_attributes();
		$this->_attributes = ! empty( $attributes ) ? es_parse_args( $attributes, $default ) : $default; // ?????
	}

	/**
	 * @param $query
	 */
	public function set_query( $query ) {
		$this->_query = $query;
	}

	/**
	 * @return WP_Query
	 */
	public function get_query() {
		if ( ! $this->_query ) {
			$this->set_loop_uid();
			$query_args = $this->get_query_args();
			$this->_query = new WP_Query( $query_args );
			$this->_query->query_vars['loop_uid'] = $this->_attributes['loop_uid'];
		}

		return $this->_query;
	}

	/**
	 * Set loop id for correct work of pagination & sort.
	 *
	 * @return void
	 */
	protected function set_loop_uid() {
		if ( empty( $this->_attributes['loop_uid'] ) ) {
			global $agencies_shortcode_counter;
			$agencies_shortcode_counter++;
			$this->_attributes['loop_uid'] = $agencies_shortcode_counter;
		}
	}
}
