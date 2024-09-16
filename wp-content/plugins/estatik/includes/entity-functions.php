<?php

/**
 * Check visible permissions for fb field or section.
 *
 * @param $fb_item array Section or Field config.
 * @param string $entity_name
 * @param string $context
 *
 * @return bool
 * @see es_property_get_field_info()
 */
function es_is_visible( $fb_item, $entity_name = 'property', $context = 'field' ) {
	$is_visible = true;

	if ( empty( $fb_item ) ) {
		$is_visible = false;
	} else {
		if ( $context == 'section' && es_is_entity_default_section_deactivated( $fb_item['machine_name'], $entity_name ) ) {
			$is_visible = false;
		} else if ( $context == 'field' && es_is_entity_default_field_deactivated( $fb_item['machine_name'], $entity_name ) ) {
			$is_visible = false;
		} else if ( isset( $fb_item['is_visible'] ) ) {
			if ( ! $fb_item['is_visible'] ) {
				$is_visible = false;
			} else {
				if ( ! empty( $fb_item['is_visible_for'] ) ) {
					$visible_for = maybe_unserialize( $fb_item['is_visible_for'] );

					if ( in_array( 'all_users', $visible_for ) ) {
						$is_visible = true;
					} else if ( in_array( 'admin', $visible_for ) && current_user_can('administrator') ) {
						$is_visible = true;
					} else if ( in_array( 'authenticated_users', $visible_for ) && is_user_logged_in() ) {
						$is_visible = true;
					} else {
						$is_visible = false;
					}
				}
			}
		}
	}

	return apply_filters( 'es_is_visible', $is_visible, $fb_item );
}

if ( ! function_exists( 'es_get_the_section_fields_html' ) ) {

	/**
	 * @param $section
	 * @param $post_id
	 *
	 * @return mixed|void
	 */
	function es_get_the_section_fields_html( $section, $post_id = 0 ) {
		$fb_instance = es_get_fields_builder_instance();
		$entity = es_get_entity_by_id( $post_id );
		$entity_name = $entity::get_entity_name();
		$fields = $fb_instance::get_section_fields( $section['machine_name'], $entity_name );
		$content = null;
		$need_terms_more_link = false;

        if ( ! empty( $section['frontend_action'] ) ) {
            ob_start();
            do_action( $section['frontend_action'], $post_id, $section );
            $content = ob_get_clean();
        } else if ( ! empty( $fields ) ) {
			ob_start();
            include es_locate_template( 'front/partials/entity-section-content.php' );
            $content = ob_get_clean();
		}

		if ( $section['machine_name'] == 'features' && $need_terms_more_link ) {
			$label = __( 'Show all amenities & features', 'es' ) . '<span class="es-icon es-icon_chevron-bottom"></span>';
			$toggle_label = __( 'Hide', 'es' ) . '<span class="es-icon es-icon_chevron-top"></span>';
			$content .= sprintf( "<a href='#' data-container='.es-property_section--features' 
                class='es-full-content-link es-secondary-color js-es-toggle-class' 
                data-toggle-label='%s'
                data-class='es-property_section--features--show-all'>%s</a>", esc_attr( $toggle_label ), $label );
		}

		return apply_filters( 'es_get_the_section_fields_html', $content, $section, $post_id );
	}
}

/**
 * @param $str
 *
 * @return string
 */
function es_create_slug_transliterator ($str) {
	if (preg_match('/^[a-zA-Z0-9_\-!?+\-]*$/', $str)) {
        return $str; 
    } else {
        $string = transliterator_transliterate("Any-Latin; NFD; [:Nonspacing Mark:] Remove; NFC; [:Punctuation:] Remove; Lower();", $str);
  		return $string; 
    }
}