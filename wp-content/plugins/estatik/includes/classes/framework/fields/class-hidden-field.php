<?php

/**
 * Class Es_Framework_Hidden_Field.
 */
class Es_Framework_Hidden_Field extends Es_Framework_Field {

	public function get_markup() {
		$this->set_type( 'hidden' );
		return parent::get_input_markup();
	}
}
