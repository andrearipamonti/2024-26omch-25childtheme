<?php
/**
 * Componente FAQ - Accordion
 * Basato su: https://www.w3schools.com/howto/howto_js_accordion.asp
 * Usa ACF repeater field per domande/risposte e campo true/false per apertura multipla
 */

// Recupera i campi ACF
$faq_items = get_field('faq_items');
$allow_multiple = get_field('allow_multiple_open');
$faq_title = get_field('faq_title');

// Ancora HTML
$anchor = '';
if ( ! empty( $block['anchor'] ) ) {
    $anchor = esc_attr( $block['anchor'] );
}

$class_name = 'faq';
if ( ! empty( $block['className'] ) ) {
    $class_name .= ' ' . $block['className'];
}

// Aggiungi classe per apertura multipla
if ( $allow_multiple ) {
    $class_name .= ' faq--multiple';
}
?>

<div id="<?php echo $anchor ?>" class="<?php echo $class_name ?>" data-allow-multiple="<?php echo $allow_multiple ? 'true' : 'false'; ?>">
    <?php if ( $faq_title ) : ?>
        <h2 class="faq__title"><?php echo esc_html( $faq_title ); ?></h2>
    <?php endif; ?>
    
    <?php if ( $faq_items ) : ?>
        <div class="faq__container">
            <?php foreach ( $faq_items as $index => $item ) : 
                $question = $item['question'];
                $answer = $item['answer'];
                $item_id = 'faq-item-' . $index;
            ?>
                <div class="faq__item">
                    <button class="faq__question" type="button" aria-expanded="false" aria-controls="<?php echo $item_id; ?>">
                        <span class="faq__question-text"><?php echo esc_html( $question ); ?></span>
                        <span class="faq__icon" aria-hidden="true">+</span>
                    </button>
                    <div class="faq__answer" id="<?php echo $item_id; ?>" aria-hidden="true">
                        <div class="faq__answer-content">
                            <?php echo wp_kses_post( $answer ); ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>