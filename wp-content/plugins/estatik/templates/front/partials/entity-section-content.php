<?php

/**
 * @var $fields array
 * @var $section array
 * @var $entity_name string
 * @var $post_id int
 */

$wrapper_rendered = false;

foreach ( $fields as $field => $field_info ) :
    if ( ! es_is_visible( $field_info, $entity_name, 'field' ) ) continue;
    $formatter = ! empty( $field_info['formatter'] ) ? $field_info['formatter'] : 'default';

    if ( ! empty( $field_info['taxonomy'] ) ) {
        if ( $section['machine_name'] == 'features' ) {
            $terms = get_the_terms( $post_id, $field );
            $class = ests( 'is_terms_icons_enabled' ) ? 'es-property-field__terms--icons' : '';
            $value = es_get_the_term_list( $post_id, $field, '<div class="es-property-field__terms ' . $class . '">', '', '</div>' );
            $need_terms_more_link = ! is_wp_error( $terms ) && $terms && count( $terms ) > 4 ? true : $need_terms_more_link;
        } else {
            $value = get_the_term_list( $post_id, $field, '', ', ' );
        }
    } else {
        $value = empty( $field_info['is_single_page_formatter_disabled'] ) ?
            es_get_the_formatted_field( $field, $post_id ) : es_get_the_field( $field, $post_id );
    }

    if ( ( is_string( $value ) || is_numeric( $value ) ) && strlen( $value ) ) :
        if ( ! $wrapper_rendered && empty( $field_info['ignore_field_wrapper'] ) ) : $wrapper_rendered = true; ?>
            <ul class='es-<?php echo $entity_name; ?>-fields es-entity-fields'>
        <?php endif;

        if ( ! empty( $field_info['ignore_field_wrapper'] ) ) : ?>
            <?php echo $value; ?>
        <?php else :
            $class_list = array( 'es-entity-field', "es-entity-field--{$field}", "es-{$entity_name}-field", "es-{$entity_name}-field--{$field}", "es-{$entity_name}-field--{$formatter}" );
            if ( ! empty( $field_info['is_full_width'] ) ) $class_list[] = 'es-entity-field--full-width'; ?>

            <li class='<?php echo implode( " ", $class_list ); ?>'>
                <?php if ( ! empty( $field_info['label'] ) ) :
                    $label = ! empty( $field_info['frontend_visible_name'] ) ? es_mulultilingual_translate_string( $field_info['frontend_visible_name'] ) : ( ! empty( $field_info['id'] ) ? __( $field_info['label'], 'es' ) : $field_info['label'] ); ?>
                    <?php printf( "<span class='es-{$entity_name}-field__label'>%s<span class='es-{$entity_name}-field__sep'>:</span> </span>", $label );
                endif;

                $show_all = ! empty( $field_info['show_more_label'] ) ? $field_info['show_more_label'] : __( 'Show all', 'es' );

                printf( "<span class='es-{$entity_name}-field__value es-entity-field__value'>%s</span>", $value );

                if ( $field == 'post_content' && $value && es_is_collapsed_description( $entity_name ) ) : ?>
                    <a href='#' class='es-hidden js-es-full-description-link es-full-content-link js-es-toggle-class es-secondary-color'
                                    data-container='.es-<?php echo $entity_name; ?>-field--post_content' data-toggle-label='<?php echo esc_attr( __( 'Hide description', 'es' ) . '<span class="es-icon es-icon_chevron-top"></span>' ); ?>'
                    data-class='es-entity-field--post_content--collapsed'><?php echo $show_all; ?><span class='es-icon es-icon_chevron-bottom'></span></a>
                <?php endif; ?>
            </li>
        <?php endif;
    endif;
endforeach;

if ( $wrapper_rendered && empty( $field_info['ignore_field_wrapper'] ) ) : ?>
    </ul>
<?php endif;
