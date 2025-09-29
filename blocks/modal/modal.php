<?php
/**
 * Modal Block
 * Modal collegata da button o auto-open con gestione cookie
 * - Button trigger con contenuto CTA configurabile
 * - Modal con titolo e contenuto
 * - Chiusura con X e click fuori
 * - Auto-open dopo 3 secondi (opzionale)
 * - Gestione cookie per non riaprire
 */

// Recupera i campi ACF
$modal_title = get_field('modal_title');
$modal_content = get_field('modal_content');
$button_text = get_field('button_text');
$button_style = get_field('button_style') ?: 'primary'; // primary, secondary, outline
$show_button = get_field('show_button'); // true/false
$auto_open = get_field('auto_open'); // true/false
$auto_open_delay = get_field('auto_open_delay') ?: 3; // secondi
$modal_size = get_field('modal_size') ?: 'medium'; // small, medium, large
$prevent_reopen = get_field('prevent_reopen'); // true/false - usa cookie
$modal_id_custom = get_field('modal_id'); // ID personalizzato per il cookie

// Ancora HTML
$anchor = '';
if ( ! empty( $block['anchor'] ) ) {
    $anchor = esc_attr( $block['anchor'] );
}

$class_name = 'modal-block';
if ( ! empty( $block['className'] ) ) {
    $class_name .= ' ' . $block['className'];
}

// ID unico per la modal
$modal_id = $modal_id_custom ?: uniqid('modal-');
$button_id = $modal_id . '-trigger';
?>

<div id="<?php echo $anchor ?>" class="<?php echo $class_name ?>" 
     data-modal-id="<?php echo $modal_id; ?>"
     data-auto-open="<?php echo $auto_open ? 'true' : 'false'; ?>"
     data-auto-delay="<?php echo $auto_open_delay; ?>"
     data-prevent-reopen="<?php echo $prevent_reopen ? 'true' : 'false'; ?>">
     
    <?php if ( $show_button && $button_text ) : ?>
        <!-- Button Trigger -->
        <button type="button" 
                class="modal-block__trigger modal-block__trigger--<?php echo esc_attr( $button_style ); ?>" 
                id="<?php echo $button_id; ?>"
                data-modal-target="<?php echo $modal_id; ?>"
                aria-haspopup="dialog">
            <span class="modal-block__trigger-text"><?php echo esc_html( $button_text ); ?></span>
            <span class="modal-block__trigger-icon" aria-hidden="true">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </span>
        </button>
    <?php endif; ?>
    
    <!-- Modal -->
    <div class="modal-block__modal modal-block__modal--<?php echo esc_attr( $modal_size ); ?>" 
         id="<?php echo $modal_id; ?>"
         role="dialog" 
         aria-modal="true" 
         aria-labelledby="<?php echo $modal_id; ?>-title"
         aria-hidden="true">
         
        <!-- Overlay -->
        <div class="modal-block__overlay" data-modal-close="<?php echo $modal_id; ?>"></div>
        
        <!-- Content -->
        <div class="modal-block__content">
            <!-- Header -->
            <div class="modal-block__header">
                <?php if ( $modal_title ) : ?>
                    <h2 class="modal-block__title" id="<?php echo $modal_id; ?>-title">
                        <?php echo esc_html( $modal_title ); ?>
                    </h2>
                <?php endif; ?>
                
                <button type="button" 
                        class="modal-block__close" 
                        data-modal-close="<?php echo $modal_id; ?>"
                        aria-label="Chiudi modal">
                    <span class="modal-block__close-icon" aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <!-- Body -->
            <?php if ( $modal_content ) : ?>
                <div class="modal-block__body">
                    <?php echo wp_kses_post( $modal_content ); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>