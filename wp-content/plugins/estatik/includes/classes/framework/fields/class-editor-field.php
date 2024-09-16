<?php

/**
 * Class Es_Framework_Texarea_Field.
 */
class Es_Framework_Editor_Field extends Es_Framework_Base_Field {

	/**
	 * Return field default config.
	 *
	 * @return array
	 */
	public function get_default_config() {
		$config = parent::get_default_config();

		return es_parse_args( $config, array(
			'use_media' => true,
			'editor_id' => 'editor-' . uniqid(),
		) );
	}

	/**
	 * @return string
	 */
	function get_input_markup() {
		$config = $this->get_field_config();
		ob_start();
		wp_editor( $config['value'], $config['editor_id'], array(
			'textarea_name' => $config['attributes']['name'],
			'textarea_rows' => 6,
			'media_buttons' => $config['use_media'],
		) );
		return ob_get_clean();
	}
}
