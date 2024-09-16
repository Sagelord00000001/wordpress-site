<?php

/**
 * Class Es_Terms_Creator.
 */
class Es_Terms_Creator {

	/**
	 * @var string
	 */
	protected $_taxonomy;

	/**
	 * @var int
	 */
	protected $_form_term_id;

	/**
	 * @var WP_Term
	 */
	protected $_form_term;

	/**
	 * Es_Terms_Creator constructor.
	 *
	 * @param $taxonomy
	 */
	public function __construct( $taxonomy ) {
		$this->_taxonomy = $taxonomy;
	}

    /**
     * @return string|null
     */
	public function get_title() {
        $taxonomy = $this->get_taxonomy_instance();

        return ! is_wp_error( $taxonomy ) && $taxonomy ? $taxonomy->label : null;
    }

	/**
	 * @param $term_id int
	 */
	public function set_form_term_id( $term_id ) {
	    $this->_form_term_id = $term_id;
	    $this->_form_term = get_term( $term_id, $this->_taxonomy );
    }

	/**
	 * Return wp taxonomy instance.
	 *
	 * @return WP_Taxonomy|null
	 */
	public function get_taxonomy_instance() {
		global $wp_taxonomies;
		return ! empty( $wp_taxonomies[ $this->_taxonomy ] ) ?$wp_taxonomies[ $this->_taxonomy ] : null;
	}

	/**
	 * Renter term item.
	 *
	 * @param $term_id
	 * @param $term_name
	 * @param $taxonomy
	 */
	public function render_term_item( $term_id, $term_name ) {
	    $taxonomy = $this->_taxonomy;
		/* translators: %s: option name. */
	    $delete_message = sprintf( __( 'Are you sure you want to delete <b>%s</b> Option?', 'es' ), $term_name );
	    $is_deactivated = es_is_term_deactivated( $term_id ); ?>
        <li class="es-item es-term js-es-term js-es-term-<?php echo $term_id; ?> es-<?php echo $taxonomy; ?>-term-<?php echo $term_id; ?> <?php echo $is_deactivated ? 'es-item--disabled' : ''; ?>">
            <b>
				<?php /* translators: %s: term id. */
                echo esc_attr( $term_name ); ?> <?php printf( __( '(ID: %s)', 'es' ), $term_id ); ?>
                <a href="#" class="es-term__edit es-control js-es-term__edit" data-taxonomy="<?php echo $taxonomy; ?>" data-term="<?php echo $term_id; ?>"><span class="es-icon es-icon_pencil"></a>
            </b>
            <div class="es-control__container">
                <?php if ( $is_deactivated ) : ?>
                    <a href="#" data-taxonomy="<?php echo $taxonomy; ?>" data-term="<?php echo $term_id; ?>" class="es-control es-term__delete--confirm js-es-term__restore"><span class="es-icon es-icon_plus"></span></a>
                <?php else : ?>
                <a href="#" data-taxonomy="<?php echo $taxonomy; ?>" data-message="<?php echo esc_attr( $delete_message ); ?>" data-term="<?php echo $term_id; ?>" class="es-control js-es-term__delete--confirm es-term__delete--confirm">
                    <?php if ( es_is_default_term( $term_id ) ) : ?>
                        <span class="es-icon es-icon_trash"></span>
                    <?php else : ?>
                        <span class="es-icon es-icon_close"></span>
                    <?php endif; ?>
                </a>
                <?php endif; ?>
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

    public function form_meta_fields() {}

	/**
     * Render term form.
     *
	 * @return void
	 */
    public function render_form() {
	    ?>
        <form action="" class="js-es-term-form">
            <?php if ( ! empty( $this->_form_term_id ) ) : ?>
                <input type="hidden" name="term_id" value="<?php echo $this->_form_term_id; ?>"/>
            <?php endif; ?>
            <input type="hidden" name="action" value="es_terms_creator_add_term"/>
            <input type="hidden" name="taxonomy" value="<?php echo $this->_taxonomy; ?>"/>
		    <?php wp_nonce_field( 'es_terms_creator_add_term' );
		    $this->term_field(); ?>
            <button disabled type="submit" class="es-btn es-btn--secondary">
                <?php if ( $this->_form_term_id ) : ?>
	                <?php echo _x( 'Save', 'data manager term add', 'es' ); ?>
                <?php else : ?>
	                <?php echo _x( 'Add', 'data manager term add', 'es' ); ?>
                <?php endif; ?>
            </button>
            <?php $this->form_meta_fields(); ?>
        </form>
        <?php
    }

    /**
     * Render term field.
     *
     * @return void
     */
    public function term_field() {
        es_framework_field_render( 'term_name', array(
            'type' => 'text',
            'attributes' => array(
                'class' => 'es-field__input js-es-term-name',
            ),
            'label' => _x( 'Add new option', 'data manager term add', 'es' ),
            'value' => $this->_form_term instanceof WP_Term ? $this->_form_term->name : '',
        ) );
    }

	/**
	 * @return array
	 */
    public function get_terms() {
        return apply_filters( 'es_terms_creator_get_terms', es_get_terms_list( $this->_taxonomy ), $this->_taxonomy, $this );
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

    public function get_id() {
        return $this->_taxonomy;
    }

    /**
     * @return void
     */
    public function classes() {}
    public function before_list() {}

	/**
	 * Render taxonomy creator form.
	 *
	 * @return void
	 */
	public function render() {
		/* translators: %d: options num. */
		if ( $taxonomy = $this->get_taxonomy_instance() ) : $message = __( 'Are you sure you want to delete %d Option(s)?', 'es' ); ?>
			<div class="es-terms-creator js-es-terms-creator <?php $this->classes(); ?>" id="es-terms-<?php echo $this->get_id(); ?>-creator">
				<h4><?php echo $this->get_title(); ?></h4>
                <?php $this->before_list(); ?>
                <ul class="es-items js-es-terms">
                    <?php $this->render_list(); ?>
                </ul>
                <a href="" data-taxonomy="<?php echo $this->_taxonomy; ?>" data-message="<?php echo esc_attr( $message ); ?>" class="es-terms-selected-delete js-es-terms-selected-delete es-hidden">
                    <?php _e( 'Delete selected options', 'es' ); ?>
                </a>

				<div class="es-terms-creator__form js-es-terms-creator__form">
					<?php $this->render_form(); ?>
				</div>
			</div>
		<?php else :
			/* translators: %s: tax name. */?>
			<p><?php printf( __( "Taxonomy <b>%s</b> doesn't exist.", 'es' ), $this->_taxonomy ); ?></p>
		<?php endif;
	}
}
