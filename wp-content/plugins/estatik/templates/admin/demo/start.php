<?php

/**
 * @var $products array
 * @var $features array
 */

?>
<div class="es-demo__header">
    <img class="es-logo" src="<?php echo ES_PLUGIN_URL . 'admin/images/estatik-logo.svg'; ?>" alt="Estatik Logo"/>

    <h1><?php _e( 'Welcome to Estatik', 'es' ); ?></h1>
    <p><?php _e( 'Thank you for choosing Estatik. Grab your instant 5% OFF for<br> any Estatik product using coupon code below.', 'es' ); ?></p>

    <div class="es-coupon-wrapper">
        <div class="es-coupon">
            <div class="es-coupon__left">
                <span><?php _e( '5% OFF', 'es' ); ?></span>
                <b><?php _e( 'coupon code', 'es' ); ?></b>
            </div>
            <div class="es-coupon__right">
                <b>THANKYOU</b>
                <span data-clipboard-text="THANKYOU" class="js-es-copy es-icon es-icon_copy"></span>
            </div>
        </div>
    </div>

    <a href="<?php echo esc_url( add_query_arg( 'step', 'start', admin_url( 'admin.php?page=es_demo' ) ) ); ?>#step1" class="es-btn es-btn--large es-btn--primary"><?php _e( 'adjust plugin to your needs', 'es' ); ?></a>
    <sup class="es-small-text"><?php _e( '4-minute setup with explanations', 'es' ); ?></sup>
</div>

<div class="es-price-table__wrapper">
    <div class="es-price-table">
        <div class="es-price-table__header">
            <h2><?php _e( 'Compare and choose <br>your version of Estatik', 'es' ); ?></h2>
            <?php foreach ( $products as $key => $product ) : ?>
                <div class="es-product es-product--<?php echo $key; ?>">
                    <?php echo $product['icon']; ?>
                    <h3><?php echo $product['label']; ?></h3>
                </div>
            <?php endforeach; ?>
        </div>

        <?php foreach ( $features as $feature_name => $supports ) : ?>
            <div class="es-feature">
                <div class="es-feature__inner">
                    <b><?php echo $feature_name; ?></b>
                    <?php foreach ( array_keys( $products ) as $product_key ) : ?>
                        <div class="es-feature__support es-feature__support--<?php echo $product_key; ?>">
                            <?php if ( ! empty( $supports[ $product_key ] ) ) : ?>
                                <span class="es-icon es-icon_check-mark"></span>
                            <?php else : ?>
                                <span class="es-icon es-icon_minus"></span>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>

        <div class="es-price-table__footer">
            <div class="es-price-table__footer--inner">
                <div class="empty"></div>
                <?php foreach ( $products as $key => $product ) : ?>
                    <div class="es-price-wrap es-price-wrap--<?php echo $key; ?>">
                        <?php if ( ! empty( $product['price'] ) ) : ?>
                            <span class="es-price"><?php echo $product['price']; ?></span>
                        <?php endif; ?>
                        <?php if ( $key == 'simple' ) : ?>
                            <sup class="es-small-text"><?php echo __( 'Your version now', 'es' ); ?></sup>
                        <?php endif; ?>
                        <?php if ( ! empty( $product['link'] ) ) : ?>
                            <a href="<?php echo esc_url( $product['link'] ); ?>" class="es-btn es-btn--secondary"><?php _e( 'Upgrade', 'es' ); ?></a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<?php es_load_template( 'admin/partials/help.php' ); ?>
