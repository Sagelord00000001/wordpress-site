<?php

/**
 * Class Es_Framework_Color_Field.
 */
class Es_Framework_Color_Field extends Es_Framework_Field {

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
                                   <label for='{id}'>{caption}{label}{description}</label>
                                   <div class='es-field__color-inner'>{input}{reset}</div>
                               </div>
                           {after}",
		);

		return es_parse_args( $args, $parent );
	}
}