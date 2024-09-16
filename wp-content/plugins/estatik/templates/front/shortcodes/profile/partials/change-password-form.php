<form action="#" method="POST" class="js-es-ajax-form js-es-confirm-by-pwd">
	<?php es_framework_field_render( 'es_current_password', array(
		'label' => __( 'Verify current password', 'es' ),
		'type' => 'password',
		'skeleton' => $skeleton,
		'attributes' => array(
			'required' => 'required',
		),
	) );

	es_framework_field_render( 'es_new_password', array(
		'label'       => __( 'New password', 'es' ),
		'type'        => 'password',
		'attributes' => array(
			'class' => 'js-es-password-field',
			'data-email' => $user_entity->get_email(),
			'required' => 'required',
		),
		'skeleton' => $skeleton,
		'description' => "<ul class='es-field__validate-list'>
                                <li class='es-validate-item es-validate-item__contain'>" . __( 'Can\'t contain the name or email address', 'es' ) . "</li>
                                <li class='es-validate-item es-validate-item__length'>" . __( 'At least 8 characters', 'es' ) . "</li>
                                <li class='es-validate-item es-validate-item__char'>" . __( 'Contains a number or symbol', 'es' ) . "</li>
                            </ul>",
	) );

	es_framework_field_render( 'es_confirm_password', array(
		'label'       => __( 'Confirm new password', 'es' ),
		'type'        => 'password',
		'attributes' => array(
			'required' => 'required',
		),
		'skeleton' => $skeleton,
	) ); ?>

	<input type="hidden" name="action" value="es_profile_save_pwd"/>
	<?php wp_nonce_field( 'es_profile_save_pwd', 'es_profile_pwd_nonce' ); ?>
	<button type="submit" class="es-btn es-btn--primary"><?php _e( 'Update password', 'es' ); ?></button>
</form>