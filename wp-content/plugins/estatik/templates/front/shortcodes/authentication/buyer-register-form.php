<?php /** @var $args array */ ?>
<div class="es-auth__item es-auth__buyer-register-form <?php echo $args['auth_item'] != 'buyer-register-form' ? 'es-auth__item--hidden' : ''; ?>">
	<?php if ( ! empty( $args['buyer_register_title'] ) ) : ?>
        <h3 class="heading-font"><?php echo $args['buyer_register_title']; ?></h3>
	<?php endif; ?>

	<?php if ( ! empty( $args['buyer_register_subtitle'] ) ) : ?>
        <p><?php echo $args['buyer_register_subtitle']; ?></p>
	<?php endif; ?>

    <div class="all-login-back">
        <a href="#" class="js-es-auth-item__switcher" data-auth-item="buyer-register-buttons">
            <span class="es-icon es-icon_chevron-left"></span><?php _e( 'All sign up options', 'es' ); ?>
        </a>
    </div>

    <form action="" method="POST">
		<?php $uniqud = uniqid(); ?>
        <input type="hidden" name="uniqid" value="<?php echo $uniqud; ?>"/>
        <?php wp_nonce_field( 'es_register', 'es_register_nonce_' . $uniqud );

		es_framework_field_render( 'redirect_url', array(
			'type' => 'hidden',
			'value' => filter_input( INPUT_GET, 'redirect_url' ),
			'attributes' => array(
				'id' => sprintf( '%s-%s', 'redirect_url', uniqid() ),
			),
		) );

		es_framework_field_render( 'es_type', array(
			'type' => 'hidden',
			'value' => 'buyer',
			'attributes' => array(
				'id' => sprintf( '%s-%s', 'es_type', uniqid() ),
			),
		) );

		if ( ! empty( $args['is_popup'] ) ) :
			es_framework_field_render( 'is_popup', array(
				'type' => 'hidden',
				'value' => 1,
				'attributes' => array(
					'id' => sprintf( '%s-%s', 'is_popup', uniqid() ),
				),
			) );
		endif;

		es_framework_field_render( 'es_user_email', array(
			'type' => 'email',
			'label' => _x( 'Email', 'authenticate form', 'es' ),
			'attributes' => array(
				'required' => 'required',
				'id' => sprintf( '%s-%s', 'es_user_email', uniqid() ),
			),
			'description' => __( "You'll use it to sign in, and we'll use it to contact you.", 'es' ),
		) );

        es_framework_field_render( 'es_user_password', array(
			'label' => _x( 'Password', 'authenticate form', 'es' ),
			'type' => 'password',
			'attributes' => array(
				'required' => 'required',
				'minlength' => '8',
				'class' => 'js-es-password-field',
				'id' => sprintf( '%s-%s', 'es_user_password', uniqid() ),
			),
			'skeleton' => "{before}
                                   <div class='es-field es-field__{field_key} es-field--{type} {wrapper_class}'>
                                       <label for='{id}'>{label}{caption}<div class='es-input__wrap'>{input}</div>{description}</label>
                                   </div>
                               {after}",
			'description' => "<ul class='es-field__validate-list'>
                                <li class='es-validate-item es-validate-item__contain'>" . __( 'Can\'t contain the name or email address', 'es' ) . "</li>
                                <li class='es-validate-item es-validate-item__length'>" . __( 'At least 8 characters', 'es' ) . "</li>
                                <li class='es-validate-item es-validate-item__char'>" . __( 'Contains a number or symbol', 'es' ) . "</li>
                            </ul>",
		) ); ?>

	    <?php do_action( 'es_recaptcha', 'sign_up_form' ); ?>

        <button type="submit" disabled class="es-btn es-btn--primary es-btn--signup"><?php _e( 'Sign up', 'es' ); ?></button>
		<?php do_action( 'es_privacy_policy', 'sign_up_form' ); ?>
        <p class="sign-in-text"><?php _e( 'Already have an account? <a href="#" class="js-es-auth-item__switcher" data-auth-item="login-buttons">Log in</a>', 'es' ); ?></p>
    </form>
    <div class="es-space"></div>
</div>
