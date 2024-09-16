<h2 id="es-locations"><?php $taxonomy = 'es_location'; echo _x( 'Locations', 'data manager title', 'es' ); ?></h2>

<div class="es-row">
    <?php foreach ( Es_Data_Manager_Page::get_locations_config() as $id => $config ) : ?>
        <div class="es-col-xl-6 es-col-lg-6" id="es-<?php echo $id; ?>">
            <?php $creator = es_get_terms_creator_factory( $taxonomy, $config ); $creator->render(); ?>
        </div>
    <?php endforeach; ?>
</div>


<div class="es-row">
    <div class="es-col-md-6" id="es-neighborhood">
		<?php $creator = es_get_terms_creator_factory( 'es_neighborhood' );
		$creator->render(); ?>
    </div>
</div>
