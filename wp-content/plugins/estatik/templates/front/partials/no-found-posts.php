<div class="es-no-found-posts">
	<h2 class="es-no-found-posts__title heading-font"><?php _e( 'No results', 'es' ); ?></h2>
	<p class="content-font"><?php _e( 'Adjust your search by changing filters.', 'es' ); ?></p>
    <?php if ( ! empty( $args['enable_search'] ) ) : ?>
        <button type="button" class="es-btn es-btn--secondary js-es-remove-filters">
            <span class="es-icon es-icon_close"></span>
            <?php _e( 'Remove all filters', 'es' ); ?>
        </button>
    <?php endif; ?>
</div>
