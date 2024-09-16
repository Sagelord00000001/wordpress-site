<?php

/**
 * Class Es_Framework_Radio_Field.
 */
class Es_Framework_Radio_Field extends Es_Framework_Multi_Fields_Field {

	/**
	 * @return string
	 */
	function get_input_markup() {
		$config = $this->get_field_config();
		$input = '';
        unset( $config['skeleton'] );

		if ( empty( $config['disable_hidden_input'] ) ) {
			$input = "<input type='hidden' name='{$config['attributes']['name']}' value=''>";
		}

		if ( ! empty( $config['options'] ) ) {
			foreach ( $config['options'] as $value => $label ) {
				$field_config = $this->get_multi_field_config( $label, $value );

                unset( $config['before'], $config['after'] );
				$field_config = es_parse_args( $field_config, $config );

				$field = new Es_Framework_Field( $this->_field_key, $field_config );
				$input .= $field->get_markup();
			}
		}

		return $input;
	}

	/**
	 * Return field default config.
	 *
	 * @return array
	 */
	public function get_default_config() {

		$default = array(
            'items_attributes' => array(),
            'skeleton' => "{before}
                               <div class='es-field es-field__{field_key} es-field--{type}-multiple {wrapper_class}'>
                                   {label}{caption}{hidden_input}{input}{description}
                               </div>
                           {after}",
		);

		return es_parse_args( $default, parent::get_default_config() );
	}

    public function get_multi_field_type() {
        return 'radio';
    }
}
