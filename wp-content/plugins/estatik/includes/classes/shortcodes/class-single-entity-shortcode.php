<?php

/**
 * Class Es_Single_Shortcode
 */
abstract class Es_Single_Entity_Shortcode extends Es_Shortcode {

	/**
	 * Return entity name.
	 *
	 * @return string
	 * @throws Exception
	 */
	public static function get_entity_name() {
		throw new Exception( 'Set entity name in child class.' );
	}

	/**
	 * Return shortcode default atts.
	 *
	 * @return array
	 */
	public function get_default_attributes() {
		return array(
			'id' => get_the_ID(),
		);
	}

	/**
	 * Return shortcode content.
	 *
	 * @return string
	 */
	public function get_content() {
		$attr = $this->get_attributes();
		$content = '';

		if ( ! empty( $attr['id'] ) ) {
			ob_start();
			$entity = es_get_entity( static::get_entity_name(), $attr['id'] );

			$query = new WP_Query( array(
				'post_type' => $entity::get_post_type_name(),
				'p' => $attr['id']
			) );

			if ( is_singular( $entity::get_post_type_name() ) ) {
				es_load_template( static::get_single_template_path(), array(
					'entity_name' => $entity::get_entity_name(),
				) );
			} else {
				if ( $query->have_posts() ) {
					while( $query->have_posts() ) {
						$query->the_post();
						es_load_template( static::get_single_template_path(), array(
							'entity_name' => $entity::get_entity_name(),
						) );
					}
				}
				wp_reset_postdata();
			}

			$content = ob_get_clean();
		}

		return $content;
	}

	/**
	 * @return string
	 * @throws Exception
	 */
	public static function get_single_template_path(){
		$locate_template = es_locate_template( 'front/' . static::get_entity_name() . '/single.php' );
		return file_exists( $locate_template ) ? 'front/' . static::get_entity_name() . '/single.php' : 'front/entity/single.php';
	}

	/**
	 * @inheritdoc
	 */
	public static function get_shortcode_name() {
		return 'es_single_' . static::get_entity_name();
	}
}
