<?php

/**
 * Class Es_Framework_Field.
 */
class Es_Framework_Date_Field extends Es_Framework_Field {

	/**
	 * @return string
	 */
	function get_input_markup() {
		$this->set_type(  'text' );
		return parent::get_input_markup();
	}

	/**
	 * Return field default config.
	 *
	 * @return array
	 */
	public function get_default_config() {

		$default = array(
			'attributes' => array(
				'class' => 'es-field__input js-es-field__date',
				'data-date-format' => 'm/d/y',
			),
		);

		return es_parse_args( $default, parent::get_default_config() );
	}
}
