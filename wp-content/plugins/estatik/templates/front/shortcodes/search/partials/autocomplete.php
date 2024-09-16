<?php

/**
 * @var $addresses
 * @var $posts
 */

?>
<div class="es-autocomplete js-es-autocomplete content-font">
    <div class="es-address-list es-address-list--results">
        <?php if ( $addresses || $posts ) : ?>
            <ul>
                <?php if ( ! empty( $addresses ) ) : ?>
                    <?php foreach ( $addresses as $term_id => $address ) : ?>
                        <li class="es-address-list__item es-address-list__item--<?php echo $term_id; ?>">
                            <a href="" class="js-autocomplete-item" data-query="<?php echo esc_attr( $address ); ?>"><span class="es-icon es-icon_marker"></span><?php echo $address; ?></a>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
	            <?php if ( ! empty( $posts ) ) : ?>
		            <?php foreach ( $posts as $post ) :
                        $address = es_get_the_field( 'address', $post->ID ); ?>
                        <li class="es-address-list__item es-address-list__post-item--<?php echo $post->ID; ?>">
                            <a href="" class="js-autocomplete-item" data-query="<?php echo esc_attr( $address ); ?>"><span class="es-icon es-icon_marker"></span><?php echo $address; ?></a>
                        </li>
		            <?php endforeach; ?>
	            <?php endif; ?>
            </ul>
        <?php else: ?>
            <b><?php _e( 'Location not found', 'es' ); ?></b>
        <?php endif; ?>
    </div>

    <?php if ( ! empty( $recent ) ) : ?>
        <div class="es-address-list es-address-list--recent">
            <div class="es-address-list__head"><?php _e( 'Recent searches', 'es' ); ?></div>
            <?php if ( $recent ) : ?>
                <ul>
                    <?php foreach ( $recent as $term_id => $address ) : ?>
                        <li class="es-address-list__item es-address-list__item--<?php echo $term_id; ?>">
                            <a href=""><span class="es-icon es-icon_marker"></span><?php echo $address; ?></a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <b><?php _e( 'Location not found', 'es' ); ?></b>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
