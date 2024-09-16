<?php /** @var $args array */ ?>
<div class="es-auth__item es-auth__login-buttons <?php echo $args['auth_item'] != 'login-buttons' ? 'es-auth__item--hidden' : ''; ?>">
	<?php if ( ! empty( $args['login_title'] ) ) : ?>
        <h3 class="heading-font"><?php echo $args['login_title']; ?></h3>
	<?php endif; ?>

	<?php if ( ! empty( $args['login_subtitle'] ) ) : ?>
        <p><?php echo $args['login_subtitle']; ?></p>
	<?php endif; ?>

	<?php

	/**
	 * @var $args array
	 */

	foreach ( es_get_auth_networks_list() as $network ) : ?>
		<?php if ( ! empty( $args[ 'enable_' . $network ] ) ) :
			$auth = es_get_auth_instance( $network, array(
				'context' => 'login-buttons'
			) );

			if ( $auth instanceof Es_Authentication && $auth->is_valid() ) : ?>
                <a class="es-btn es-btn--<?php echo $network; ?> es-btn--auth " href="<?php echo $auth->create_auth_url(); ?>">
                    <span class="es-icon es-icon_<?php echo $network; ?>"></span>
					<?php printf( __( 'Log in with %s', 'es' ), __( ucfirst( $network ), 'es' ) ); ?>
                </a>
			<?php endif; ?>
		<?php endif; ?>
	<?php endforeach;

	if ( ! empty( $args['enable_login_form'] ) ) : ?>
        <a href="#" data-auth-item="login-form" class="js-es-auth-item__switcher es-btn es-btn--default es-btn--auth ">
			<?php _e( 'Log in with email', 'es' ); ?>
        </a>
	<?php endif; ?>

	<?php if ( ! empty( $args['enable_buyers_register'] ) ) : ?>
        <p class="sign-in-text"><?php _e( 'Don\'t have an account? <a href="#" data-auth-item="buyer-register-buttons" class="js-es-auth-item__switcher">Sign up</a>', 'es' ); ?></p>
	<?php endif; ?>

    <div class="es-space"></div>
</div>
