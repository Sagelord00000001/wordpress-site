<?php

/**
 * @var $attachments_ids array
 */

if ( ! empty( $attachments_ids ) ) : ?>
    <div class="es-documents">
        <?php foreach ( $attachments_ids as $attachment_id ) :
            $filename = basename( get_attached_file( $attachment_id ) );
            $filesize = size_format( filesize( get_attached_file( $attachment_id ) ), 2 );
            $caption = wp_get_attachment_caption( $attachment_id ); ?>
            <div class="es-document">
                <span class="es-icon es-icon_file"></span>
                <div class="es-document__info">
                    <?php if ( ! empty( $caption ) ) : ?>
                        <span class="es-document__title es-document__title--caption"><?php echo $caption; ?></span>
                        <ul class="es-listing__terms">
                            <li><?php echo $filename; ?></li>
                            <li><?php echo $filesize; ?></li>
                        </ul>
                    <?php else : ?>
                        <span class="es-document__title"><?php echo $filename; ?></span>
                        <span><?php echo $filesize; ?></span>
                    <?php endif; ?>
                </div>
                <a target="_blank" href="<?php echo wp_get_attachment_url( $attachment_id ); ?>" class="es-secondary-color">
                    <span class="es-icon es-icon_download"></span>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif;
