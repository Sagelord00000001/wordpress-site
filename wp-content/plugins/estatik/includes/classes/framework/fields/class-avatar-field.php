<?php

/**
 * Class Es_Framework_Checkbox_Field.
 */
class Es_Framework_Avatar_Field extends Es_Framework_Base_Field {

	function get_input_markup() {
		$config = $this->get_field_config();
		ob_start(); ?>

		<div class="es-photo js-es-photo">
			<div class="es-photo__image js-es-photo__image" data-photo="<?php echo esc_attr( $config['default_image'] ); ?>">
				<?php if ( ! empty( $config['image_callback'] ) ) : ?>
					<?php echo is_string( $config['image_callback'] ) ? call_user_func( $config['image_callback'] ) : ''; ?>
				<?php elseif ( ! empty( $config['image'] ) )  :
					echo $config['image'];
				elseif ( ! empty( $config['value'] ) ) :
					echo wp_get_attachment_image( $config['value'], $config['image_size'] );
				elseif ( ! empty( $config['default_image'] ) ) :
                    echo $config['default_image'];
                endif; ?>
			</div>
			<a href="#" data-trigger-click="#<?php echo $config['attributes']['id']; ?>" class="<?php echo $config['upload_button_classes']; ?>">
				<?php if ( ! empty( $config['value'] ) ) : ?>
					<?php echo $config['exists_upload_button_label']; ?>
				<?php else : ?>
					<?php echo $config['upload_button_label']; ?>
				<?php endif; ?>
			</a>
			<a href="#" class="<?php echo empty( $config['value'] ) ? 'es-hidden' : '' ; ?> es-delete-photo es-secondary-color js-es-delete-photo"><?php echo $config['delete_button_label']; ?></a>
			<input style="display: none;" accept="image/*" data-img=".es-photo__image .avatar, img" class="js-es-image-field" name="<?php echo $config['file_name']; ?>" type="file" id="<?php echo $config['attributes']['id']; ?>"/>
			<input type="hidden" name="<?php echo $config['attributes']['name']; ?>" class="js-es-avatar-field" value="<?php echo $config['value']; ?>"/>
		</div>

		<?php return ob_get_clean();
	}

	/**
	 * Return field default config.
	 *
	 * @return array
	 */
	public function get_default_config() {
		$parent = parent::get_default_config();
		$def = array(
			'upload_button_label' => "<span class='es-icon es-icon_upload'></span>" . __( 'Upload profile photo', 'es' ),
			'exists_upload_button_label' => "<span class='es-icon es-icon_upload'></span>" . __( 'Upload new photo', 'es' ),
			'upload_button_classes' => 'es-btn es-btn--third es-btn--small es-btn--upload-photo',
			'delete_button_label' => __( 'Delete', 'es' ),
			'file_name' => 'avatar',
			'default_image' => get_avatar( -1 ),
			'image' => '',
			'image_size' => 'thumbnail',
		);

		return es_parse_args( $def, $parent );
	}
}
