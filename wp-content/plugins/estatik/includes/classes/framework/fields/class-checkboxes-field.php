<?php

/**
 * Class Es_Framework_Multiple_Checkboxes_Field.
 */
class Es_Framework_Multiple_Checkboxes_Field extends Es_Framework_Base_Field {

	/**
	 * @return string
	 */
	function get_input_markup() {
		$config = $this->get_field_config();
		$values = ! is_array( $config['value'] ) ? array( $config['value'] ) : $config['value'];
		$values = array_filter( $values );
		$values = empty( $values ) && ! empty( $config['default_value'] ) ? $config['default_value'] : $values;
		$values = is_array( $values ) ? $values : array( $values );

		$framework = Es_Framework::get_instance();
		$field_factory = $framework->fields_factory();
        $input = '';

		if ( empty( $config['disable_hidden_input'] ) ) {
            $input = "<input type='hidden' name='{$config['attributes']['name']}' value=''>";
        }

		if ( ! empty( $config['options'] ) ) {
		    $counter = 1;
		    $index = 0;

			foreach ( $config['options'] as $value => $label ) {
				$input_config = array(
					'attributes' => array(
						'value' => $value,
						'name' => $config['attributes']['name'] . "[{$index}]",
						'id' => $config['attributes']['id'] . '-' . $value
					),
					'disable_hidden_input' => true,
					'label' => $label,
					'type' => 'checkbox',
					'value' => in_array( $value, $values ) ? $value : '',
                    'wrapper_class' => $config['visible_items'] && $config['visible_items'] < $counter ?
                        'es-field--visibility es-hidden' : ''
				);

				$index++;

				unset( $config['skeleton'] );

				if ( ! empty( $config['items_attributes'][ $value ] ) ) {
					$input_config = es_parse_args( $config['items_attributes'][ $value ], $input_config );
				}

				$input_config = es_parse_args( $input_config, $config );
				unset( $input_config['before'], $input_config['after'] );
				$field = $field_factory::get_field_instance( $this->_field_key, $input_config );
				$input .= $field->get_markup();

				$counter++;
            }

            if ( $config['visible_items'] && $config['visible_items'] < count( $config['options'] ) ) {
                $input .= strtr( $config['show_more_button'], array( '{button_label}' => $config['button_label'] ) );
            }
		}

		return $input;
	}

	/**
	 * @return array
	 */
	public function get_default_config() {

		$default = array(
		    'visible_items' => false,
		    'disable_hidden_input' => true,
            'button_label' => __( 'Show more', 'es' ),
            'show_more_button' => "<a href='#' class='js-es-field__show-more es-field__show-more es-field--visibility'>{button_label}</a>",
            'skeleton' => "{before}
                               <div class='js-es-field es-field es-field__{field_key} es-field--{type} {wrapper_class}'>
                                   {label}{caption}{input}{description}
                               </div>
                           {after}",
            'items_attributes' => array(),
		);

		return es_parse_args( $default, parent::get_default_config() );
	}
}
