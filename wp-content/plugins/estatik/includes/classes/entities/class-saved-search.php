<?php

/**
 * Class Es_Saved_Search.
 *
 * @property $address string
 * @property $search_data string
 */
class Es_Saved_Search extends Es_Post {

    /**
     * @return string
     */
    public function get_entity_prefix() {
        return 'es_saved_search_';
    }

    /**
     * @return mixed
     */
    public static function get_post_type_name() {
        return 'saved_search';
    }

	/**
	 * @return array|mixed|void
	 */
	public static function get_default_fields() {
		$fields = array(
			'update_type' => array(
				'default_value' => 'none',
			),
			'mailed_properties_ids' => array(
				'default_value' => null,
			),
			'search_data' => array(),
		);

		return apply_filters( 'es_saved_search_default_fields', $fields );
	}

	/**
     * @return mixed
     */
    public static function get_entity_name() {
        return 'saved_search';
    }

    /**
     * @return bool|false|int|string
     */
    public function get_title() {
         $entity = $this->get_wp_entity();

         if ( empty( $entity->post_title ) ) {
             return get_the_time( 'l, F jS, H:i' );
         } else {
             return get_the_title( $entity->ID );
         }
    }

    /**
     * Return search query readable string.
     *
     * @return string
     */
    public function get_formatted_query_string() {
        $data = $this->search_data;
        $result = array();

        if ( ! empty( $data ) ) {
            foreach ( $data as $field => $value ) {
	            $field_info = es_property_get_field_info( $field );

                if ( $field === 'address' || empty( $field_info ) ) continue;

                $base_field = $field_info['base_field_key'];

                if ( ! $field_info ) continue;

                if ( stristr( $field, 'min_' ) || stristr( $field, 'from_' ) ) {
                    $max = ! empty( $data[ 'max_' . $base_field ] ) ? $data[ 'max_' . $base_field ] : false;
                    $result[ $base_field ] = ! $max ?
	                    /* translators: %s: min-max. */
                        sprintf( __( 'From %s', 'es' ), es_format_value( $value, $field_info['formatter'] ) ) :
                        $value . ' - ' . es_format_value( $max, $field_info['formatter'] );
                }

                if ( stristr( $field, 'max_' ) ) {
                    $min = ! empty( $data[ 'min_' . $base_field ] ) ? $data[ 'min_' . $base_field ] : 0;
                    $min = ! empty( $data[ 'from_' . $base_field ] ) ? $data[ 'from_' . $base_field ] : $min;
	                $min = es_format_value( $min, $field_info['formatter'] );
                    $result[ $base_field ] = $min . ' - ' . es_format_value( $value, $field_info['formatter'] );
                }

                if ( ! empty( $field_info['taxonomy'] ) ) {
                    $result[ $base_field ] = implode( ', ', get_terms( array(
                        'taxonomy' => $base_field,
                        'fields' => 'names',
                        'term_taxonomy_id' => $value
                    ) ) );
                } else if ( empty( $result[ $base_field ] ) ) {
                	$value = is_array( $value ) ? implode( ', ', $value ) : $value;

                	if ( is_string( $value ) && strlen( $value ) ) {
		                $result[ $base_field ] = $value;
	                }
                }
            }
        }

        return apply_filters( 'es_saved_search_get_formatted_query_string', implode( ' | ', $result ), $this );
    }

    /**
     * Save search args.
     *
     * @param $data
     */
    public function save_fields( $data ) {
        $entity = static::get_entity_name();

        if ( ! empty( $data ) ) {
            foreach ( $data as $field => $value ) {
                $this->save_field_value( $field, $value );
            }

            $this->save_field_value( 'search_data', $data );
        }

        do_action( "es_{$entity}_after_save_fields", $data, $this );
    }
}
