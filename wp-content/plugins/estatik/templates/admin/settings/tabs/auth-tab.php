<h2><?php echo _x( 'Log in & Sign up', 'es' ); ?></h2>

<div class="es-settings-fields es-settings-fields--max-width">
    <?php es_settings_field_render( 'is_login_form_enabled', array(
        'label' => __( 'Enable log in form', 'es' ),
        'type' => 'switcher',
        'attributes' => array(
            'data-toggle-container' => '#es-login-container',
        ),
    ) ); ?>

    <div id="es-login-container">
		
        <?php es_settings_recommended_page_render( 'login_page_id', array(
            'page_name' => __( 'Log in', 'es' ),
            'page_display_name' => __( 'Log in page', 'es' ),
            'page_content' => '[es_authentication auth_item="login-buttons"]',
        ) ); ?>
    </div>

    <?php es_settings_field_render( 'is_login_facebook_enabled', array(
        'label' => __( 'Enable log in with Facebook', 'es' ),
        'type' => 'switcher',
        'attributes' => array(
            'data-toggle-container' => '#es-facebook-container',
        ),
    ) ); ?>

    <div id="es-facebook-container">
        <?php es_settings_field_render( 'facebook_app_id', array(
            'label' => __( 'App ID', 'es' ),
            'type' => 'text',
            'caption' => __( sprintf( 'You can %s.', "<a target='_blank' href='https://developers.facebook.com/docs/apps/#register'>" . __( 'Generate App ID here' ) . "</a>" ), 'es' )
        ) );

        es_settings_field_render( 'facebook_app_secret', array(
            'label' => __( 'App secret', 'es' ),
            'type' => 'text',
            'caption' => __( sprintf( 'You can %s.', "<a target='_blank' href='https://developers.facebook.com/docs/apps/#register'>" . __( 'Generate App Secret here' ) . "</a>" ), 'es' )
        ) ); ?>

        <b><?php _e( 'Redirect urls' ); ?></b>
        <ul>
            <li><?php echo add_query_arg( 'auth_network', 'facebook-login-buttons', site_url( '/' ) ); ?></li>
            <li><?php echo add_query_arg( 'auth_network', 'facebook-buyer-register-buttons', site_url( '/' ) ); ?></li>
        </ul>
    </div>

    <?php es_settings_field_render( 'is_login_google_enabled', array(
        'label' => __( 'Enable log in with Google', 'es' ),
        'type' => 'switcher',
        'attributes' => array(
            'data-toggle-container' => '#es-google-container',
        ),
    ) ); ?>

    <div id="es-google-container">
        <?php es_settings_field_render( 'google_client_key', array(
            'label' => __( 'Client key', 'es' ),
            'type' => 'text',
            'caption' => __( sprintf( 'You can %s.', "<a target='_blank' href='https://developers.google.com/identity/sign-in/web/sign-in'>" . __( 'generate Client key here' ) . "</a>" ), 'es' )
        ) );

        es_settings_field_render( 'google_client_secret', array(
            'label' => __( 'Client secret', 'es' ),
            'type' => 'text',
            'caption' => __( sprintf( 'You can %s.', "<a target='_blank' href='https://developers.google.com/identity/sign-in/web/sign-in'>" . __( 'generate Client secret here' ) . "</a>" ), 'es' )
        ) ); ?>

        <b><?php _e( 'Redirect urls' ); ?></b>
        <ul>
            <li><?php echo add_query_arg( 'auth_network', 'google-login-buttons', site_url( '/' ) ); ?></li>
            <li><?php echo add_query_arg( 'auth_network', 'google-buyer-register-buttons', site_url( '/' ) ); ?></li>
        </ul>
    </div>

    <?php es_settings_field_render( 'login_title', array(
        'label' => __( 'Title for sign in page', 'es' ),
        'type' => 'text',
    ) );

    es_settings_field_render( 'login_subtitle', array(
        'label' => __( 'Subtitle for sign in page', 'es' ),
        'type' => 'text',
    ) );

    es_settings_field_render( 'is_buyers_register_enabled', array(
        'label' => __( 'Enable sign up form for buyers', 'es' ),
        'type' => 'switcher',
        'attributes' => array(
            'data-toggle-container' => '#es-buyers-register-container',
        ),
    ) ); ?>

    <div id="es-buyers-register-container">
        <?php es_settings_recommended_page_render( 'buyer_register_page_id', array(
            'page_name' => __( 'Buyer registration', 'es' ),
            'page_display_name' => __( 'Buyer registration page', 'es' ),
            'page_content' => '[es_authentication auth_item="buyer-register-buttons"]',
        ) );

        es_settings_field_render( 'buyer_register_title', array(
            'label' => __( 'Title for buyer sign up page', 'es' ),
            'type' => 'text',
        ) );

        es_settings_field_render( 'buyer_register_subtitle', array(
            'label' => __( 'Subtitle for buyer sign up page', 'es' ),
            'type' => 'text',
        ) ); ?>
    </div>

    <?php es_settings_field_render( 'is_agents_register_enabled', array(
        'label' => __( 'Enable sign up form for agents', 'es' ),
        'type' => 'switcher',
        'pro' => true,
        'attributes' => array(
            'data-toggle-container' => '#es-agents-register-container',
        ),
    ) ); ?>

    <div id="es-agents-register-container">
        <?php es_settings_recommended_page_render( 'agent_register_page_id', array(
            'page_name' => __( 'Agent registration', 'es' ),
            'page_display_name' => __( 'Agent registration page', 'es' ),
            'page_content' => '[es_authentication auth_item="agent-register-buttons"]'
        ) );

        es_settings_field_render( 'agent_register_title', array(
            'label' => __( 'Title for agent sign up page', 'es' ),
            'type' => 'text',
        ) );

        es_settings_field_render( 'agent_register_subtitle', array(
            'label' => __( 'Subtitle for agent sign up page', 'es' ),
            'type' => 'text',
        ) ); ?>
    </div>
</div>
