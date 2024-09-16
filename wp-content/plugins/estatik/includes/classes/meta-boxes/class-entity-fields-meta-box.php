<?php

/**
 * Class Es_Property_Meta_Box
 */
abstract class Es_Entity_Fields_Meta_Box {

	/**
	 * @return string
	 */
	public static function get_entity_name() {}

	/**
	 * @return string
	 */
	public static function get_post_type_name() {}

	/**
	 * @return string
	 */
	public static function get_metabox_title() {}

	/**
	 * @var string
	 */
	public static $render_field_callback = 'es_framework_field_render';

	/**
	 * Initialize property metabox.
	 *
	 * @return void
	 */
	public static function init() {
		$_class = get_called_class();
		global $pagenow;

		add_action( 'add_meta_boxes', array( $_class, 'register_meta_box' ) );

		add_filter( 'postbox_classes_' . static::get_post_type_name() . '_' . 'es_' . static::get_entity_name() . '_meta_box', array( $_class, 'add_meta_box_classes' ) );

		// Tabs content actions.
		add_action( 'es_' . static::get_entity_name() . '_metabox_tab', array( $_class, 'tab_content' ), 10, 2 );

		// Save property action.
		add_action( 'save_post_' . static::get_post_type_name(), array( $_class, 'save_entity' ) );
		add_filter( 'admin_body_class', array( $_class, 'admin_page_class' ) );
	}

	/**
	 * @param $classes
	 *
	 * @return string
	 */
	public static function admin_page_class( $classes ) {
		$post = get_post();

		if ( $post && $post->post_type == static::get_post_type_name() ) {
			$classes .= ' es-entity-form js-es-entity-form';
		}

		return $classes;
	}


	/**
	 * @param $classes
	 *
	 * @return mixed
	 */
	public static function add_meta_box_classes( $classes ) {
		array_push( $classes,' js-es-metabox es-metabox es-metabox--' . static::get_entity_name() );
		return $classes;
	}

	/**
	 * Save property action handler.
	 *
	 * @param $post_id
	 */
	public static function save_entity( $post_id ) {

		// check user capabilities
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$data = ! empty( $_GET['es_' . static::get_entity_name()] ) ? $_GET['es_' . static::get_entity_name()] : null;
		$data = ! empty( $_POST['es_' . static::get_entity_name()] ) ? $_POST['es_' . static::get_entity_name()] : $data;

		if ( $data ) {
			$entity = es_get_entity( static::get_entity_name(), $post_id );
			$result = $entity->save_fields( $data );

			if ( is_wp_error( $result ) ) {
				es_set_wp_error_flash( 'entity-form', $result );
			} else {
				if ( in_array( static::get_post_type_name(), es_builders_supported_post_types() ) ) {
					$use_divi = filter_input( INPUT_POST, 'et_pb_use_builder' );
					$elementor_editor_mode = get_post_meta( $post_id, '_elementor_edit_mode', true );
					$content_copied = get_post_meta( $post_id, 'es_post_content_copied', true );

					if ( $use_divi != 'on' ) {
						$post = get_post( $post_id );
						$entity = es_get_entity_by_id( $post_id );

						if ( $elementor_editor_mode != 'builder' ) {
							if ( stristr( $post->post_content, '[es_single' ) === false ) {
								$entity->save_field_value( 'alternative_description', $post->post_content );
								if ( $data ) {
									$data['alternative_description'] = $post->post_content;
								}
								update_post_meta( $post_id, 'es_post_content_copied', 1 );
							}
						} else {
							$is_valid_content = stristr( $post->post_content, '[es_single' ) === false && stristr( $post->post_content, 'es-single' ) === false;
							if ( ! $content_copied && empty( $property->alternative_description ) && $is_valid_content ) {
								$entity->save_field_value( 'alternative_description', $post->post_content );
								if ( $data ) {
									$data['alternative_description'] = $post->post_content;
								}
								update_post_meta( $post_id, 'es_post_content_copied', 1 );
							}
						}
					}
				}

				do_action( 'es_after_save_' . static::get_entity_name(), $post_id );
			}
		}

		if ( ! empty( $_REQUEST['bulk_edit'] ) ) {
			if ( ! empty( $_REQUEST['tax_input'] ) ) {
				foreach ( $_REQUEST['tax_input'] as $taxonomy => $items ) {
					if ( ! empty( $items ) ) {
						wp_set_object_terms( $post_id, $items, $taxonomy );
					}
				}
			}
		}
	}

	/**
	 * Check is section allowed to render.
	 *
	 * @param $section_id
	 *
	 * @return bool
	 */
	public static function can_render_tab( $section_id ) {
		$can_render = es_can_render_tab( static::get_entity_name(), $section_id );

		return apply_filters( 'es_' . static::get_entity_name() . '_meta_box_can_render_tab', $can_render, $section_id );
	}

	/**
	 * Render meta box tabs content.
	 *
	 * @param $item
	 * @param $section_id
	 */
	public static function tab_content( $item, $section_id ) {

		$fields_builder = es_get_fields_builder_instance();

		if ( $fields = $fields_builder::get_tab_fields( $section_id, static::get_entity_name() ) ) {
			foreach ( $fields as $field_key => $field_config ) {
				call_user_func( static::$render_field_callback, $field_key );
			}
		}
	}

	/**
	 * Register properties post type meta box.
	 *
	 * @return void
	 */
	public static function register_meta_box() {
		$class_name = get_called_class();
		add_meta_box( 'es_' . static::get_entity_name() . '_meta_box', static::get_metabox_title(), array( $class_name, 'render' ), static::get_post_type_name() );
	}

	/**
	 * Return property meta box tabs.
	 *
	 * @return array
	 */
	public static function get_tabs() {
		$tabs = es_get_entity_tabs( static::get_entity_name() );

		return apply_filters( 'es_' . static::get_entity_name() .  '_meta_box_tabs', $tabs );
	}

	/**
	 * Render property fields meta box.
	 *
	 * @param $post
	 * @param $meta
	 *
	 * @return void
	 */
	public static function render( $post, $meta ) {
		static::enqueue_scripts();

		es_load_template( 'admin/' . static::get_entity_name() . '/metabox.php', array(
			'tabs' => static::get_tabs(),
		) );
	}

	/**
	 * Enqueue scrips.
	 *
	 * @retun void
	 */
	public static function enqueue_scripts() {
		$f = es_framework_instance();
		$f->load_assets();
		wp_enqueue_style( 'es-metabox', ES_PLUGIN_URL . 'admin/css/metabox.min.css', array( 'es-admin', 'es-select2' ), Estatik::get_version() );
	}
}
