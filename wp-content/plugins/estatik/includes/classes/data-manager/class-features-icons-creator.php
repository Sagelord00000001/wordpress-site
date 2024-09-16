<?php

/**
 * Class Es_Features_Icons_Creator.
 *
 * @return void
 */
class Es_Features_Icons_Creator extends Es_Features_Creator {

	/**
	 * Render term icon.
	 *
	 * @param $term_id
	 * @param $term_name
	 * @param $taxonomy
	 */
	public static function render_icon( $term_id, $term_name, $taxonomy ) {
		$icon = get_term_meta( $term_id, 'es_icon', true );

		if ( ! $icon ) {
			echo "<span class='es-icon es-icon_icon'></span>";
		} else {
			$icon = (array)$icon;
			/** @var Es_Framework_Icon_Field $framework */
			$framework = es_framework_get_field( 'term_icon', array( 'type' => 'icon' ) );
			echo $framework::get_icon_markup( $icon );
		}
	}

	/**
	 * Render term form.
	 *
	 * @return void
	 */
	public function render_form() {
		?>
		<form action="" class="js-es-term-form">
			<?php if ( ! empty( $this->_form_term_id ) ) : ?>
				<input type="hidden" name="term_id" value="<?php echo $this->_form_term_id; ?>"/>
			<?php endif; ?>
			<input type="hidden" name="action" value="es_terms_creator_add_term"/>
			<input type="hidden" name="taxonomy" value="<?php echo $this->_taxonomy; ?>"/>
			<?php wp_nonce_field( 'es_terms_creator_add_term' );

			es_framework_field_render( 'term_name', array(
				'type' => 'text',
				'attributes' => array(
					'class' => 'es-field__input js-es-term-name',
				),
				'label' => _x( 'Add new option', 'data manager term add', 'es' ),
				'value' => $this->_form_term instanceof WP_Term ? $this->_form_term->name : '',
			) );

			$term_icon = $this->_form_term_id ? get_term_meta( $this->_form_term_id, 'es_icon', true ) : '';
			$term_icon = $term_icon ? (array)$term_icon : '';

			es_framework_field_render( 'term_icon', array(
				'type' => 'icon',
				'label' => __( 'Select icon', 'es' ),
                'upload_title' => "<span class='es-pro-label'>" . __( 'Upload your custom icon', 'es' ) . '</span>',
                'value' => $term_icon,
			) ); ?>
			<button disabled type="submit" class="es-btn es-btn--secondary">
				<?php if ( $this->_form_term_id ) : ?>
					<?php echo _x( 'Save', 'data manager term add', 'es' ); ?>
				<?php else : ?>
					<?php echo _x( 'Add', 'data manager term add', 'es' ); ?>
				<?php endif; ?>
			</button>
		</form>
		<?php
	}
}
