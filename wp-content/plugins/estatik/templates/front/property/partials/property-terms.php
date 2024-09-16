<ul class="es-listing__terms">
    <?php foreach ( $taxonomies_list = apply_filters( 'es_property_footer_taxonomies_list', array( 'es_type', 'es_category' ) ) as $taxonomy ) :
        $terms = get_the_terms( get_the_ID(), $taxonomy );
        if ( ! is_wp_error( $terms ) && $terms ) : ?>
            <?php foreach ( $terms as $term ) : ?>
                <li><a href="<?php echo get_term_link( $term, $taxonomy ); ?>"><?php echo $term->name; ?></a></li>
            <?php endforeach; ?>
        <?php endif; ?>
    <?php endforeach; ?>
</ul>
