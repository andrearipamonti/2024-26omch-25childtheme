<?php
/**
 * Componente FAQ Light - Pure CSS Accordion
 * Versione senza JavaScript, utilizza solo HTML e CSS con checkbox nascosti
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

$class_name = 'faq-light';
if ( ! empty( $block['className'] ) ) {
    $class_name .= ' ' . $block['className'];
}

// Aggiungi classe per apertura multipla
if ( $allow_multiple ) {
    $class_name .= ' faq-light--multiple';
}

// ID unico per il gruppo (necessario per radio buttons se non multiplo)
$group_id = uniqid('faq-light-');
?>

<div id="<?php echo $anchor ?>" class="<?php echo $class_name ?>">
    <?php if ( $faq_title ) : ?>
        <h2 class="faq-light__title"><?php echo esc_html( $faq_title ); ?></h2>
    <?php endif; ?>
    
    <?php if ( $faq_items ) : ?>
        <div class="faq-light__container">
            <?php foreach ( $faq_items as $index => $item ) : 
                $question = $item['question'];
                $answer = $item['answer'];
                $item_id = $group_id . '-item-' . $index;
                $input_name = $allow_multiple ? $item_id : $group_id; // Radio group se non multiplo
                $input_type = $allow_multiple ? 'checkbox' : 'radio';
            ?>
                <div class="faq-light__item">
                    <input 
                        type="<?php echo $input_type; ?>" 
                        id="<?php echo $item_id; ?>" 
                        name="<?php echo $input_name; ?>" 
                        class="faq-light__toggle"
                        <?php if ( !$allow_multiple ) : ?>value="<?php echo $index; ?>"<?php endif; ?>
                    >
                    <label for="<?php echo $item_id; ?>" class="faq-light__question">
                        <span class="faq-light__question-text"><?php echo esc_html( $question ); ?></span>
                        <span class="faq-light__icon" aria-hidden="true">+</span>
                    </label>
                    <div class="faq-light__answer">
                        <div class="faq-light__answer-content">
                            <?php echo wp_kses_post( $answer ); ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>