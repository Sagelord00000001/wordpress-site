<div class="es-single__header">
    <div class="es-single__header-terms">
        <?php es_load_template( 'front/property/partials/property-terms.php' ); ?>
    </div>
    <div class="es-single__header-left">
        <?php es_the_title( '<h1 class="property-title heading-font">', '</h1>' );
        do_action( 'es_single_property_after_title', get_the_ID() );
        es_the_address( '<span class="es-address">', '</span>' );
        es_load_template( 'front/property/partials/property-meta.php', array(
            'use_icons' => true
        ) ); ?>
    </div>
    <div class="es-single__header-right">
        <?php if ( es_get_the_field( 'price' ) ) : ?>
            <div class="es-price-container">
                <?php es_the_price(); es_the_field( 'price_note', '<span class="es-badge es-price-badge es-badge--normal">', '</span>' ); ?>
            </div>
        <?php endif; ?>
	    <?php if ( es_is_request_form_active() && ! ests( 'is_request_form_button_disabled' ) ) : ?>
            <a href="#request_form" class="es-btn--request-info es-btn es-btn--primary js-es-scroll-to"><?php _e( 'Request info', 'es' ); ?></a>
	    <?php endif; ?>
    </div>
</div>
