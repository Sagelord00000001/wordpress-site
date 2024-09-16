<?php

/**
 * @var $query WP_Query
 * @var $layout string
 * @var $wrapper_class string
 * @var $args array Shortcode attributes
 * @var $hash string Encoded shortcode attributes.
 * @var $search_form Es_Search_Form_Shortcode
 */

if ( empty( $args['ignore_search'] ) && ! empty( $args['search_form_selector'] ) ) : ?>
    <div data-search-form-selector='<?php echo $args['search_form_selector']; ?>'>
<?php endif; ?>

<div class="<?php echo $wrapper_class; ?>">
    <?php if ( ! empty( $search_form ) ) : ?>
        <div class="es-properties__search">
	        <?php echo $search_form->get_content(); ?>
        </div>
    <?php endif; ?>

    <div class="es-properties__list">
	    <?php include es_locate_template( 'front/property/listings.php' ); ?>
    </div>
    <div class="js-es-properties__map es-properties__map <?php echo $args['layout'] == 'half_map' ? 'es-properties__map--visible' : ''; ?>">
	    <?php include es_locate_template( 'front/property/map.php' ); ?>
    </div>
</div>

<?php if ( empty( $args['ignore_search'] ) && ! empty( $args['search_form_selector'] ) ) : ?>
    </div>
<?php endif;
