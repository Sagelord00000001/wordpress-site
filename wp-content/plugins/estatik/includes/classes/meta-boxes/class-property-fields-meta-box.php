<?php

/**
 * Class Es_Property_Meta_Box
 */
class Es_Property_Fields_Meta_Box extends Es_Entity_Fields_Meta_Box {

	/**
	 * @var
	 */
	static $static_fields = array();

	/**
	 * @var string
	 */
	public static $render_field_callback = 'es_property_field_render';

	/**
	 * Initialize property metabox.
	 *
	 * @return void
	 */
	public static function init() {

		// Set static fields.
		if ( ! static::$static_fields ) {
			static::$static_fields = apply_filters( 'es_property_metabox_static_fields', array(
				'country', 'state', 'province', 'city', 'address', 'latitude', 'longitude', 'postal_code', 'is_address_disabled',
			) );
		}

		parent::init();
	}

	/**
	 * Check is section allowed to render.
	 *
	 * @param $section_id
	 *
	 * @return bool
	 */
	public static function can_render_tab( $section_id ) {
		$fields_builder = es_get_fields_builder_instance();
		$fields = $fields_builder::get_tab_fields( $section_id );
		$can_render = false;

		if ( $section_id == 'location' || $section_id == 'basic-facts' ) {
            $can_render = true;
        }
		 else if ( ! empty( $fields ) ) {
			foreach ( $fields as $field_key => $field_config ) {
				if ( in_array( $field_key, static::$static_fields ) ) {
					unset( $fields[ $field_key ] );
				}
			}

			$can_render = ! empty( $fields );
		}

		return apply_filters( 'es_property_meta_box_can_render_tab', $can_render, $section_id );
	}

	/**
	 * Render meta box tabs content.
	 *
	 * @param $item
	 * @param $section_id
	 */
	public static function tab_content( $item, $section_id ) {

		$fields_builder = es_get_fields_builder_instance();

		if ( $fields = $fields_builder::get_tab_fields( $section_id ) ) {
			foreach ( $fields as $field_key => $field_config ) {
				if ( ! in_array( $field_key, static::$static_fields ) ) {
					es_property_field_render( $field_key );
				}
			}
		}
	}

	/**
	 * @return void
	 */
	public static function enqueue_scripts() {
		parent::enqueue_scripts();

		wp_enqueue_script( 'es-property-metabox' );
		wp_localize_script( 'es-property-metabox', 'EstatikMetabox', array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'tr' => es_js_get_translations(),
		) );
	}

	/**
	 * @return string
	 */
	public static function get_entity_name() {
		return 'property';
	}

	/**
	 * @return string
	 */
	public static function get_post_type_name() {
		return 'properties';
	}

	/**
	 * @return string|void
	 */
	public static function get_metabox_title() {
		return __( 'Properties info', 'es' );
	}
}

Es_Property_Fields_Meta_Box::init();
