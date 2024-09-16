<?php

/**
 * @var $coordinates array
 */

if ( ests( 'google_api_key' ) ) : ?>
    <div class="js-es-map es-map" data-listings="<?php echo es_esc_json_attr( $coordinates ); ?>"></div>
<?php else : ?>
    <div class="es-map-error content-font">
        <?php _e( "Oops! This page didn't load Google Maps correctly. Please contact admin to fix this.", 'es' ); ?>
    </div>
<?php endif;
