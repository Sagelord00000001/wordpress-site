<?php

/**
 * Class Es_Framework_View.
 */
abstract class Es_Framework_View {

	/**
	 * @var array
	 */
	protected $_args;

	/**
	 * Es_Tabs_View constructor.
	 *
	 * @param $args
	 */
	public function __construct( $args ) {
		$this->_args = $args;
	}

	/**
	 * @return array
	 */
	public function get_args() {
		return $this->_args;
	}

	/**
	 * Render UI element.
	 */
	abstract public function render();
}
