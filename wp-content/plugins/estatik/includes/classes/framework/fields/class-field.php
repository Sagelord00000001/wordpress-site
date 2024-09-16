<?php

/**
 * Class Es_Framework_Field.
 */
class Es_Framework_Field extends Es_Framework_Base_Field {

	/**
	 * @return string
	 */
	function get_input_markup() {
		$this->_field_config['attributes']['type'] = esc_attr( $this->_field_config['type'] );
		$this->_field_config['attributes']['value'] = esc_attr( $this->_field_config['value'] );
		$input = sprintf( "<input %s/>", $this->build_attributes_string() );

		$count = is_string( $this->_field_config['value'] ) ? strlen( $this->_field_config['value'] ) : 0;
		$strlen = ! empty( $this->_field_config['attributes']['maxlength'] ) && ! empty( $this->_field_config['enable_counter'] ) ?
			"<span class='es-field__strlen'><span class='js-es-strlen'>$count</span> / {$this->_field_config['attributes']['maxlength']} " . __( 'characters remaining', 'es' ) . "</span>" : '';

		$input .= $strlen;

		if ( $this->_field_config['type'] == 'password' ) {
		    $input .= "<a href='#' class='es-toggle-pwd js-es-toggle-pwd'><span class='es-icon es-icon_eye'></span></a>";
        }
		return $input;
	}

    /**
     * Return field default config.
     *
     * @return array
     */
    public function get_default_config() {
        $parent = parent::get_default_config();
        $args = array(
            'skeleton' => "{before}
                               <div class='es-field es-field__{field_key} es-field--{type} {wrapper_class}'>
                                   <label for='{id}'>{label}{caption}{unit_before}{input}{unit_after}{description}</label>
                               </div>
                           {after}",
            'unit_field_before_config' => array(),
            'unit_field_after_config' => array(),
            'unit_field_before_key' => '',
            'unit_field_after_key' => '',
        );

        return es_parse_args( $args, $parent );
    }

    /**
     * @param $position
     *
     * @return string
     */
    public function get_unit_input_markup( $position ) {
        $config = $this->get_field_config();

        if ( ! empty( $config['unit_field_' . $position . '_config'] ) && is_array( $config['unit_field_' . $position . '_config'] ) ) {
            $field = es_framework_get_field( $config['unit_field_' . $position . '_key'], $config['unit_field_' . $position . '_config'] );
            return $field->get_markup();
        } else {
            return is_string( $config['unit_field_' . $position . '_config'] ) ?
	            $config['unit_field_' . $position . '_config'] : '';
        }
    }

    /**
     * @inheritDoc
     */
    public function get_tokens() {

        $parent = parent::get_tokens();

        return es_parse_args( $parent , array(
            '{unit_before}' => $this->get_unit_input_markup( 'before' ),
            '{unit_after}' => $this->get_unit_input_markup( 'after' ),
        ) );
    }
}
