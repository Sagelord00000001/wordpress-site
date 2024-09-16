<div class="es-step" id="step1">
    <h1><?php _e( 'Let’s adjust Estatik to your needs', 'es' ); ?></h1>

    <div class="es-demo__field-container es-demo__field-container--agent">
        <h2><?php _e( 'Who is your website for?', 'es' ); ?></h2>

        <div class="es-demo__radio-grid">
            <?php
            $countries = ests_values( 'country' );

            es_framework_field_render( 'agent_type', array(
                'type' => 'radio-boxed',
                'value' => 'single_agent',
                'size' => 6,
                'options' => array(
                    'single_agent' => '<span class="es-icon es-icon_icon es-icon--rounded es-icon--blue"></span>
                                           <h3>' . __( 'Single owner', 'es' ) . '</h3><span class="es-icon es-icon_info"></span>
                                           <p>' . __( 'You are a single agent or an owner of your website.', 'es' ) . '</p>',

                    'agency' => '<span class="es-icon es-icon_icon es-icon--rounded es-icon--blue"></span>
                                           <h3>' . __( 'Agency', 'es' ) . '</h3><span class="es-icon es-icon_info"></span>
                                           <p>' . __( 'You can add multiple agents & agencies.', 'es' ) . '</p>
                                           <span class="es-label es-label--green">PRO</span>',
                ),
                'items_attributes' => array(
                    'agency' => array(
                        'attributes' => array(
                            'disabled' => 'disabled',
                        )
                    )
                ),
            ) ); ?>
        </div>
    </div>

    <div class="es-demo__field-container es-demo__field-container--country">
        <h2><?php _e( 'What is your country?', 'es' ); ?></h2>
        <p><?php _e( 'The plugin will pick up the language, currency, area dimensions<br> and other settings of the country you select.', 'es' ); ?></p>
        <?php $description = __( '{0} language, {1} currency, {2} and {3} for area and lot sizes.<br>Go to Settings to change it.', 'es' );

        es_framework_field_render( 'country', array(
            'type' => 'radio-bordered',
            'options' => $countries,
            'value' => ests( 'country' ),
            'attributes' => array(
                'data-description' => $description,
                'class' => 'js-es-country-field'
            ),
            'description' => '-'
        ) ); ?>
    </div>

    <a href="#step2" class="es-btn es-btn--step es-btn--large es-btn--primary es-btn--icon-right js-es-next-step"><?php _e( 'Continue', 'es' ); ?><span class="es-icon es-icon_arrow-right"></a>
</div>
