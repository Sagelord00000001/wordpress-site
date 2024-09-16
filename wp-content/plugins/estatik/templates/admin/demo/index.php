<?php

/**
 * @var $args array
 */

$step = es_clean( filter_input( INPUT_GET, 'step' ) ); ?>

<div class="es-wrap es-demo">
    <?php if ( ! $step ) {
        es_load_template( 'admin/demo/start.php', $args );
    } else {
        es_load_template( 'admin/demo/steps.php' );
    } ?>
</div>
