<div class="es-products-container">
	<h2><?php _e( 'Estatik themes & freebies', 'es' ); ?></h2>
	<div class="es-product-slick-themes js-es-slick " data-slick="<?php echo es_esc_json_attr( array(
		'infinite' => true,
		'arrows' => true,
		'dots' => false,
		'slidesToShow' => 4,
		'prevArrow' => '<span class="slick-prev es-icon es-icon_chevron-left"></span>',
		'nextArrow' => '<span class="slick-next es-icon es-icon_chevron-right"></span>',
		'navText' => '<span class="es-icon es-icon_chevron-right"></span>',
		'margin' => 30,
		'slide' => 'div',
		'rows' => 0,
		'responsive' => array(
			array(
				'breakpoint' => 600,
				'settings' => array(
					'slidesToShow' => 1,
				),
			),
			array(
				'breakpoint' => 1000,
				'settings' => array(
					'slidesToShow' => 3,
				),
			),
			array(
				'breakpoint' => 700,
				'settings' => array(
					'slidesToShow' => 2,
				),
			),
		)
	) ); ?>">
		<?php foreach ( $products as $id => $product ) : ?>
			<div class="item">
				<div class="es-product">
					<?php if ( ! empty( $product['free'] ) ) : ?>
						<span class="es-label es-label--green">FREE</span>
					<?php endif; ?>
					<div class="es-product__image">
						<img src="<?php echo $product['image_url']; ?>" alt=""/>
						<div class="es-control">
							<?php if ( ! empty( $product['demo_link'] ) ) : ?>
								<a target="_blank" href="<?php echo $product['demo_link']; ?>" class="es-preview"><?php _e( 'Preview', 'es' ); ?></a>
							<?php endif; ?>
							<a target="_blank" href="<?php echo $product['link']; ?>" class="es-btn es-btn--third es-btn--small"><?php _e( 'Details', 'es' ); ?></a>
						</div>
					</div>
					<a target="_blank" href="<?php echo $product['link']; ?>" class="es-link"><?php echo $product['name']; ?></a>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
</div>