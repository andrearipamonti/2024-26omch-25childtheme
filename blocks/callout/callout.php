<?php

// Cosa è un callout? https://www.w3schools.com/howto/howto_js_callout.asp 

// Recupera i campi ACF
$title = get_field('callout_title');
$text = get_field('callout_text');
$link = get_field('callout_link');

    // ancora html
    $anchor = '';
    if ( ! empty( $block['anchor'] ) ) {
        $anchor = esc_attr( $block['anchor'] );
    }

    $class_name = 'callout';
    if ( ! empty( $block['className'] ) ) {
        $class_name .= ' ' . $block['className'];
    }
?>

<div id="<?php echo $anchor ?>" class="<?php echo $class_name ?>">
    <button class="callout__close" type="button" aria-label="Chiudi callout">
        <span class="callout__close-icon">&times;</span>
    </button>
    <?php if ( $title ) : ?>
        <h2 class="callout__title"><?php echo esc_html( $title ); ?></h2>
    <?php endif; ?>
    <?php if ( $text ) : ?>
        <p class="callout__text"><?php echo esc_html( $text ); ?></p>
    <?php endif; ?>
    <?php if ( $link ) : ?>
        <a class="callout__link" href="<?php echo esc_url( $link['url'] ); ?>" target="<?php echo esc_attr( $link['target'] ); ?>">
            <?php echo esc_html( $link['title'] ); ?>
        </a>
    <?php endif; ?>
</div>