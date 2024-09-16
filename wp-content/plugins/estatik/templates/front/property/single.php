<?php do_action( 'es_before_single_wrapper', get_the_ID() ); ?>
<div class='js-es-single es-single es-single--<?php echo ests( 'single_layout' ); ?>' data-layout="<?php echo ests( 'single_layout' ); ?>">
	<?php do_action( 'es_property_breadcrumbs', get_the_ID() ); ?>
    <div class="js-es-single-property-layout">
	    <?php do_action( 'es_single_property_layout', get_the_ID() ); ?>
    </div>

	<?php $instance = es_get_sections_builder_instance();
	if ( $sections = $instance::get_items( 'property' ) ) :
		foreach ( $sections as $section ) :
			do_action( 'es_single_property_section', $section, get_the_ID() );
		endforeach;
	endif; ?>

	<?php do_action( 'es_after_single_property_content' ); ?>
	<?php do_action( 'es_after_single_content' ); ?>
</div>
<?php do_action( 'es_after_single_wrapper', get_the_ID() );
