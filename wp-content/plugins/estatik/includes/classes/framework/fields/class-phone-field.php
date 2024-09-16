<?php

/**
 * Class Es_Framework_Phone_Field
 */
class Es_Framework_Phone_Field extends Es_Framework_Field {

	/**
	 * @return string
	 */
	public function get_input_markup() {
		$config = $this->get_field_config();

		$config['code_config']['attributes']['data-codes'] = $config['codes'];
		$config['code_config']['attributes']['data-icons'] = $config['icons'];
		$config['tel_config']['type'] = 'text';

		if ( ! empty( $config['attributes']['disabled'] ) ) {
			$config['code_config']['attributes']['disabled'] = $config['attributes']['disabled'];
			$config['tel_config']['attributes']['disabled'] = $config['attributes']['disabled'];
		}

		$string_val = '';

		if ( is_array( $config['value'] ) ) {
			if ( ! empty( $config['value']['code'] ) ) {
				$config['code_config']['value'] = $config['value']['code'];
			}

			if ( ! empty( $config['value']['tel'] ) ) {
				$config['tel_config']['value'] = $config['value']['tel'];
				$string_val = $config['value']['tel'];
			}
		}

		if ( is_string( $config['value'] ) ) {
			$string_val = $config['value'];
		}

		if ( $config['is_country_code_disabled'] ) {
			$config['type'] = 'text';
			$config['value'] = $string_val;
			$this->_field_config = $config;
			$f = parent::get_input_markup();
		} else {
			$field_code = es_framework_get_field_html( 'code', $config['code_config'] );
			$field_phone = es_framework_get_field_html( 'tel', $config['tel_config'] );
			$f = $field_code . $field_phone;
		}

		return $f;
	}

	/**
	 * @return array
	 */
	public function get_default_config() {
		$parent_def = parent::get_default_config();

		$def = array(
			'codes' => array(),
			'icons' => array(),
			'is_country_code_disabled' => false,

			'code_config' => array(
				'type' => 'select',
				'options' => array(),
				'attributes' => array(
					'class' => 'js-es-phone-field',
					'name' => $parent_def['attributes']['name'] . '[code]',
				),
			),
			'tel_config' => array(
				'type' => 'text',
				'attributes' => array(
					'class' => 'js-es-phone',
					'maxlength' => 40,
					'minlength' => 4,
					'name' => $parent_def['attributes']['name'] . '[tel]',
				),
			),
		);

		return es_parse_args( $def, $parent_def );
	}
}
