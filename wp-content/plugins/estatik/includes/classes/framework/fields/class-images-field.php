<?php

/**
 * Class Es_Framework_Images_Field
 */
class Es_Framework_Images_Field extends Es_Framework_Media_Field {

	/**
	 * @return array
	 */
	public function get_default_config() {

		$args = array(
			'image_size' => 'thumbnail',
			'files_wrapper' => '<ul class="js-es-media-files es-images-list">{items}</ul>',
			'delete_link' => '<a href="#" class="js-es-media-delete-attachment es-image-delete" data-attachment-id="{attachment_id}"><span class="es-icon es-icon_close"></a>',
			'media_item' => "<li class='js-es-media-item es-image-item' data-attachment-id='{attachment_id}'>
								<div class='es-image-item__background' style='background-image: url(\"{url}\"); background-size: cover;'>
									<div class='es-overlay es-overlay--white'></div>
									{delete}
									<span class='es-icon es-icon_arrows-hv'></span>
								</div>
								<div class='es-file__caption-container'>
						            <a href='#' class='es-file__caption {no_caption} js-es-caption'>{caption}</a>
						            <input type='text' data-attachment-id='{attachment_id}' value='{input_caption}' class='es-file__caption-field js-es-file__caption-field'/>
								</div>{file_input}
							</li>",
		);

		return es_parse_args( $args, parent::get_default_config() );
	}

	public function get_item_config( $attachment_id ) {
		$config = $this->get_field_config();

		$file_input = sprintf( "<input %s/>", $this->build_attributes_string() );
		$caption = wp_get_attachment_caption( $attachment_id );

		return strtr( $config['media_item'], array(
			'{attachment_id}' => $attachment_id,
			'{url}' => wp_get_attachment_image_url( $attachment_id, $config['image_size'] ),
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
}
