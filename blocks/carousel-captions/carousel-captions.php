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
        
    </div>