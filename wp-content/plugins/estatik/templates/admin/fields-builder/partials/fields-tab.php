<?php /** @var $args array */ ?>
<h2><?php _e( 'Listing fields' ); ?></h2>

<?php if ( 'request_form' == $args['section_machine_name'] ) : ?>
    <p><?php _e( 'You can\'t add new fields to Request form section here. You can only use drag & drop feature and change its basic Settings on the right. ' ); ?></p>
<?php elseif ( ! empty( $section_fields ) ) : ?>
    <ul class="es-fields-list js-es-fields-list js-es-fields-builder-fields-list">
        <?php foreach ( $section_fields as $section_field ) : ?>
            <?php echo es_field_builder_get_field_markup( $section_field ); ?>
        <?php endforeach; ?>
    </ul>
<?php else : ?>
    <div class="js-es-fields-builder-remove">
        <p><?php _e( 'You don’t have any fields in this section yet.', 'es' ); ?></p>
        <button data-section-machine-name="<?php echo $args['section_machine_name']; ?>" class="es-btn es-btn--third js-es-fields-builder-add-field">
            <span class="es-icon es-icon_plus"></span>
		    <?php _e( 'Add field', 'es' ); ?>
        </button>
    </div>
    <ul class="es-fields-list js-es-fields-list js-es-fields-builder-fields-list"></ul>
<?php endif;
