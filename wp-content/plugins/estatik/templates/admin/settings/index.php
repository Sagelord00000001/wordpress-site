<div class="es-wrap" id="es-settings">
    <form method="post" class="js-es-settings-form">
        <div class="js-es-notifications"><?php echo es_notification_admin(); ?></div>

        <?php

        /**
         * @return $tabs array
         */

        es_framework_view_render( 'tabs', array(
            'tabs' => $tabs,
            'nav_title' => __( 'Settings', 'es' ),
            'after_content_tabs' => '<button type="submit" class="es-btn es-btn--primary es-btn--large es-btn--save js-es-save-settings">' . __( "Save changes", "es" ) . '</button>',
        ) );

        wp_nonce_field( 'es_save_settings' ); ?>
        <input type="hidden" name="action" value="es_save_settings"/>
    </form>
</div>
