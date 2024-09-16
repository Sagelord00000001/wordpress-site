<?php

/**
 * Class Es_Framework_Radio_Boxed_Field.
 */
class Es_Framework_Radio_Bordered_Field extends Es_Framework_Multi_Fields_Field {

	/**
	 * @return string
	 */
	function get_input_markup() {
		$config = $this->get_field_config();

		$input = '';

		if ( empty( $config['disable_hidden_input'] ) ) {
			$input = "<input type='hidden' name='{$config['attributes']['name']}' value=''>";
		}

		if ( ! empty( $config['options'] ) ) {
		    $index = 0;
			foreach ( $config['options'] as $value => $label ) {
				$field_config = $this->get_multi_field_config( $label, $value );

                $field_config['skeleton'] = "{before}
                               <div class='es-field es-field__{field_key} es-field--{type}-item-bordered {wrapper_class}'>
                                   <div class='es-radio--bordered'>{input}<label for='{id}'>
                                        <span class='es-icon es-icon_check-mark'></span>{label}</label>
                                  </div>
                               </div>
                           {after}";

                unset( $config['before'], $config['after'], $config['wrapper_class'] );
                $config['multiple_index'] = $index;
				$field_config = es_parse_args( $field_config, $config );

				$field = new Es_Framework_Field( $this->_field_key, $field_config );

				$input .= $field->get_markup();
				$index++;
			}

			$input = strtr( $config['items_wrapper'], array(
				'{items}' => $input
			) );
		}

		return $input;
	}

	/**
	 * @return array
	 */
	public function get_default_config() {

		$default = array(
			'items_wrapper' => "<div class='es-field-row'>{items}</div>",
            'skeleton' => "{before}
                               <div class='es-field es-field__{field_key} es-field--{type} {wrapper_class}'>
                                   {label}{caption}{input}{description}
                               </div>
                           {after}",
            'items_attributes' => array(),
		);

		return es_parse_args( $default, parent::get_default_config() );
	}

    public function get_multi_field_type() {
        return 'radio';
    }
}
