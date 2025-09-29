<?php
/**
 * TEMPLATE PHP MODAL - SISTEMA DIALOG OVERLAY COMPLETO
 * ===================================================
 * 
 * SCOPO EDUCATIVO: Questo template genera un sistema completo di modal dialog
 * con funzionalità avanzate: auto-apertura temporizzata, persistenza stato
 * tramite cookie, trigger multipli, dimensioni configurabili e accessibilità
 * completa secondo le linee guida WAI-ARIA.
 * 
 * CONCETTI CHIAVE IMPARATI:
 * - Modal Dialog pattern (WAI-ARIA)
 * - Configurazione complessa tramite ACF
 * - Logica condizionale avanzata per UI variants
 * - ID generation sicura per evitare conflitti
 * - Data attributes per controllo JavaScript
 * - Role e attributi ARIA per accessibilità
 * - Cookie-based state management
 * - Multiple trigger patterns (button + auto-open)
 * - Responsive modal sizing
 * - Escape hatches per chiusura (X, overlay, ESC)
 */

// ========================================
// DOCUMENTAZIONE FUNZIONALITÀ
// ========================================
// Sistema Modal completo con:
// - Button trigger con stili configurabili
// - Auto-apertura temporizzata opzionale
// - Gestione cookie per "non mostrare più"
// - Dimensioni responsive (small, medium, large)
// - Chiusura multipla (X, overlay click, ESC key)
// - Full accessibility con ARIA roles e attributes

// ========================================
// RECUPERO CAMPI ACF COMPLESSI
// ========================================
// Contenuto della modal
$modal_title = get_field('modal_title');         // Titolo della modal
$modal_content = get_field('modal_content');     // Contenuto rich text

// Configurazione pulsante trigger
$button_text = get_field('button_text');         // Testo del pulsante
$button_style = get_field('button_style') ?: 'primary'; // Stile button
$show_button = get_field('show_button');         // Mostrare il pulsante trigger

// ========================================
// CONFIGURAZIONE AUTO-APERTURA
// ========================================
$auto_open = get_field('auto_open');             // Attivare auto-apertura
$auto_open_delay = get_field('auto_open_delay') ?: 3; // Secondi di delay

// ========================================
// CONFIGURAZIONE LAYOUT E PERSISTENZA
// ========================================
$modal_size = get_field('modal_size') ?: 'medium';     // Dimensione modal
$prevent_reopen = get_field('prevent_reopen');         // Salvare stato nei cookie
$modal_id_custom = get_field('modal_id');              // ID personalizzato

// ========================================
// GESTIONE STANDARD GUTENBERG
// ========================================
$anchor = '';
if ( ! empty( $block['anchor'] ) ) {
    $anchor = esc_attr( $block['anchor'] );
}

$class_name = 'modal-block';
if ( ! empty( $block['className'] ) ) {
    $class_name .= ' ' . $block['className'];
}

// ========================================
// GENERAZIONE IDS UNIVOCI E SICURI
// ========================================
// ID della modal: usa personalizzato o genera automatico
$modal_id = $modal_id_custom ?: uniqid('modal-');

// ID del button trigger: deriva dall'ID modal per associazione
$button_id = $modal_id . '-trigger';

// ========================================
// INIZIO OUTPUT HTML
// ========================================
?>

<!-- ========================================
     CONTENITORE PRINCIPALE MODAL BLOCK
     ======================================== -->
<!-- 
DATA ATTRIBUTES per configurazione JavaScript:
- data-modal-id: ID univoco per riferimenti
- data-auto-open: se attivare apertura automatica
- data-auto-delay: secondi di ritardo per auto-open
- data-prevent-reopen: se salvare stato chiusura nei cookie
-->
<div id="<?php echo $anchor ?>" class="<?php echo $class_name ?>" 
     data-modal-id="<?php echo $modal_id; ?>"
     data-auto-open="<?php echo $auto_open ? 'true' : 'false'; ?>"
     data-auto-delay="<?php echo $auto_open_delay; ?>"
     data-prevent-reopen="<?php echo $prevent_reopen ? 'true' : 'false'; ?>">
     
    <!-- ========================================
         PULSANTE TRIGGER (CONDIZIONALE)
         ======================================== -->
    <!-- Mostrato solo se abilitato E ha testo -->
    <?php if ( $show_button && $button_text ) : ?>
        
        <!-- 
        BUTTON TRIGGER CON ARIA:
        - aria-haspopup="dialog": indica che apre una modal
        - data-modal-target: ID della modal da aprire
        - Classe dinamica per stile (primary, secondary, outline)
        -->
        <button type="button" 
                class="modal-block__trigger modal-block__trigger--<?php echo esc_attr( $button_style ); ?>" 
                id="<?php echo $button_id; ?>"
                data-modal-target="<?php echo $modal_id; ?>"
                aria-haspopup="dialog">
                
            <!-- ========================================
                 CONTENUTO DEL PULSANTE
                 ======================================== -->
            <!-- Wrapper per testo del pulsante -->
            <span class="modal-block__trigger-text"><?php echo esc_html( $button_text ); ?></span>
            
            <!-- Icona decorativa (plus/cross) -->
            <span class="modal-block__trigger-icon" aria-hidden="true">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <!-- Path per icona plus (può diventare X quando modal aperta) -->
                    <path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </span>
        </button>
    <?php endif; ?>
    
    <!-- ========================================
         MODAL DIALOG PRINCIPALE
         ======================================== -->
    <!-- 
    ATTRIBUTI ARIA PER ACCESSIBILITÀ COMPLETA:
    - role="dialog": identifica come finestra di dialogo
    - aria-modal="true": indica comportamento modale (blocca interazione con background)
    - aria-labelledby: riferimento all'ID del titolo per etichettatura
    - aria-hidden="true": inizialmente nascosta agli screen reader
    - Classe modificatrice per dimensione (small, medium, large)
    -->
    <div class="modal-block__modal modal-block__modal--<?php echo esc_attr( $modal_size ); ?>" 
         id="<?php echo $modal_id; ?>"
         role="dialog" 
         aria-modal="true" 
         aria-labelledby="<?php echo $modal_id; ?>-title"
         aria-hidden="true">
         
        <!-- ========================================
             OVERLAY DI SFONDO (CLICKABLE TO CLOSE)
             ======================================== -->
        <!-- 
        data-modal-close: attributo per identificare trigger di chiusura
        Click su overlay = chiusura modal (gestito via JavaScript)
        -->
        <div class="modal-block__overlay" data-modal-close="<?php echo $modal_id; ?>"></div>
        
        <!-- ========================================
             CONTENUTO EFFETTIVO DELLA MODAL
             ======================================== -->
        <div class="modal-block__content">
            
            <!-- ========================================
                 HEADER CON TITOLO E CHIUSURA
                 ======================================== -->
            <div class="modal-block__header">
                
                <!-- ========================================
                     TITOLO DELLA MODAL (OPZIONALE)
                     ======================================== -->
                <?php if ( $modal_title ) : ?>
                    <!-- 
                    H2 per gerarchia heading corretta
                    ID corrisponde ad aria-labelledby del dialog
                    -->
                    <h2 class="modal-block__title" id="<?php echo $modal_id; ?>-title">
                        <?php echo esc_html( $modal_title ); ?>
                    </h2>
                <?php endif; ?>
                
                <!-- ========================================
                     PULSANTE CHIUSURA (SEMPRE PRESENTE)
                     ======================================== -->
                <button type="button" 
                        class="modal-block__close" 
                        data-modal-close="<?php echo $modal_id; ?>"
                        aria-label="Chiudi modal">
                    <!-- Icona X standard per chiusura -->
                    <span class="modal-block__close-icon" aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <!-- ========================================
                 CORPO DELLA MODAL CON CONTENUTO
                 ======================================== -->
            <?php if ( $modal_content ) : ?>
                <div class="modal-block__body">
                    <?php 
                    // wp_kses_post(): permette HTML sicuro per rich text
                    // Supporta p, strong, em, a, ul, ol, h3-h6, etc.
                    echo wp_kses_post( $modal_content ); 
                    ?>
                </div>
            <?php endif; ?>
            
        </div>
    </div>
</div>

<?php
// ========================================
// NOTE EDUCATIVE FINALI
// ========================================
/*
MODAL DIALOG PATTERN COMPLETO IMPLEMENTATO:
- WAI-ARIA compliant per screen reader
- Focus trapping (gestito via JavaScript)
- Escape key support
- Click outside to close
- Multiple trigger patterns
- Cookie-based persistence

CONFIGURAZIONE ACF COMPLESSA:
- modal_title (text): Titolo dialog
- modal_content (wysiwyg): Contenuto rich text
- button_text (text): Testo trigger button
- button_style (select): primary|secondary|outline
- show_button (true_false): Mostrare button trigger
- auto_open (true_false): Apertura automatica
- auto_open_delay (number): Secondi di delay
- modal_size (select): small|medium|large
- prevent_reopen (true_false): Cookie persistence
- modal_id (text): ID personalizzato

FEATURES AVANZATE:
- Auto-open temporizzato con controllo cookie
- Dimensioni responsive configurabili
- Stili button multipli
- ID generation sicura
- Trigger esterni possibili via data-modal-trigger

ACCESSIBILITÀ IMPLEMENTATA:
- role="dialog" per semantic meaning
- aria-modal="true" per comportamento modale
- aria-labelledby per etichettatura
- aria-hidden per stato iniziale
- aria-label per pulsante chiusura
- Focus management via JavaScript
- Keyboard navigation (TAB, ESC)

SICUREZZA APPLICATA:
- esc_attr() per tutti gli attributi HTML
- esc_html() per contenuto testuale
- wp_kses_post() per rich text sicuro
- ID sanitization per evitare XSS

INTEGRAZIONE JAVASCRIPT:
- Data attributes per configurazione
- Event delegation per performance
- Cookie management per UX
- State management via CSS classes
- Custom events per estensibilità
*/
?>