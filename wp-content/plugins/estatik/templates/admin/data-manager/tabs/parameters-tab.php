<h2 id="es-parameters"><?php echo _x( 'Parameters', 'data manager title', 'es' ); ?></h2>

<?php do_action( 'es_data_manager_parameters_before' ); ?>

<div class="es-row">
    <?php foreach ( array( 'es_category', 'es_status' ) as $taxonomy ) : ?>
        <div class="es-col-md-6">
            <?php $creator = es_get_terms_creator_factory( $taxonomy );
            $creator->render(); ?>
        </div>
    <?php endforeach; ?>

    <div class="es-col-md-7">
	    <?php $creator = es_get_terms_creator_factory( 'es_label' );
	    $creator->render(); ?>
    </div>

    <?php foreach ( array( 'es_type', 'es_rent_period' ) as $taxonomy ) : ?>
        <div class="es-col-md-6">
            <?php $creator = es_get_terms_creator_factory( $taxonomy );
            $creator->render(); ?>
        </div>
    <?php endforeach; ?>
</div>

<h3 id="es-features" style="margin: 40px 0 40px;"><?php _e( 'Amenities & Features', 'es' ); ?></h3>

<?php es_settings_field_render( 'is_terms_icons_enabled', array(
    'type' => 'switcher',
    'label' => __( 'Use icons or check marks for amenities and features', 'es' ),
    'attributes' => array(
	    'data-toggle-container' => '#es-data-manager-icon-types',
        'data-save-container' => 'estatik-settings',
        'data-save-field' => 'is_terms_icons_enabled',
    ),
) );

es_settings_field_render( 'term_icon_type', array(
    'before' => '<div id="es-data-manager-icon-types">',
    'type' => 'radio-bordered',
    'after' => '</div>',
    'attributes' => array(
	    'data-save-container' => 'estatik-settings',
	    'data-save-field' => 'term_icon_type',
    ),
) ); ?>

<div class="es-row">
	<?php foreach ( array( 'es_amenity', 'es_feature' ) as $taxonomy ) : ?>
        <div class="es-col-md-6">
            <?php $type = ests( 'is_terms_icons_enabled' ) ? ests( 'term_icon_type' ) : 'simple';
            $creator = es_get_terms_creator_factory( $taxonomy, $type );
            $creator->render(); ?>
        </div>
	<?php endforeach; ?>
</div>

<h3 style="margin: 40px 0 20px;"><?php _e( 'Building details', 'es' ); ?></h3>

<div class="es-row">
    <?php foreach ( array( 'es_floor_covering', 'es_basement' ) as $taxonomy ) : ?>
        <div class="es-col-md-6">
	        <?php $creator = es_get_terms_creator_factory( $taxonomy );
	        $creator->render(); ?>
        </div>
    <?php endforeach; ?>
</div>

<div class="es-row">
    <div class="es-col-md-6">
		<?php foreach ( array( 'es_exterior_material', 'es_roof' ) as $taxonomy ) : ?>
			<?php $creator = es_get_terms_creator_factory( $taxonomy );
			$creator->render(); ?>
		<?php endforeach; ?>
    </div>
    <div class="es-col-md-6">
        <?php $taxonomy = 'es_parking';
        $creator = es_get_terms_creator_factory( $taxonomy );
        $creator->render(); ?>
    </div>

    <div class="es-col-md-6">
        <?php $taxonomy = 'es_tag';
        $creator = es_get_terms_creator_factory( $taxonomy );
        $creator->render(); ?>
    </div>
</div>

<?php do_action( 'es_data_manager_parameters_after' ); ?>