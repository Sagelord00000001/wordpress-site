<?php /** @var $args array */ ?>
<div class="es-auth__item es-auth__buyer-register-buttons <?php echo $args['auth_item'] != 'buyer-register-buttons' ? 'es-auth__item--hidden' : ''; ?>">
	<?php if ( ! empty( $args['buyer_register_title'] ) ) : ?>
        <h3 class="heading-font"><?php echo $args['buyer_register_title']; ?></h3>
	<?php endif; ?>

	<?php if ( ! empty( $args['buyer_register_subtitle'] ) ) : ?>
        <p><?php echo $args['buyer_register_subtitle']; ?></p>
	<?php endif; ?>

	<?php

	/**
	 * @var $args array
	 */

	foreach ( es_get_auth_networks_list() as $network ) : ?>
		<?php if ( ! empty( $args[ 'enable_' . $network ] ) ) :
			$auth = es_get_auth_instance( $network, array(
				'context' => 'buyer-register-buttons'
			) );

			if ( $auth instanceof Es_Authentication && $auth->is_valid() ) : ?>
                <a class="es-btn es-btn--<?php echo $network; ?> es-btn--auth " href="<?php echo $auth->create_auth_url(); ?>">
                    <span class="es-icon es-icon_<?php echo $network; ?>"></span>
					<?php printf( __( 'Sign up with %s', 'es' ), __( ucfirst( $network ), 'es' ) ); ?>
                </a>
			<?php endif; ?>
		<?php endif; ?>
	<?php endforeach;

	if ( ! empty( $args['enable_login_form'] ) ) : ?>
        <a href="#" data-auth-item="buyer-register-form" class="js-es-auth-item__switcher es-btn es-btn--default es-btn--auth "><?php _e( 'Sign up with email', 'es' ); ?></a>
	<?php endif; ?>

    <p class="sign-in-text"><?php _e( 'Already have an account? <a href="#" data-auth-item="login-buttons" class="js-es-auth-item__switcher">Log in</a>', 'es' ); ?></p>
    <div class="es-space"></div>
</div>
