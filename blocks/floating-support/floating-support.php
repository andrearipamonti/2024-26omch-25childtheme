<?php
/**
 * Floating Support Block
 * Elemento galleggiante per il supporto con contatti
 * Una volta cliccato mostra telefono e email
 */

// Recupera i campi ACF
$support_phone = get_field('support_phone');
$support_email = get_field('support_email');
$support_text = get_field('support_text') ?: 'Hai bisogno di aiuto?';
$button_text = get_field('button_text') ?: 'Supporto';
$position = get_field('position') ?: 'bottom-right'; // bottom-right, bottom-left, top-right, top-left

// Ancora HTML
$anchor = '';
if ( ! empty( $block['anchor'] ) ) {
    $anchor = esc_attr( $block['anchor'] );
}

$class_name = 'floating-support';
if ( ! empty( $block['className'] ) ) {
    $class_name .= ' ' . $block['className'];
}

// Aggiungi classe per posizione
$class_name .= ' floating-support--' . $position;

// ID unico per il componente
$support_id = uniqid('floating-support-');
?>

<div id="<?php echo $anchor ?>" class="<?php echo $class_name ?>" data-support-id="<?php echo $support_id; ?>">
    <!-- Pulsante floating -->
    <button class="floating-support__button" type="button" aria-expanded="false" aria-controls="<?php echo $support_id; ?>-panel">
        <span class="floating-support__icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M20 2H4C2.9 2 2 2.9 2 4V22L6 18H20C21.1 18 22 17.1 22 16V4C22 2.9 21.1 2 20 2Z" fill="currentColor"/>
            </svg>
        </span>
        <span class="floating-support__text"><?php echo esc_html( $button_text ); ?></span>
        <span class="floating-support__close" aria-hidden="true">&times;</span>
    </button>
    
    <!-- Panel dei contatti -->
    <div class="floating-support__panel" id="<?php echo $support_id; ?>-panel" aria-hidden="true">
        <div class="floating-support__panel-content">
            <?php if ( $support_text ) : ?>
                <h3 class="floating-support__title"><?php echo esc_html( $support_text ); ?></h3>
            <?php endif; ?>
            
            <div class="floating-support__contacts">
                <?php if ( $support_phone ) : ?>
                    <a href="tel:<?php echo esc_attr( preg_replace('/[^+0-9]/', '', $support_phone) ); ?>" class="floating-support__contact floating-support__contact--phone">
                        <span class="floating-support__contact-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M6.62 10.79C8.06 13.62 10.38 15.94 13.21 17.38L15.41 15.18C15.69 14.9 16.08 14.82 16.43 14.93C17.55 15.3 18.75 15.5 20 15.5C20.55 15.5 21 15.95 21 16.5V20C21 20.55 20.55 21 20 21C10.61 21 3 13.39 3 4C3 3.45 3.45 3 4 3H7.5C8.05 3 8.5 3.45 8.5 4C8.5 5.25 8.7 6.45 9.07 7.57C9.18 7.92 9.1 8.31 8.82 8.59L6.62 10.79Z" fill="currentColor"/>
                            </svg>
                        </span>
                        <div class="floating-support__contact-info">
                            <span class="floating-support__contact-label">Telefono</span>
                            <span class="floating-support__contact-value"><?php echo esc_html( $support_phone ); ?></span>
                        </div>
                    </a>
                <?php endif; ?>
                
                <?php if ( $support_email ) : ?>
                    <a href="mailto:<?php echo esc_attr( $support_email ); ?>" class="floating-support__contact floating-support__contact--email">
                        <span class="floating-support__contact-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M20 4H4C2.9 4 2 4.9 2 6V18C2 19.1 2.9 20 4 20H20C21.1 20 22 19.1 22 18V6C22 4.9 21.1 4 20 4ZM20 8L12 13L4 8V6L12 11L20 6V8Z" fill="currentColor"/>
                            </svg>
                        </span>
                        <div class="floating-support__contact-info">
                            <span class="floating-support__contact-label">Email</span>
                            <span class="floating-support__contact-value"><?php echo esc_html( $support_email ); ?></span>
                        </div>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>