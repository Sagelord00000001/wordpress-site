<div class="es-magnific-popup es-authentication-popup mfp-hide" id="es-authentication-popup">
    <?php $shortcode = es_get_shortcode_instance( 'es_authentication', array(
        'is_popup' => true,
    ) );

    if ( $shortcode instanceof Es_Shortcode ) :
        echo $shortcode->get_content();
    endif; ?>
</div>
