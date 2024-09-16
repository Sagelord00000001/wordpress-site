<?php

/**
 * Class Es_Properties_Archive_Page.
 */
abstract class Es_Entities_Archive_Page {

	/**
	 * @param $columns
	 */
	public static function add_table_columns( $columns ) {}

	/**
	 * @return string
	 */
	public static function get_post_type_name() {}

	/**
	 * @return array
	 */
	public static function get_sort_options() {}

	/**
	 * @return string
	 */
	public static function get_default_sort() {}
	public static function filter_query( $query ) {}

	/**
	 * @return string
	 */
	public static function get_entity_name() {}

	/**
	 * Initialize admin properties archive page.
	 *
	 * @return void
	 */
	public static function init() {
		global $pagenow;

		$_class = get_called_class();

		if ( is_admin() && 'edit.php' == $pagenow && static::get_post_type_name() == filter_input( INPUT_GET, 'post_type' ) ) {
			add_action( 'admin_enqueue_scripts', array( $_class, 'enqueue_assets' ) );
			add_action( 'pre_get_posts', array( $_class, 'filter_query' ) );
			add_action( 'manage_posts_extra_tablenav', array( $_class, 'table_sort_pagination' ), 10, 1 );
			add_filter( 'admin_body_class', array( $_class, 'admin_page_class' ) );
		}

		// Add action for render custom columns values.
		add_action( 'manage_' . static::get_post_type_name() . '_posts_custom_column' , array( $_class, 'add_table_columns_values' ), 10, 2 );

		// Add custom columns to the list table.
		add_filter( 'manage_' . static::get_post_type_name() . '_posts_columns', array( $_class, 'add_table_columns' ) );
		add_action( 'init', array( $_class, 'entities_actions' ) );
	}

	/**
	 * @param $classes
	 *
	 * @return string
	 */
	public static function admin_page_class( $classes ) {
		return "$classes es-entities-archive ";
	}

	/**
	 * Display sort dropdown.
	 */
	public static function sort_dropdown() {
		$entity = es_get_entity( static::get_entity_name() );
		$count = $entity::count();

		$options = static::get_sort_options();

		if ( $options && $count ) {
			$sort = filter_input( INPUT_GET, 'sort' );
			$sort = $sort ? $sort : static::get_default_sort();

			es_framework_field_render( 'sort', array(
				'type' => 'select',
				'label' => __( 'Sort by', 'es' ),
				'attributes' => array(
					'class' => 'js-es-submit-on-change',
				),
				'value' => $sort,
				'options' => $options,
				'wrapper_class' => 'es-field--small'
			) );
		}
	}

	/**
	 * Display post pagination.
	 *
	 * @param $pos
	 *
	 * @return void
	 */
	public static function table_sort_pagination( $pos ) {
		$entity = es_get_entity( static::get_entity_name() );
		$count = $entity::count();

		if ( 'bottom' == $pos && $count ) {
			global $wp_query;

			the_posts_pagination( array(
				'mid_size' => 2,
				'end_size' => 1,
				'format' => '?paged=%#%',
				'prev_text' => '<span class="es-icon es-icon_chevron-left"></span>' . __( 'Prev', 'es' ),
				'next_text' => __( 'Next', 'es' ) . '<span class="es-icon es-icon_chevron-right"></span>',
				'base' => add_query_arg( 'paged', '%#%' ),
			) );

			if ( $wp_query && $wp_query->max_num_pages > 1 ) {
				static::navigation_meta();
			}
		}
	}

	public static function navigation_meta() {
		global $wp_query;
		$page_num = get_query_var( 'paged' );
		$page_num = $page_num ? $page_num : 1;
		$limit = $wp_query->get( 'posts_per_page' );
		echo "<div class='navigation-meta'>";
		$limit_start = ( $page_num * $limit ) - $limit + 1;
		$limit_end = $limit * $page_num;
		$limit_end = $wp_query->found_posts > $limit_end ? $limit_end : $wp_query->found_posts;
		echo "<span class='es-navigation'>";
		/* translators: %1$s: limit start, %2$s: limit end, %3$s: found posts. */
		printf( __( '%1$s - %2$s of %3$s properties', 'es' ), $limit_start, $limit_end, $wp_query->found_posts );
		echo "</span></div>";
	}

	/**
	 * Delete & copy property actions handlers.
	 *
	 * @return void
	 */
	public static function entities_actions() {
		$nonce = 'es_entities_actions';

		if ( ! empty( $_GET['_nonce'] ) && wp_verify_nonce( $_GET['_nonce'], $nonce ) ) {
			$action = es_clean( filter_input( INPUT_GET, 'action' ) );
			$posts_ids = ! empty( $_GET['post_ids'] ) ? $_GET['post_ids'] : array();
			$posts_ids = is_array( $posts_ids ) ? $posts_ids : explode( ',', $_GET['post_ids'] );
			$posts_ids = es_clean( $posts_ids );

			$post_type = get_post_type_object( static::get_post_type_name() );

			if ( $posts_ids ) {
				switch ( $action ) {
					case 'delete':
						foreach ( $posts_ids as $post_id ) {
							if ( current_user_can( 'delete_post', $post_id ) )
								wp_delete_post( $post_id, true );
						}
						break;

					case 'copy':
						if ( current_user_can( $post_type->cap->create_posts ) ) {
							foreach ( $posts_ids as $post_id ) {
								es_duplicate_post( $post_id );
							}
						}
						break;

					case 'publish':
					case 'draft':
						foreach ( $posts_ids as $post_id ) {
							if ( current_user_can( $post_type->cap->publish_posts ) ) {
								wp_update_post( array(
									'ID' => $post_id,
									'post_status' => $action
								) );
							}
						}
						break;
				}

				do_action( 'es_after_entities_actions', $posts_ids, $action );
			}

			wp_safe_redirect( $_SERVER['HTTP_REFERER'] ); die;
		}
	}

	/**
	 * Enqueue assets for properties archive page.
	 *
	 * @return void
	 */
	public static function enqueue_assets() {
		$deps = array( 'es-admin' );
		wp_enqueue_style( 'es-archive-entities', ES_PLUGIN_URL . 'admin/css/archive-entities.min.css', $deps, Estatik::get_version() );
		wp_enqueue_script( 'es-archive-entities', ES_PLUGIN_URL . 'admin/js/entities-list.min.js', array( 'jquery', 'es-admin', 'inline-edit-post' ), Estatik::get_version() );
		wp_localize_script( 'es-archive-entities', 'EstatikList', array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
		) );
	}

	/**
	 * Render table column value.
	 *
	 * @param $column
	 * @param $post_id
	 */
	public static function add_table_columns_values( $column, $post_id ) {
		if ( 'post_id' == $column ) {
			echo $post_id;
		}

		if ( 'thumbnail' == $column ) {
			the_post_thumbnail( 'thumbnail' );
		}
	}
}
