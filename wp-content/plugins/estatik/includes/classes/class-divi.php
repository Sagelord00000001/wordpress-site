<?php

/**
 * Class Es_Divi_Init.
 */
class Es_Divi_Init {

	/**
	 * Init divi functionality.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'et_builder_ready', array( 'Es_Divi_Init', 'load_modules' ) );
		add_filter( 'et_fb_load_raw_post_content', array( 'Es_Divi_Init', 'load_raw_post_content' ), 10, 2 );
	}

	/**
	 * @param $post_content
	 * @param $post_id
	 * @return string
	 */
	public static function load_raw_post_content( $post_content, $post_id ) {
		$entity = es_get_entity_by_id( $post_id );

		if ( ! $entity ) {
			return $post_content;
		}

		if ( has_shortcode( $post_content, 'es_single_entity_page' ) && ! empty( $post_content ) ) {
			return $post_content;
		}

		return es_et_builder_estatik_get_initial_property_content();
	}

	/**
	 * @return void
	 */
	public static function load_modules() {
		if ( class_exists( 'ET_Builder_Module' ) ) {
			require_once ES_PLUGIN_CLASSES . 'class-divi-single-entity-module.php';
		}
	}
}

Es_Divi_Init::init();
