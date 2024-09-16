<div class="es-step es-step--hidden" id="step3">
    <h1><?php _e( 'Add recommended pages', 'es' ); ?></h1>

    <div class="es-demo__radio-grid es-demo__radio-grid--pages">
        <?php es_framework_field_render( 'pages', array(
            'type' => 'checkboxes-boxed',
            'size' => 3,
            'value' => array( 'list-layout', 'grid-layout', 'map-view', 'search-results', 'sign-up',
                'log-in', 'reset-password', 'profile' ),
            'items_attributes' => array(
                'agents' => array(
                    'attributes' => array(
                        'disabled' => 'disabled',
                    )
                ),
                'agent-page' => array(
                    'attributes' => array(
                        'disabled' => 'disabled',
                    )
                ),
                'agencies' => array(
                    'attributes' => array(
                        'disabled' => 'disabled',
                    )
                ),
                'frontend-management' => array(
                    'attributes' => array(
                        'disabled' => 'disabled',
                    )
                ),
            ),
            'options' => array(
                'list-layout' => '<span class="es-icon es-icon_list-view es-icon--rounded es-icon--blue"></span>
                                      <h3>' . __( 'List layout', 'es' ) . '</h3>',

                'grid-layout' => '<span class="es-icon es-icon_grid es-icon--rounded es-icon--blue"></span>
                                      <h3>' . __( 'Grid layout', 'es' ) . '</h3>',

                'map-view' => '<span class="es-icon es-icon_icon es-icon--rounded es-icon--blue"></span>
                                      <h3>' . __( 'Map view', 'es' ) . '</h3>',

                'search-results' => '<span class="es-icon es-icon_search es-icon--rounded es-icon--blue"></span>
                                      <h3>' . __( 'Search results', 'es' ) . '</h3>',

                'sign-up' => '<span class="es-icon es-icon_key es-icon--rounded es-icon--blue"></span>
                                      <h3>' . __( 'Sign up', 'es' ) . '</h3>',

                'log-in' => '<span class="es-icon es-icon_login es-icon--rounded es-icon--blue"></span>
                                      <h3>' . __( 'Log in', 'es' ) . '</h3>',

                'reset-password' => '<span class="es-icon es-icon_reload es-icon--rounded es-icon--blue"></span>
                                      <h3>' . __( 'Reset password', 'es' ) . '</h3>',

                'profile' => '<span class="es-icon es-icon_profile es-icon--rounded es-icon--blue"></span>
                                      <h3>' . __( 'Profile', 'es' ) . '</h3>',

                'frontend-management' => '<span class="es-icon es-icon_settings es-icon--rounded es-icon--blue"></span>
                                      <h3>' . __( 'Front-end management', 'es' ) . '</h3>
                                      <span class="es-label es-label--green">PRO</span>',

                'agents' => '<span class="es-icon es-icon_glasses es-icon--rounded es-icon--blue"></span>
                                      <h3>' . __( 'Agents', 'es' ) . '</h3>
                                      <span class="es-label es-label--green">PRO</span>',

                'agencies' => '<span class="es-icon es-icon_case es-icon--rounded es-icon--blue"></span>
                                      <h3>' . __( 'Agencies', 'es' ) . '</h3>
                                      <span class="es-label es-label--green">PRO</span>',

                'agent-page' => '<span class="es-icon es-icon_page es-icon--rounded es-icon--blue"></span>
                                      <h3>' . __( 'Agent page', 'es' ) . '</h3>
                                      <span class="es-label es-label--green">PRO</span>',
            ),
        ) ); ?>
    </div>

    <a href="#step4" class="es-btn es-btn--step es-btn--large es-btn--primary es-btn--icon-right js-es-next-step"><?php _e( 'Continue', 'es' ); ?><span class="es-icon es-icon_arrow-right"></a>
</div>
