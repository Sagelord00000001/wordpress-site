<?php /** @var $args array */ ?>
<div class="es-auth__item js-es-auth__login-form es-auth__login-form <?php echo $args['auth_item'] != 'login-form' ? 'es-auth__item--hidden' : ''; ?>">
	<?php if ( ! empty( $args['login_title'] ) ) : ?>
        <h3 class="heading-font"><?php echo $args['login_title']; ?></h3>
	<?php endif; ?>

	<?php if ( ! empty( $args['login_subtitle'] ) ) : ?>
        <p><?php echo $args['login_subtitle']; ?></p>
	<?php endif; ?>

    <div class="all-login-back">
        <a href="#" data-auth-item="login-buttons" class="js-es-auth-item__switcher">
            <span class="es-icon es-icon_chevron-left"></span><?php _e( 'All log in options', 'es' ); ?>
        </a>
    </div>

    <form action="" method="POST">
		<?php if ( ! empty( $_GET['redirect_url'] ) ) {
			es_framework_field_render( 'redirect_url', array(
				'type' => 'hidden',
				'value' => $_GET['redirect_url'],
                'attributes' => array(
                    'id' => sprintf( '%s-%s', 'redirect_url', uniqid() ),
                )
			) );
		}

		es_framework_field_render( 'es_user_login', array(
			'label' => _x( 'Email', 'authenticate form', 'es' ),
			'attributes' => array(
				'required' => 'required',
				'id' => sprintf( '%s-%s', 'es_user_login', uniqid() ),
			),
		) );

		es_framework_field_render( 'es_user_password', array(
			'label' => _x( 'Password', 'authenticate form', 'es' ),
			'type' => 'password',
			'skeleton' => "{before}
                                   <div class='es-field es-field__{field_key} es-field--{type} {wrapper_class}'>
                                       <label for='{id}'>{label}{caption}<div class='es-input__wrap'>{input}</div>{description}</label>
                                   </div>
                               {after}",
			'attributes' => array(
				'required' => 'required',
				'id' => sprintf( '%s-%s', 'es_user_password', uniqid() ),
			),
		) ); ?>
        <div class="forgot-pwd">
            <a href="#" data-auth-item="reset-form" class="js-es-auth-item__switcher"><?php _e( 'Forgot password?', 'es' ); ?></a>
        </div>

		<?php if ( ! empty( $args['is_popup'] ) ) :
			es_framework_field_render( 'is_popup', array(
				'type' => 'hidden',
				'value' => 1,
				'attributes' => array(
					'id' => sprintf( '%s-%s', 'is_popup', uniqid() ),
				),
			) );
		endif;

        $uniqud = uniqid(); ?>
        <input type="hidden" name="uniqid" value="<?php echo $uniqud; ?>"/>
		<?php wp_nonce_field( 'es_authenticate', 'es_auth_nonce_' . $uniqud ); ?>
	    <?php do_action( 'es_recaptcha', 'sign_in_form' ); ?>
        <button type="submit" class="es-btn es-btn--primary js-es-btn--login es-btn--login" disabled><?php _e( 'Log in', 'es' ); ?></button>
    </form>

	<?php if ( ! empty( $args['enable_buyers_register'] ) ) : ?>
        <p class="sign-in-text"><?php _e( 'Don\'t have an account? <a href="#" class="js-es-auth-item__switcher" data-auth-item="buyer-register-buttons">Sign up</a>', 'es' ); ?></p>
	<?php endif; ?>
    <div class="es-space"></div>
</div>
