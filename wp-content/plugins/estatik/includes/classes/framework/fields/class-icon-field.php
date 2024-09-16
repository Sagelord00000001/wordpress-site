<?php

/**
 * Class Es_Framework_Icon_Field.
 */
class Es_Framework_Icon_Field extends Es_Framework_Field {

	/**
	 * Render icon by icon config.
	 *
	 * @param $icon_config
	 *
	 * @return string
	 */
	public static function get_icon_markup( $icon_config ) {

		$result = null;

		if ( ! empty( $icon_config['type'] ) ) {
			switch ( $icon_config['type'] ) {
				case 'es-icon':
				case 'es-custom-icon':
					$result = $icon_config['icon'];
			}
		}

		return apply_filters( 'es_framework_icon_field_icon_markup', $result, $icon_config );
	}

	/**
	 * Return hmtl field markup.
	 *
	 * @return string
	 */
	function get_input_markup() {
		$config = $this->get_field_config();
		$this->set_type( 'hidden' );
		$value = $config['value'] ?(array) $config['value'] : array( 'icon' => '', 'type' => '' );
		ob_start();
		$id = $config['attributes']['id']; ?>

        <div class='es-icon-field-wrap'>
        <div class='es-icon-field js-es-icon-field'>
            <div class="es-icon-html js-es-icon-html">
				<?php echo static::get_icon_markup( $value ); ?>
            </div>
            <span class="es-icon es-icon_chevron-bottom es-icon-close"></span>
			<?php echo parent::get_input_markup(); ?>
        </div>
        <div class='es-icons-overlay'>
            <ul class='es-icons-list'>
				<?php foreach ( $config['icons'] as $icon ) :
					$active = $value['icon'] == $icon['icon'] ? 'es-icon-item--active' : ''; ?>
                    <li class='es-icon-item js-es-icon-item <?php echo $active; ?>' data-icon='<?php echo es_esc_json_attr( $icon ); ?>'>
						<?php echo static::get_icon_markup( $icon ); ?>
                    </li>
				<?php endforeach; ?>
            </ul>

            <b class='es-overlay__title'><?php echo $config['upload_title']; ?></b>
            <p><?php echo $config['upload_text']; ?></p>
            <button class='es-btn es-btn--third es-btn--small' disabled><span class='es-icon es-icon_upload'></span><?php echo $config['upload_button_label']; ?></button>
        </div>
        </div><?php

		return ob_get_clean();
	}

	/**
	 * @return array
	 */
	public function get_default_config() {
		$args = array(
			'upload_button_label'=> __( 'Upload icon', 'es' ),
			'upload_title' => __( 'Upload your custom icon', 'es' ),
			'upload_text' => __( 'You can choose to upload more than one file at a time. Make sure each file size doesn’t exceed 100 Kb. PNG, SVG are supported.', 'es' ),
			'icons' => esf_get_icons_list()
		);

		return es_parse_args( $args, parent::get_default_config() );
	}
}
