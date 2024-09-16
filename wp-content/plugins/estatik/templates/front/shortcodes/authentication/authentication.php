<?php

/**
 * @var $args array
 */

$flashes = es_get_flash_instance( 'authenticate' ); ?>
<div class="es-auth js-es-auth content-font">
    <?php if ( ! is_user_logged_in() ) : ?>
        <?php $flashes->render_messages(); ?>
        <?php es_load_template( 'front/shortcodes/authentication/login-buttons.php', $args ); ?>
        <?php es_load_template( 'front/shortcodes/authentication/login-form.php', $args ); ?>
        <?php es_load_template( 'front/shortcodes/authentication/reset-form.php', $args ); ?>
	    <?php if ( ! empty( $args['enable_buyers_register'] ) ) : ?>
            <?php es_load_template( 'front/shortcodes/authentication/buyer-register-buttons.php', $args ); ?>
            <?php es_load_template( 'front/shortcodes/authentication/buyer-register-form.php', $args ); ?>
        <?php endif; ?>
    <?php else : ?>
        <p><?php _e( 'You’re already logged in.', 'es' ); ?></p>
        <a href="<?php echo wp_logout_url( es_get_current_url() ); ?>" class="es-btn es-btn--primary"><?php _e( 'Log out', 'es' ); ?></a>
    <?php endif; ?>

    <?php do_action( 'es_after_authentication' ); ?>
</div>
