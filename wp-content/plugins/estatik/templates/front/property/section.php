<?php

/**
 * @var $machine_name string
 * @var $content string
 * @var $label string
 */

if ( ! empty( $content ) ) : ?>
    <div class="es-property-section es-property_section--<?php echo $machine_name; ?>" id="<?php echo $machine_name; ?>">
        <?php if ( ! empty( $label ) ) : ?>
            <h3 class="es-property-section__title"><?php _e( $label, 'es' ); ?></h3>
        <?php endif; ?>
        <div class="es-property-section__content">
            <?php echo $content; ?>
        </div>
    </div>
<?php endif;
