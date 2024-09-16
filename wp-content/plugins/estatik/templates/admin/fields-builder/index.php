<div class="es-wrap" id="es-fields-builder">
    <?php do_action( 'es_admin_page_bar' ); ?>
    <div class="js-es-notifications"><?php echo es_notification_admin(); ?></div>
	<?php

	/** @var $tabs_config array */
	es_framework_view_render( 'tabs', $tabs_config ); ?>
</div>
