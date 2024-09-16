<?php

/**
 * @var $args array
 * @var $attributes array
 */

?><div class="es-request-form es-request-form--sidebar" style="background: <?php echo $attributes['background']; ?>">
    <?php if ( ! empty( $attributes['title'] ) ) : ?>
        <h3 class="es-widget__title"><?php echo $attributes['title']; ?></h3>
    <?php endif;
    include es_locate_template( 'front/shortcodes/request/form.php' ); ?>
    <div class="js-es-request-form__response"></div>
</div>
