<?php

/**
 * Class Es_Framework_Radio_Boxed_Field.
 */
class Es_Framework_Radio_Boxed_Field extends Es_Framework_Multi_Fields_Field {

	/**
	 * @return string
	 */
	function get_input_markup() {
		$config = $this->get_field_config();
		$input = '';
		$type_class = $config['type'] == 'radio-text' ? 'es-box--titled-text' : null;

		if ( ! empty( $config['options'] ) ) {
			foreach ( $config['options'] as $value => $label ) {
				$input_field = '';
				$field_config = $this->get_multi_field_config( $label, $value );

                unset( $config['before'], $config['after'] );

				$field_config = es_parse_args( $field_config, $config );

				$field = new Es_Framework_Field( $this->_field_key, $field_config );

				$input_field .= $field->get_input_markup();

				$input .= strtr( $config['item_wrapper'], array(
					'{input}' => $input_field,
					'{label}' => $label,
					'{id}' => $field_config['attributes']['id'],
					'{type}' => $type_class,
					'{size}' => $config['size'] ? "es-col-{$config['size']}" : 'es-col',
					'{item_class}' => $config['item_class'],
				) );
			}
		}

		return strtr( $config['items_wrapper'], array(
			'{items}' => $input,
		) );
	}

	/**
	 * @return array
	 */
	public function get_default_config() {

		// es-radio-image

		$default = array(
			'disable_labels' => false,
			'item_class' => '',
			'size' => 4, // From 1 to 12
			'items_wrapper' => "<div class='es-row js-es-boxes'>{items}</div>",
			'wrapper_class' => "es-field es-field__{field_key} es-field--radio-boxed es-field--{type}",
			'item_wrapper' => "<div class='{size}'>{input}<label class='es-box es-box--input es-box--bordered js-es-box--input {type} {item_class}' for='{id}'><span class='es-icon es-icon_check-mark'></span>{label}</label></div>",
			'skeleton' => "{before}<div class='{wrapper_class}'>{label}{caption}{input}{description}</div>{after}",
		);

		return es_parse_args( $default, parent::get_default_config() );
	}

    public function get_multi_field_type() {
        return 'radio';
    }
}
