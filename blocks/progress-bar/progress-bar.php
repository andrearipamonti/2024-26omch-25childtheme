<?php

    $percentage  = get_field('progress_percentage');
    $color  = get_field('progress_color');

    // ancora html
    $anchor = '';
    if ( ! empty( $block['anchor'] ) ) {
        $anchor = esc_attr( $block['anchor'] );
    }

    $class_name = 'progress-bar';
    if ( ! empty( $block['className'] ) ) {
        $class_name .= ' ' . $block['className'];
    }

    if ( ! empty( $block['align'] ) ) {
        $class_name .= ' align' . $block['align'];
    }

?>

<div id="<?php echo $anchor ?>" class="<?php echo $class_name ?>">
    <span class="number"><?php echo $percentage ?><small>%</small></span>
    <span class="bar-container">
        <span class="bar" style="max-width: <?php echo esc_attr( $percentage ); ?>%; background-color: <?php echo esc_attr( $color ) ?>;"></span>
    </span>
</div>