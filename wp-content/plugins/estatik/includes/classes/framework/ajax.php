<?php

add_action( 'wp_ajax_es_framework_attachment_save_caption', 'es_framework_ajax_save_attachment_caption' );

/**
 * Update image caption.
 *
 * @return void
 */
function es_framework_ajax_save_attachment_caption() {

	$action = 'es_framework_attachment_save_caption';

	if ( check_ajax_referer( $action, 'nonce' ) ) {
		$post_id = intval( filter_input( INPUT_POST, 'attachment_id' ) );
		$caption = sanitize_text_field( filter_input( INPUT_POST, 'caption' ) );
		$attachment = get_post( $post_id );

		if ( $post_id && ! empty( $attachment->post_type ) && $attachment->post_type == 'attachment'  ) {
			if ( current_user_can( 'edit_post', $post_id ) ) {
				wp_update_post( array(
					'ID' => $post_id,
					'post_excerpt' => $caption,
				) );
			}
		}
	}

	wp_die();
}

add_action( 'wp_ajax_es_framework_upload_file', 'es_framework_ajax_upload_file' );

/**
 * Upload file as attachment on the server.
 *
 * @return void
 */
function es_framework_ajax_upload_file() {
	$action = 'es_framework_upload_file';

	$response = array(
		'status' => 'error',
		'message' => __( 'Invalid security nonce. Please, reload the page and try again.', 'es' ),
	);

	if ( check_ajax_referer( $action, '_file-nonce' ) ) {
		if ( ! empty( $_FILES['file'] ) ) {
			if ( ! function_exists( 'wp_handle_upload' ) ) {
				require_once( ABSPATH . 'wp-admin/includes/image.php' );
				require_once( ABSPATH . 'wp-admin/includes/file.php' );
				require_once( ABSPATH . 'wp-admin/includes/media.php' );
			}

			if ( $attachment_id = media_handle_sideload( $_FILES['file'], -1 ) ) {
				if ( ! $attachment_id instanceof WP_Error ) {
					if ( ! empty( $_POST['file_type'] ) ) {
						$file_type = sanitize_text_field( $_POST['file_type'] );
						update_post_meta( $attachment_id, 'esf_file_type', $file_type );
						wp_update_post( array( 'ID' => $attachment_id, 'post_status' => 'private' ) );
					}

					$response = array(
						'attachment_id' => $attachment_id,
						'status' => 'success',
						'attachment_url' => wp_attachment_is_image( $attachment_id ) ?
							wp_get_attachment_image_url( $attachment_id, 'thumbnail' ) : wp_get_attachment_url( $attachment_id )
					);
				} else {
					$response = array(
						'status' => 'error',
						'message' => $attachment_id->get_error_message(),
					);
				}
			}
		}
	}

	wp_die( json_encode( $response ) );
}