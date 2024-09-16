<?php

/**
 * @var $css_layout
 * @var $query
 * @var $hash
 * @var $args
 */

?>

<div class="es-listings__wrap-inner js-es-listings__wrap-inner js-es-entities__wrap_inner et_smooth_scroll_disabled">
    <?php do_action( 'es_before_listings', array_merge( $args, array( 'query' => $query ) ) ); ?>

    <div data-entity="listings" class="js-es-listings js-es-entities es-listings <?php echo $css_layout; ?>" data-layout="<?php echo $css_layout; ?>" data-hash="<?php echo esc_attr( $hash ); ?>">
        <?php if ( $query->have_posts() ) : ?>
            <?php while( $query->have_posts() ) : $query->the_post(); ?>
                <?php include es_locate_template( 'front/property/content-archive.php' ); ?>
            <?php endwhile; wp_reset_postdata(); ?>
        <?php endif; ?>
    </div>
	<?php if ( ! $query->have_posts() ) : ?>
        <?php do_action( 'es_properties_no_found_posts', $args ); ?>
    <?php endif; ?>
    <?php if ( $query->have_posts() && ! empty( $args['view_all_page_id'] ) && ! empty( $args['view_all_link_name'] ) ) : ?>
        <div class="es-view-all">
            <a href="<?php echo get_permalink( $args['view_all_page_id'] ); ?>" class="es-btn es-btn--secondary es-btn--bordered">
                <?php _e( $args['view_all_link_name'], 'es' ); ?>
            </a>
        </div>
    <?php endif;

    if ( empty( $args['disable_pagination'] ) ) {
        es_the_pagination( $query );
    }

    do_action( 'es_after_listings' ); ?>
</div>
