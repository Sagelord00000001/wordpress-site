<?php

/**
 * Class Es_Address_Components.
 */
class Es_Address_Components {

    /**
     * @param $type
     *
     * @return string|null
     */
    public static function get_label_by_type( $type ) {
        $labels = apply_filters( 'es_address_components_get_types_label', array(
	        'street_address' => __( 'street_address', 'es' ),
	        'route' => __( 'route', 'es' ),
	        'intersection' => __( 'intersection', 'es' ),
	        'political' => __( 'political', 'es' ),
	        'country' => __( 'country', 'es' ),
	        'administrative_area_level_1' => __( 'administrative_area_level_1', 'es' ),
	        'administrative_area_level_2' => __( 'administrative_area_level_2', 'es' ),
	        'administrative_area_level_3' => __( 'administrative_area_level_3', 'es' ),
	        'administrative_area_level_4' => __( 'administrative_area_level_4', 'es' ),
	        'administrative_area_level_5' => __( 'administrative_area_level_5', 'es' ),
	        'colloquial_area' => __( 'colloquial_area', 'es' ),
	        'locality' => __( 'locality', 'es' ),
	        'neighborhood' => __( 'neighborhood', 'es' ),
	        'sublocality_level_1' => __( 'Sublocality level 1', 'es' ),
	        'sublocality_level_2' => __( 'Sublocality level 2', 'es' ),
	        'sublocality_level_3' => __( 'Sublocality level 3', 'es' ),
	        'sublocality_level_4' => __( 'Sublocality level 4', 'es' ),
	        'sublocality_level_5' => __( 'Sublocality level 5', 'es' ),
	        'sublocality' => __( 'sublocality', 'es' ),
	        'premise' => __( 'premise', 'es' ),
	        'subpremise' => __( 'subpremise', 'es' ),
	        'establishment' => __( 'establishment', 'es' ),
	        'landmark' => __( 'landmark', 'es' ),
	        'postal_town' => __( 'postal_town', 'es' ),
	        'postal_code' => __( 'Postal Code', 'es' ),
        ) );

        return ! empty( $labels[ $type ] ) ? $labels[ $type ] : null;
    }

    /**
     * @param $types array
     * @param null $parent_id int|null
     *
     * @return WP_Term[]|null
     */
    public static function get_locations( $types, $parent_id = null ) {
        if ( ! $parent_id ) {
            $locations = es_get_terms_list( 'es_location', false, array(
                array( 'key' => 'type', 'value' => $types, 'compare' => 'IN' ),
            ) );
        } else {
            $locations = es_get_terms_list( 'es_location', false, array(
                array( 'key' => 'type', 'value' => $types, 'compare' => 'IN' ),
                array( 'key' => 'parent_component', 'value' => $parent_id ),
            ) );

			if ( ! $locations ) {
				$location_term = get_term( $parent_id, 'es_location' );

				if ( $location_term ) {
					$term_types = get_term_meta( $parent_id, 'type' );
					if ( ! empty( $types[0] ) && is_array( $term_types ) && in_array( $types[0], $term_types ) ) {
						$locations[ $parent_id ] = $location_term->name;
					}
				}
			}
        }

        return apply_filters( 'es_address_components_get_locations', $locations );
    }

    /**
     * Save property address components.
     *
     * @param $data
     * @param $property_id
     */
    public static function save_property_components( $data, $property_id ) {
        $child = array();
        wp_delete_object_term_relationships( $property_id, 'es_location' );

		$ignore_components = array( 'route', 'street_number', 'postal_code_prefix', 'postal_code_suffix', 'postal_code' );

        if ( ! empty( $data ) ) {
            $data = array_reverse( $data );
            foreach ( $data as $key => $component ) {
                $component = (array) $component;
				$ignore = false;

				if ( ! empty( $component['types'] ) && is_array( $component['types'] ) ) {

					if ($component['types'][0] == 'postal_code' && !empty($component['long_name'])) {
						update_post_meta( $property_id, 'es_property_postal_code', $component['long_name']);
					}
					
					foreach ( $component['types'] as $check_type ) {
						if ( in_array( $check_type, $ignore_components ) ) {
							$ignore = true;
							break;
						}
					}
				}

				if ( $ignore == false) {

					$data[$key] = $component;
					if ( ! empty( $component['term_id'] ) ) {
						$object_id[0] = $component['term_id'];
					} else {
						$object_id = wp_set_object_terms( $property_id, $component['long_name'], 'es_location', true );
					}
					if ( ! is_wp_error( $object_id ) && ! empty( $object_id ) ) {
						$data[ $key ]['term_id'] = $object_id[0];
						if ( ! empty( $component['types'] ) ) {
							$existing_types = get_term_meta( $object_id[0], 'type', false );
							foreach ( $component['types'] as $type ) {
								if ( empty( $existing_types ) || ! in_array( $type, $existing_types ) ) {
									add_term_meta( $object_id[0], 'type', $type );
								}
							}
						}

						if ( ! empty( $child ) ) {
							$parents = get_term_meta( $object_id[0], 'parent_component', false );
							foreach ( $child as $children ) {
								if ( empty( $parents ) || ! in_array( $children['term_id'], $parents ) ) {
									add_term_meta( $object_id[0], 'parent_component', $children['term_id'] );
								}
							}
						}

						$child[] = $data[ $key ];
					}
				}	
            }
        }
    }
}
