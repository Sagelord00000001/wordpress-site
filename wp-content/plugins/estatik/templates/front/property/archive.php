<?php $template = get_option( 'template' );
get_header(); ?>

<div class="wrap">
    <div id="primary" class="content-area">
        <main id="main" class="site-main">
            <?php if ( have_posts() ) : ?>

                <header class="page-header">
                    <?php the_archive_title( '<h1 class="page-title">', '</h1>' ); ?>
                </header>

                <?php global $wp_query;
                /** @var Es_My_Listing_Shortcode $shortcode */
                $shortcode = es_get_shortcode_instance( 'es_my_listing', array(
                    'show_page_title' => false,
                ) );
                $shortcode->set_query( $wp_query );
                echo $shortcode->get_content(); ?>

            <?php endif; ?>
        </main>
    </div>
    <?php if ( is_active_sidebar( 'sidebar-1' ) ) : ?>
        <aside id="secondary" class="sidebar widget-area" role="complementary">
            <?php dynamic_sidebar( 'sidebar-1' ); ?>
        </aside><!-- .sidebar .widget-area -->
    <?php endif; ?>
</div>
<?php get_footer();
