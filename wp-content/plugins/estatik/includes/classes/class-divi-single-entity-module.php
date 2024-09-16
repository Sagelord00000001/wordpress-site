<?php

/**
 * Class Es_Divi_Single_Property_Builder_Module.
 */
class Es_Divi_Single_Entity_Builder_Module extends ET_Builder_Module_Type_PostContent {

	public $slug = 'es_single_entity_page';

	/**
	 * @return void
	 */
	public function init() {
		$this->name = esc_html__( 'Estatik Single Page', 'es' );
		$this->plural = esc_html__( 'Estatik Single Page', 'es' );
		$this->slug = 'es_single_entity_page';
		$this->vb_support = 'on';
		$this->post_types = array_merge( et_builder_get_builder_post_types(), es_builders_supported_post_types() );
	}

	public function get_fields() {
		return parent::get_fields();
	}

	/**
	 * @param array $unprocessed_props
	 * @param null $content
	 * @param string $render_slug
	 * @return bool|string|void|null
	 */
	public function render( $attrs, $content, $render_slug ) {
		$entity = es_get_entity_by_id( $this->get_the_ID() );

		if ( $entity ) {
			return do_shortcode( sprintf( "[es_single_%s]", $entity::get_entity_name() ) );
		} else {
			return $content;
		}

	}
}

new Es_Divi_Single_Entity_Builder_Module();
