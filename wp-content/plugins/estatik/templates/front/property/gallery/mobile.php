<?php

/**
 * @var $slick_config array
 * @var $images array
 */

?>
<div class="es-mobile-gallery-wrap">
    <?php do_action( 'es_property_badges' ); ?>
    <?php do_action( 'es_property_control', array(
        'show_sharing' => true,
        'is_full' => true,
        'context' => 'mobile-gallery'
    ) ); ?>

    <div class="es-mobile-gallery js-es-mobile-gallery">
        <?php if ( is_array( $images ) && ! empty( $images ) ) : ?>
            <?php foreach ( $images as $attachment_id ) : ?>
                <?php echo wp_get_attachment_image( $attachment_id, 'medium' ); ?>
            <?php endforeach; ?>
        <?php else : ?>
            <img src="<?php echo es_get_the_featured_image_url( 'full' ) ; ?>" alt="<?php esc_attr( strip_tags( get_the_title() ) ); ?>"/>
        <?php endif; ?>
    </div>
	<?php if ( is_array( $images ) && ! empty( $images ) ) : ?>
        <div class="es-mobile-gallery__pager">
            <div class="es-icon es-icon_icon"></div>
            <div class="js-es-mobile-gallery__pager"></div>
        </div>
    <?php endif; ?>
</div>
