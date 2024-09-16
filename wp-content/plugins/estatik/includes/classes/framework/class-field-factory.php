<?php

/**
 * Class Es_Framework_Field_Factory.
 */
class Es_Framework_Field_Factory {

	/**
	 * Return list of fields by types and their classes.
	 *
	 * @return array
	 */
	public static function get_fields_type_classes() {

		$classes = array(
			'text' => 'Es_Framework_Field', // also number, email, tel. color etc.
			'date' => 'Es_Framework_Date_Field',
			'url' => 'Es_Framework_Field',
			'link' => 'Es_Framework_Link_Field',
			'date-time' => 'Es_Framework_Date_Time_Field',
			'repeater' => 'Es_Framework_Repeater_Field',
			'select' => 'Es_Framework_Select_Field',
			'phone' => 'Es_Framework_Phone_Field',
			'list' => 'Es_Framework_Select_Field',
			'editor' => 'Es_Framework_Editor_Field',
			'color' => 'Es_Framework_Color_Field',
			'checkbox' => 'Es_Framework_Checkbox_Field',
			'switcher' => 'Es_Framework_Switcher_Field',
			'checkboxes' => 'Es_Framework_Multiple_Checkboxes_Field',
			'radio' => 'Es_Framework_Radio_Field',
			'rating' => 'Es_Framework_Rating_Field',
			'media' => 'Es_Framework_Media_Field',
			'images' => 'Es_Framework_Images_Field',
			'color-picker' => 'Es_Framework_Iris_Color_Picker_Field',
			'incrementer' => 'Es_Framework_Incrementer_Field',
			'checkboxes-bordered' => 'Es_Framework_Checkboxes_Bordered_Field',
			'radio-bordered' => 'Es_Framework_Radio_Bordered_Field',
			'textarea' => 'Es_Framework_Textarea_Field',
			'radio-boxed' => 'Es_Framework_Radio_Boxed_Field',
			'checkboxes-boxed' => 'Es_Framework_Checkboxes_Boxed_Field',
			'fields-list-selector' => 'Es_Framework_Fields_List_Selector_Field',
			'radio-text' => 'Es_Framework_Radio_Text_Field',
			'radio-image' => 'Es_Framework_Radio_Image_Field',
			'hidden' => 'Es_Framework_Hidden_Field',
			'icon' => 'Es_Framework_Icon_Field',
			'avatar' => 'Es_Framework_Avatar_Field',
		);

		return apply_filters( 'es_framework_field_type_classes', $classes );
	}

	/**
	 * Return fields instance.
	 *
	 * @param $field_key string
	 * @param $field_config array
	 *
	 * @return Es_Framework_Base_Field
	 */
	public static function get_field_instance( $field_key, $field_config ) {

		$field_instance = null;
		$classes = static::get_fields_type_classes();

		if ( ! empty( $field_config['type'] ) && ! empty( $classes[ $field_config['type'] ] ) ) {
			/** @var Es_Framework_Base_Field $class_name */
			$class_name = $classes[ $field_config['type'] ];
			$field_instance = new $class_name( $field_key, $field_config );
		} else {
			$field_instance = new Es_Framework_Field( $field_key, $field_config );
		}

		return apply_filters( 'es_framework_field_instance', $field_instance, $field_config );
	}
}
