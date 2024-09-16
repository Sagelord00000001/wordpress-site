<?php

/**
 * @var $user_entity Es_User
 * @var $tabs array
 */

$flashes = es_get_flash_instance( 'profile' );
$current_tab = filter_input( INPUT_GET, 'tab' ); ?>

<div class="es-wrap et_smooth_scroll_disabled">
    <div class="es-profile js-es-profile">
        <div class="es-profile__flashes"><?php $flashes->render_messages(); ?></div>
        <div class="es-profile__nav-bar">
            <div class="es-profile__nav-bar__user">
                <div class="es-profile__image">
                    <a href="<?php echo esc_url( add_query_arg( array( 'tab' => 'user' ), get_the_permalink() ) ); ?>">
						<?php echo get_avatar( $user_entity->get_id() ); ?>
                    </a>
                </div>
                <b class="es-user__name">
                    <a href="<?php echo esc_url( add_query_arg( array( 'tab' => 'user' ), get_the_permalink() ) ); ?>">
						<?php echo $user_entity->get_full_name() ?
							$user_entity->get_full_name() : $user_entity->get_email(); ?>
                    </a>
                </b>
            </div>

            <form action="" method="get">
	            <?php if ( ! get_option( 'permalink_structure' ) && get_the_ID() ) : ?>
                    <input type="hidden" name="page_id" value="<?php echo get_the_ID(); ?>"/>
	            <?php endif;

                es_framework_field_render( 'tab', array(
					'type' => 'select',
					'options' => array_merge( array( '' => __( 'Profile', 'es' ) ), wp_list_pluck( $tabs, 'label', 'id' ) ),
					'attributes' => array(
						'class' => 'js-es-submit-on-change',
					),
					'value' => $current_tab,
                    'before' => '<div>',
                    'after' => '</div>',
				) ); ?>

                <a class="profile-logout es-secondary-color" href="<?php echo wp_logout_url() ?>"><span class="es-icon es-icon_logout"></span></a>
            </form>
        </div>
        <div class="es-profile__sidebar">
            <a href="<?php echo esc_url( add_query_arg( array( 'tab' => 'user' ), get_the_permalink() ) ); ?>">
                <div class="es-profile__sidebar__user">
                    <div class="es-profile__image">
						<?php echo get_avatar( $user_entity->get_id() ); ?>
                    </div>
                    <b class="es-user__name">
						<?php echo $user_entity->get_full_name() ?
							$user_entity->get_full_name() : $user_entity->get_email(); ?>
                    </b>
                </div>
            </a>
            <ul class="es-profile__menu">
				<?php foreach ( $tabs as $id => $tab_config ) : ?>
                    <li class="<?php es_active_class( $id, $current_tab, 'active' ); ?>">
                        <a href="<?php echo add_query_arg( 'tab',  $id, get_the_permalink() ); ?>">
							<?php echo $tab_config['icon'] . $tab_config['label']; ?>
							<?php if ( ! empty( $tab_config['counter'] ) ) : ?>
                                <span class="es-counter es-primary-bg"><?php echo $tab_config['counter']; ?></span>
							<?php endif; ?>
                        </a>
                    </li>
				<?php endforeach; ?>
                <li><a href="<?php echo esc_url( wp_logout_url( es_get_current_url() ) ); ?>">
                        <span class="es-icon es-icon_logout"></span><?php echo __( 'Log out', 'es' ); ?></a></li>
            </ul>
        </div>
        <div class="es-profile__main">
			<?php if ( ! empty( $tabs[ $current_tab ]['template'] ) ) : $tab_config = $tabs[ $current_tab ]; ?>
				<?php include( $tab_config['template'] ); ?>
			<?php else:
				$current_tab = 'user';
				$template_path = apply_filters( 'es_profile_form_tab_template_path',
					'front/shortcodes/profile/tabs/profile-form.php' );

				include es_locate_template( $template_path ); ?>
			<?php endif; ?>
        </div>
    </div>
	<?php do_action( 'es_after_profile' ); ?>
</div>