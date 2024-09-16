<?php

/**
 * @var $wishlist_confirm bool
 */

$images_num = ests( 'property_item_carousel_images_num' );
$gallery = es_get_the_field( 'gallery' );
$target_blank = ! empty( $target_blank ) ? $target_blank : '';
$carousel_config = array(
    'slidesToShow' => 1,
    'dots' => true,
    'infinite' => true,
    'arrows' => true,
    'prevArrow' => "<button class='slick-prev'><span class='es-icon es-icon_chevron-left slick-prev'></span></button>",
    'nextArrow' => "<button class='slick-next'><span class='es-icon es-icon_chevron-right slick-next'></span></button>",
    'slide' => 'div',
    'rows' => 0,
    'adaptiveHeight' => true,
    'dotsClass' => 'es-slick-dots',
    'lazyLoad' => 'ondemand',
); ?>

<div class="es-listing__image">
    <?php do_action( 'es_property_badges' ); ?>
    <div class="es-property__control es-listing--hide-on-list">
        <?php do_action( 'es_property_control', array(
            'show_sharing' => false,
            'is_full' => false,
            'context' => 'property-image',
            'wishlist_confirm' => $wishlist_confirm,
        ) ); ?>
    </div>
    <?php if ( is_array( $gallery ) && count( $gallery ) > 1 && $images_num > 1 && ests( 'is_property_carousel_enabled' ) ) : ?>
        <div class="es-listing__image__slider js-es-slick" data-slick="<?php echo es_esc_json_attr( $carousel_config ); ?>">
            <?php foreach ( $gallery as $attachment_id ) : if ( ! $images_num ) break; ?>
                <div>
                    <a class="es-listings__image__link" <?php echo $target_blank; ?> href="<?php echo es_get_the_permalink(); ?>">
                        <img data-lazy="<?php echo wp_get_attachment_image_url( $attachment_id, ests( 'property_item_image_size' ) ); ?>"/>
                    </a>
                </div>
            <?php $images_num--; endforeach; ?>
        </div>
    <?php else : ?>
        <div class="es-listing__image__background" style="background-image: url('<?php echo es_get_the_featured_image_url( ests( 'property_item_image_size' ) ); ?>')">
            <a class="es-listings__image__link" <?php echo $target_blank; ?> href="<?php echo es_get_the_permalink(); ?>"></a>
        </div>
    <?php endif; ?>
</div>
