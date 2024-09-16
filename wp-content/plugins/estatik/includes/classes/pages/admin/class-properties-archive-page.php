<?php

/**
 * Class Es_Properties_Archive_Page.
 */
class Es_Properties_Archive_Page extends Es_Entities_Archive_Page {

	/**
	 * Initialize admin properties archive page.
	 *
	 * @return void
	 */
	public static function init() {
		global $pagenow;

		parent::init();

		if ( is_admin() && 'edit.php' == $pagenow && 'properties' == filter_input( INPUT_GET, 'post_type' ) ) {
			// Add bulk actions container.
			add_action( 'network_admin_notices', array( __CLASS__, 'add_bulk_actions' ) );
			add_action( 'user_admin_notices', array( __CLASS__, 'add_bulk_actions' ) );
			add_action( 'admin_notices', array( __CLASS__, 'add_bulk_actions' ) );
		}

        add_filter( 'views_edit-properties', array( __CLASS__, 'render_properties_filter' ) );
		add_filter( 'manage_edit-' . static::get_post_type_name() . '_sortable_columns', array( __CLASS__, 'add_sortable_columns' ) );
        add_action( 'wp_ajax_es_property_quick_edit_form', array( __CLASS__, 'ajax_quick_edit_form' ) );
        add_action( 'wp_ajax_es_property_quick_edit_bulk_form', array( __CLASS__, 'ajax_quick_edit_bulk_form' ) );
	}

    /**
     * Return quick edit form for single property via ajax.
     *
     * @return void
     */
	public static function ajax_quick_edit_form() {
        if ( check_ajax_referer( 'es_property_quick_edit_form', 'nonce', false ) ) {
            $post_id = filter_input( INPUT_GET, 'post_id' );

            if ( $post_id ) {
                $post = get_post( $post_id );
                $property = es_get_property( $post_id );

                if ( $post->post_type == $property::get_post_type_name() ) {
                    ob_start();
                    es_load_template( 'admin/properties-archive/quick-edit.php', array(
                        'property' => $property,
                        'post' => $post,
                    ) );
                    $response = ob_get_clean();
                } else {
                    $response = es_notification_ajax_response( __( 'Invalid post type.', 'es' ), 'error' );
                }
            } else {
                $response = es_notification_ajax_response( __( 'Post ID is empty', 'es' ), 'error' );
            }
        } else {
            $response = es_ajax_invalid_nonce_response();
        }

        wp_die( json_encode( $response ) );
    }

    /**
     * Return quick edit form for single property via ajax.
     *
     * @return void
     */
	public static function ajax_quick_edit_bulk_form() {
        if ( check_ajax_referer( 'es_property_quick_edit_bulk_form', 'nonce', false ) ) {
            ob_start();
            es_load_template( 'admin/properties-archive/quick-edit-bulk.php' );
            $response = ob_get_clean();
        } else {
            $response = es_ajax_invalid_nonce_response();
        }

        wp_die( json_encode( $response ) );
    }

	/**
	 * @param $columns array
	 *
	 * @return array
	 */
	public static function add_sortable_columns( $columns ) {
	    $columns['price'] = 'es_property_price';
	    $columns['address'] = 'es_property_address';
	    $columns['post_id'] = 'post_id';

        unset( $columns['date'] );

        return $columns;
    }

	/**
     * Properties list query filter.
     *
	 * @param $query WP_Query
     *
     * @return void|WP_Query
	 */
	public static function filter_query( $query ) {
		if ( ! $query->is_main_query() ) return;

		$order = es_get( 'order' ); // ASC, DESC
		$orderby = es_get( 'orderby' );
		$sort = es_get( 'sort' );
        $orderby_query = $query->get( 'orderby' );
        $order_query = $query->get( 'order' );
        $query->set( 'orderby', '' );
        $query->set( 'order', '' );

        $new_order = array();

        if ( $order_query && $orderby_query ) {
            $new_order[ $orderby_query ] = $order_query;
        }

        $allowed_meta_order = apply_filters( 'es_property_archive_meta_order_keys',
            array( 'es_property_price', 'es_property_address' ) );

		if ( $sort ) {
			switch ( $sort ) {
				case 'featured':
					if ( term_exists( $sort, 'es_label' ) ) {
						$tax_query[ 'sort_' . $sort ] = array(
							'taxonomy' => 'es_label',
							'field' => 'slug',
							'terms' => $sort
						);
					}
					break;

				case 'bedrooms':
				case 'bathrooms':
                    $new_order['exists_' . $sort ] = 'ASC';
					$meta_query['exists_' . $sort ] = array( 'key' => 'es_property_' . $sort, 'compare' => 'EXISTS', 'type' => 'NUMERIC' );
					break;

				case 'oldest':
					$new_order['date'] = 'ASC';
//					$query->set( 'orderby', 'date' );
//					$query->set( 'order', 'ASC' );
					break;

				default:
					$new_order['date'] = 'DESC';
//					$query->set( 'orderby', 'date' );
//					$query->set( 'order', 'DESC' );
			}
		}

        if ( in_array( $orderby, $allowed_meta_order ) ) {
            $meta_query[ $orderby. '_clause' ] = array(
                'compare' => 'EXISTS',
                'key' => $orderby,
                'type' => stristr( $orderby, 'address' ) ? '' : 'NUMERIC'
            );

            $new_order[ $orderby. '_clause'] = $order;
        }

		if ( $new_order ) {
			$query->set( 'orderby', $new_order );
		}

        if ( ! empty( $_GET['entities_filter'] ) ) {
		    $filter = $_GET['entities_filter'];

		    foreach ( array( 'es_category', 'es_type', 'es_status' ) as $taxonomy ) {
		        if ( ! empty( $filter[ $taxonomy ] ) ) {
                    $tax_query[] = array(
                        'taxonomy' => $taxonomy,
                        'field'    => 'id',
                        'terms'    => $filter[ $taxonomy ],
                    );
                }
            }

            foreach ( es_get_location_fields() as $location_field ) {
                if ( ! empty( $filter[ $location_field ] ) ) {
                    $tax_query['es_location'][] = array( 'taxonomy' => 'es_location', 'field' => 'id', 'terms' => $filter[ $location_field ] );
                }
            }

            if ( ! empty( $filter['date_added'] ) ) {
                $value = DateTime::createFromFormat( ests( 'date_format' ), $filter['date_added'] );
                $query->set( 'year', $value->format( 'Y' ) );
                $query->set( 'day', $value->format( 'd' ) );
                $query->set( 'monthnum', $value->format( 'm' ) );
            }

            if ( ! empty( $filter['price_min'] ) ) {
                $meta_query['price_min'] = array(
                    'value' => $filter['price_min'],
                    'key' => 'es_property_price',
                    'type' => 'NUMERIC',
                    'compare' => '>=',
                );
            }

            if ( ! empty( $filter['price_max'] ) ) {
                $meta_query['price_max'] = array(
                    'key' => 'es_property_price',
                    'value' => $filter['price_max'],
                    'type' => 'NUMERIC',
                    'compare' => '<=',
                );
            }

            if ( ! empty( $filter['s'] ) ) {
                $meta_query['keywords'] = array(
                    'key' => 'es_property_keywords',
                    'value' => stripslashes( $filter['s'] ),
                    'compare' => 'LIKE'
                );
            }
        }

        if ( ! empty( $tax_query ) ) {
		    $query->set( 'tax_query', apply_filters( 'es_admin_properties_tax_query', $tax_query, $query ) );
        }

		if ( ! empty( $meta_query ) ) {
			$query->set( 'meta_query', apply_filters( 'es_admin_properties_meta_query', $meta_query, $query ) );
		}

        return $query;
    }

	/**
	 * Add properties bulk action's container.
     *
     * @return void
	 */
	public static function add_bulk_actions() {
        es_load_template( 'admin/properties-archive/bulk-actions.php' );
    }

	/**
	 * Render table column value.
	 *
	 * @param $column
	 * @param $post_id
	 */
	public static function add_table_columns_values( $column, $post_id ) {
        if ( 'thumbnail' !== $column ) {
	        parent::add_table_columns_values( $column, $post_id );
        }

        if ( 'thumbnail' == $column && has_post_thumbnail() ) :
            $gallery = es_get_the_field( 'gallery' ); ?>
            <div class="es-image">
                <?php the_post_thumbnail( 'thumbnail' ); ?>
                <?php if ( is_array( $gallery ) ) : ?>
                    <div class="es-image__counter">
                        <span class="es-icon es-icon_camera"></span>
                        <span class="es-counter-text"><?php echo count( $gallery ); ?></span>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif;

		if ( in_array( $column, array( 'es_category', 'es_status', 'es_type' ) ) ) {
			the_terms( $post_id, $column );
		}

		if ( 'price' == $column ) {
		    es_the_price();
        }

        if ( 'address' == $column ) {
		    es_the_field( $column );
        }

		if ( 'actions' == $column ) :
			$post = get_post( $post_id );
			$title            = _draft_or_post_title();
			$post_type_object = get_post_type_object( $post->post_type );
			$can_edit_post    = current_user_can( 'edit_post', $post->ID ); ?>

			<div class="es-actions">
				<a href='#' class='es-more js-es-more'><span class='es-icon es-icon_more'></a>
				<div class="es-actions__dropdown">
					<ul>
						<?php if ( current_user_can( 'edit_post', $post_id ) ) {
						    echo '<li><a href="#" class="js-es-quick-edit" data-post-id="' . $post_id . '">' . _x( 'Quick edit', 'edit property', 'es' ) . '</a></li>';

						    printf(
                                '<li style="display: none;"><a href="#" class="button-link editinline" aria-label="%s" aria-expanded="false">%s</a></li>',
                                /* translators: %s: Post title. */
                                esc_attr( sprintf( __( 'Quick edit &#8220;%s&#8221; inline' ), $title ) ),
                                __( 'Quick&nbsp;Edit' )
                            );
                        }

						if ( is_post_type_viewable( $post_type_object ) ) {
								if ( in_array( $post->post_status, array( 'pending', 'draft', 'future' ) ) ) {
									if ( $can_edit_post ) {
										$preview_link    = get_preview_post_link( $post );
										printf(
											'<li><a href="%s" target="_blank" rel="bookmark" aria-label="%s">%s</a></li>',
											esc_url( $preview_link ),
											/* translators: %s: Post title. */
											esc_attr( sprintf( __( 'Preview &#8220;%s&#8221;' ), $title ) ),
											__( 'Preview' )
										);
									}
								} elseif ( 'trash' != $post->post_status ) {
									printf(
										'<li><a href="%s" target="_blank" rel="bookmark" aria-label="%s">%s</a></li>',
										get_permalink( $post_id ),
										/* translators: %s: Post title. */
										esc_attr( sprintf( __( 'View &#8220;%s&#8221;' ), $title ) ),
										__( 'View' )
									);
								}
							} ?>
						<?php if ( $can_edit_post ) : ?>
							<li>
								<?php printf( '<a href="%s" aria-label="%s">%s</a>',
									get_edit_post_link( $post_id ),
									/* translators: %s: post title. */
									esc_attr( sprintf( __( 'Edit &#8220;%s&#8221;' ), $title ) ),
									__( 'Edit' )
								); ?>
							</li>
						<?php endif; ?>
						<?php if ( $post->post_status == 'publish' ) : ?>
							<li><a href="<?php echo esc_url( es_get_action_post_link( $post_id, 'draft' ) ); ?>"><?php _e( 'Unpublish', 'es' ); ?></a></li>
						<?php else : ?>
							<li><a href="<?php echo esc_url( es_get_action_post_link( $post_id, 'publish' ) ); ?>"><?php _e( 'Publish', 'es' ); ?></a></li>
						<?php endif; ?>
						<li><a href="<?php echo esc_url( es_get_action_post_link( $post_id, 'copy' ) ); ?>"><?php _e( 'Duplicate', 'es' ); ?></a></li>
						<?php if ( current_user_can( 'delete_post', $post_id ) ) : ?>
							<li>
								<a href="<?php echo get_delete_post_link( $post_id, '', true ); ?>">
									<?php echo _x( 'Delete', 'delete property', 'es' ); ?>
								</a>
							</li>
						<?php endif; ?>
					</ul>
				</div>
			</div>

		<?php endif;
	}

	/**
	 * Render header of properties page.
	 *
	 * @param $views
	 *
	 * @return string
	 */
	public static function render_properties_filter( $views ) {
        $f = es_framework_instance();
        $f->load_assets();
		es_load_template( 'admin/properties-archive/header.php' );

		$entity = es_get_entity( static::get_entity_name() );
		if ( $entity::count() ) {
			es_load_template( 'admin/properties-archive/filter.php' );
		} else {
			es_load_template( 'admin/partials/empty-archive.php', array(
				'entity_name' => static::get_entity_name(),
				'post_type' => static::get_post_type_name(),
			) );
		}

		return $views;
	}

	public static function get_post_type_name() {
		return 'properties';
	}

	public static function get_sort_options() {
		return apply_filters( 'es_admin_properties_sort_options', array(
			'newest' => __( 'Newest', 'es' ),
			'featured' => __( 'Featured', 'es' ),
			'bedrooms' => __( 'Beds', 'es' ),
			'bathrooms' => __( 'Baths', 'es' ),
			'oldest' => __( 'Oldest', 'es' ),
		) );
	}

	/**
	 * Add custom columns for estatik properties table.
	 *
	 * @param $columns array
	 *
	 * @return mixed
	 */
	public static function add_table_columns( $columns ) {

		// Unset unused columns.
		unset( $columns['author'], $columns['date'] );
		// Add post ID column.
		$columns = es_push_array_pos( array( 'post_id' => __( 'ID', 'es' ) ),  $columns, 1 );
		// Add image column.
		$columns = es_push_array_pos( array( 'thumbnail' => __( 'Image', 'es' ) ), $columns, 2 );
		// Add image column.
		$columns = es_push_array_pos( array( 'address' => __( 'Address', 'es' ) ), $columns, 4 );
		// Add date column on new position with new label.
		$columns = es_push_array_pos( array( 'date' => __( 'Date added', 'es' ) ), $columns, 5 );
		// Add date column on new position with new label.
		$columns = es_push_array_pos( array( 'price' => __( 'Price', 'es' ) ), $columns, 6 );
		// Add date column on new position with new label.
		$columns = es_push_array_pos( array( 'es_category' => __( 'Category', 'es' ) ), $columns, 7 );
		// Add date column on new position with new label.
		$columns = es_push_array_pos( array( 'es_type' => __( 'Type', 'es' ) ), $columns, 8 );
		// Add date column on new position with new label.
		$columns = es_push_array_pos( array( 'es_status' => __( 'Status', 'es' ) ), $columns, 9 );
		// Add date column on new position with new label.
		$columns = es_push_array_pos( array( 'actions' => '<span class="es-icon es-icon_settings"></span>' ), $columns, 100 );

		return $columns;
	}

	public static function get_default_sort() {
		return 'newest';
	}

	public static function get_entity_name() {
		return 'property';
	}
}

Es_Properties_Archive_Page::init();
