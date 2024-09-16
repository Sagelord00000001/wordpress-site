<?php

/**
 * Class Es_Labels_Creator.
 */
class Es_Labels_Creator extends Es_Terms_Creator {

	/**
	 * Renter term item.
	 *
	 * @param $term_id
	 * @param $term_name
	 * @param $taxonomy
	 */
	public function render_term_item( $term_id, $term_name ) {
	    $taxonomy = $this->_taxonomy;
		/* translators: %s: option name. */
		$delete_message = sprintf( __( 'Are you sure you want to delete <b>%s</b> Option?', 'es' ), $term_name );
		$is_deactivated = es_is_term_deactivated( $term_id );
        $term = get_term($term_id );
		$color = es_get_term_color( $term_id ); ?>

		<li class="es-item es-term js-es-term js-es-term-<?php echo $term_id; ?> es-<?php echo $taxonomy; ?>-term-<?php echo $term_id; ?> <?php echo $is_deactivated ? 'es-item--disabled' : ''; ?>">
			<div class="es-item__name">
				<div class="es-term__color <?php echo $color =='#ffffff' ? 'es-term__color--bordered' : ''; ?>" style="background: <?php echo $color; ?>"></div>
                <?php /* translators: %s: term id. */ ?>
				<span><?php echo esc_attr( $term_name ); ?></span> <?php printf( __( '(ID: %s)', 'es' ), $term_id ); ?>
                <span style="display: inline-block; margin-left: 30px; color: gray; font-size: 12px;">
                    <?php /* translators: %s: term slug. */
                    printf( __( 'Machine name: %s' ), $term->slug ); ?>
                </span>
				<a href="#" class="es-term__edit es-control js-es-term__edit" data-taxonomy="<?php echo $taxonomy; ?>" data-term="<?php echo $term_id; ?>"><span class="es-icon es-icon_pencil"></a>
			</div>
			<div class="es-control__container">
				<?php if ( $is_deactivated ) : ?>
					<a href="#" data-taxonomy="<?php echo $taxonomy; ?>" data-term="<?php echo $term_id; ?>" class="es-control es-term__delete--confirm js-es-term__restore"><span class="es-icon es-icon_plus"></span></a>
				<?php else : ?>
					<a href="#" data-taxonomy="<?php echo $taxonomy; ?>" data-message="<?php echo esc_attr( $delete_message ); ?>" data-term="<?php echo $term_id; ?>" class="es-control js-es-term__delete--confirm es-term__delete--confirm">
						<?php if ( es_is_default_term( $term_id ) ) : ?>
							<span class="es-icon es-icon_trash"></span>
						<?php else : ?>
							<span class="es-icon es-icon_close"></span>
						<?php endif; ?>
					</a>
				<?php endif; ?>
				<?php es_framework_field_render( "es_term_{$term_id}", array(
					'type' => 'checkbox',
					'wrapper_class' => "es-field es-field__{field_key} es-field--{type} js-es-field--term es-field--term",
					'attributes' => array(
						'id' => sprintf( "es-field-%s", $term_id ),
						'value' => $term_id,
					),
				) ); ?>
			</div>
		</li><?php
	}

	/**
	 * Render term form.
	 *
	 * @return void
	 */
	public function render_form() { ?>
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

			es_framework_field_render( 'term_color', array(
                'wrapper_class' => 'es-field--color--break-label',
				'type' => 'color',
				'label' => _x( 'Label color', 'data manager term add', 'es' ),
				'value' => $this->_form_term instanceof WP_Term ? es_get_term_color( $this->_form_term->term_id ) : ests( 'default_label_color' ),
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
