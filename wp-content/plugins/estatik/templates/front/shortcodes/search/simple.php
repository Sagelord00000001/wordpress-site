<?php
/**
 * @var $container_classes string
 * @var $args array
 * @var $title string
 * @var $attributes array
 * @var $search_page_uri string
 * @var $search_page_exists bool
 * @var $search_page_id int
 */

$uniqid = uniqid();
$collapsed_fields_active = ! empty( $attributes['collapsed_fields'] ) && ! empty( $attributes['is_collapsed_filter_enabled'] );
$main_fields_active = ! empty( $attributes['main_fields'] ) && ! empty( $attributes['is_main_filter_enabled'] );

if ( $collapsed_fields_active || $main_fields_active || ! empty( $attributes['is_address_search_enabled'] ) ) : ?>

    <div class="<?php echo $container_classes; ?>" id="es-search--<?php echo $uniqid; ?>">
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

			<?php if ( $tab = es_get( 'tab' ) ) : ?>
                <input type="hidden" name="tab" value="<?php echo esc_attr( $tab ); ?>"/>
			<?php endif; ?>

			<?php if ( ! empty( $attributes['is_address_search_enabled'] ) ) : ?>
                <div class="es-search__address">
                    <label class="es-field es-field__address">
                        <input type="text" value="<?php echo esc_attr( filter_input( INPUT_GET, 'address' ) ); ?>" name="address" class="js-es-address" placeholder="<?php echo $attributes['address_placeholder']; ?>">
                    </label>
                    <button type="submit" class="es-btn es-btn--primary es-btn--icon">
                        <span class="es-icon es-icon_search"></span>
                    </button>
                </div>
			<?php endif; ?>

			<?php if ( $collapsed_fields_active || $main_fields_active ) : ?>
                <a href="" class="js-es-search__collapse-link es-search__collapse-link es-leave-border <?php echo empty ( ests( 'is_open_collapsed_filter_mobile_enabled') ) ? '' : 'es-search-nav-mobile--show'; ?> " >
					<?php _e( 'Filters', 'es' ); ?><span class="es-icon es-icon_chevron-bottom"></span>
                </a>

				<?php if ( ! empty( $attributes['enable_saved_search'] ) ) : ?>
					<?php if ( get_current_user_id() ) : ?>
                        <button data-label="<?php _e( 'Save search', 'es' ); ?>" data-nonce="<?php echo wp_create_nonce( 'es_save_search' ); ?>" type="button" disabled class="es-btn es-btn--saved-search es-btn--secondary js-es-save-search es-btn--bordered has-text-color"><?php _e( 'Save search', 'es' ); ?></button>
					<?php else : ?>
                        <a href="#" data-popup-id="#es-authentication-popup" type="button" class="es-btn es-btn--secondary es-btn--saved-search es-btn--bordered js-es-popup-link"><?php _e( 'Save search', 'es' ); ?></a>
					<?php endif; ?>
				<?php endif; ?>
				<?php if ( empty( $attributes['is_address_search_enabled'] ) ) : ?>
                    <button type="submit" class="es-btn es-btn--primary es-btn--icon es-btn--search">
                        <span class="es-icon es-icon_search"></span>
                    </button>
				<?php endif; ?>
                <div class="es-search-nav-wrap">
                    <ul class="es-search-nav es-search-nav--dropdowns js-es-search-nav <?php echo empty ( ests( 'is_open_collapsed_filter_mobile_enabled') ) ? '' : 'es-search-nav-mobile--show'; ?> <?php echo empty( $attributes['is_address_search_enabled'] ) ? 'es-search-nav--dropdowns' : ''; ?>">
						<?php if ( $main_fields_active ) : ?>
							<?php foreach ( $attributes['main_fields'] as $field ) :
								$field_config = es_search_get_field_config( $field );
                                if ( !empty ( $field_config['frontend_visible_name'] ) ) {
                                    $label = es_mulultilingual_translate_string( $field_config['frontend_visible_name'] );
                                } else {
                                    $label = $field_config['label'];
                                }
								if ( $field_config && ! empty( $field_config['search_support'] ) ) :
									$is_range_mode = ests( "is_search_{$field}_range_enabled" ) || $field_config['type'] == 'range' || ! empty( $field_config['search_settings']['range'] ) || $field == 'price';
									ob_start(); do_action( 'es_search_render_field', $field, $attributes ); $content = ob_get_clean();
									if ( $content ) : ?>
                                        <li class="js-es-search-nav__single-item js-es-search-nav__item" data-placeholder="<?php echo $label; ?>"
                                            data-field="<?php echo $field; ?>"
                                            data-range-enabled="<?php echo $is_range_mode; ?>"
                                            data-formatter="<?php echo $field_config['formatter']; ?>">
                                            <a href="#" data-nav-id="<?php echo $field; ?>-<?php echo $uniqid; ?>">
                                                <span class="js-es-search-nav__label"><?php echo $label; ?></span>
                                                <span class="es-icon es-icon_chevron-bottom js-es-search-nav__open"></span>
                                                <span class="es-icon es-icon_close js-es-search-nav__reset es-search-nav__reset es-hidden"></span>
                                            </a>
                                            <div id="nav-<?php echo $field; ?>-<?php echo $uniqid; ?>" class="es-search-nav__content">
												<?php echo $content; ?>
                                            </div>
                                        </li>
									<?php endif; ?>
								<?php endif; ?>
							<?php endforeach; ?>
						<?php endif; ?>
						<?php if ( $collapsed_fields_active ) : ?>
                            <li class="js-es-search-nav__item js-es-search-nav__item--more">
                                <a href="#">
									<?php _e( 'More filters', 'es' ); ?>
                                    <span class="es-icon es-icon_chevron-bottom js-es-search-nav__open"></span>
                                    <span class="es-icon es-icon_close js-es-search-nav__reset es-search-nav__reset es-hidden"></span>
                                </a>
                                <div id="nav-more-<?php echo $uniqid; ?>" class="es-search-nav__content">
									<?php foreach ( $attributes['collapsed_fields'] as $field ) : ?>
										<?php do_action( 'es_search_render_field', $field, $attributes ); ?>
									<?php endforeach; ?>
                                </div>
                            </li>
						<?php endif; ?>
                        <a href="#" class="js-es-remove-filters es-search-nav__reset es-secondary-color es-search-nav__reset-mobile">
                            <span class="es-icon es-icon_close"></span><?php _e( 'Clear all filters', 'es' ); ?>
                        </a>
                    </ul>
                    <a href="#" class="js-es-search__collapse-link es-search__collapse-link es-secondary-color">
						<?php _e( 'Hide filters', 'es' ); ?><span class="es-icon es-icon_chevron-top"></span>
                    </a>
                </div>
			<?php endif; ?>
        </form>
    </div>
<?php endif;
