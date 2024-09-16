<?php

/**
 * @var $query WP_Query
 * @var $args array Shortcode attribtes
 */

?>
<div class="es-listings-filter js-es-listings-filter">
    <?php if ( ! empty( $args['show_page_title'] ) && $query->found_posts ) : ?>
        <span class="es-title"><?php echo $args['page_title']; ?></span>
    <?php endif; ?>

    <?php if ( ! empty( $args['show_total'] ) && $query->found_posts ) : ?>
        <span class="es-total">
            <?php printf( _n( '%d result', '%d results', $query->found_posts, 'es' ), $query->found_posts ); ?>
        </span>
    <?php endif; ?>

    <?php if ( ! empty( $args['show_sort'] ) || ! empty( $args['show_layouts'] ) ) : ?>
        <?php if ( ! empty( $args['show_sort'] ) ) : ?>
            <?php do_action( 'es_sort_dropdown', $args['sort'] ); ?>
        <?php endif; ?>
        <?php if ( ! empty( $args['show_layouts'] ) ) : ?>
            <?php do_action( 'es_layouts', $args ); ?>
        <?php endif; ?>
    <?php endif; ?>
</div>

