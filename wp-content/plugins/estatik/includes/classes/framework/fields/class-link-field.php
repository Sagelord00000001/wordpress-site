<?php

/**
 * Class Es_Framework_Link_Field.
 */
class Es_Framework_Link_Field extends Es_Framework_Base_Field {

	function get_input_markup() {
		$config = $this->get_field_config();
		$label_value = isset( $config['value']['label'] ) ? $config['value']['label'] : '';
		$url_value = isset( $config['value']['url'] ) ? $config['value']['url'] : '';

		$input = "<div class='es-row'>
					<div class='es-col-auto'><input name='" .esc_attr( $config['attributes']['name'] ) . "[label]' type='text' value='" . esc_attr( $label_value ) . "' placeholder='" . __( 'Link name', 'es' ) . "'/></div>
					<div class='es-col-auto'><input name='" .esc_attr( $config['attributes']['name'] ) . "[url]' value='" . esc_attr( $url_value ) . "' type='url' placeholder='" . __( 'URL', 'es' ) . "'/></div>
				</div>";

		return $input;
	}
}
