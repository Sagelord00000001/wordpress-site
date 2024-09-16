<?php

/**
 * @var $images array
 */
?>
<div class="es-slider js-es-slider">
    <div class="es-slider__image">
        <?php do_action( 'es_property_badges' ); ?>
        <?php do_action( 'es_property_control', array(
            'show_sharing' => true,
            'is_full' => true
        ) ); ?>
        <div class="es-slider__page-info">
            <span class="es-icon es-icon_icon"></span>
            <span class="es-slider__page-info-text"></span>
        </div>
        <div class="es-slider__image-inner js-es-slider__image">
            <?php if ( is_array( $images ) && ! empty( $images ) ) : ?>
                <?php foreach ( $images as $attachment_id ) : $caption = wp_get_attachment_caption( $attachment_id ); ?>
                    <a href="<?php echo wp_get_attachment_image_url( $attachment_id, 'full' ); ?>"
                       class="es-slider__item js-es-image" title="<?php echo $caption; ?>"
                       style="background-image: url('<?php echo wp_get_attachment_image_url( $attachment_id, 'full' ); ?>'); background-size: cover;"></a>
                <?php endforeach; ?>
            <?php else : ?>
                <div class="es-slider__item" title="<?php esc_attr( strip_tags( get_the_title() ) ); ?>"
                   style="background-image: url('<?php echo es_get_the_featured_image_url( 'full' ); ?>'); background-size: cover;"></div>
            <?php endif; ?>
        </div>
    </div>
    <?php if ( is_array( $images ) && ! empty( $images ) && count( $images ) > 1 ) : ?>
        <div class="es-slider__pager js-es-slider__pager">
            <?php foreach ( $images as $attachment_id ) : ?>
            <div>
                <div class="es-slider__item"
                     style="background-image: url('<?php echo wp_get_attachment_image_url( $attachment_id, 'medium' ); ?>'); background-size: cover;"></div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
