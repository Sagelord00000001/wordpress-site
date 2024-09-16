<div class="es-wrap">
    <div class="js-es-notifications"><?php echo es_notification_admin(); ?></div>
    <div class="es-head">
        <h1><?php _e( 'My listings', 'es' ); ?></h1>
        <?php if ( Es_Property::count() ) : ?>
            <a href="<?php echo admin_url( 'post-new.php?post_type=properties' ); ?>" class="es-btn es-btn--secondary es-btn--icon">
                <span class="es-icon es-icon_plus"></span>
                <?php _e( 'Add new property', 'es' ); ?>
            </a>
        <?php endif; ?>
        <div class="es-head__logo">
            <?php do_action( 'es_logo' ); ?>
        </div>
    </div>
</div>
