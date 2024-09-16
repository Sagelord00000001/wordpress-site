<?php

/**
 * Class Es_Tabs_View.
 */
class Es_Tabs_View extends Es_Framework_View {

	/**
	 * Es_Tabs_View constructor.
	 *
	 * @param $args
	 */
	public function __construct( $args ) {

		parent::__construct( $args );

		$default = array(
			'layout'   => 'vertical',
			'template' => Es_Framework::get_path() . 'templates' . DS . 'views' . DS . 'tabs.php',
			'nav_title' => '',
			'after_content_tabs' => '',
			'after_nav' => '',
			'ul_class' => '',
            'show_logo' => true,
            'before_content_tabs' => '',
            'use_data_attr_tab_id' => false,
		);

		$default['wrapper_nav_class'] = 'es-tabs__nav';
		$default['wrapper_tabs_class'] = 'es-tabs__wrapper';
		$default['wrapper_tab_class'] = 'es-tabs__content';
		$default['tab_link_wrapper'] = '<a class="es-tabs__nav-link" href="#{id}">{label}</a>';

		$this->_args = es_parse_args( $this->_args, $default );

		if ( empty( $this->_args['wrapper_class'] ) ) {
			$this->_args['wrapper_class'] = sprintf( "es-tabs es-tabs--%s", $this->_args['layout'] );
		}
	}

	/**
	 * Render tabs nav.
	 *
	 * @return void
	 */
	public function render_nav() {
		$config = $this->get_args(); ?>
		<ul class='<?php echo $config['ul_class']; ?>'>
			<?php foreach ( $config['tabs'] as $id => $item ) : ?>
				<li <?php echo ! empty( $item['li_attributes'] ) ? $item['li_attributes'] : ''; ?>>
					<?php if ( ! empty( $item['link_html'] ) ) : ?>
						<?php echo $item['link_html']; ?>
					<?php else : ?>
						<a class="es-tabs__nav-link" data-tab="<?php echo '#' . $id; ?>" href="<?php echo $config['use_data_attr_tab_id'] ? '' : '#' . $id; ?>"><?php echo $item['label']; ?></a>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>
		<?php
	}

	/**
	 * Render tabs UI element.
	 */
	public function render() {

		/**
		 * @var $this Es_Tabs_View
		 */
		$config = $this->get_args(); ?>

		<div class="js-es-tabs <?php echo $config['wrapper_class']; ?>">
			<div class="js-es-tabs__nav <?php echo $config['wrapper_nav_class']; ?>">
				<div class="es-tabs__nav-inner">
					<?php if ( ! empty( $config['nav_title'] ) ) : ?>
						<h1><?php echo $config['nav_title']; ?></h1>
					<?php endif;

					$this->render_nav();

					echo $config['after_nav']; ?>
				</div>
			</div>
			<div class="js-es-tabs__wrapper <?php echo $config['wrapper_tabs_class']; ?>">

				<?php if ( $config['show_logo'] ) do_action( 'es_logo' ); ?>
                <?php if ( ! empty( $config['before_content_tabs'] ) ) : ?>
                    <?php echo $config['before_content_tabs']; ?>
                <?php endif; ?>
				<?php foreach ( $config['tabs'] as $id => $item ) : ?>
					<div id="<?php echo $id; ?>" class="js-es-tabs__content es-hidden <?php echo $config['wrapper_tab_class']; ?>">
                        <?php echo ! empty( $item['before'] ) ? $item['before'] : '';

                        if ( ! empty( $item['template'] ) && file_exists( $item['template'] ) ) {
							include $item['template'];
						} else if ( ! empty( $item['action'] ) ) {
							do_action( $item['action'], $item, $id, $config );
						}

						echo ! empty( $item['after'] ) ? $item['after'] : ''; ?>
					</div>
				<?php endforeach; ?>

				<?php echo $config['after_content_tabs']; ?>
			</div>
		</div>
	<?php }
}
