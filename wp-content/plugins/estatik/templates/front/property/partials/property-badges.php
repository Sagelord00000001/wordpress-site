<?php

/**
 * @var $property Es_Property
 */

$labels = apply_filters( 'es_property_badges_terms', get_the_terms( get_the_ID(), 'es_label' ), get_the_ID() );

if ( $labels && ! is_wp_error( $labels ) ) : $badges_num = apply_filters( 'es_property_badges_terms_num', 4 ); ?>
    <ul class="es-badges es-badges--rounded">
        <?php foreach ( $labels as $label ) :
            if ( ! $badges_num ) break;
            $badges_num--;
            $color = es_get_term_color( $label->term_id );
            $color = $color ? $color : ests( 'default_label_color' );
	        $style_color = stristr( $color, 'fff' ) ? 'style="color: #000;"' : ''; ?>
            <li class="es-badge" style="background: <?php echo $color; ?>;">
                <a <?php echo $style_color; ?> href="<?php echo get_term_link( $label->term_id ); ?>"><?php echo $label->name; ?></a></li>
        <?php endforeach; ?>
    </ul>
<?php endif;
