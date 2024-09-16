<?php

/**
 * Class Es_Framework_Incrementer_Field
 */
class Es_Framework_Incrementer_Field extends Es_Framework_Field {

	/**
	 * @return string
	 */
	public function get_input_markup() {
		$this->_field_config['attributes']['type'] = 'number';
		$this->_field_config['type'] = 'number';
		$this->_field_config['attributes']['value'] = $this->_field_config['value'];

		$input = parent::get_input_markup();

		$input = "<button class='js-incrementer-button' data-method='decrement'>-</button>" .
		         $input .
		         "<button class='js-incrementer-button' data-method='increment'>+</button>";

		$input = strtr( $this->_field_config['input_wrapper'], array(
			'{input}' => $input,
		) );

		return $input;
	}

	/**
	 * @return array
	 */
	public function get_default_config() {
		$default = array(
			'input_wrapper' => "<div class='es-incrementer-field js-es-incrementer-field'>{input}</div>",
		);
		return es_parse_args( $default, parent::get_default_config() );
	}
}
