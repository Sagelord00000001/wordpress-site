<?php

/**
 * @var $images array
 */

?>
<div class="es-gallery js-es-property-gallery">
    <div class="es-gallery__image">
        <?php do_action( 'es_property_badges' ); ?>
        <?php do_action( 'es_property_control', array(
            'show_sharing' => true,
            'is_full' => true,
            'icon_size' => 'big',
            'context' => 'property-gallery'
        ) ); ?>
        <?php if ( ! empty( $images ) ) : ?>
        <a href="<?php echo es_get_the_featured_image_url( 'full' ); ?>" class="js-es-image" title="<?php echo wp_get_attachment_caption( $images[0] ); ?>">
            <div class="es-gallery__image-background"
                 style="background-image: url('<?php echo es_get_the_featured_image_url( 'full' ); ?>'); background-size: cover;">
            </div>
        </a>
        <?php else : ?>
            <div class="es-gallery__image-background"
                 style="background-image: url('<?php echo es_get_the_featured_image_url( 'full' ); ?>'); background-size: cover;">
            </div>
        <?php endif; ?>
    </div>
    <?php if ( is_array( $images ) && count( $images ) >= 2 ) : $index = 0; ?>
        <div class="es-gallery__thumbs">
            <?php foreach ( $images as $attachment_id ) : $caption = wp_get_attachment_caption( $attachment_id );
                if ( ! $index ) { $index++; continue; }
                if ( $index <= 3 ) : ?>
                <div class="es-gallery__thumb">
                    <?php if ( $index == 3 && count( $images ) > 4 ) : ?>
                        <span class="es-gallery__thumb-count heading-font">+<?php echo count( $images ) - 4; ?></span>
                    <?php endif; ?>
                    <a href="<?php echo wp_get_attachment_image_url( $attachment_id, 'full' ); ?>" class="js-es-image" title="<?php echo $caption; ?>">
                        <div class="es-gallery__thumb-inner" style="background-image: url('<?php echo wp_get_attachment_image_url( $attachment_id, 'medium' ); ?>')"></div>
                    </a>
                </div>
                <?php else : ?>
                    <a href="<?php echo wp_get_attachment_image_url( $attachment_id, 'full' ); ?>" title="<?php echo $caption; ?>" class="js-es-image es-hidden"></a>
                <?php endif; ?>
            <?php $index++; endforeach; ?>
            <?php if ( count( $images ) < 4 ) : ?>
                <?php for( $i = 0; $i < ( 4 - count( $images ) ); $i++ ) : ?>
                    <div class="es-gallery__thumb"></div>
                <?php endfor; ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
