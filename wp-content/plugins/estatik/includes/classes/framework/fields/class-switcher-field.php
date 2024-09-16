<?php

/**
 * Class Es_Framework_Radio_Field.
 */
class Es_Framework_Switcher_Field extends Es_Framework_Field {

	/**
	 * @return string
	 */
	function get_input_markup() {
		$config = $this->_field_config;

		$this->_field_config['attributes']['type'] = 'checkbox';
		$current_value = ! is_null( $config['value'] ) ? $config['value'] : $config['default_value'];

		if ( $current_value == $config['attributes']['value'] ) {
			$this->_field_config['attributes']['checked'] = 'checked';
		}

		$disabled_class = ! empty( $config['attributes']['disabled'] ) ? 'es-switcher--disabled' : '';

		$hidden = $this->_field_config;
		$hidden['attributes'] = array();
		$hidden['type'] = 'hidden';
		$hidden['attributes']['name'] = $config['attributes']['name'];
		$hidden['value'] = '';
		$hidden['attributes']['value'] = '';
		$hidden['attributes']['id'] = empty( $hidden['attributes']['id'] ) ? $this->_field_config['attributes']['id'] : $hidden['attributes']['id'];
		$hidden['attributes']['id'] .= '-hidden';

		$hidden = es_framework_get_field_html( $this->_field_key, $hidden );

		return sprintf( "
			{$hidden}
			<label class='es-switcher {$disabled_class}'>
				<input %s/>
				<span class='es-switcher-slider es-switcher-slider--round'></span>
			</label>
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
		);

		return es_parse_args( $default, parent::get_default_config() );
	}
}
