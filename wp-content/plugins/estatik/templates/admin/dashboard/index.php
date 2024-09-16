<?php
/**
 * @var $links array
 * @var $posts array
 * @var $products array
 * @var $services array
 * @var $changelog array
 */
?>
<div class="es-wrap es-dashboard">
    <div class="wrap">
        <div class="es-head">
            <h1><?php _e( 'Dashboard', 'es' ); ?></h1>
            <div class="es-head__logo">
                <?php do_action( 'es_logo' ); ?>
            </div>
        </div>

        <div class="js-es-notifications"><?php echo es_notification_admin(); ?></div>

        <div class="es-row es-dashboard-nav">
            <?php foreach ( $links as $id => $link ) :
                $classes = 'es-box es-box--shadowed es-box--' . $id;
                if ( ! empty( $link['disabled'] ) ) : $classes .= ' es-box--disabled'; endif; ?>

                <a href="<?php echo $link['url']; ?>" class="es-col-lg-3 es-col-md-4 es-col-sm-6">
                    <div class="<?php echo $classes; ?>">
                        <?php if ( ! empty( $link['icon'] ) ) : echo $link['icon']; endif; ?>
                        <h2 class="es-box__title"><?php echo $link['name']; ?></h2>
                        <?php if ( ! empty( $link['label'] ) ) : ?><?php echo $link['label']; ?><?php endif; ?>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>

<!--        --><?php //include es_locate_template( 'admin/dashboard/partials/themes.php' ); ?>

        <div class="es-info-container">
            <div class="es-row">
                <div class="es-col-lg-4 es-col-sm-6">
                    <h3><?php _e( 'Sales & News', 'es' ); ?></h3>
                    <div class="es-articles">
                        <?php if ( $posts ) : ?>
                            <?php foreach ( $posts as $post ) : ?>
                                <div class="es-article">
                                    <span class="es-article__date"><?php echo date( 'Y-m-d', strtotime( $post->modified ) ); ?></span>
                                    <a target="_blank" href="<?php echo esc_url( $post->link ); ?>" class="es-article__title"><?php echo $post->title->rendered; ?></a>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="es-col-lg-4 es-col-sm-6">
                    <h3><?php _e( 'Services', 'es' ); ?></h3>
                    <div class="es-services">
                        <?php foreach ( $services as $service ) : ?>
                            <div class="es-service">
                                <a href="<?php echo esc_url( $service['link'] ); ?>" target="_blank"><?php echo $service['title']; ?></a>
                                <?php if ( ! empty( $service['text'] ) ) : ?>
                                    <p><?php echo $service['text']; ?></p>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php if ( ! empty( $changelog ) ) : ?>
                    <div class="es-col-lg-4 es-col-sm-6">
                        <h3><?php _e( 'Changelog', 'es' ); ?></h3>
                        <div class="es-changelog-container">
                            <?php foreach ( $changelog as $version => $log ) : ?>
                            <div class="es-release">
                                <div class="es-release__header">
                                    <span class="es-release__version"><?php echo $version; ?></span>
                                    <?php if ( ! empty( $log['date'] ) ) : ?>
                                        <span class="es-release__date"><?php echo $log['date']; ?></span>
                                    <?php endif; ?>
                                </div>
                                <?php if ( ! empty( $log['changes'] ) ) : ?>
                                    <ul class="es-changelog-list">
                                        <?php foreach ( $log['changes'] as $item ) : ?>
                                            <li class="es-changelog">
                                                <div class="es-label__wrap">
                                                    <span class="es-label es-label--<?php echo $item['label'] == 'bugfix' ? 'gray' : 'black'; ?>">
                                                        <?php echo $item['label']; ?>
                                                    </span>
                                                </div>
                                                <div class="es-changelog__text"><?php echo $item['text']; ?></div>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="es-upgrade-container">
        <div class="wrap">
            <h2><?php _e( 'Check out other <br>Estatik Real Estate Web Solutions', 'es' ); ?></h2>

            <div class="es-row" style="justify-content: center;">
                <div class="es-col-md-4 es-col-sm-6">
                    <div class="es-upgrade-item">
                        <span class="es-icon es-icon_simple es-icon--rounded"></span>
                        <h4><?php _e( 'PRO', 'es' ); ?></h4>
                        <p><?php _e( 'Unlock advanced features like PDF flyer, Compare, Frontend Submission, Agents & Agencies, Subscriptions or one-time payments, CSV/XML import via WP ALL Import, White Label, Slideshow widgets, and others.', 'es' ); ?></p>
                        <a href="https://estatik.net/choose-your-version/" target="_blank" class="es-btn es-btn--secondary"><?php _e( 'Upgrade', 'es' ); ?></a>
                    </div>
                </div>
                <div class="es-col-md-4 es-col-sm-6">
                    <div class="es-upgrade-item">
                        <span class="es-icon es-icon_premium es-icon--rounded"></span>
                        <h4><?php _e( 'Premium', 'es' ); ?></h4>
                        <p><?php printf( __( 'Import listings from your MLS via RETS, Web API or CREA DDF facility. Plugin setup service is included. Sit back and let us handle everything! Click <a href="%s" target="%s">here</a> to read details.', 'es' ), 'https://estatik.net/rets-and-api-listings-import/', '_blank' ); ?></p>
                        <a href="https://estatik.net/choose-your-version/" target="_blank" class="es-btn es-btn--secondary"><?php _e( 'Upgrade', 'es' ); ?></a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php es_load_template( 'admin/partials/help.php' ); ?>
</div>
