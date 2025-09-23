<?php
    // ancora html
    $anchor = '';
    if ( ! empty( $block['anchor'] ) ) {
        $anchor = esc_attr( $block['anchor'] );
    }

    $class_name = 'carousel-captions';
    if ( ! empty( $block['className'] ) ) {
        $class_name .= ' ' . $block['className'];
    }

?>
    
    <div id="<?php echo $anchor ?>" class="<?php echo $class_name ?>">
        <?php if( have_rows('carousel_repeater') ): ?>
            <?php while( have_rows('carousel_repeater') ): the_row(); 
                $image = get_sub_field('carousel_repeater_img');
                $txt = get_sub_field('carousel_repeater_txt');
            ?>
                <div>
                    <?php echo wp_get_attachment_image( $image['ID'], 'full' ); ?>
                    <p><?php echo $txt; ?></p>
                </div>
            <?php endwhile; ?>
        <?php endif; ?>
    </div>