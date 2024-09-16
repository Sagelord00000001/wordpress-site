<?php if ( ! empty( $attachments_ids ) ) : ?>
    <div class="es-images js-es-images">
        <?php foreach ( $attachments_ids as $attachment_id ) : ?>
            <div class="es-image">
                <a class="js-es-image" href="<?php echo wp_get_attachment_image_url( $attachment_id, 'full' ); ?>">
                    <div class="es-image__background" style="background-image: url('<?php echo wp_get_attachment_image_url( $attachment_id, 'medium' ); ?>'); background-size: cover; background-repeat: no-repeat"></div>
                    <?php if ( $caption = wp_get_attachment_caption( $attachment_id ) ) : ?>
                        <span class="es-image__name"><?php echo $caption; ?></span>
                    <?php endif; ?>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif;
