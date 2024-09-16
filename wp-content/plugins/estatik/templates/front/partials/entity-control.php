<?php

/**
 * @var $is_full bool
 * @var $show_sharing bool
 * @var $entity_plural string
 * @var $entity string
 */

$wishlist = es_get_wishlist_instance( $entity );
$icon_size = ! empty( $icon_size ) ? $icon_size : 'small';
$classes = array( 'es-btn__wishlist', 'es-btn', 'es-btn--default', 'es-btn--' . $icon_size );
$context = ! empty( $context ) ? $context : 'es-control--default';

if ( ! $is_full  ) {
    $classes[] = 'es-btn--icon';
} ?>

<ul class="js-es-control es-control es-control--<?php echo $context; ?>">
    <?php if ( ests( 'is_' . $entity_plural . '_wishlist_enabled' ) ) :
	    if ( $wishlist->has( get_the_ID() ) ) $classes[] = 'es-btn--active'; ?>
        <li class="es-control__item es-control__item--wishlist">
            <?php if ( ! is_user_logged_in() ) :
            $classes[] = 'js-es-popup-link'; ?>
                <a href="#" data-popup-id="#es-authentication-popup" class="<?php echo implode( ' ', $classes ); ?>">
            <?php else :
                if ( ! empty( $wishlist_confirm ) ) $classes[] = 'js-es-wishlist--confirm';
                $classes[] = 'js-es-wishlist'; ?>
                <a href="#" data-entity="<?php echo $entity; ?>" data-id="<?php the_ID(); ?>" class="<?php echo implode( ' ', $classes ); ?>">
            <?php endif; ?>
                <span class="es-icon es-icon_heart"></span>
                <span class="es-btn__label"><?php _e( 'Save', 'es' ); ?></span>
            </a>
        </li>
    <?php endif; ?>
    <?php if ( ests( 'is_' . $entity_plural . '_sharing_enabled' ) && $show_sharing ) :
	    $share_popup_id = ! empty( $share_popup_id ) ? $share_popup_id : 'es-share-popup'; ?>
        <li class="es-control__item es-control__item--sharing">
            <a href="#" data-popup-id="#<?php echo $share_popup_id; ?>" class="js-es-popup-link es-btn es-btn--<?php echo $icon_size; ?> es-btn--default <?php echo ! $is_full ? 'es-btn--icon' : ''; ?>">
                <span class="es-icon es-icon_sharing"></span>
                <span class="es-btn__label"><?php _e( 'Share', 'es' ); ?></span>
            </a>
        </li>
    <?php endif; ?>
    <?php do_action( 'es_after_' . $entity . '_control_inner' ); ?>
</ul>
