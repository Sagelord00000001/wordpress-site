<?php

/**
 * Class Es_Single_Shortcode
 */
class Es_Single_Property_Shortcode extends Es_Single_Entity_Shortcode {
	/**
	 * @inheritdoc
	 */
	public static function get_shortcode_name() {
		return array( 'es_single', 'es_single_property' );
	}

	public static function get_entity_name() {
		return 'property';
	}
}
