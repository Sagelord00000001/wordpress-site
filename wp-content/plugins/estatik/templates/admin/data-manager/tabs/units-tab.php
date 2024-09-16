<h2 id="es-units"><?php echo _x( 'Units & formats', 'data manager title', 'es' ); ?></h2>

<form class="js-es-settings-form">
    <div class="es-row">
        <div class="es-col-md-6">
            <h4 id="es-area-units"><?php _e( 'Area units', 'es' ); ?></h4>
            <?php es_settings_field_render( 'area_unit', array(
                'type' => 'radio',
            ) ); ?>
        </div>
        <div class="es-col-md-6">
            <h4 id="es-lot-size-units"><?php _e( 'Lot size units', 'es' ); ?></h4>
            <?php es_settings_field_render( 'lot_size_unit', array(
                'type' => 'radio',
            ) ); ?>
        </div>
    </div>

    <h4 id="es-currencies"><?php _e( 'Currency settings', 'es' ); ?></h4>

    <div class="es-field-row">
        <?php es_settings_field_render( 'currency', array(
            'type' => 'select',
            'options' => ests_values( 'currency' ),
            'label' => __( 'Currency code', 'es' ),
            'attributes' => array(
	            'class' => 'js-es-currency'
            ),
        ) ); ?>

        <?php es_settings_field_render( 'currency_sign', array(
            'type' => 'select',
            'options' => ests_values( 'currency_sign' ),
            'attributes' => array(
                'class' => 'js-es-sign',
                'placeholder' => __( 'Currency code', 'es' ),
            ),
            'label' => __( 'Sign', 'es' ),
        ) ); ?>

        <?php es_settings_field_render( 'currency_position', array(
            'type' => 'select',
            'options' => array(
                'before' => __( 'Before $100', 'es' ),
                'after' => __( 'After 100$', 'es' ),
                'before_space' => __( 'Before with space $ 100', 'es' ),
                'after_space' => __( 'After with space 100 $', 'es' ),
            ),
            'attributes' => array(
                'class' => 'js-es-append-sign',
                'data-before' => __( 'Before {sign}100', 'es' ),
                'data-after' => __( 'After 100{sign}', 'es' ),
                'data-before_space' => __( 'Before with space $ {sign}', 'es' ),
                'data-after_space' => __( 'After with space 100 {sign}', 'es' ),
            ),
            'label' => __( 'Sign position', 'es' ),
        ) ); ?>

        <?php es_settings_field_render( 'currency_sup', array(
	        'type' => 'text',
	        'label' => __( 'Thousand separator', 'es' ),
        ) ); ?>

        <?php es_settings_field_render( 'currency_dec', array(
	        'type' => 'text',
	        'label' => __( 'Decimal separator', 'es' ),
        ) ); ?>

        <?php es_settings_field_render( 'currency_dec_num', array(
	        'type' => 'number',
	        'label' => __( 'Decimal digits', 'es' ),
            'attributes' => array(
                'min' => 0,
                'max' => 3,
            ),
        ) ); ?>
    </div>

    <?php wp_nonce_field( 'es_save_settings' ); ?>
    <input type="hidden" name="action" value="es_save_settings"/>

    <button style="margin-top: 20px;" type="submit" class="es-btn es-btn--primary es-btn--large js-es-save-settings"><?php _e( 'Save changes', 'es' ); ?></button>
</form>
