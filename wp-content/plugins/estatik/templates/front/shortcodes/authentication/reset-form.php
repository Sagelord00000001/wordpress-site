<?php /** @var $args array */ ?>
<div class="es-auth__item es-auth__reset-form <?php echo $args['auth_item'] != 'reset-form' ? 'es-auth__item--hidden' : ''; ?>">
    <h3 class="heading-font"><?php _e( 'Reset password' ); ?></h3>
    <p><?php _e( 'Enter your email address and we will send you a link to change your password.', 'es' ); ?></p>

    <form action="" method="POST">
		<?php if ( ! empty( $args['is_popup'] ) ) :
			es_framework_field_render( 'is_popup', array(
				'type' => 'hidden',
				'value' => 1,
				'attributes' => array(
					'id' => sprintf( '%s-%s', 'is_popup', uniqid() ),
				),
			) );
		endif; $uniqud = uniqid(); ?>

        <input type="hidden" name="uniqid" value="<?php echo $uniqud; ?>"/>

		<?php if ( ! empty( $_GET['key'] ) && ! empty( $_GET['login'] ) ) :
			$key = sanitize_text_field( filter_input( INPUT_GET, 'key' ) );
			$login = sanitize_text_field( filter_input( INPUT_GET, 'login' ) );
			$user = get_user_by( 'login', $login ); ?>
            <input type="hidden" name="login" value="<?php echo esc_attr( $login ); ?>"/>
            <input type="hidden" name="key" value="<?php echo esc_attr( $key ); ?>"/>

			<?php es_framework_field_render( 'es_new_password', array(
			'label'       => __( 'New password', 'es' ),
			'type'        => 'password',
			'attributes' => array(
				'class' => 'js-es-password-field',
				'data-email' => $user->user_email,
				'required' => 'required',
				'id' => sprintf( '%s-%s', 'es_new_password', uniqid() ),
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
			<?php wp_nonce_field( 'es_reset_pwd', 'es_reset_pwd_nonce_' . $uniqud ); ?>
            <button type="submit" class="es-btn es-btn--primary es-btn--reset"><?php _e( 'Save new password', 'es' ); ?></button>
		<?php else : ?>
			<?php es_framework_field_render( 'es_user_email', array(
				'label' => _x( 'Email', 'authenticate form', 'es' ),
				'type' => 'email',
				'attributes' => array(
					'required' => 'required',
					'id' => sprintf( '%s-%s', 'es_user_email', uniqid() ),
				),
			) ); ?>
			<?php wp_nonce_field( 'es_retrieve_pwd', 'es_retrieve_pwd_nonce_' . $uniqud ); ?>
			<?php do_action( 'es_recaptcha', 'reset_pwd_form' ); ?>
            <button type="submit" class="es-btn es-btn--primary es-btn--reset"><?php _e( 'Send reset link', 'es' ); ?></button>
		<?php endif; ?>

        <div><a href="#" data-auth-item="login-buttons" class="js-es-auth-item__switcher login-back"><span class="es-icon es-icon_chevron-left"></span><?php _e( 'Back to login', 'es' ); ?></a></div>
    </form>
    <div class="es-space"></div>
</div>
