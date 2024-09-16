<?php
/**
 * @var $container_classes string
 * @var $args array
 * @var $attributes array
 * @var $search_page_uri string
 * @var $search_page_exists bool
 * @var $search_page_id int
 */
?>
<div class="<?php echo $container_classes; ?>">
    <form action="<?php echo $search_page_uri; ?>" role="search" method="get">
        <input type="hidden" name="es" value="1"/>

	    <?php if ( ! $search_page_exists ) : ?>
            <input type="hidden" name="s"/>
            <input type="hidden" name="post_type" value="properties"/>
	    <?php else: ?>
		    <?php if ( ! get_option( 'permalink_structure' ) ) : ?>
                <input type="hidden" name="page_id" value="<?php echo $search_page_id; ?>"/>
		    <?php endif; ?>
	    <?php endif; ?>

        <?php if ( ! empty( $attributes['title'] ) ) : ?>
            <h3><?php echo $attributes['title']; ?></h3>
        <?php endif; ?>

	    <?php if ( ! empty( $attributes['is_address_search_enabled'] ) ) : ?>
            <div class="es-search__address">
                <label class="es-field es-field__address">
                    <input type="text" value="<?php echo esc_attr( filter_input( INPUT_GET, 'address' ) ); ?>" name="address" class="js-es-address" placeholder="<?php echo esc_attr( $attributes['address_placeholder'] ); ?>">
                </label>
                <button type="submit" class="es-btn es-btn--primary es-btn--icon">
                    <span class="es-icon es-icon_search"></span>
                </button>
            </div>
	    <?php endif; ?>

        <?php if ( ! empty( $attributes['fields'] ) ) : ?>
            <?php foreach ( $attributes['fields'] as $field ) :
                if ( 'address' == $field && ! empty( $attributes['is_address_search_enabled'] ) ) continue; ?>
                <?php do_action( 'es_search_render_field', $field, $attributes ); ?>
            <?php endforeach; ?>
        <?php endif; ?>

        <a href="#" data-toggle-label="<?php _e( 'Less filters', 'es' ); ?><?php echo esc_attr( '<span class="es-icon es-icon_chevron-top"></span>' ); ?>" class="es-search-more js-es-search-more es-hidden es-secondary-color es-hidden">
            <?php _e( 'More filters', 'es' ); ?><span class="es-icon es-icon_chevron-bottom"></span>
        </a>

        <div class="es-search__buttons">
	        <?php if ( ! empty( $attributes['enable_saved_search'] ) ) : ?>
                <?php if ( get_current_user_id() ) : ?>
                    <button data-label="<?php _e( 'Save search', 'es' ); ?>" disabled data-nonce="<?php echo wp_create_nonce( 'es_save_search' ); ?>" type="button" class="es-btn es-btn--secondary js-es-save-search es-btn--bordered has-text-color"><?php _e( 'Save search', 'es' ); ?></button>
                <?php else : ?>
                    <a href="#" data-popup-id="#es-authentication-popup" type="button" class="es-btn es-btn--secondary es-btn--bordered js-es-popup-link has-text-color"><?php _e( 'Save search', 'es' ); ?></a>
                <?php endif; ?>
            <?php endif; ?>
            <button type="submit" class="es-btn es-btn--primary"> <span class="es-icon es-icon_search"></span><?php _e( 'Search', 'es' ); ?></button>
            <button type="reset" class="es-btn es-btn--default"> <span class="es-icon es-icon_close"></span><?php _e( 'Reset', 'es' ); ?></button>
        </div>
    </form>
</div>
