<?php

/**
 * Class Es_Locations_Creator.
 */
class Es_Locations_Creator extends Es_Terms_Creator {

    /**
     * Location type.
     *
     * @var array
     */
    protected $_config;

    /**
     * Es_Terms_Creator constructor.
     *
     * @param $taxonomy
     */
    public function __construct( $taxonomy, $config ) {
        parent::__construct( $taxonomy );
        $this->_config = wp_parse_args( $config, array(
            'components' => array( 'country' ),
            'label' => __( 'Country', 'es' ),
            'id' => 'countries',
            'parent_id' => '',
        ) );

        $this->_config['initial'] = ! isset( $this->_config['initial'] ) ? ! empty( $this->_config['parent_id'] ) : $this->_config['initial'];
    }

    public function classes() {
        echo ! $this->_config['initial'] ? 'es-terms-creator--disabled' : '';
    }

    /**
     *
     */
    public function before_list() {
        es_framework_field_render( 'search', array(
            'type' => 'text',
            'attributes' => array(
                'class' => 'js-es-search-terms',
                'placeholder' => __( 'Search', 'es' ),
            ),
        ) );
    }

    /**
     * @return string|null
     */
    public function get_title(){
        return $this->_config['label'];
    }

    /**
     * Render term field.
     *
     * @return void
     */
    public function term_field() {
        $config = array(
            'type' => 'text',
            'attributes' => array(
                'class' => 'es-field__input js-es-term-name',
            ),
            'label' => _x( 'Add new option', 'data manager term add', 'es' ),
            'value' => $this->_form_term instanceof WP_Term ? $this->_form_term->name : '',
        );
//        if ( ! $this->_config['initial'] ) {
//            $config['attributes']['disabled'] = 'disabled';
//        }
        es_framework_field_render( 'term_name', $config );
    }

    /**
     * @return array
     */
    public function get_terms() {
        $meta = array(
            array(
                'key' => 'type',
                'value' => $this->_config['components'],
                'compare' => 'IN',
            ),
        );

        if ( ! empty( $this->_config['parent_id'] ) ) {
            $meta[] = array(
                'key' => 'parent_component',
                'value' => $this->_config['parent_id'],
            );
        }

        $list = $this->_config['initial'] ? es_get_terms_list( $this->_taxonomy,false, $meta ) : array();
        return apply_filters( 'es_terms_creator_get_terms', $list, $this->_taxonomy, $this );
    }

    /**
     * Render terms list.
     *
     * @return void
     */
    public function render_list() {
        if ( $terms = $this->get_terms() ) : ?>
            <?php foreach ( $terms as $term_id => $term_name ) : ?>
                <?php $this->render_term_item( $term_id, $term_name ); ?>
            <?php endforeach; ?>
        <?php endif;
    }

    /**
     * Renter term item.
     *
     * @param $term_id
     * @param $term_name
     */
    public function render_term_item( $term_id, $term_name ) {
        $term = get_term( $term_id, 'es_location' );
	    /* translators: %s: term name. */
        $delete_message = sprintf( __( 'Are you sure you want to delete <b>%s</b> Option?', 'es' ), $term_name ); ?>
        <li class="es-item es-term es-location-term js-es-term-<?php echo $term_id; ?>  js-es-term es-es_location-term-<?php echo $term_id; ?>">
            <b>
                <?php es_framework_field_render( $this->_config['id'], array(
                    'type' => 'radio',
                    'options' => array( $term_id => '' ),
                    'attributes' => array(
                        'data-config' => es_esc_json_attr( $this->_config ),
                        'class' => 'js-es-location-dep-radio'
                    )
                ) ); ?>
                <span class="es-term-label">
                    <?php printf( '(ID: %s)', $term->term_id ); ?>
                    <?php echo esc_attr( $term_name ); ?></span> (<?php echo $term->count; ?>)
                <a href="#" class="es-term__edit es-control js-es-term__edit" data-type="<?php echo $this->_config['id']; ?>" data-taxonomy="es_location" data-term="<?php echo $term_id; ?>"><span class="es-icon es-icon_pencil"></a>
            </b>
            <div class="es-control__container">
                <a href="#" data-taxonomy="es_location" data-message="<?php echo esc_attr( $delete_message ); ?>" data-term="<?php echo $term_id; ?>" class="es-control js-es-term__delete--confirm es-term__delete--confirm">
                    <?php if ( es_is_default_term( $term_id ) ) : ?>
                        <span class="es-icon es-icon_trash"></span>
                    <?php else : ?>
                        <span class="es-icon es-icon_close"></span>
                    <?php endif; ?>
                </a>

                <?php es_framework_field_render( "es_term_{$term_id}", array(
                    'type' => 'checkbox',
                    'wrapper_class' => "es-field es-field__{field_key} es-field--{type} js-es-field--term es-field--term",
                    'attributes' => array(
                        'id' => sprintf( "es-field-%s", $term_id ),
                        'value' => $term_id,
                    ),
                ) ); ?>
            </div>
        </li><?php
    }

    /**
     * Display form meta fields.
     *
     * @return void
     */
    public function form_meta_fields() {
        $parent_ids = es_get_recursive_parent_locations( $this->_config['parent_id'] ); ?><input type="hidden" name="type" value="<?php echo $this->_config['components'][0]; ?>"/>
        <?php if ( $parent_ids ) : ?>
            <?php foreach ( $parent_ids as $parent_id ) : ?>
                <input type="hidden" name="parent_id[]" value="<?php echo $parent_id; ?>"/>
            <?php endforeach; ?>
        <?php endif; ?>
        <input type="hidden" name="dep" value="<?php echo $this->_config['id']; ?>"/><?php
    }

    /**
     * @return mixed|string
     */
    public function get_id(){
        return $this->_config['id'];
    }
}
