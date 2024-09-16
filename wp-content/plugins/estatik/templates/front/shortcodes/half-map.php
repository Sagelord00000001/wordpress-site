<?php

/**
 * @var $search_form Es_Search_Form_Shortcode
 * @var $search_listings Es_My_Listing_Shortcode
 * @var $coordinates string
 * @var $args array
 */

if ( ! empty( $args['search_form_selector'] ) ) : ?>
    <div data-search-form-selector='<?php echo $args['search_form_selector']; ?>'>
<?php endif; ?>
<div class="es-hfm js-es-hfm <?php echo $args['hfm_full_width'] ? 'es-hfm--full-width' : ''; ?>">
    <?php if ( $search_form instanceof Es_Shortcode ) : ?>
        <div class="es-hfm__navbar"><?php echo $search_form->get_content(); ?></div>
    <?php endif; ?>
    <div class="es-hfm__wrap">
        <div class="es-hfm__listings js-es-hfm__listings">
            <?php echo $search_listings->get_content(); ?>
        </div>
        <div class="es-hfm__map-container">
            <?php if ( ests( 'google_api_key' ) ) : ?>
                <div class="js-es-hfm__map es-hfm__map es-map" data-listings="<?php echo es_esc_json_attr( $coordinates ); ?>"></div>
            <?php else : ?>
                <div class="es-hfm__map-error content-font">
                    <?php _e( "Oops! This page didn't load Google Maps correctly. Please contact admin to fix this.", 'es' ); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php if ( ! empty( $args['search_form_selector'] ) ) : ?>
    </div>
<?php endif;