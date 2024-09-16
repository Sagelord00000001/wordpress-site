<?php

/**
 * Class Es_Framework_Iris_Color_Picker_Field.
 */
class Es_Framework_Iris_Color_Picker_Field extends Es_Framework_Field {

	/**
	 * @return string
	 */
	function get_input_markup() {
		$this->_field_config['attributes']['class'] .= ' js-es-iris-color-picker';

		return parent::get_input_markup();
	}

	/**
	 * @return array
	 */
	public function get_default_config() {
		$default = array(
			'skeleton' => "{before}<div class='es-field es-field__{field_key} es-field--{type} {wrapper_class}'><label for='es-field-{id}'>{label}</label>{input}{description}</div>{after}",
		);
		return es_parse_args( $default, parent::get_default_config() );
	}

	public function render() {
		parent::render();
	}
}
