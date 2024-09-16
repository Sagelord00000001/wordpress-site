<?php

/**
 * Class Es_Framework_Select_Field.
 */
class Es_Framework_Select_Field extends Es_Framework_Base_Field {

	/**
	 * @return string
	 */
	function get_input_markup() {
		$config = $this->_field_config;

		// Prepare selectbox values to array.
		$values = ! is_array( $config['value'] ) ? array( $config['value'] ) : $config['value'];

		// Get placeholder string and remove it from input attributes.
		$placeholder = $config['attributes']['placeholder'];

		$options = ! empty( $config['attributes']['placeholder'] ) ? "<option value=''>$placeholder</option>" : '';

		unset( $config['attributes']['placeholder'] );

		if ( ! empty( $config['options_callback'] ) && empty( $config['options'] ) ) {
			if ( is_string( $config['options_callback'] ) ) {
				$config['options'] = call_user_func( $config['options_callback'] );
			}
		}

		if ( ! empty( $config['options'] ) ) {
			foreach ( $config['options'] as $value => $label ) {
				$selected = selected( in_array( $value, $values ), true, false );
				if ( $value == '' && $label == '' && empty( $placeholder ) ) {
					$options .= "<option></option>";
				} else {
					$options .= "<option value='{$value}' {$selected}>{$label}</option>";
				}
			}
		} else if ( ! empty( $config['ajax_term_id_field'] ) && ! empty( $values ) ) {
			foreach ( $values as $value ) {
				$selected = selected( 1, 1, false );
				$label = $value ? get_term_field( 'name', $value ) : '';
				if ( $value == '' && $label == '' && empty( $placeholder ) ) {
					$options .= "<option></option>";
				} else {
					$options .= "<option value='{$value}' {$selected}>{$label}</option>";
				}
			}
		}

		return sprintf( "
			<select %s>%s</select>
		", $this->build_attributes_string(), $options );
	}

	/**
	 * @return array|bool[]
	 */
	public function get_default_config() {
		return array_merge( parent::get_default_config(), array(
			'ajax_term_id_field' => false,
		) );
	}
}
