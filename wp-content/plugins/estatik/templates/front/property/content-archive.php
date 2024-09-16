<?php $target_blank = ! empty( $target_blank ) ? $target_blank : '';

if ( empty( $ignore_wrapper ) ) : ?>
    <div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
<?php endif; ?>
    <div class="js-es-listing es-listing es-listing--<?php the_ID(); ?>" data-post-id="<?php the_ID(); ?>">
        <?php es_load_template( 'front/property/content-archive-image.php', array(
                'target_blank' => $target_blank,
                'wishlist_confirm' => ! empty( $wishlist_confirm ) ? $wishlist_confirm : null,
        ) ); ?>
        <div class="es-listing__content">
            <div class="es-listing__content__inner">
                <div class="es-listing__content__left">
                    <?php es_the_title( '<h3 class="es-listing__title">
                        <a href="' . es_get_the_permalink() . '" ' . $target_blank . '>', '</a></h3>' ); ?>
                    <div class='es-badges es-listing--hide-on-list'>
                        <?php es_the_price();
                        es_the_field( 'price_note', '<span class="es-badge es-badge--normal">', '</span>' ); ?></div>
                    <?php es_the_address( '<div class="es-address es-listing--hide-on-grid">', '</div>' );
                    if ( get_the_excerpt() && ests( 'is_listing_description_enabled' ) ) : ?>
                        <p class="es-excerpt es-listing--hide-on-grid"><?php the_excerpt(); ?></p>
                    <?php endif;
                    do_action( 'es_property_meta', array( 'use_icons' => true ) );
                    es_the_address( '<div class="es-address es-listing--hide-on-list">', '</div>' ); ?>
                </div>
                <div class="es-listing__content__right es-listing--hide-on-grid">
                    <div class="es-property__control es-listing--hide-on-grid">
                        <?php do_action( 'es_property_control', array(
                            'show_sharing' => false,
                            'is_full' => false,
                            'icon_size' => 'big',
                            'context' => 'property-content'
                        ) ); ?>
                    </div>
                    <?php es_the_price(); ?>
                    <?php es_the_field( 'price_note', '<span class="es-badge es-badge--normal">', '</span>' ); ?>
                </div>
            </div>
            <div class="es-listing__footer">
                <?php es_load_template( 'front/property/partials/property-terms.php' ); ?>
            </div>

        </div>
    </div>
<?php if ( empty( $ignore_wrapper ) ) : ?>
    </div>
<?php endif;
