<?php

/**
 * Class Es_Features_Creator.
 */
class Es_Features_Creator extends Es_Terms_Creator {

	/**
	 * @return void
	 */
    public static function render_icon( $term_id, $term_name, $taxonomy ) {
        echo '<span class="es-icon es-icon_check-mark"></span>';
    }

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
		$is_deactivated = es_is_term_deactivated( $term_id ); ?>
		<li class="es-item es-term js-es-term js-es-term-<?php echo $term_id; ?> es-<?php echo $taxonomy; ?>-term-<?php echo $term_id; ?> <?php echo $is_deactivated ? 'es-item--disabled' : ''; ?>">
            <div class="es-item__name">
                <?php static::render_icon( $term_id, $term_name, $taxonomy ); ?>
                <?php /* translators: %s: term id. */
                echo esc_attr( $term_name ); ?> <?php printf( __( '(ID: %s)', 'es' ), $term_id ); ?>
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
}
