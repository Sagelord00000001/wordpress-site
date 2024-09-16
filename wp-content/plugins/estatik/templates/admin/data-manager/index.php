<div class="es-wrap" id="es-data-manager">
	<?php do_action( 'es_admin_page_bar' ); ?>
	<div class="js-es-notifications"><?php echo es_notification_admin(); ?></div>

    <div class="es-page">
        <div class="es-sidebar">
            <div class="js-es-fixed-nav">
                <?php
                /** @var $nav_items array */
                foreach ( $nav_items as $nav_id => $nav ) : ?>
                    <ul class="es-nav js-es-data-manager-nav" id="es-<?php echo $nav_id; ?>-nav">
                        <?php foreach ( $nav as $item ) : ?>
                            <li><a href="<?php echo $item['hash']; ?>"><?php echo $item['label']; ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="es-content">
	        <?php foreach ( $nav_items as $nav_id => $nav ) : ?>
                <div id="<?php echo $nav_id; ?>" class="es-content__inner js-es-data-manager__inner es-hidden">
                    <?php do_action( 'es_data_manager_content', $nav_id ); ?>
                </div>
	        <?php endforeach; ?>
        </div>
    </div>
</div>


