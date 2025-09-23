<?php

    $images = get_field('carousel');

    // ancora html
    $anchor = '';
    if ( ! empty( $block['anchor'] ) ) {
        $anchor = esc_attr( $block['anchor'] );
    }

    $class_name = 'carousel';
    if ( ! empty( $block['className'] ) ) {
        $class_name .= ' ' . $block['className'];
    }

?>
    
    <div id="<?php echo $anchor ?>" class="<?php echo $class_name ?>">
        <?php 
            foreach( $images as $image ): 
                echo wp_get_attachment_image( $image['ID'], 'full' );
            endforeach; 
        ?>
    </div>