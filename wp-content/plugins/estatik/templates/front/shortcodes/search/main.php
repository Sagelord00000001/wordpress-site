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
<style>
    #es-search--<?php echo $uniqid; ?> {
        padding: <?php echo $attributes['padding']; ?>;
        <?php if ( ! empty( $attributes['background'] ) ) : ?>
            background: <?php echo $attributes['background']; ?>;
        <?php endif; ?>
    }
</style>

<div class="<?php echo $container_classes; ?>" id="es-search--<?php echo $uniqid; ?>" data-same-price="<?php echo ests( 'is_same_price_for_categories_enabled' ); ?>">
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
                    <input type="text" name="address" class="js-es-address" placeholder="<?php echo $attributes['address_placeholder']; ?>">
                </label>
                <button type="submit" class="es-btn es-btn--primary">
                    <span class="es-icon es-icon_search"></span>
                    <span class="es-btn__label"><?php _e( 'Search', 'es' ); ?></span>
                </button>
            </div>
        <?php endif; ?>

        <?php if ( $collapsed_fields_active || $main_fields_active ) : ?>
            <ul class="es-search-nav js-es-search-nav <?php echo empty ( ests( 'is_open_collapsed_filter_mobile_enabled') ) ? '' : 'es-search-nav-mobile--show'; ?> <?php echo empty ( $attributes['is_address_search_enabled'] ) ? 'es-search-nav--dropdowns' : ''; ?>">
                <?php if ( $main_fields_active ) : ?>
                    <?php foreach ( $attributes['main_fields'] as $field ) :
                        $field_config = es_search_get_field_config( $field );
                        if ( !empty ( $field_config['frontend_visible_name'] ) ) {
                            $label = es_mulultilingual_translate_string( $field_config['frontend_visible_name'] );
                        } else {
                            $label = $field_config['label'];
                        }
                        ob_start(); do_action( 'es_search_render_field', $field, $attributes );
                        $fields = ob_get_clean();
		                $is_range_mode = ests( "is_search_{$field}_range_enabled" ) || $field_config['type'] == 'range' || ! empty( $field_config['search_settings']['range'] ) || $field == 'price';
                        if ( $fields ) : ?>
                            <li class="js-es-search-nav__single-item js-es-search-nav__item" data-placeholder="<?php echo $field_config['label']; ?>"
                                data-field="<?php echo $field; ?>"
                                data-range-enabled="<?php echo $is_range_mode; ?>"
                                data-formatter="<?php echo $field_config['formatter']; ?>">
                                <a href="#">
                                    <span class="js-es-search-nav__label"><?php echo $label; ?></span>
                                    <span class="es-icon es-icon_chevron-bottom js-es-search-nav__open"></span>
                                    <span class="es-icon es-icon_close js-es-search-nav__reset es-search-nav__reset es-hidden"></span>
                                </a>
                                <div id="nav-<?php echo $field; ?>-<?php echo $uniqid; ?>" class="es-search-nav__content">
                                    <?php echo $fields; ?>
                                </div>
                            </li>
                        <?php endif;
                    endforeach; ?>
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
                <?php if ( empty( $attributes['is_address_search_enabled'] ) ) : ?>
                    <li class="es-search--submit-item">
                        <button type="submit" class="es-btn es-btn--primary"> <span class="es-icon es-icon_search"></span><?php _e( 'Search', 'es' ); ?>
                    </li>
                <?php endif; ?>
            </ul>
            <a href="#" data-toggle-label="<?php _e( 'Less filters', 'es' ); ?><?php echo esc_attr( '<span class="es-icon es-icon_chevron-top"></span>' ); ?>" class="js-es-search__collapse-link es-search__collapse-link es-secondary-color <?php echo empty ( ests( 'is_open_collapsed_filter_mobile_enabled') ) ? '' : 'es-search-nav-mobile--show'; ?>">
		        <?php _e( 'Filters', 'es' ); ?><span class="es-icon es-icon_chevron-bottom"></span>
            </a>
        <?php endif; ?>
    </form>
</div><?php endif;
