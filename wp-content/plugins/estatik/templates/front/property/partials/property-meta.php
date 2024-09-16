<?php

$property = es_get_property( get_the_ID() );
$fields = es_property_get_meta_fields();

if ( ! empty( $fields ) ) : ?><ul class="es-listing__meta"><?php
	foreach ( $fields as $field ) :
		if ( ! empty( $field['enabled'] ) && ! empty( $property->{$field['field']} )  ) : ?>
            <li class="es-listing__meta-<?php echo $field['field']; ?>">
				<?php if ( ! empty( $use_icons ) ) : ?>
					<?php if ( ! empty( $field['svg'] ) ) : ?>
						<?php echo $field['svg']; ?>
					<?php elseif ( ! empty( $field['icon'] ) ) : ?>
                        <img class="es-meta-icon" src="<?php echo $field['icon'] ?>" alt="<?php printf( __( 'Property %s' ), $field['field'] ); ?>"/>
					<?php endif; ?>
				<?php endif; ?>
				<?php es_the_formatted_field( $field['field'] ); ?>
            </li>
		<?php endif;
	endforeach;
	?></ul><?php
endif;
