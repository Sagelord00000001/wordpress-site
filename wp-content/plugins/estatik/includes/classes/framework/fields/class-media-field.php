<?php

/**
 * Class Es_Framework_Media_Field.
 */
class Es_Framework_Media_Field extends Es_Framework_Base_Field {

	/**
	 * @return string
	 */
	function get_input_markup() {
		$config = $this->get_field_config();
		wp_enqueue_media( $config['media_args'] );
		$classes = $config['attributes']['class'];

		$this->_field_config['attributes']['type'] = 'hidden';
		$this->_field_config['attributes']['class'] = $classes . ' js-es-media-field';

		if ( ! empty( $config['value'] ) ) {
			$this->_field_config['attributes']['value'] = $config['value'];
		}

		$items = '';

        if ( ! empty( $config['value'] ) && is_array( $config['value'] ) ) {
            $config['value'] = array_filter( $config['value'] );
        }

		if ( ! empty( $config['value'] ) ) {
			$value = is_scalar( $config['value'] ) ? explode( ',', $config['value'] ) : $config['value'];
			$value = is_scalar( $value ) ? array( $value ) : $value;

			foreach ( $value as $attachment_id ) {
			    $this->_field_config['attributes']['value'] = $attachment_id;

				$items .= $this->get_item_config( $attachment_id );
			}
		}

		$files_wrapper = strtr( $config['files_wrapper'], array(
			'{items}' => $items,
		) );

		$this->_field_config['attributes']['value'] = '{attachment_id}';
		$file_input = sprintf( "<input %s/>", $this->build_attributes_string() );

        $config['media_item'] = strtr( $config['media_item'], array(
            '{delete}' => $config['delete_link'],
            '{file_input}' => $file_input,
        ) );

		$input = strtr( $config['upload_button'], array(
            '{button_label}' => $config['button_label'],
            '{multiple}' => ! empty( $config['attributes']['multiple'] ) ? $config['attributes']['multiple'] : '',
            '{media_item}' => esc_attr( $config['media_item'] ),
        ) );

		$input .= $files_wrapper;

		return $input;
	}

	public function get_item_config( $attachment_id ) {
		$config = $this->get_field_config();

		$file_input = sprintf( "<input %s/>", $this->build_attributes_string() );
		$caption = wp_get_attachment_caption( $attachment_id );

		return strtr( $config['media_item'], array(
			'{attachment_id}' => $attachment_id,
			'{url}' => wp_get_attachment_url( $attachment_id ),
			'{filename}' => basename ( get_attached_file( $attachment_id ) ),
			'{delete}' => strtr( $config['delete_link'], array(
				'{attachment_id}' => $attachment_id,
			) ),
			'{file_input}' => $file_input,
			'{caption}' => ! $caption ? __( 'Add caption', 'es' ) : $caption,
			'{input_caption}' => $caption,
			'{no_caption}' => empty( $caption ) ? 'no-caption' : '',
			'{filesize}' => size_format( filesize( get_attached_file( $attachment_id ) ), 2 )
		) );
	}

	/**
	 * Default media field args.
	 *
	 * @return array
	 */
	public function get_default_config() {

		$default = array(
			'media_item' => "<li class='js-es-media-item es-file' data-attachment-id='{attachment_id}'>
							 	<span class='es-icon es-icon_paperclip'></span>
							 	<div class='es-file__info'>
							 		<div class='es-file__caption-container'>
							 			<a href='#' class='es-file__caption {no_caption} js-es-caption'>{caption} <span class='es-icon es-icon_pencil'></span></a>
							 			<input type='text' data-attachment-id='{attachment_id}' value='{input_caption}' class='es-file__caption-field js-es-file__caption-field'/>
									</div>
							 		<a href='{url}' target='_blank' class='es-file__name'>{filename}</a>
								</div>
							 	<div class='es-file__control'></div>
							 	<span class='es-file-size'>{filesize}</span>{delete}
							 	{file_input}
						     </li>",
			'delete_link' => '<a href="#" class="js-es-media-delete-attachment es-delete-file" data-attachment-id="{attachment_id}"><span class="es-icon es-icon_trash"></span></a>',
			'files_wrapper' => '<ul class="js-es-media-files es-files-list">{items}</ul>',
			'media_args' => array(),
			'upload_button' => "<button data-caption='" .  __( 'Add caption', 'es' ) . "' data-item-markup='{media_item}' class='js-es-media-button es-btn es-btn--secondary es-btn--third es-btn--small' data-multiple='{multiple}'><span class='es-icon es-icon_upload'></span>{button_label}</button>",
			'button_label' => __( 'Upload' ),
			'skeleton' => "{before}<div class='es-field es-field__{field_key} es-field--files-input es-field--{type} {wrapper_class}'>{hidden_input}{label}{input}{description}</div>{after}",
            'enable_hidden_input' => true,
		);

		return es_parse_args( $default, parent::get_default_config() );
	}
}
