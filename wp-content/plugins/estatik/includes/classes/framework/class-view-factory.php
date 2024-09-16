<?php

/**
 * Class Es_Framework_Field_Factory.
 */
class Es_Framework_View_Factory {

	/**
	 * Return list of fields by types and their classes.
	 *
	 * @return array
	 */
	public static function get_views_classes() {

		$classes = array(
			'tabs' => 'Es_Tabs_View',
		);

		return apply_filters( 'es_framework_field_type_classes', $classes );
	}

	/**
	 * Return fields instance.
	 *
	 * @param $view string
	 * @param $config array
	 *
	 * @return Es_Framework_View
	 */
	public static function get_view_instance( $view, $config ) {

		$instance = null;
		$classes = static::get_views_classes();

		if ( ! empty( $view ) && ! empty( $classes[ $view ] ) ) {
			/** @var Es_Framework_Base_Field $class_name */
			$class_name = $classes[ $view ];
			$instance = new $class_name( $config );
		}

		return apply_filters( 'es_framework_view_instance', $instance, $view, $config );
	}
}
