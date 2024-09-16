<?php

/**
 * @var $entity_plural string
 * @var $title string
 */

$popup_id = ! empty( $popup_id ) ? $popup_id : 'es-share-popup';

if ( ests( 'is_' . $entity_plural . '_sharing_enabled' ) ) : ?>
    <div class="es-magnific-popup es-share-popup mfp-hide" id="<?php echo $popup_id; ?>">
        <h4><?php echo $title; ?></h4>

		<?php if ( ests( 'is_link_sharing_enabled' ) ) : ?>
            <div class="es-fields-list__selector">
				<?php es_framework_field_render( 'property_link', array(
					'label' => __( 'Copy this URL to share', 'es' ),
					'type' => 'text',
					'value' => get_the_permalink(),
					'attributes' => array(
						'readonly' => 'readonly',
						'class' => 'js-es-select-text-click'
					),
				) ); ?>
                <a href="#" data-clipboard-text="<?php echo esc_attr( get_the_permalink() ); ?>" data-copied="<?php esc_attr_e( 'Copied', 'es' ); ?>" class="js-es-copy js-es-property-copy es-btn es-btn--secondary"><?php echo _x( 'Copy', 'property link copy', 'es' ); ?></a>
            </div>
		<?php endif; ?>

		<?php if ( ( ests( 'is_social_sharing_enabled' ) && ! empty( ests( 'social_networks' ) ) ) || ests( 'is_pdf_enabled' ) ) : ?>
            <div class="es-share-container">
				<?php if ( ests( 'is_link_sharing_enabled' ) ) : ?>
                    <p><?php _e( 'Or share with', 'es' ); ?></p>
				<?php else : ?>
                    <p><?php _e( 'Share with', 'es' ); ?></p>
				<?php endif; ?>
                <ul class="a2a_kit es-control">
					<?php do_action( 'es_before_share_block_buttons' ); ?>
					<?php if ( ( $networks = ests( 'social_networks' ) ) && ests( 'is_social_sharing_enabled' ) ):
						foreach ( $networks as $social_network ) : ?>
                            <li class="es-control__item"><a class="es-btn es-btn--default es-btn--icon es-btn--small a2a_button_<?php echo $social_network; ?>">
                                    <span class="es-icon es-icon_<?php echo $social_network; ?>"></span>
                                </a></li>
						<?php endforeach;
					endif;

					do_action( 'es_after_share_block_buttons' ); ?>
                </ul>
            </div>
		<?php endif; ?>
    </div>
<?php endif;
