<?php

/**
 * Class Es_Framework_Texarea_Field.
 */
class Es_Framework_Textarea_Field extends Es_Framework_Base_Field {

	/**
	 * @return string
	 */
	function get_input_markup() {
		$config = $this->get_field_config();
		$count = is_string( $config['value'] ) ? strlen( $config['value'] ) : 0;
		$strlen = ! empty( $config['attributes']['maxlength'] ) && ! empty( $config['enable_counter'] ) ?
			"<span class='es-field__strlen'><span class='js-es-strlen'>$count</span> / {$config['attributes']['maxlength']} " . __( 'characters remaining', 'es' ) . "</span>" : '';
		return sprintf( "<textarea %s>%s</textarea>$strlen", $this->build_attributes_string(), $config['value'] );
	}
}
