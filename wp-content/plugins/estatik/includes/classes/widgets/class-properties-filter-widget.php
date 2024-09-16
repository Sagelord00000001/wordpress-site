<?php

/**
 * Class Es_Properties_Filter_Widget.
 */
abstract class Es_Properties_Filter_Widget extends Es_Widget {

	protected static $_taxonomies_values;

	/**
	 * Return default widget data.
	 *
	 * @return array
	 */
	public function get_default_data() {
		return array(
			'page_access' => 'show',
			'show_properties_by' => 'id',
			'posts_per_page' => 20,
		);
	}

	/**
	 * Return widget form filter fields for wp_query.
	 *
	 * @param $instance
	 *
	 * @return array|array[]
	 */
	public function get_widget_form( $instance ) {
		foreach ( array( 'es_category', 'es_type' ) as $taxonomy ) {
			if ( empty( static::$_taxonomies_values[ $taxonomy ] ) ) {
				$terms = es_get_terms_list( $taxonomy );
				$terms = $terms ? $terms : array();
				static::$_taxonomies_values[ $taxonomy ] = $terms;
			}
		}

		return apply_filters( 'es_widget_properties_filter_form', array_merge( array(
			'sort' => array(
				'label' => __( 'Default sort', 'es' ),
				'type' => 'select',
				'options' => ests_selected( 'properties_sorting_options' ),
			),
			'show_properties_by' => array(
				'label' => __( 'Show properties', 'es' ),
				'type' => 'radio-bordered',
				'attributes' => array(
					'class' => 'js-es-show-properties-by',
				),
				'options' => array(
					'id' => __( 'by ID', 'es' ),
					'filter' => __( 'by filtering', 'es' ),
				),
			),
			'prop_id' => array(
				'before' => "<div class='es-listings-by-id'>",
				'label' => __( 'Listings IDs', 'es' ),
				'type' => 'text',
				'attributes' => array(
					'placeholder' => __( 'Enter IDs separated by commas', 'es' ),
				),
				'after' => '</div>',
			),
			'posts_per_page' => array(
				'type' => 'number',
				'label' => __( 'Listings to show', 'es' ),
				'before' => '<div class="es-listings-by-filter">',
			),
			'es_category' => array(
				'label' => __( 'Property categories to show', 'es' ),
				'type' => 'checkboxes',
				'options' => static::$_taxonomies_values['es_category'],
				'attributes' => array(
					'class' => 'js-es-check-state'
				),
				'items_attributes' => array(
					'' => array(
						'attributes' => array(
							'class' => 'js-es-check-all'
						),
					),
				),
			),
			'es_type' => array(
				'label' => __( 'Property types to show', 'es' ),
				'type' => 'checkboxes',
				'options' => static::$_taxonomies_values['es_type'],
				'attributes' => array(
					'class' => 'js-es-check-state'
				),
				'items_attributes' => array(
					'' => array(
						'attributes' => array(
							'class' => 'js-es-check-all'
						),
					),
				),
				'after' => '</div>',
			),
		), parent::get_widget_form( $instance ) ), $instance, $this ) ;
	}
}
