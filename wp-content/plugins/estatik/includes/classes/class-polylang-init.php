<?php

/**
 * Class Es_Pll_Init
 */
class Es_Pll_Integration {

	/**
	 * @return void
	 */
	public static function init() {
		add_action( 'rest_api_init', array( 'Es_Pll_Integration', 'copy_attachments_handler' ), 2 );
		add_action( 'add_meta_boxes', array( 'Es_Pll_Integration', 'copy_attachments_handler' ), 5 );
		add_filter( 'wp_generate_attachment_metadata', array( 'Es_Pll_Integration', 'wp_generate_attachment_metadata'), 10, 2 );
        add_action( 'pll_save_post', array( 'Es_Pll_Integration', 'save_post_attachments' ), 10, 3 );
	}

	/**
	 * @param $post
	 * @param $from_post_id
	 * @param $new_lang_slug
	 */
	public static function copy_featured_image( $post, $from_post_id, $new_lang_slug ) {
		if ( has_post_thumbnail( $from_post_id ) ) {
			$post_thumbnail_id = get_post_thumbnail_id( $from_post_id );
			if ( PLL()->model->options['media_support'] ) {
				$post_thumbnail_id = PLL()->model->post->get_translation( $post_thumbnail_id, $new_lang_slug );
                if ( $post_thumbnail_id ) {
	                set_post_thumbnail( $post, $post_thumbnail_id );
                }
			}
		}
	}

	/**
	 * @param $post_id
	 * @param $post
	 * @param $pll_tr
	 */
    public static function save_post_attachments( $post_id, $post, $pll_tr ) {
        if ( ! function_exists( 'PLL' ) ) {
            return;
        }

	    if ( ! PLL()->model->options['media_support'] ) return;

        if ( isset( $_GET['from_post'] ) || ( isset( $_POST['action'] ) && $_POST['action'] == 'editpost' ) ) {
	        return;
        }

	    if ( ! ( $post instanceof WP_Post ) || ! PLL()->model->is_translated_post_type( $post->post_type ) || ! in_array( $post->post_type, es_builders_supported_post_types() ) ) {
		    return;
	    }

	    $entity = es_get_entity_by_id( $post_id );

        if ( ! $entity ) {
            return;
        }

        $def_lang = pll_default_language();

        if ( $def_lang && is_array( $pll_tr ) && ! empty( $pll_tr[ $def_lang ] ) && ( $lang = PLL()->model->post->get_language( $post_id ) ) ) {
            if ( $post_id != $pll_tr[ $def_lang ] && ! get_post_meta( $post_id, 'es_pll_attachments_migrated', true ) ) {
	            static::copy_attached_media( $post, $pll_tr[ $def_lang ], $lang );
                static::copy_featured_image( $post, $pll_tr[ $def_lang ], $lang );
	            update_post_meta( $post_id, 'es_pll_attachments_migrated', 1 );
            }
        }
    }

	/**
	 * Copy property attachments to translations handler.
     *
     * @return void
	 */
	public static function copy_attachments_handler() {
        if ( ! function_exists( 'PLL' ) ) return;

		if ( ! PLL()->model->options['media_support'] ) return;

		if ( isset( $GLOBALS['pagenow'] ) && $GLOBALS['pagenow'] == 'post-new.php' && isset( $_GET['from_post'], $_GET['new_lang'] ) ) {
			global $post;

			if ( ! ( $post instanceof WP_Post ) || ! PLL()->model->is_translated_post_type( $post->post_type ) || ! in_array( $post->post_type, es_builders_supported_post_types() ) ) {
				return;
			}

			$from_post_id = es_get( 'from_post', 'intval' );
			$new_lang     = PLL()->model->get_language( es_get( 'new_lang' ) );

			// Copy all attachments to translated post.
			if ( static::copy_attached_media( $post, $from_post_id, $new_lang->slug ) ) {
				add_action( 'admin_notices', array( 'Es_Pll_Integration', 'success_notice' ) );
            }
		}
	}

	/**
	 * Media copied admin notice.
     *
     * @return void
	 */
    public static function success_notice() {
	    $from_post_id = es_get( 'from_post', 'intval' );
        $lang = pll_get_post_language( $from_post_id, 'name' );
        $post_name = get_the_title( $from_post_id ) ? get_the_title( $from_post_id ) : '#' . $from_post_id; ?>
        <div class="notice notice-success is-dismissible">
            <p><?php /* translators: %1$s: post name, %2$s: lang. */
                printf( __( 'Attachments successfully copied from %1$s (in %2$s)', 'es' ), $post_name, $lang ); ?></p>
        </div>
	    <?php
    }

	/**
	 * @param $post
	 * @param $from_post_id
	 * @param $new_lang_slug
	 *
	 * @return bool
	 */
	public static function copy_attached_media( $post, $from_post_id, $new_lang_slug ) {
		if ( PLL()->model->options['media_support'] ) {
			if ( $new_lang_slug instanceof PLL_Language ) {
				$new_lang_slug = $new_lang_slug->slug;
			}

			$from_lang = pll_get_post_language( $from_post_id );
			$args = array(
				'post_type' => 'attachment',
				'posts_per_page' => -1,
				'post_parent' => $from_post_id,
				'post_status' => 'any',
				'lang' => $from_lang,
			);
			$attachments = get_posts( $args );

            if ( ! empty( $attachments ) ) {
                foreach ( $attachments as $attachment ) {
	                static::translate_attachment( $attachment->ID, $new_lang_slug, $post->ID );
                }

                return true;
            }
		}

        return false;
	}

	/**
	 * Translate attachment
	 *
	 * @param int $attachment_id id of the attachment in original language
	 * @param string $new_lang new language slug
	 * @param int $parent_id id of the parent of the translated attachments (post ID)
	 *
	 * @return int translated id
	 */
	public static function translate_attachment( $attachment_id, $new_lang, $parent_id ) {
        if ( ! function_exists( 'PLL' ) ) return false;

        if ( $new_lang instanceof PLL_Language ) {
            $new_lang = $new_lang->slug;
        }

		global $polylang_copy_content_attachment_cache;

		if ( empty( $polylang_copy_content_attachment_cache ) ) {
			$polylang_copy_content_attachment_cache = array();
		}

		// don't create multiple translations of same image on one request
		if ( isset( $polylang_copy_content_attachment_cache[ $attachment_id ] ) ) {
			return $polylang_copy_content_attachment_cache[ $attachment_id ];
		}

		$_post = get_post( $attachment_id );

		if ( empty( $_post ) || is_wp_error( $_post ) || $_post->post_type != 'attachment' ) {
			return $attachment_id;
		}

		$post_id = $_post->ID;

		// if there's existing translation, use it
		$existing_translation = pll_get_post( $post_id, $new_lang );

		if ( ! empty( $existing_translation ) ) {
            wp_update_post( array(
                'ID' => $existing_translation,
                'post_parent' => $parent_id,
            ) );

			return $existing_translation; // existing translated attachment
		}

		$_post->ID = null; // will force the creation
		$_post->post_parent = $parent_id ? $parent_id : 0;

		$tr_id = wp_insert_attachment( $_post );

        $meta_keys = array( '_wp_attachment_metadata', '_wp_attached_file', '_wp_attachment_image_alt',
            'es_attachment_type', 'es_attachment_order' );

        foreach ( $meta_keys as $key ) {
	        $value = get_post_meta( $post_id, $key, true );
            if ( $value || $key == 'es_attachment_order' ) {
                $value = ! $value ? 99 : $value;
                add_post_meta( $tr_id, $key, $value );
            }
        }

		// set language of the attachment
		PLL()->model->post->set_language( $tr_id, $new_lang );
		$translations = PLL()->model->post->get_translations( $post_id );

		if ( ! $translations && $lang = PLL()->model->post->get_language( $post_id ) ) {
			$translations[ $lang->slug ] = $post_id;
		}

		$translations[ $new_lang ] = $tr_id;
		PLL()->model->post->save_translations( $tr_id, $translations );

		// save ids to cache for multiple calls in same request
		$polylang_copy_content_attachment_cache[ $attachment_id ] = $tr_id;

		return $tr_id;
	}

	/**
	 * @param $metadata
	 * @param $attachment_id
	 *
	 * @return mixed
	 */
	public static function wp_generate_attachment_metadata( $metadata, $attachment_id ) {
        if ( ! function_exists( 'PLL' ) ) return $metadata;

		$attachment_lang = PLL()->model->post->get_language( $attachment_id );
		$translations = PLL()->model->post->get_translations( $attachment_id );

		foreach ( $translations as $lang => $tr_id ) {
			if ( ! $tr_id ) continue;

			if ( $attachment_lang->slug !== $lang ) {
				update_post_meta( $tr_id, '_wp_attachment_metadata', $metadata );
			}
		}

		return $metadata;
	}
}

Es_Pll_Integration::init();
