<?php

/**
 * @var $current_tab string
 */

$query = new WP_Query( array(
	'post_type' => 'saved_search',
	'post_status' => 'private',
	'author' => get_current_user_id(),
	'posts_per_page' => ests( 'saved_searches_per_page' ),
) );

$have_posts = false; ?>

<div id="<?php echo $current_tab; ?>" class="es-profile__content es-profile__content--<?php echo $current_tab; ?>">
	<?php if ( ! empty( $tab_config['label'] ) ) : ?>
        <h2 class="heading-font"><?php echo $tab_config['label']; ?></h2>
	<?php endif;

	if ( $query->have_posts() ) : $have_posts = true; ?>
        <div class="es-saved-searches">
			<?php while ( $query->have_posts() ) : $query->the_post(); $saved_search = es_get_saved_search( get_the_ID() ); ?>
                <div class="es-saved-search js-es-saved-search" id="es-saved-search-<?php the_ID(); ?>">
                    <h4 class="content-font"><?php echo $saved_search->get_title(); ?></h4>
					<?php if ( $saved_search->address ) : ?>
                        <span class="es-address"><?php echo $saved_search->address; ?></span>
					<?php endif; ?>
					<?php if ( $query_string = $saved_search->get_formatted_query_string() ) : ?>
                        <div class="es-saved-search__query">
							<?php echo $query_string; ?>
                        </div>
					<?php endif; ?>
					<?php do_action( 'es_saved_search_after_query', get_the_ID() ); ?>
                    <div class="es-saved-search__buttons">
                        <a href="#" class="es-btn es-btn--default js-es-remove-saved-search" data-hash="<?php echo es_encode( get_the_ID() ); ?>">
                            <span class="es-icon es-icon_trash"></span>
							<?php _e( 'Remove', 'es' ); ?>
                        </a>
						<?php if ( ( $search_url = es_get_search_page_url() ) && ( $search_data = $saved_search->search_data ) ) : ?>
                            <a target="_blank" href="<?php echo add_query_arg( $search_data, $search_url ); ?>" class="es-btn es-btn--secondary">
                                <span class="es-icon es-icon_search"></span>
								<?php _e( 'Search results', 'es' ); ?>
                            </a>
						<?php endif; ?>
                    </div>
                </div>
			<?php endwhile; ?>
        </div>
		<?php wp_reset_postdata();
	endif; ?>

    <div class="js-es-no-posts <?php echo ! $have_posts ? '' : 'es-hidden'; ?>">
        <p class="es-subtitle"><?php _e( 'You haven’t saved any searches yet.', 'es' ); ?></p>
        <p><?php _e( 'Start searching for properties to add now.', 'es' ); ?></p>
		<?php if ( $url = es_get_search_page_url() ) : ?>
            <a href="<?php echo $url; ?>" class="es-btn es-btn--secondary">
                <span class="es-icon es-icon_search"></span><?php _e( 'Go to search', 'es' ); ?>
            </a>
		<?php endif; ?>
    </div>
</div>
