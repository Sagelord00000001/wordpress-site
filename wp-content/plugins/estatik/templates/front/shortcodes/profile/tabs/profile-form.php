<?php

/**
 * @var $user_entity Es_User
 * @var $current_tab string
 */

$skeleton = "{before}
                   <div class='es-field es-field__{field_key} es-field--{type} {wrapper_class}'>
                       <label for='{id}'>{label}{caption}<div class='es-input__wrap'>{input}</div>{description}</label>
                   </div>
               {after}"; ?>
<div id="<?php echo $current_tab; ?>" class="es-profile__content es-profile__content--<?php echo $current_tab; ?>">
    <h2 class="heading-font"><?php _e( 'Edit profile', 'es' ); ?></h2>
    <form action="#" method="POST" enctype="multipart/form-data" class="js-es-ajax-form js-es-form-enable-on-change js-es-confirm-by-pwd">
        <?php es_framework_field_render( 'avatar_id', array(
            'type' => 'avatar',
            'image' => get_avatar( $user_entity->get_id(), 96 ),
            'upload_button_classes' => 'es-btn es-btn--secondary es-btn--bordered es-btn--upload-photo',
            'upload_button_label' => __( 'Upload profile photo', 'es' ),
            'exists_upload_button_label' => __( 'Upload new photo', 'es' ),
            'default_image' => "<img src='" . es_user_get_default_image_url( $user_entity->get_id() ) . "' class='avatar'>",
            'value' => $user_entity->avatar_id,
        ) );

        es_framework_field_render( 'es_user_name', array(
            'label' => __( 'Name', 'es' ),
            'value' => $user_entity->get_full_name(),
            'attributes' => array(
                'maxlength' => 50,
            )
        ) );

        es_framework_field_render( 'es_user_email', array(
            'label' => __( 'Email', 'es' ),
            'attributes' => array(
                'maxlength' => 256,
            ),
            'value' => $user_entity->get_email(),
            'description' => __( "You'll use it to sign in, and we'll use it to contact you", 'es' ),
        ) );

        if ( ! es_is_user_registered_via_social_network( $user_entity->get_id() ) ) :
            es_framework_field_render( 'es_confirm_password', array(
                'label'       => __( 'Confirm with password', 'es' ),
                'type'        => 'password',
                'attributes' => array(
                    'required' => 'required',
                    'id' => sprintf( '%s-%s', 'es_confirm_password', uniqid() ),
                ),
                'wrapper_class' => 'js-es-confirm-field es-hidden',
                'skeleton' => $skeleton,
            ) );
        endif;
        ?>

        <input type="hidden" name="action" value="es_profile_save_info"/>
        <?php wp_nonce_field( 'es_profile_save_info', 'es_profile_nonce' ); ?>
        <button type="submit" class="es-btn es-btn--primary" disabled><?php _e( 'Save changes', 'es' ); ?></button>
    </form>

    <?php if ( ! es_is_user_registered_via_social_network( $user_entity->get_id() ) ) : ?>
        <h3 class="heading-font es-profile-heading"><?php _e( 'Change Password', 'es' ); ?></h3>
        <?php include es_locate_template( 'front/shortcodes/profile/partials/change-password-form.php' ); ?>
    <?php endif; ?>
</div>