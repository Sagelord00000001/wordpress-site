<div class="es-wrap es-dashboard">
    <div class="wrap">
        <div class="js-es-notifications"><?php echo es_notification_admin(); ?></div>
        <div class="es-head">
            <h1><?php _e( 'Migration from Estatik 3', 'es' ); ?></h1>
            <div class="es-head__logo">
				<?php do_action( 'es_logo' ); ?>
            </div>
        </div>

        <div class="es-content">
            <p class="es-msg-1">
		        <?php _e( 'To update the previous version to major version 4 you need to migrate your listings 
                    as the new version of the plugin requires. Before doing this, please make sure that your version 
                    of the plugin doesn\'t have custom changes of the code.', 'es' ); ?>
            </p>

            <p class="es-msg-2 es-hidden">
		        <?php _e( 'Migration process may take some time, especially if you have lots of properties added. 
                    Please be patient.', 'es' ); ?>
            </p>

            <div id="es-migration-progress"></div>

            <form action="" id="es-migrate-form">
                <input type="hidden" name="action" value="es_migration">
	            <?php echo wp_nonce_field( 'es_migration', 'es_migration' ); ?>

                <div style="text-align: center; margin: 30px 0;">
                    <button type="submit" class="es-btn es-btn--primary es-btn--large"><?php _e( 'Start migration', 'es' ); ?></button>
                </div>
            </form>

            <div id="es-logger-container"></div>
        </div>
    </div>
</div>
