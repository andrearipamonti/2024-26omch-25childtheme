<?php
/**
 * TEMPLATE PHP FAQ - SISTEMA ACCORDION ACCESSIBILE
 * ===============================================
 * 
 * SCOPO EDUCATIVO: Questo template genera un sistema di FAQ (Frequently Asked Questions)
 * implementando il pattern Accordion con piena accessibilità WAI-ARIA. Utilizza ACF Repeater
 * per gestire domande/risposte multiple e configurazioni avanzate.
 * 
 * CONCETTI CHIAVE IMPARATI:
 * - ACF Repeater Fields per contenuti dinamici
 * - Pattern Accordion UI/UX
 * - WAI-ARIA per accessibilità completa
 * - Data attributes per configurazione JavaScript
 * - Generazione IDs univoci per associazioni ARIA
 * - Metodologia BEM CSS avanzata
 * - Cicli PHP e gestione array multidimensionali
 * - Escape e sanitizzazione di contenuto HTML/Rich Text
 */

// ========================================
// RIFERIMENTI E DOCUMENTAZIONE
// ========================================
// Pattern Accordion: https://www.w3schools.com/howto/howto_js_accordion.asp
// WAI-ARIA Accordion: https://www.w3.org/WAI/ARIA/apg/patterns/accordion/

// ========================================
// RECUPERO DATI ACF AVANZATI
// ========================================
// ACF REPEATER: Campo ripetibile per gestire N domande/risposte
$faq_items = get_field('faq_items');        // Array di sottocampi [question, answer]
$allow_multiple = get_field('allow_multiple_open'); // Boolean: permettere aperture multiple
$faq_title = get_field('faq_title');        // Titolo opzionale per la sezione FAQ

// ========================================
// GESTIONE ATTRIBUTI GUTENBERG
// ========================================
$anchor = '';
if ( ! empty( $block['anchor'] ) ) {
    $anchor = esc_attr( $block['anchor'] );
}

// ========================================
// COSTRUZIONE CLASSI CSS DINAMICHE CON LOGICA
// ========================================
$class_name = 'faq'; // Classe base BEM

// Aggiunta classi personalizzate da editor
if ( ! empty( $block['className'] ) ) {
    $class_name .= ' ' . $block['className'];
}

// ========================================
// CLASSE CONDIZIONALE PER COMPORTAMENTO
// ========================================
// Aggiunge modificatore BEM per styling diverso se permettono aperture multiple
if ( $allow_multiple ) {
    $class_name .= ' faq--multiple'; // Modificatore BEM per apertura multipla
}

// ========================================
// INIZIO OUTPUT TEMPLATE
// ========================================
?>

<!-- ========================================
     CONTENITORE PRINCIPALE FAQ CON DATA ATTRIBUTES
     ======================================== -->
<!-- data-allow-multiple: configurazione JavaScript per comportamento accordion -->
<div id="<?php echo $anchor ?>" class="<?php echo $class_name ?>" data-allow-multiple="<?php echo $allow_multiple ? 'true' : 'false'; ?>">
    
    <!-- ========================================
         TITOLO OPZIONALE DELLA SEZIONE FAQ
         ======================================== -->
    <?php if ( $faq_title ) : ?>
        <!-- H2 semanticamente corretto per struttura heading -->
        <h2 class="faq__title"><?php echo esc_html( $faq_title ); ?></h2>
    <?php endif; ?>
    
    <!-- ========================================
         CONTAINER DELLE FAQ (SE PRESENTI)
         ======================================== -->
    <?php if ( $faq_items ) : ?>
        <div class="faq__container">
            
            <!-- ========================================
                 LOOP THROUGH ACF REPEATER ITEMS
                 ======================================== -->
            <?php foreach ( $faq_items as $index => $item ) : 
                
                // ========================================
                // ESTRAZIONE DATI DAL REPEATER ITEM
                // ========================================
                // Ogni $item è un array con i sottocampi del repeater
                $question = $item['question'];  // Sottocampo: testo della domanda
                $answer = $item['answer'];      // Sottocampo: risposta (rich text)
                
                // ========================================
                // GENERAZIONE ID UNIVOCO PER ASSOCIAZIONI ARIA
                // ========================================
                // Crea ID unico per ogni item per collegare button e content
                $item_id = 'faq-item-' . $index; // Es: faq-item-0, faq-item-1, etc.
            ?>
            
                <!-- ========================================
                     SINGOLO ITEM FAQ (DOMANDA + RISPOSTA)
                     ======================================== -->
                <div class="faq__item">
                    
                    <!-- ========================================
                         PULSANTE DOMANDA (TRIGGER ACCORDION)
                         ======================================== -->
                    <!-- 
                    ARIA ATTRIBUTES PER ACCESSIBILITÀ:
                    - aria-expanded="false": indica lo stato iniziale (chiuso)
                    - aria-controls: ID dell'elemento controllato da questo button
                    - type="button": previene invio form se dentro un form
                    -->
                    <button class="faq__question" 
                            type="button" 
                            aria-expanded="false" 
                            aria-controls="<?php echo $item_id; ?>">
                        
                        <!-- ========================================
                             CONTENUTO DEL PULSANTE
                             ======================================== -->
                        <!-- Wrapper per il testo della domanda -->
                        <span class="faq__question-text"><?php echo esc_html( $question ); ?></span>
                        
                        <!-- Icona indicatrice di stato (+ quando chiuso, - quando aperto) -->
                        <!-- aria-hidden="true": nasconde icona dagli screen reader (decorativa) -->
                        <span class="faq__icon" aria-hidden="true">+</span>
                    </button>
                    
                    <!-- ========================================
                         CONTENITORE RISPOSTA (COLLAPSIBLE CONTENT)
                         ======================================== -->
                    <!-- 
                    ARIA ATTRIBUTES:
                    - id: corrisponde all'aria-controls del button
                    - aria-hidden="true": inizialmente nascosto agli screen reader
                    -->
                    <div class="faq__answer" id="<?php echo $item_id; ?>" aria-hidden="true">
                        
                        <!-- ========================================
                             WRAPPER INTERNO PER PADDING/STYLING
                             ======================================== -->
                        <div class="faq__answer-content">
                            <?php 
                            // ========================================
                            // OUTPUT RICH TEXT CON SICUREZZA
                            // ========================================
                            // wp_kses_post() permette HTML safe (p, strong, em, a, etc.)
                            // ma rimuove JavaScript e altri elementi pericolosi
                            echo wp_kses_post( $answer ); 
                            ?>
                        </div>
                    </div>
                </div>
                
            <?php endforeach; ?>
            
        </div>
    <?php endif; ?>
    
</div>

<?php
// ========================================
// NOTE EDUCATIVE FINALI
// ========================================
/*
PATTERN ACCORDION IMPLEMENTATO:
- Ogni domanda è un pulsante cliccabile
- Il contenuto si espande/collassa dinamicamente
- Stati ARIA aggiornati via JavaScript
- Focus management per navigazione da tastiera

ACF REPEATER FIELD STRUCTURE:
- faq_items (repeater)
  ├── question (text)
  └── answer (textarea/wysiwyg)

ASSOCIAZIONI ARIA UTILIZZATE:
- aria-controls: collega button al content
- aria-expanded: stato attuale (true/false)
- aria-hidden: visibilità per screen reader

SICUREZZA IMPLEMENTATA:
- esc_html() per testi puri
- wp_kses_post() per contenuto rich text
- ID generation sicura con index numerico

ACCESSIBILITÀ FEATURES:
- Navigazione da tastiera (frecce, home, end)
- Screen reader support completo
- Focus indicators visibili
- Semantic HTML structure
*/
?>