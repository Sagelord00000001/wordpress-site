<?php

/**
 * @var $query WP_Query
 * @var $item_template WP_Query
 * @var $slider_config array
 * @var $attributes array
 * @var $container_classes string
 */

if ( $query->have_posts() ):
    if ( ! empty( ! empty( $attributes['space_between_slides'] ) ) ) : ?>
        <style><?php $margin = $attributes['space_between_slides']; ?>
            #<?php echo $attributes['id']; ?>>.slick-list {
                margin: -<?php echo $margin / 2; ?>px;
            }

            #<?php echo $attributes['id']; ?>>.slick-list>.slick-track>.slick-slide {
                 margin: <?php echo $margin / 2; ?>px;
            }
        </style>
    <?php endif; ?>
    <div id="<?php echo $attributes['id']; ?>" class="<?php echo $container_classes; ?>" data-slick="<?php echo es_esc_json_attr( $slider_config ); ?>"><?php
        while ( $query->have_posts() ) : $query->the_post();
            ?><div class="es-listing-wrapper es-listing-wrapper--<?php the_ID(); ?>">
                <?php es_load_template( $item_template, array(
                    'ignore_wrapper' => true,
                ) );
            ?></div><?php
        endwhile;
    ?></div><?php wp_reset_postdata();
endif;
