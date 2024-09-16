<?php

/**
 * Template Loader
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class Es_Template_Loader.
 */
class Es_Template_Loader {

	public static $temp_content = array();

	/**
	 * Store whether we're processing a product inside the_content filter.
	 *
	 * @var boolean
	 */
	private static $in_content_filter = false;

	/**
	 * @var bool
	 */
	private static $single_divi_template = false;

	/**
	 * @return void
	 */
	public static function init() {
		add_filter( 'template_include', array( __CLASS__, 'plugin_template_include' ) );
		add_filter( 'the_content', array( __CLASS__, 'single_content_filter' ) );
		add_filter( 'post_thumbnail_html', array( __CLASS__, 'featured_image_filter' ) );

		add_action( 'loop_start', array( __CLASS__, 'divi_post_content_loop_start' ) );
		add_action( 'loop_end', array( __CLASS__, 'divi_post_content_loop_end' ) );
	}

	/**
	 * @return void
	 */
	public static function divi_post_content_loop_start() {
		if ( class_exists( 'ET_Builder_Element' ) ) {
			$post_id = ET_Builder_Element::get_theme_builder_layout_id();
			if ( $post_id && es_get_entity_by_id( $post_id ) ) {
				static::$single_divi_template = true;
			}
		}
	}

	/**
	 * @return void
	 */
	public static function divi_post_content_loop_end() {
		if ( class_exists( 'ET_Builder_Element' ) ) {
			$post_id = ET_Builder_Element::get_theme_builder_layout_id();
			if ( $post_id && es_get_entity_by_id( $post_id ) ) {
				static::$single_divi_template = false;
			}
		}
	}

	/**
	 * @param $template
	 *
	 * @return string
	 */
	public static function plugin_template_include( $template ) {
		if ( is_tax( get_object_taxonomies( 'properties' ) ) || ( is_post_type_archive( 'properties' ) && ! is_search() ) ) {
			$template =  es_locate_template( 'front/property/archive.php' );
		}

		return $template;
	}

	/**
	 * Filter the_content method for render property single page.
	 *
	 * @param $content
	 *
	 * @return string
	 */
	public static function single_content_filter( $content ) {
		$entity = es_get_entity_by_id( get_the_ID() );

		if ( static::$single_divi_template ) {
			if ( et_builder_is_enabled( get_the_ID() ) ) {
				return $content;
			} else {
				if ( $entity ) {
					$shortcode = es_get_shortcode_instance(
						sprintf( 'es_single_%s', $entity::get_entity_name() ), array( 'id' => get_the_ID() ) );

					return $shortcode->get_content();
				}
			}
		}

		if ( ! is_main_query() || ! in_the_loop() || ! is_singular( es_builders_supported_post_types() ) ) {
			return $content;
		}

		self::$in_content_filter = true;

		// Remove the filter we're in to avoid nested calls.
		remove_filter( 'the_content', array( __CLASS__, 'single_content_filter' ) );

		$elementor_builder = es_is_elementor_builder_enabled( get_the_ID() );
		$divi_layout_active = function_exists( 'et_theme_builder_is_layout_post_type' ) && et_theme_builder_is_layout_post_type( 'properties' );
		$divi_builder = function_exists( 'et_pb_is_pagebuilder_used' ) && et_pb_is_pagebuilder_used( get_the_ID() );

		if ( ! $elementor_builder && ! $divi_builder && ! $divi_layout_active ) {
			if ( $entity ) {
				$shortcode = es_get_shortcode_instance(
					sprintf( 'es_single_%s', $entity::get_entity_name() ), array( 'id' => get_the_ID() ) );

				return $shortcode->get_content();
			}
		}

		self::$in_content_filter = false;

		return $content;
	}

	/**
	 * Prevent the main featured image on property pages.

	 * @return string
	 */
	public static function featured_image_filter( $html ) {
		if ( self::in_content_filter() || ! es_is_single_property() || ! is_main_query() ) {
			return $html;
		}

		return '';
	}

	/**
	 * Is property content filtering.
	 *
	 * @return bool
	 */
	public static function in_content_filter() {
		return (bool) self::$in_content_filter;
	}
}

add_action( 'init', array( 'Es_Template_Loader', 'init' ) );
