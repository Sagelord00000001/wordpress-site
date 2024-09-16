<?php

/**
 * @var $args array
 * @var $shortcode_instance Es_Request_Form_Shortcode
 * @var $attributes array
 */

$id = uniqid(); ?>
<div class="es-component">
    <form method="post" class="js-es-request-form es-request-form">
		<?php do_action( 'es_before_request_form', $args ); ?>
        <div class="es-request-form__fields">
			<?php if ( $fields = $shortcode_instance->get_fields_config() ) :
				foreach ( $fields as $field_key => $field_config ) :
					if ( $field_key == 'name' && ! empty( $attributes['disable_name'] ) ) continue;
					if ( $field_key == 'phone' && ! empty( $attributes['disable_tel'] ) ) continue;
					if ( $field_key == 'email' && ! empty( $attributes['disable_email'] ) ) continue;

					es_framework_field_render( $field_key, $field_config );
				endforeach;
			endif;

			wp_nonce_field( 'es_submit_request_form', 'es_request_form_nonce_' . $id ); ?>

			<?php do_action( 'es_recaptcha', 'request_form' ); ?>
            <input type="hidden" name="uniqid" value="<?php echo $id; ?>">

            <input type="hidden" name="action" value="es_submit_request_form"/>

			<?php if ( ! empty( $attributes['post_id'] ) ) : ?>
                <input type="hidden" name="post_id" value="<?php echo esc_attr( $attributes['post_id'] ); ?>"/>
			<?php endif; ?>

			<?php if ( ! empty( $attributes['subject'] ) ) : ?>
                <input type="hidden" name="subject" value="<?php echo esc_attr( $attributes['subject'] ); ?>"/>
			<?php endif; ?>

            <input type="hidden" name="recipient_type" value="<?php echo esc_attr( $attributes['recipient_type'] ); ?>">

			<?php if ( ! empty( $attributes['custom_email'] ) ) : ?>
                <input type="hidden" name="send_to_emails" value="<?php echo esc_attr( $attributes['custom_email'] ); ?>"/>
			<?php endif; ?>

			<?php do_action( 'es_before_request_form_submit_button', $args ); ?>

			<?php do_action( 'es_privacy_policy', 'request_form' ); ?>
			
            <div class="es-btn-wrapper es-btn-wrapper--center es-btn-wrapper-submit--margin">
                <button type="submit" class="es-btn es-btn--primary js-es-request-form-submit"><?php echo $attributes['button_text']; ?></button>
            </div>

			<?php do_action( 'es_after_request_form', $args ); ?>
        </div>
    </form>
</div>
