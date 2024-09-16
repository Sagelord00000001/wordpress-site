<?php

/**
 * Class Es_Framework_Checkbox_Field.
 */
class Es_Framework_Checkbox_Field extends Es_Framework_Field {

	/**
	 * @return string
	 */
	function get_input_markup() {
		$config = $this->_field_config;

		$this->_field_config['attributes']['type'] = 'checkbox';
		$current_value = $config['value'];

		if ( $current_value == $config['attributes']['value'] ) {
			$this->_field_config['attributes']['checked'] = 'checked';
		}

		$hidden_input = empty( $config['disable_hidden_input'] ) ?
			"<input type='hidden' name='" . $config['attributes']['name'] . "' value='0'/>" : '';

		return sprintf( "
			{$hidden_input}
			<input %s/>
		", $this->build_attributes_string() );
	}

	/**
	 * Return field default config.
	 *
	 * @return array
	 */
	public function get_default_config() {

		$default = array(
			'attributes' => array(
				'value' => 1,
			),
			'skeleton' => "{before}<div class='es-field es-field__{field_key} es-field--{type} {wrapper_class}'>{input}<!----><label for='{id}'>{label}</label>{description}</div>{after}"
		);

		return es_parse_args( $default, parent::get_default_config() );
	}
}
