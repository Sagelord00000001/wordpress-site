<?php

/**
 * @param $name
 * @param string $validator_callback
 *
 * @return mixed
 */
function es_get( $name, $validator_callback = 'sanitize_text_field' ) {
	if ( $validator_callback )
		return call_user_func_array( $validator_callback, array( filter_input( INPUT_GET, $name ) ) );
	else
		return filter_input( INPUT_GET, $name );
}

/**
 * @param $name
 * @param string $validator_callback
 *
 * @return mixed
 */
function es_post( $name, $validator_callback = 'sanitize_text_field' ) {
	if ( $validator_callback )
		return call_user_func_array( $validator_callback, array( filter_input( INPUT_POST, $name ) ) );
	else
		return filter_input( INPUT_POST, $name );
}

/**
 * Return estatik setting value.
 *
 * @param $name
 * @return mixed
 */
function ests( $name ) {
    $settings = es_get_settings_container();
    return apply_filters( 'es_get_setting', $settings->{$name}, $name );
}

/**
 * @param $name
 * @return mixed
 */
function ests_label( $name ) {
	$settings = es_get_settings_container();
	return apply_filters( 'es_get_setting_label', $settings->get_label( $name, ests( $name ) ) );
}

/**
 * Return estatik setting values list.
 *
 * @param $name
 * @return mixed
 */
function ests_values( $name ) {
    $settings = es_get_settings_container();
    return apply_filters( 'es_get_setting_values', $settings::get_available_values( $name ), $name );
}

/**
 * Return estatik setting values list.
 *
 * @param $name
 * @return mixed
 */
function ests_default( $name ) {
	$settings = es_get_settings_container();
	return apply_filters( 'es_get_setting_default_value', $settings->get_default_value( $name ), $name );
}

/**
 * Return estatik setting selected values.
 *
 * @param $name
 * @return mixed
 */
function ests_selected( $name ) {
    $settings = es_get_settings_container();
    return apply_filters( 'es_get_setting_selected_options', $settings->get_selected_options( $name ), $name );
}

/**
 * @param $name
 * @param $value
 */
function ests_save_option( $name, $value ) {
	$settings = es_get_settings_container();
	$settings->save_one( $name, $value );
}

/**
 * Return current URL.
 *
 * @return string
 */
function es_get_current_url() {
	return set_url_scheme( '//' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
}

/**
 * @param $page_name
 *
 * @param $default
 *
 * @return mixed|void
 */
function es_get_page_url( $page_name, $default = null ) {
	$page_id = ests( sprintf( '%s_page_id', $page_name ) );
	$link = null;

	if ( $page_id && get_post_status( $page_id ) == 'publish' ) {
		$link = get_permalink( $page_id );
	}

	return apply_filters( 'es_get_page_url', $link ? $link : $default, $page_id );
}

if ( ! function_exists( 'es_parse_args' ) ) {

	/**
	 * Recursive function for parse array args.
	 *
	 * @param $args
	 * @param $defaults
	 *
	 * @return array
	 */
	function es_parse_args( &$args, $defaults ) {
		$args     = (array) $args;
		$defaults = (array) $defaults;
		$result   = $defaults;

		foreach ( $args as $k => &$v ) {
			if ( is_array( $v ) && isset( $result[ $k ] ) ) {
				$result[ $k ] = es_parse_args( $v, $result[ $k ] );
			} else {
				$result[ $k ] = $v;
			}
		}

		return $result;
	}
}

/**
 * @return mixed|void
 */
function es_get_address_fields_list() {
	return apply_filters( 'es_get_address_fields_list', array( 'country', 'state', 'province', 'city', 'postal_code' ) );
}

/**
 * Return notification markup.
 *
 * @param $message
 * @param string $type
 *
 * @return string
 */
function es_get_notification_markup( $message, $type = 'success' ) {
	$markup = "<div class='es-notification es-notification--{$type}'>{$message}<a href='#' class='es-notification__close js-es-notification-close'>×</a></div>";

	return apply_filters( 'es_get_notification_markup', $markup, $message, $type );
}

/**
 * Return flash messages container.
 *
 * @param $context
 *
 * @return Es_Flash_Message
 */
function es_get_flash_instance( $context ) {
	return apply_filters( 'es_get_flash_instance', new Es_Flash_Message( $context ) );
}

/**
 * Set flash message.
 *
 * @param $context string
 * @param $message string
 * @param $type string
 */
function es_set_flash( $context, $message, $type = 'success' ) {
	$container = es_get_flash_instance( $context );
	$container->set_message( $message, $type );
}

/**
 * Set flash message.
 *
 * @param $context string
 * @param $wp_error WP_Error
 */
function es_set_wp_error_flash( $context, $wp_error ) {
	$container = es_get_flash_instance( $context );
	$container->set_wp_error( $wp_error );
}

/**
 * Return pages list.
 *
 * @return array
 */
function es_get_pages() {
	$pages = get_pages();

	return $pages ? wp_list_pluck( $pages, 'post_title', 'ID' ) : array();
}

/**
 * Return field nonce.
 *
 * @param string $name
 *
 * @return null
 */
function es_get_nonce( $name = '_wpnonce' ) {
	return isset( $_REQUEST[ $name ] ) ? $_REQUEST[ $name ] : null;
}

/**
 * Render filter field for properties archive page.
 *
 * @return void
 */
function es_entities_filter_field_render( $field_key, $field_config ) {

	$filter = es_clean( filter_input( INPUT_GET, 'entities_filter', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY ) );

	$field_config = es_parse_args( $field_config, array(
		'attributes' => array(
			'name' => "entities_filter[{$field_key}]",
            'data-value' => es_clean( ! empty( $filter[ $field_key ] ) ? $filter[ $field_key ] : null ),
		),
		'value'      => ! empty( $filter[ $field_key ] ) ? $filter[ $field_key ] : null,
	) );

	es_framework_field_render( $field_key, $field_config );
}

if ( ! function_exists( 'es_property_field_render' ) ) {

	/**
	 * Render property field.
	 *
	 * @param $field_key
	 * @param $field_config
	 */
	function es_property_field_render( $field_key, $field_config = array() ) {
		$property = es_get_the_property();

		if ( ( $field_info = es_property_get_field_info( $field_key ) ) && ! es_is_entity_default_field_deactivated( $field_key, 'property' ) ) {
			$field_config          = es_parse_args( $field_config, $field_info );
			$field_config['type']  = ! empty( $field_config['admin_type'] ) ? $field_config['admin_type'] : $field_config['type'];
			$field_config['value'] = $property->{$field_key};
			$name                  = "es_property[{$field_key}]";

			if ( in_array( $field_config['type'], array( 'price', 'area', 'lot_size' ) ) ) {
				$field_config['type']               = 'number';
				$field_config['attributes']['step'] = ! empty( $field_config['attributes']['step'] ) ? $field_config['attributes']['step'] : 'any';
			}

			$max_upload_size = wp_max_upload_size();
			if ( ! $max_upload_size ) {
				$max_upload_size = 0;
			}

			if ( ! empty( $field_config['type'] ) && $field_config['type'] == 'media' ) {
				$field_config['media_args']['post'] = $property->get_id();
			}

			if ( 'video' == $field_key ) {
				unset( $field_config['value'] );

				$video_value = $property->{$field_key} ? $property->{$field_key} : array();
				$video_value = wp_parse_args( $video_value, array(
					'video_url'    => '',
					'video_iframe' => '',
					'video_file'   => '',
				) );

				echo "<h3>" . _x( 'Video', 'property edit', 'es' ) . "</h3>";

				es_framework_field_render( 'video-url', array_merge( $field_config, array(
					'label'      => __( 'Video URL', 'es' ),
					'type'       => 'text',
					'attributes' => array(
						'name'        => $name . '[video_url]',
						'placeholder' => __( 'Link to your video from YouTube or Vimeo', 'es' ),
					),
					'value'      => $video_value['video_url'],
				) ) );

				es_framework_field_render( 'video-iframe', array_merge( $field_config, array(
					'label'      => __( 'Embed the code', 'es' ),
					'type'       => 'text',
					'attributes' => array(
						'name' => $name . '[video_iframe]',
					),
					'value'      => $video_value['video_iframe'],
					/* translators: %s: video example. */
					'caption'    => esc_attr( sprintf( __( 'E.g. %s', 'es' ), '<iframe width="100%" height="400" src="https://www.youtube.com/embed/SPyHzY-KnA4" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>' ) )
				) ) );

				unset( $field_config['label'] );

				es_framework_field_render( 'video-file', array_merge( $field_config, array(
					'type'         => 'media',
					'button_label' => __( 'Upload video', 'es' ),
					'attributes'   => array(
						'name' => $name . '[video_file]',
					),
					'value'        => $video_value['video_file'],
					/* translators: %1$s: file size, %2$s: file extensions. */
					'description'  => sprintf( __( 'One file must be less than %1$s. Allowed Extensions: %2$s.', 'es' ), esc_html( size_format( $max_upload_size ) ), implode( ', ', wp_get_video_extensions() ) ),
				) ) );
			} else {
				if ( ! empty( $field_info['taxonomy'] ) ) {
					$field_config['options'] = es_get_terms_list( $field_key );
				}

				$field_config['disable_hidden_input'] = false;
				$field_config['attributes']['name']       = $name;
				$field_config['attributes']['data-value'] = $field_config['value'];

				es_framework_field_render( $field_key, $field_config );
			}
		}
	}
}

/**
 * Render settings field.
 *
 * @param $field_key
 * @param $field_config
 */
function es_settings_field_render( $field_key, $field_config ) {

	$field_config = es_parse_args( $field_config, array(
		'attributes' => array(
			'name' => "es_settings[{$field_key}]"
		),
		'value'      => wp_unslash( ests( $field_key ) ),
	) );

	if ( empty( $field_config['options'] ) && ( $values = ests_values( $field_key ) ) ) {
		$field_config['options'] = $values;
	}

	if ( ! empty( $field_config['pro'] ) ) {
		$field_config['attributes']['disabled'] = 'disabled';
		$field_config['wrapper_class']          = 'es-field--pro-version';
	}

	es_framework_field_render( $field_key, $field_config );
}

/**
 * Render field builder form field.
 *
 * @param $field_key string
 * @param $field_config array
 * @param $field array
 */
function es_field_builder_field_render( $field_key, $field_config, $field ) {

	$field_config = es_parse_args( $field_config, array(
		'attributes' => array(
			'name' => "es_fields_builder[{$field_key}]"
		),
		'value'      => ! empty( $field[ $field_key ] ) ? maybe_unserialize( $field[ $field_key ] ) : '',
	) );

	foreach ( array( 'pro', 'premium' ) as $ver ) {
		if ( ! empty( $field_config[ $ver ] ) ) {
			if ( is_array( $field_config[ $ver ] ) ) {
				if ( $field_config['type'] == 'checkboxes' ) {
					foreach ( $field_config[ $ver ] as $value ) {
						$field_config['checkboxes_config'][ $value ] = array(
							'attributes'    => array( 'disabled' => 'disabled' ),
							'wrapper_class' => 'es-field--pro-version',
						);
					}
				}
			} else {
				$field_config['attributes']['disabled'] = 'disabled';
				$field_config['wrapper_class']          = 'es-field--' . $ver . '-version';
			}
		}
	}

	es_framework_field_render( $field_key, $field_config );
}

/**
 * @param string $field_key
 * @param bool $is_attribute
 * @param $field_config
 * @param $field
 */
function es_field_builder_field_option_render( $field_key, $is_attribute = false, $field_config = array(), $field = array() ) {
	$attribute = $is_attribute ? '[attributes]' : '';

	if ( $is_attribute ) {
		$value = isset( $field['attributes'][ $field_key ] ) ? $field['attributes'][ $field_key ] : '';
	} else {
        $value = isset( $field[ $field_key ] ) ? $field[ $field_key ] : '';
        $value = ! $value && isset( $field['options'][ $field_key ] ) ? $field['options'][ $field_key ] : $value;
	}

	$field_config = es_parse_args( $field_config, array(
		'attributes' => array(
			'name' => "es_fields_builder[options]" . $attribute . "[$field_key]",
		),
		'value'      => maybe_unserialize( $value ),
	) );

	es_framework_field_render( $field_key, $field_config );
}

/**
 * Display recommended page creating settings block.
 *
 * @param $field_key
 * @param $field_config
 */
function es_settings_recommended_page_render( $field_key, $field_config ) {

	$field_config = wp_parse_args( $field_config, array(
		'page_name'         => '',
		'page_display_name' => '',
		'label'             => '',
		'page_content'      => '',
		'_wpnonce'          => wp_create_nonce( 'es_settings_create_page' ),
		'field'             => $field_key,
		'action'            => 'es_settings_create_page'
	) );

	if ( empty( $GLOBALS['es_pages_list'] ) ) {
		$GLOBALS['es_pages_list'] = es_get_pages();
	}

    $page_id = ests( $field_key );

	if ( $page_id && get_post_status( $page_id ) == 'publish' ) :
		es_settings_field_render( $field_key, array(
			'type'       => 'select',
			'attributes' => array( 'placeholder' => __( 'Select page', 'es' ) ),
			'label'      => $field_config['page_display_name'],
			'options'    => $GLOBALS['es_pages_list'],
		) );
	else : ?>
        <div class="es-recommended-page js-es-recommended-page">
            <div class="es-recommended-page__content">
                <img src="<?php echo plugin_dir_url( ES_FILE ) . '/admin/images/register-page.svg'; ?>"/>
                <div class="es-recommended-page__content-inner">
                    <b><?php _e( 'Add recommended page', 'es' ); ?></b>
                    <span><?php echo $field_config['page_display_name']; ?></span>
                </div>
            </div>
            <button class="es-btn es-btn--third es-btn--small js-es-create-page"
                    data-request='<?php echo es_esc_json_attr( $field_config ); ?>'>
				<?php _e( 'Create page', 'es' ); ?>
            </button>
        </div>
	<?php endif;
}

/**
 * Return terms list for dropdown as id=>name prepared.
 *
 * @param $taxonomy
 * @param bool $hide_empty
 *
 * @param array $meta
 *
 * @param string $fields
 *
 * @return WP_Term[]|WP_Error
 */
function es_get_terms_list( $taxonomy, $hide_empty = false, $meta = array(), $fields = 'id=>name' ) {

	$args = array(
		'taxonomy'   => $taxonomy,
		'hide_empty' => $hide_empty,
		'fields'     => $fields,
	);

	if ( ! empty( $meta ) ) {
		$args['meta_query'] = $meta;
	}

	return get_terms( $args );
}

/**
 * Return fields builder instance.
 *
 * @param bool $force_reload
 * @return Es_Fields_Builder
 */
function es_get_fields_builder_instance( $force_reload = false ) {
	return apply_filters( 'es_get_fields_builder_instance', new Es_Fields_Builder( $force_reload ) );
}

/**
 * Return fields builder instance.
 *
 * @param bool $force_reload
 * @return Es_Sections_Builder
 */
function es_get_sections_builder_instance( $force_reload = false ) {
	return apply_filters( 'es_get_sections_builder_instance', new Es_Sections_Builder( $force_reload ) );
}

/**
 * Return fields builder fields list field markup.
 *
 * @param $field stdClass
 *
 * @return string
 */
function es_field_builder_get_field_markup( $field ) {
	ob_start();

	$default_field = es_is_property_default_field( $field['machine_name'] );
	$deactivated   = es_is_property_default_field_deactivated( $field['machine_name'] );
	$editable      = empty( $field['fb_settings']['disable_edit'] ); ?>

    <li class="es-fields-list__item js-es-fields-list__item <?php echo ! $editable ? 'disable-edit' : ''; ?> <?php echo $deactivated ? 'disable-edit' : ''; ?>"
        data-section-machine-name="<?php echo $field['section_machine_name']; ?>"
        data-machine-name="<?php echo $field['machine_name']; ?>">
        <h3 class="es-fields-list__item-label"><?php echo $field['label']; ?></h3>
        <a href="#" class="es-fields-list__copy js-es-fields-list__copy"
           data-clipboard-text="<?php echo $field['machine_name']; ?>">
			<?php echo $field['machine_name']; ?><span class="es-icon es-icon_copy">
        </a>
        <div class="es-fields-list__item-manage">
			<?php if ( ! $deactivated ) :
                $id_attr = ! $default_field && ! empty( $field['id'] ) ? sprintf( 'data-id="%s"', $field['id'] ) : ''; ?>
                <a href="#" data-section-machine-name="<?php echo $field['section_machine_name']; ?>"
                   data-field-label="<?php echo $field['label']; ?>"
                   data-machine-name="<?php echo $field['machine_name']; ?>" <?php echo $id_attr; ?>
                   class="es-fields-list__item-delete js-es-fields-list__item-delete">
					<?php if ( $default_field ) : ?>
                    <span class="es-icon es-icon_trash">
                    <?php else : ?>
                        <span class="es-icon es-icon_close">
                    <?php endif; ?>
                </a>
			<?php else : ?>
                <a href="#" data-section-machine-name="<?php echo $field['section_machine_name']; ?>"
                   data-machine-name="<?php echo $field['machine_name']; ?>"
                   class="es-fields-list__item-add js-es-fields-list__item-add"><span class="es-icon es-icon_plus"></a>
			<?php endif; ?>
            <a href="#" class="js-es-fields-list__item-drag es-fields-list__item-drag"><span
                        class="es-icon es-icon_arrows-hv"></a>
        </div>
    </li>

	<?php return apply_filters( 'es_field_builder_get_field_markup', ob_get_clean(), $field );
}

/**
 * Return nav tab link markup.
 *
 * @param $section
 *
 * @return string
 */
function es_field_builder_get_tab_link_markup( $section ) {
	$deactivated = es_is_property_default_section_deactivated( $section['machine_name'] );

	if ( ! $deactivated ) {
		$links = "<div class='es-fields-builder__manage-links'>
                <a href='#' class='js-es-section-item' data-machine-name='{$section['machine_name']}'>
                    <span class='es-icon es-icon_settings'></span>
                </a>";
	} else {
		$links = "<div class='es-fields-builder__manage-links'>
                <a href='#' class='js-es-section-item-restore' data-machine-name='{$section['machine_name']}'>
                    <span class='es-icon es-icon_plus'></span>
                </a>";
	}

	if ( empty( $section['fb_settings']['disable_order'] ) ) {
		$links .= "<a href='#'><span class='es-icon es-icon_arrows-hv'></span></a>";
	}

	$links .= "</div>";

	$name = ! empty( $section['section_name'] ) ? $section['section_name'] : $section['label'];

	return "<a href='#{$section['machine_name']}' class='es-tabs__nav-link'>{$name}</a>" . $links;
}

/**
 * @param $string
 *
 * @return bool
 */
function es_is_html( $string ) {
	return $string != strip_tags( $string );
}

/**
 * Sanitize provided value.
 *
 * @param $var
 *
 * @return array|string
 */
function es_clean( $var ) {
	if ( is_array( $var ) ) {
		return array_map( 'es_clean', $var );
	} else {
		if ( is_scalar( $var ) ) {
			if ( es_is_html( $var ) ) {
				return wp_kses_post( $var );
			}
		} else {
			return sanitize_text_field( $var );
		}

		return is_scalar( $var ) ? sanitize_text_field( $var ) : $var;
	}
}

/**
 * Return translations placeeholders for js fies
 *
 * @return array
 */
function es_js_get_translations() {
	return apply_filters( 'es_js_translations', array(
		'close' => __( 'Close', 'es' ),
		'unknown_error' => __( 'Something wrong. Please contact the support.', 'es' ),
		'remove_saved_property' => __( 'Remove this home from saved?', 'es' ),
		'got_it'         => __( 'Got it', 'es' ),
		/* translators: %s: num of homes. */
		'delete_homes' => __( 'Delete %s homes from your listings?', 'es' ),
		'delete_homes_btn' => __( 'Delete homes', 'es' ),
		'cancel'         => __( 'Cancel', 'es' ),
		'remove'         => __( 'Remove', 'es' ),
		/* translators: %s: field name. */
		'delete_field'   => __( 'Are you sure you want to delete %s field?', 'es' ),
		/* translators: %s: section name. */
		'delete_section' => __( 'Are you sure you want to delete %s section?', 'es' ),
        'set_pin' => __( 'Edit pin location', 'es' ),
        'save_pin' => __( 'Save pin location', 'es' ),
	) );
}

/**
 * @param $entity_id .
 *
 * @param string $wp_type
 *
 * @return Es_Entity|Es_Post
 */
function es_get_entity_by_id( $entity_id, $wp_type = 'post' ) {
	$entity = null;

	if ( $wp_type == 'post' ) {
		$post = get_post( $entity_id );

        if ( $post instanceof WP_Post ) {
	        if ( 'properties' == $post->post_type ) {
		        $entity = es_get_property( $post->ID );
	        }
        }
	}

	return apply_filters( 'es_get_entity_by_id', $entity, $entity_id, $wp_type );
}

/**
 * @param $entity_type
 * @param int $entity_id
 *
 * @return Es_User|Es_Post|null
 */
function es_get_entity( $entity_type, $entity_id = -1 ) {
	$entity = null;

	if ( function_exists( 'es_get_' . $entity_type ) ) {
		$entity = call_user_func( 'es_get_' . $entity_type, $entity_id );
	}

	return apply_filters( 'es_get_entity', $entity, $entity_type, $entity_id );
}

/**
 * Return entity default fields.
 *
 * @param $entity
 *
 * @return array
 */
function es_get_entity_default_fields( $entity ) {
	$fields = array();

	if ( 'property' == $entity ) {
		$entity = es_get_property();
		$fields = $entity::get_default_fields();
	} else if ( 'user' == $entity ) {
	    $entity = es_get_user_entity( -1 );
	    $fields = $entity::get_default_fields();
    }

	return apply_filters( 'es_get_entity_default_fields', $fields, $entity );
}

/**
 * Return entity fields.
 *
 * @param $entity
 *
 * @return mixed|void
 */
function es_get_entity_fields( $entity ) {
	$fields = array();

	if ( 'property' == $entity ) {
		$fb_instance = es_get_fields_builder_instance();
		return $fb_instance::get_items( $entity );
	}

	return apply_filters( 'es_get_entity_fields', $fields, $entity );
}

/**
 * Check is property field is default.
 *
 * @param $machine_name string
 *
 * @return bool
 */
function es_is_property_default_field( $machine_name ) {
	$property = es_get_property();
	$fields   = $property::get_default_fields();

	return isset( $fields[ $machine_name ] );
}

/**
 * Check is property section default.
 *
 * @param $machine_name string
 *
 * @return bool
 */
function es_is_property_default_section( $machine_name ) {
	$sections = es_get_default_sections( 'property' );

	return isset( $sections[ $machine_name ] );
}

/**
 * Check demo content execution status.
 *
 * @return false|mixed|void
 */
function es_is_demo_executed() {
    return get_option( 'es_is_demo_executed' );
}

/**
 * Set demo content status as executed.
 *
 * @return void
 */
function es_set_demo_as_executed() {
    update_option( 'es_is_demo_executed', 1 );
}

/**
 * @param $machine_name
 *
 * @return bool
 */
function es_is_property_field_active( $machine_name ) {
	$fields   = ests( 'fb_property_deleted_fields' );

    if ( ! empty( $fields ) ) {
	    if ( array_search( $machine_name, $fields ) !== false ) {
            return false;
	    }
    }

    return true;
}

/**
 * Deactivate property default field.
 *
 * @param $machine_name
 */
function es_deactivate_default_property_field( $machine_name ) {
	$fields   = ests( 'fb_property_deleted_fields' );
	$fields[] = $machine_name;
	$fields   = array_unique( $fields );
	ests_save_option( 'fb_property_deleted_fields', $fields );
}

/**
 * Activate property default field.
 *
 * @param $machine_name
 */
function es_activate_default_property_field( $machine_name ) {
	$fields   = ests( 'fb_property_deleted_fields' );

	if ( ( $key = array_search( $machine_name, $fields ) ) !== false ) {
		unset( $fields[ $key ] );
		ests_save_option( 'fb_property_deleted_fields', $fields );
	}
}

/**
 * Activate property default field.
 *
 * @param $machine_name
 */
function es_deactivate_default_property_section( $machine_name ) {
	$fields   = ests( 'fb_property_deleted_sections' );
	$fields[] = $machine_name;
	$fields   = array_unique( $fields );
	ests_save_option( 'fb_property_deleted_sections', $fields );
}

/**
 * Activate default property section.
 *
 * @param $machine_name
 */
function es_activate_default_property_section( $machine_name ) {
	$sections = ests( 'fb_property_deleted_sections' );

	if ( ( $key = array_search( $machine_name, $sections ) ) !== false ) {
		unset( $sections[ $key ] );
		ests_save_option( 'fb_property_deleted_sections', $sections );
	}
}

/**
 * @param $machine_name
 *
 * @return bool
 */
function es_is_tab_active( $machine_name ) {
	$sections = ests( 'fb_property_deleted_sections' );
    $sections = is_array( $sections ) ? $sections : array();

    return ! in_array( $machine_name, $sections );
}

/**
 * Is property field deactivated.
 *
 * @param $machine_name string
 *
 * @param $entity_name
 *
 * @return bool
 */
function es_is_entity_default_field_deactivated( $machine_name, $entity_name ) {
	$fields = ests( 'fb_' . $entity_name . '_deleted_fields' );
	$fields = $fields ? $fields : array();

	return in_array( $machine_name, $fields );
}

/**
 * Is property field deactivated.
 *
 * @param $machine_name string
 *
 * @return bool
 */
function es_is_property_default_field_deactivated( $machine_name ) {
	return es_is_entity_default_field_deactivated( $machine_name, 'property' );
}

/**
 * @param $machine_name
 * @param $entity_name
 *
 * @return bool
 */
function es_is_entity_default_section_deactivated( $machine_name, $entity_name ) {
	$sections = ests( 'fb_' . $entity_name . '_deleted_sections' );
	$sections = $sections ? $sections : array();

	return in_array( $machine_name, $sections );
}

/**
 * Is property section deactivated.
 *
 * @param $machine_name string
 *
 * @return bool
 */
function es_is_property_default_section_deactivated( $machine_name ) {
	return es_is_entity_default_section_deactivated( $machine_name, 'property' );
}

/**
 * Generate simple ajax response array.
 *
 * @param $message
 * @param $type
 *
 * @return array
 */
function es_notification_ajax_response( $message, $type ) {
	return array(
		'status'  => $type,
		'message' => es_get_notification_markup( $message, $type ),
	);
}

/**
 * @param $message
 * @param $type
 *
 * @return array
 */
function es_simple_ajax_response( $message, $type ) {
	return array(
		'status'  => $type,
		'message' => $message
	);
}

/**
 * @param $message
 *
 * @return array
 */
function es_success_ajax_response( $message ) {
	return es_simple_ajax_response( $message, 'success' );
}

/**
 * @param $message
 *
 * @return array
 */
function es_error_ajax_response( $message ) {
	return es_simple_ajax_response( $message, 'error' );
}

/**
 * Return invalid nonce ajax response array.
 *
 * @return array
 */
function es_ajax_invalid_nonce_response() {
	$message = es_get_notification_markup(
		__( 'Invalid security nonce. Please, reload the page and try again.', 'es' ),
		'error'
	);

	return es_notification_ajax_response( $message, 'error' );
}

/**
 * Return terms creator instance.
 *
 * @param $taxonomy
 *
 * @param bool|string $type
 *
 * @return Es_Terms_Creator
 */
function es_get_terms_creator_factory( $taxonomy, $type = false ) {

	$instance = null;

	if ( 'es_location' == $taxonomy ) {
        $instance = new Es_Locations_Creator( $taxonomy, $type );
    } else if ( $type ) {
		if ( 'colored' === $type ) {
			$instance = new Es_Labels_Creator( $taxonomy );
		} else if ( 'icon' === $type ) {
			$instance = new Es_Features_Icons_Creator( $taxonomy );
		} else if ( 'check' === $type ) {
			$instance = new Es_Features_Creator( $taxonomy );
		} else if ( 'simple' === $type ) {
			$instance = new Es_Terms_Creator( $taxonomy );
		}
	} else {
		if ( 'es_label' == $taxonomy ) {
			$instance = new Es_Labels_Creator( $taxonomy );
		} else if ( in_array( $taxonomy, array( 'es_amenity', 'es_feature' ) ) ) {
			$type     = ests( 'is_terms_icons_enabled' ) ? ests( 'term_icon_type' ) : 'simple';
			$instance = es_get_terms_creator_factory( $taxonomy, $type );
		} else {
			$instance = new Es_Terms_Creator( $taxonomy );
		}
	}

	return apply_filters( 'es_get_terms_creator_factory', $instance, $taxonomy, $type );
}

/**
 * Check is term default.
 *
 * @param $term_id
 *
 * @return bool
 */
function es_is_default_term( $term_id ) {
	return (bool) get_term_meta( $term_id, 'es_default_term', true );
}

/**
 * Check is term deactivated.
 *
 * @param $term_id
 *
 * @return bool
 */
function es_is_term_deactivated( $term_id ) {
	return (bool) get_term_meta( $term_id, 'es_is_deactivated_term', true );
}

/**
 * Deactivate default term.
 *
 * @param $term_id
 */
function es_deactivate_term( $term_id ) {
	update_term_meta( $term_id, 'es_is_deactivated_term', 1 );
}

/**
 * Activate default term.
 *
 * @param $term_id
 */
function es_activate_term( $term_id ) {
	delete_term_meta( $term_id, 'es_is_deactivated_term' );
}

/**
 * Helper strtolower.
 *
 * @param $string
 *
 * @return mixed|null|string|string[]
 */
function es_strtolower( $string ) {
	return function_exists( 'mb_strtolower' ) ? mb_strtolower( $string ) : strtolower( $string );
}

/**
 * @param $column
 * @param $list
 * @param $index
 *
 * @return array
 */
function es_push_array_pos( $column, $list, $index ) {
	return array_merge( array_slice( $list, 0, $index ), $column, array_slice( $list, $index ) );
}

/**
 * Return times array.
 *
 * @return array
 */
function es_get_times_array() {

	$times = array();

	foreach ( range( 0, 23 ) as $time ) {
		$hours_formatted = strlen( $time ) == 1 ? 0 . $time : $time;
		$am_pm           = $time >= 12 ? 'pm' : 'am';
		$time            = $time > 12 ? $time - 12 : $time;

		$times[ $hours_formatted . ':00' ] = $time . ':00' . $am_pm . ( ! $time || $time == 12 ? '*' : '' );
		$times[ $hours_formatted . ':30' ] = $time . ':30' . $am_pm;
	}

	return apply_filters( 'es_get_times_array', $times );
}

/**
 * Method for getting locale with WPML|Polylang etc support.
 *
 * @return mixed
 */
function es_get_locale() {

	// Polylang integration.
	if ( ! empty( $_POST['post_lang_choice'] ) ) {
		return sanitize_text_field( $_POST['post_lang_choice'] );
	}

	if ( ! empty( $_REQUEST['icl_post_language'] ) ) {
		return sanitize_text_field( $_REQUEST['icl_post_language'] );
	}

	if ( defined( 'ICL_LANGUAGE_CODE' ) ) {
		return ICL_LANGUAGE_CODE;
	}

	$lang = ! empty( $_GET['lang'] ) ? sanitize_text_field( $_GET['lang'] ) : str_replace( '_', '-', get_locale() );

	return apply_filters( 'es_get_locale', $lang );
}

/**
 * @return mixed|void
 */
function es_get_gmap_locale() {
	$locale = es_get_locale();
	$locale = str_replace( '-', '_', $locale );
	return apply_filters( 'es_get_gmap_locale', $locale );
}

/**
 * @param $data
 *
 * @return string
 */
function es_esc_json_attr( $data ) {
	return esc_attr( htmlspecialchars( json_encode( $data ), ENT_QUOTES | JSON_HEX_QUOT | JSON_HEX_TAG, 'UTF-8' ) );
}

/**
 * Return address components container.
 *
 * @return Es_Address_Components
 */
function es_get_address_components_container() {
	return apply_filters( 'es_get_address_components_container', new Es_Address_Components() );
}

/**
 * Return property delete\copy\publish\draft action link.
 *
 * @param $post_id
 * @param $action
 *
 * @return string
 */
function es_get_action_post_link( $post_id, $action ) {
	$post_id = intval( $post_id );
	return add_query_arg( array(
		'post_ids' => array( $post_id ),
		'_nonce'   => wp_create_nonce( 'es_entities_actions' ),
		'action'   => $action,
	), admin_url( sprintf( 'edit.php?post_type=%s', esc_attr( get_post_type( $post_id ) ) ) ) );
}


if ( ! function_exists( 'es_is_single_property' ) ) {

	/**
	 * @return bool
	 */
	function es_is_single_property() {
		return is_singular( 'properties' );
	}
}

if ( ! function_exists( 'es_is_property_taxonomy' ) ) {

	/**
	 * @return bool
	 */
	function es_is_property_taxonomy() {
		return is_tax( get_object_taxonomies( 'properties' ) );
	}
}

/**
 * Create post duplicate by post ID.
 *
 * @param $post_id |WP_Post
 *
 * @return WP_Error|WP_Post
 */
function es_duplicate_post( $post_id ) {
	$post = get_post( $post_id );

	$current_user    = wp_get_current_user();
	$new_post_author = $current_user->ID;

	/*
	 * if post data exists, create the post duplicate
	 */
	if ( isset( $post ) && $post != null ) {
		$args = array(
			'comment_status' => $post->comment_status,
			'ping_status'    => $post->ping_status,
			'post_author'    => $new_post_author,
			'post_content'   => $post->post_content,
			'post_excerpt'   => $post->post_excerpt,
			'post_name'      => $post->post_name,
			'post_parent'    => $post->post_parent,
			'post_password'  => $post->post_password,
			'post_status'    => 'draft',
			'post_title'     => $post->post_title,
			'guid'           => $post->guid,
			'post_type'      => $post->post_type,
			'to_ping'        => $post->to_ping,
			'post_mime_type' => $post->post_mime_type,
			'menu_order'     => $post->menu_order
		);

		$new_post_id = wp_insert_post( $args, true );

		if ( ! is_wp_error( $new_post_id ) ) {
			$children_posts = get_children( array(
				'post_parent' => $post->ID,
                'fields' => 'ids'
			) );

			if ( ! empty( $children_posts ) ) {
				foreach ( $children_posts as $child_post ) {
					$cloned = es_duplicate_post( $child_post );

					if ( ! is_wp_error( $cloned ) ) {
						wp_update_post( array(
							'ID' => $cloned,
							'post_parent' => $new_post_id,
						) );
					}
				}
			}

			global $wpdb;
			$taxonomies = get_object_taxonomies( $post->post_type );

			foreach ( $taxonomies as $taxonomy ) {
				$post_terms = wp_get_object_terms( $post_id, $taxonomy, array( 'fields' => 'slugs' ) );
				wp_set_object_terms( $new_post_id, $post_terms, $taxonomy, false );
			}

			$meta = $wpdb->get_results( $wpdb->prepare( "SELECT meta_key, meta_value FROM {$wpdb->postmeta} WHERE post_id=%s", $post_id ) );
			if ( $meta ) {
				$sql_query = "INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value) ";
				foreach ( $meta as $meta_info ) {
					$meta_key = $meta_info->meta_key;
					if ( $meta_key == '_wp_old_slug' ) {
						continue;
					}
					$meta_value      = addslashes( $meta_info->meta_value );
					$sql_query_sel[] = "SELECT $new_post_id, '$meta_key', '$meta_value'";
				}
				$sql_query .= implode( " UNION ALL ", $sql_query_sel );
				$wpdb->query( $sql_query );
			}

            if ( es_is_property( $new_post_id ) ) {
                es_property_generate_keywords( array(), es_get_property( $new_post_id ) );
            }
		}

		return $new_post_id;
	}
}

/**
 * Filter search support fields
 *
 * @param $item array
 *
 * @return bool
 */
function es_filter_search_fields( $item ) {
	return ! empty( $item['search_support'] );
}

/**
 * Return list of google fonts.
 *
 * @param bool $update_list
 *
 * @return mixed|void
 */
function es_get_google_fonts( $update_list = false ) {
	$fonts = get_option( 'es_google_fonts' );

	if ( ! $fonts || $update_list ) {
		$api_key  = 'AIzaSyCpWX4rTrWjK32EsIGPT1Z0pPfR-niV4lw';
		$response = wp_safe_remote_get( sprintf( 'https://www.googleapis.com/webfonts/v1/webfonts?key=%s', $api_key ) );

		if ( ! is_wp_error( $response ) && ! empty( $response['body'] ) ) {
			$fonts = json_decode( $response['body'] );
			if ( ! empty( $fonts->items ) ) {
                foreach ( $fonts->items as $key => $item ) {
                    $item = (array) $item;
                    unset( $item['variants'], $item['subsets'], $item['version'], $item['lastModified'],
                        $item['category'], $item['kind'], $item['menu'] );

                    if ( ! empty( $item['files'] ) ) {
                        $item['files'] = (array) $item['files'];
                        $styles = array( 300, 400, 'regular', 700 );

                        foreach ( $item['files'] as $weight => $url ) {
                            if ( ! in_array( $weight, $styles, true ) ) {
                                unset( $item['files'][ $weight ] );
                            }
                        }
                    }

	                $fonts->items[ $key ] = $item;
                }
				update_option( 'es_google_fonts', $fonts->items );
			}
		}
	}

	return apply_filters( 'es_get_google_fonts', $fonts );
}

/**
 * Callback for array_walk function.
 *
 * @param $item
 *
 * @return string
 */
function es_arr_add_suffix_plus( &$item ) {
	$item .= '+';
}

/**
 * Return property field info
 *
 * @param $field
 *
 * @return array
 */
function es_property_get_field_info( $field ) {
	$property = es_get_property();

	return $property::get_field_info( $field );
}

/**
 * Verify recaptcha request.
 *
 * @return bool
 */
function es_verify_recaptcha() {

	if ( isset( $_POST['g-recaptcha-response'] ) ) {
		if ( ! empty( $_POST['g-recaptcha-response'] ) ) {
			$secret   = ests( 'recaptcha_secret_key' );

			$verify_response = wp_safe_remote_get( 'https://www.google.com/recaptcha/api/siteverify?secret=' . $secret . '&response=' . sanitize_text_field( $_POST['g-recaptcha-response'] ) );

			if ( ! empty( $verify_response['body'] ) ) {
				$response_data = json_decode( $verify_response['body'] );

				if ( ! empty( $response_data->success ) || ( ! empty( $response_data->score ) && $response_data->score >= 0.5 ) ) {
					return apply_filters( 'es_verify_recaptcha', true );
				}
			}
		}

		return apply_filters( 'es_verify_recaptcha', false );
	}

	return apply_filters( 'es_verify_recaptcha', true );
}

/**
 * @param $name
 * @param $value
 * @param int $expire
 * @param bool $secure
 * @param bool $httponly
 */
function es_setcookie( $name, $value, $expire = 0, $secure = false, $httponly = false ) {
	setcookie( $name, $value, $expire, COOKIEPATH ? COOKIEPATH : '/', COOKIE_DOMAIN,
		apply_filters( 'es_cookie_secure', $secure, $name, $value, $expire, $httponly ),
		apply_filters( 'es_cookie_httponly', $httponly, $name, $value, $expire, $secure ) );
}

/**
 * Display or return active class via compare $a === $b
 *
 * @param $a
 * @param $b
 * @param string $active_class
 * @param bool $_echo
 *
 * @return string|void
 */
function es_active_class( $a, $b, $active_class = 'es-btn--active', $_echo = true ) {
	$result = $a === $b ? ' ' . $active_class : '';

	if ( $_echo ) {
		echo $result;
	} else {
		return $result;
	}
}

/**
 * @param $data
 *
 * @return string
 */
function es_encode( $data ) {
	return base64_encode( build_query( $data ) );
}

/**
 * @param $data
 *
 * @return array|bool
 */
function es_decode( $data ) {
    $decoded_base64 = base64_decode( $data );
	$decoded_json = wp_parse_args( $decoded_base64, true );

	return $decoded_json;
}

/**
 * Return properties coordinates array.
 *
 * @param $query_args
 *
 * @return array
 */
function es_properties_get_markers( $query_args ) {
	$query        = new WP_Query( $query_args );
	$markers      = array();
	$markers_list = ! ests( 'is_single_map_marker_enabled' ) ? ests( 'map_markers_list' ) : array();

	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();
			$latitude  = es_get_the_field( 'latitude' );
			$longitude = es_get_the_field( 'longitude' );

			// Prevent 0.000 value as correct.
			if ( $latitude && $longitude ) {
				$markers[ get_the_ID() ] = array(
					'lat'     => $latitude,
					'lng'     => $longitude,
					'post_id' => get_the_ID(),
				);

				if ( ! empty( $markers_list ) ) {
					foreach ( $markers_list as $list ) {
						$correct_es_category = false;
						$correct_es_status   = false;
						$correct_es_type     = false;
						$correct_es_label    = false;

						foreach ( array( 'es_category', 'es_status', 'es_type', 'es_label' ) as $tax ) {
							if ( ! empty( $list[ $tax ] ) ) {
								if ( has_term( intval( $list[ $tax ] ), $tax, get_the_ID() ) ) {
									${'correct_' . $tax} = true;
								}
							} else {
								${'correct_' . $tax} = true;
							}
						}

						if ( $correct_es_category && $correct_es_status && $correct_es_type && $correct_es_label ) {
							$markers[ get_the_ID() ]['marker']       = $list['map_marker_icon'];
							$markers[ get_the_ID() ]['marker_color'] = $list['map_marker_color'];
							break;
						}
					}
				}
			}
		}
	}

	return apply_filters( 'es_properties_get_markers', $markers, $query_args );
}

/**
 * @param $context
 * @param null $default
 *
 * @param bool $is_popup
 *
 * @return string
 */
function es_get_auth_page_uri( $context, $default = null, $is_popup = false ) {
	$url      = null;
	$default  = $default ? $default : home_url();
    $login_page_id = ests( 'login_page_id' );

	if ( in_array( $context, array( 'login-buttons', 'login-form', 'reset-form', 'buyer-register-buttons', 'agent-register-buttons' ) ) ) {
		if ( $login_page_id && get_post_status( $login_page_id ) == 'publish' ) {
			$url = get_permalink( $login_page_id );
		}
	}

	$is_popup = $is_popup ? '#es-authentication-popup' : '';

	$url = $url ? $url : $default . $is_popup;

	return apply_filters( 'es_get_auth_page_uri', add_query_arg( array( 'auth_item' => $context ), $url ) );
}

/**
 * @param $array
 * @param null $callback
 * @param bool $remove_empty_arrays
 *
 * @return mixed
 */
function es_array_filter_recursive( $array, $callback = null, $remove_empty_arrays = false ) {
	foreach ( $array as $key => & $value ) {
		if ( is_array( $value ) ) {
			$value = call_user_func_array( __FUNCTION__, array( $value, $callback, $remove_empty_arrays ) );
			if ( $remove_empty_arrays && ! (bool) $value ) {
				unset( $array[ $key ] );
			}
		} else {
			if ( ! is_null( $callback ) && is_callable( $callback ) && ! $callback( $value ) ) {
				unset( $array[ $key ] );
			} elseif ( ! (bool) $value ) {
				unset( $array[ $key ] );
			}
		}
	}
	unset( $value );

	return $array;
}

/**
 * @param $term_id
 * @param $taxonomy
 * @return int|WP_Error|WP_Term[]
 */
function es_get_children_locations( $term_id, $taxonomy ) {
    return get_terms( array(
        'taxonomy' => $taxonomy,
        'hide_empty' => false,
        'meta_query' => array(
            array(
                'key' => 'parent_component',
                'value' => $term_id
            )
        ),
        'fields' => 'ids'
    ) );
}

/**
 * @param $term_id
 * @param array $parent_id
 * @return array
 */
function es_get_recursive_parent_locations( $term_id, $parent_id = array() ) {
	if ( $term_id ) {
		$parent_id[] = $term_id;
        global $wpdb;
		$parent = $wpdb->get_col( $wpdb->prepare( "SELECT * FROM {$wpdb->termmeta} a INNER JOIN {$wpdb->termmeta} b ON a.meta_value=b.term_id 
            WHERE a.term_id='%s' AND a.meta_key='parent_component' 
            AND b.meta_key='type' AND b.meta_value NOT IN ('subpremise', 'postal_code', 'postal_code_suffix', 'street_number') 
            GROUP BY a.meta_value 
            ", $term_id ) );

		if ( $parent ) {
			foreach ( $parent as $parent_recursive ) {
				if ( in_array( $parent_recursive, $parent_id ) ) continue;
				$parent_id[] = $parent_recursive;
				$parent_id = array_merge( $parent_id, es_get_recursive_parent_locations( $parent_recursive, $parent_id ) );
			}
		}
	}

	return array_unique( $parent_id );
}

/**
 * @return mixed|void
 */
function es_is_request_form_active() {
    $section_builder = es_get_sections_builder_instance();

    return apply_filters( 'es_is_request_form_active',
        es_is_visible( $section_builder::get_item_by_machine_name( 'request_form' ), 'property', 'section' ) );
}

/**
 * Upload file by url.
 *
 * @param $url
 * @param int $post_parent
 * @return WP_Error|int
 */
function es_upload_file_by_url( $url, $post_parent = 0 ) {
    if ( ! function_exists( 'wp_handle_upload' ) ) {
        require_once( ABSPATH . 'wp-admin/includes/file.php' );
    }

    if ( ! function_exists( 'wp_generate_attachment_metadata' ) ) {
        require_once( ABSPATH . 'wp-admin/includes/image.php' );
    }

    if ( ! function_exists( 'media_handle_upload' ) ) {
        require_once( ABSPATH . 'wp-admin/includes/media.php' );
    }

    $file = apply_filters( 'es_file_array', array(
        'name' => basename( $url ),
        'tmp_name' => download_url( $url ),
    ), $url );

    if ( is_wp_error( $file['tmp_name'] ) ) {
        return $file['tmp_name'];
    }

    $file = wp_handle_sideload( $file, array( 'test_form' => false ) );
    $upload_dir = wp_upload_dir();

    if ( empty( $file['error'] ) ) {
        $file_type = wp_check_filetype( basename( $file['file'] ), null );

        $attachment = array(
            'guid' => $upload_dir['baseurl'] . DS . _wp_relative_upload_path( $file['file'] ),
            'post_mime_type' => $file_type['type'],
            'post_title' => preg_replace('/\.[^.]+$/', '', basename( $file['file'] ) ),
            'post_content' => '',
            'post_status' => 'inherit'
        );

        $attachment_id = wp_insert_attachment( $attachment, $file['file'], $post_parent );

        $attach_data = wp_generate_attachment_metadata( $attachment_id,  get_attached_file( $attachment_id ) );
        wp_update_attachment_metadata( $attachment_id,  $attach_data );

        return $attachment_id;
    } else {
        return new WP_Error( '', $file['error'] );
    }
}

/**
 * @param $exist_term
 * @param $term_name
 * @param null $type
 * @return string
 */
function es_unique_term_slug( $exist_term, $term_name, $type = null ) {
    $slug = sanitize_title( $term_name );

    if ( $exist_term instanceof WP_Term && $exist_term->taxonomy == 'es_location' ) {
        $type = ! $type ? filter_input( INPUT_POST, 'type' ) : '';
        $exist_type = get_term_meta( $exist_term->term_id, 'type', true );

        if ( $exist_type != $type ) {
            $slug = sprintf( sanitize_title( $term_name ) . '-%s', uniqid() );
        }
    }

    return $slug;
}

/**
 * @param $layout_name
 *
 * @return bool
 */
function es_is_grid_layout( $layout_name ) {
	for ( $i = 1; $i <= 6; $i++ ) {
		if ( in_array( $layout_name, array( "{$i}_col", "{$i}col", "grid-{$i}", "{$i}_cols", "{$i}cols", 'grid' ) ) ) {
			return true;
		}
	}

	return false;
}

/**
 * Prepare grid name layout for listings shortcode.
 *
 * @param $layout
 *
 * @return string
 */
function es_prepare_grid_layout( $layout ) {
	for ( $i = 1; $i <= 6; $i++ ) {
		if ( in_array( $layout, array( "{$i}_col", "{$i}col", "grid-{$i}", "{$i}_cols", "{$i}cols", 'grid' ) ) ) {
			return 'grid-' . $i;
		}
	}

	return $layout;
}

/**
 * @param $current_layout
 *
 * @return mixed
 */
function es_get_active_grid_layout( $current_layout = '' ) {
    $layout = ests( 'listings_layout' );

    $is_current_grid = es_is_grid_layout( $current_layout );
    $temp = $layout != 'list' && $layout != 'half_map' ? $layout : 'grid-3';

    return $is_current_grid ? es_prepare_grid_layout( $current_layout ) : $temp;
}

/**
 * @return mixed|void
 */
function es_get_email_types_list() {
	return apply_filters( 'es_get_emails_list', array(
		'request_property_info' => 'Es_Request_Property_Info_Email',
		'new_user_info' => 'Es_New_User_Info_Email',
		'new_user_registered_admin' => 'Es_New_User_Registered_Admin_Email',
		'reset_password' => 'Es_Reset_Password_Email',
	) );
}

/**
 * @param $email_type
 *
 * @param array $data
 *
 * @return Es_Email|null
 */
function es_get_email_instance( $email_type, $data = array() ) {
	$emails_list = es_get_email_types_list();

    $class_name = ! empty( $emails_list[ $email_type ] ) ? $emails_list[ $email_type ] : false;

    return apply_filters( 'es_get_email_instance', $class_name ? new $class_name( $data ) : null, $email_type, $data );
}

/**
 * Send plugin email.
 *
 * @param $email_type
 * @param $user_email
 * @param array $data
 *
 * @return bool
 */
function es_send_email( $email_type, $user_email, $data = array() ) {
    $email = es_get_email_instance( $email_type, $data );

    return $email instanceof Es_Email && $email::is_active() && $email->send( $user_email );
}

/**
 * @param $post
 *
 * @return bool
 */
function es_is_property( $post = 0 ) {
	$post = get_post( $post );

	return $post instanceof WP_Post && $post->post_type == Es_Property::get_post_type_name();
}

/**
 * @param $tel_config
 *
 * @return string
 */
function es_get_formatted_tel( $tel_config ) {
	$formatted = null;

    if ( is_string( $tel_config ) && ! empty( $tel_config ) ) {
        $formatted = $tel_config;
    } else if ( ! empty( $tel_config['tel'] ) ) {
		$formatted = strlen( $tel_config['tel'] ) > 4 ? $tel_config['tel'] : $formatted;
	}

	return apply_filters( 'es_get_formatted_tel', $formatted, $tel_config );
}

/**
 * Check if plugin migration needed.
 *
 * @return bool
 */
function es_need_migration() {
    return ! get_option( 'es_migration_executed' ) && get_option( 'es_migration_0' );
}

/**
 * @return void
 */
function es_set_migration_as_executed() {
    update_option( 'es_migration_executed', 1 );
}

/**
 * @param int $post_id
 * @return bool
 */
function es_is_elementor_builder_enabled( $post_id = 0 ) {
	$post_id = $post_id ? $post_id : get_the_ID();

	return get_post_meta( $post_id, '_elementor_edit_mode', true ) == 'builder'
	       && defined( 'ELEMENTOR_VERSION' );
}

/**
 * @return array
 */
function es_builders_supported_post_types() {
    return apply_filters( 'es_builders_supported_post_types', array( 'properties' ) );
}

/**
 * Return properties taxonomies list.
 *
 * @return string[]
 */
function es_get_taxonomies_list() {
    return apply_filters( 'es_get_taxonomies_list', array_keys( get_taxonomies( ['object_type' => ['properties'] ] ) ) );
}

/**
 * @return mixed|void
 */
function es_get_admin_emails() {
	$emails = get_users( array(
		'role' => 'administrator',
		'fields' => array( 'user_email' )
	) );

	$emails = $emails ? wp_list_pluck( $emails, 'user_email' ) : array();
	$emails[] = get_option( 'admin_email' );

    return apply_filters( 'es_get_admin_emails', $emails );
}

/**
 * Return list of image sizes.
 *
 * @return array
 */
function es_get_image_sizes() {
    $result = array();
    $sizes = wp_get_registered_image_subsizes();

    if ( ! empty( $sizes ) ) {
        foreach ( $sizes as $id => $size_data ) {
            $w = ! empty( $size_data['width'] ) ? $size_data['width'] : 'auto';
            $h = ! empty( $size_data['height'] ) ? $size_data['height'] : 'auto';
            $crop = ! empty( $size_data['crop'] ) ? _x( 'Cropped', 'image size crop', 'es' ) : _x( 'Without crop', 'image size crop', 'es' );
            $result[ $id ] = sprintf( '%sx%s - %s (%s)', $w, $h, $crop, $id );
        }
    }

    return apply_filters( 'es_get_image_sizes', $result );
}

/**
 * @param $field_key
 * @param $field_config
 *
 * @return mixed|void
 */
function es_get_search_placeholder( $field_key, $field_config ) {
    $placeholder = $field_config['label'];

    if ( ! empty( $field_config['search_settings']['attributes']['search-placeholder'] ) ) {
        $placeholder = $field_config['search_settings']['attributes']['search-placeholder'];
    } else if ( ! empty( $field_config['search_settings']['attributes']['data-placeholder'] ) ) {
	    $placeholder = $field_config['search_settings']['attributes']['data-placeholder'];
    }

    return apply_filters( 'es_get_search_placeholder', $placeholder, $field_key, $field_config );
}

/**
 * @param $user_id
 *
 * @return bool
 */
function es_is_user_registered_via_social_network( $user_id ) {
    return get_user_meta( $user_id, 'auth_google', true ) || get_user_meta( $user_id, 'auth_facebook', true );
}

/**
 * @param array $array1
 * @param array $array2
 * @param mixed ...$arrays
 *
 * @return array|void
 */
function es_array_merge_recursive( array $array1, array $array2, array ...$arrays ) {
	// where is the array spread operator when you need it?
	array_unshift( $arrays, $array2 );
	array_unshift( $arrays, $array1 );

	$merged = [];
	while ( $arrays ) {
		$array = array_shift( $arrays );
		assert( is_array( $array ) );
		if ( ! $array ) {
			continue;
		}

		foreach ($array as $key => $value) {
			if (is_string($key)) {
				if (is_array($value) && array_key_exists($key, $merged) && is_array($merged[$key])) {
					$merged[$key] = array_merge_recursive($merged[$key], $value);
				} else {
					$merged[$key] = $value;
				}
			} else {
				$merged[] = $value;
			}
		}
	}

	return $merged;
}

/**
 * @param $val
 *
 * @return bool
 */
function es_is_decimal( $val ) {
	return is_numeric( $val ) && floor( $val ) != $val;
}

/**
 * @param $string
 *
 */
function es_mulultilingual_translate_string($string) {
    // Checking the presence and activation of Polylang
    if (function_exists('pll__')) {
        $string = pll__($string);
	// Checking the presence and activation of WPML
    } elseif (function_exists('icl_t')) {
		
        $string = icl_t('Estatik', $string, $string);
    } else {
		$string = __($string, 'es');
	}
    return $string;
}

function es_notification_admin() {
	$install_timestamp = get_option('es_plugin_install_date');
	if ( ! empty( $install_timestamp ) ) {
		$current_timestamp = time();
		$end_timestamp = strtotime('+5 days', $install_timestamp);
		$end_date_iso = date('c', $end_timestamp);
		
		if ($current_timestamp < $end_timestamp) : ?>
			<div class="es-banner--easter" style="display: flex; margin: 20px 0; width: 100%; background: #dcedc8; padding: 20px 30px; position: relative;">
				<div class="es-banner-easter--content" style="padding-left: 20px; font-size: 14px; font-weight: 500;"> <span style="color: red; font-size:16px;">&#8252;</span> <?php _e( 'Limited Time Offer: Become PRO now!', 'es' ); ?>  <?php _e( 'Here is your coupon code with 15% off discount - ', 'es' ); ?>
					<span style="font-weight: 700;">"summeralert"</span>
					<span style="margin-left: 10px" id="countdown"></span>
				</div>
			</div>
			<div id="es-banner-easter--close" style="position: absolute; right: 15px; top: 15px; cursor: pointer;">
				<svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M7.54994 6.99984L10.9046 10.3545C11.0565 10.5064 11.0565 10.7526 10.9046 10.9045C10.7527 11.0563 10.5065 11.0563 10.3546 10.9045L6.99996 7.54981L3.64532 10.9045C3.49345 11.0563 3.24722 11.0563 3.09535 10.9045C2.94348 10.7526 2.94348 10.5064 3.09535 10.3545L6.44999 6.99984L3.09535 3.6452C2.94348 3.49333 2.94348 3.2471 3.09535 3.09523C3.24722 2.94336 3.49345 2.94336 3.64532 3.09523L6.99996 6.44987L10.3546 3.09523C10.5065 2.94336 10.7527 2.94336 10.9046 3.09523C11.0565 3.2471 11.0565 3.49333 10.9046 3.6452L7.54994 6.99984Z" fill="#4F4F4F"/>
				</svg>
			</div>     

			<script>
				const endDate = new Date('<?php echo $end_date_iso; ?>');
				
				function updateCountdown() {
                const now = new Date();
                const timeRemaining = endDate - now;

                if (timeRemaining <= 0) {
                    document.getElementById('countdown').innerText = 'Expired';
                    clearInterval(countdownInterval);
                    return;
                }

                const days = Math.floor(timeRemaining / (1000 * 60 * 60 * 24));
                const hours = Math.floor((timeRemaining % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((timeRemaining % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((timeRemaining % (1000 * 60)) / 1000);

                document.getElementById('countdown').innerText = `( ${days} d ${hours} h ${minutes} m ${seconds} s )`;
            }

            const countdownInterval = setInterval(updateCountdown, 1000);

            document.addEventListener('DOMContentLoaded', function() {
                var closeButton = document.querySelector('#es-banner-easter--close');
                var bannerElement = document.querySelector('.es-banner--easter');

                if (closeButton && bannerElement) {
                    closeButton.addEventListener('click', function() {
                        bannerElement.style.display = 'none';
                    });
                }
            });
        </script>
		<?php endif;
	} 
}