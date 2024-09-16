<div class="es-step es-step--hidden" id="step4">
    <h1><?php _e( 'Would you like to add demo listings?', 'es' ); ?></h1>
    <p><?php _e( 'Demo listings show how your pages look and work like. You can delete demo content any time.', 'es' ); ?></p>

    <?php es_framework_field_render( 'import_listings', array(
        'type' => 'radio-bordered',
        'options' => array(
            '' => _x( 'No', 'import demo listings', 'es' ),
            1 => _x( 'Yes', 'import demo listings', 'es' )
        ),
        'value' => 1,
    ) ); ?>

    <button type="submit" class="es-btn es-btn--step es-btn--large es-btn--primary es-btn--icon-right js-es-btn--finish"><?php _e( 'Finish step', 'es' ); ?></button>
</div>
