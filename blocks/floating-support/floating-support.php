<?php
/**
 * TEMPLATE PHP FLOATING SUPPORT - PANNELLO AIUTO PERSISTENTE
 * =========================================================
 * 
 * SCOPO EDUCATIVO: Questo template genera un pannello di supporto che rimane
 * fisso nella viewport (position: fixed) e permette agli utenti di accedere
 * rapidamente a contatti di supporto (telefono, email). Implementa un pattern
 * UI comune nei siti di e-commerce e servizi.
 * 
 * CONCETTI CHIAVE IMPARATI:
 * - Position fixed e layering (z-index)
 * - Operatore null coalescing (?:) per valori default
 * - Generazione ID univoci con uniqid()
 * - Modificatori BEM dinamici per posizionamento
 * - Regex per sanitizzazione numeri telefono
 * - SVG inline per icone scalabili
 * - Data attributes per configurazione JavaScript
 * - Pattern UI toggle button/panel
 */

// ========================================
// DOCUMENTAZIONE COMPONENTE
// ========================================
// Elemento galleggiante per il supporto con contatti
// Pattern comune: pulsante fisso → click → pannello con contatti
// Esempi: live chat, supporto clienti, help desk

// ========================================
// RECUPERO CAMPI ACF CON VALORI DEFAULT
// ========================================
// Campi principali per funzionalità
$support_phone = get_field('support_phone');   // Numero di telefono
$support_email = get_field('support_email');   // Indirizzo email

// ========================================
// OPERATORE NULL COALESCING PER DEFAULTS
// ========================================
// Sintassi: $var = get_field('campo') ?: 'default';
// Se get_field() restituisce valore falsy, usa il default
$support_text = get_field('support_text') ?: 'Hai bisogno di aiuto?';  // Testo del pannello
$button_text = get_field('button_text') ?: 'Supporto';                 // Testo del pulsante

// ========================================
// CONFIGURAZIONE POSIZIONAMENTO
// ========================================
// Campo select con opzioni predefinite per posizione del floating button
$position = get_field('position') ?: 'bottom-right'; 
// Opzioni: bottom-right, bottom-left, top-right, top-left

// ========================================
// GESTIONE STANDARD GUTENBERG
// ========================================
$anchor = '';
if ( ! empty( $block['anchor'] ) ) {
    $anchor = esc_attr( $block['anchor'] );
}

// ========================================
// COSTRUZIONE CLASSI CON MODIFICATORI BEM
// ========================================
$class_name = 'floating-support'; // Classe base

// Aggiunta classi personalizzate da editor
if ( ! empty( $block['className'] ) ) {
    $class_name .= ' ' . $block['className'];
}

// ========================================
// MODIFICATORE BEM PER POSIZIONE
// ========================================
// Aggiunge classe modificatrice per posizionamento CSS
$class_name .= ' floating-support--' . $position;
// Risultato: "floating-support floating-support--bottom-right"

// ========================================
// GENERAZIONE ID UNIVOCO
// ========================================
// uniqid() genera un ID unico basato su timestamp + random
// Utile per evitare conflitti se ci sono più istanze nella pagina
$support_id = uniqid('floating-support-');
// Risultato esempio: "floating-support-507f1f77bcf86cd799439011"

// ========================================
// INIZIO OUTPUT HTML
// ========================================
?>

<!-- ========================================
     CONTENITORE PRINCIPALE FLOATING SUPPORT
     ======================================== -->
<!-- data-support-id: identificativo per JavaScript, diverso dall'ID HTML -->
<div id="<?php echo $anchor ?>" class="<?php echo $class_name ?>" data-support-id="<?php echo $support_id; ?>">
    
    <!-- ========================================
         PULSANTE FLOATING (SEMPRE VISIBILE)
         ======================================== -->
    <!-- 
    ARIA ATTRIBUTES PER ACCESSIBILITÀ:
    - aria-expanded: stato toggle del pannello
    - aria-controls: ID dell'elemento controllato
    - type="button": previene submit di form
    -->
    <button class="floating-support__button" 
            type="button" 
            aria-expanded="false" 
            aria-controls="<?php echo $support_id; ?>-panel">
        
        <!-- ========================================
             ICONA SVG INLINE PER PERFORMANCE
             ======================================== -->
        <span class="floating-support__icon">
            <!-- SVG ottimizzato per messaggio/chat bubble -->
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <!-- fill="currentColor": eredita colore dal CSS parent -->
                <path d="M20 2H4C2.9 2 2 2.9 2 4V22L6 18H20C21.1 18 22 17.1 22 16V4C22 2.9 21.1 2 20 2Z" fill="currentColor"/>
            </svg>
        </span>
        
        <!-- ========================================
             TESTO DEL PULSANTE
             ======================================== -->
        <span class="floating-support__text"><?php echo esc_html( $button_text ); ?></span>
        
        <!-- ========================================
             ICONA DI CHIUSURA (TOGGLE STATE)
             ======================================== -->
        <!-- Mostrata solo quando il pannello è aperto (gestito via CSS) -->
        <!-- aria-hidden="true": decorativa, nascosta agli screen reader -->
        <span class="floating-support__close" aria-hidden="true">&times;</span>
    </button>
    
    <!-- ========================================
         PANNELLO DEI CONTATTI (COLLAPSIBLE)
         ======================================== -->
    <!-- Inizialmente nascosto, mostrato al click del pulsante -->
    <div class="floating-support__panel" 
         id="<?php echo $support_id; ?>-panel" 
         aria-hidden="true">
         
        <div class="floating-support__panel-content">
            
            <!-- ========================================
                 TITOLO DEL PANNELLO (OPZIONALE)
                 ======================================== -->
            <?php if ( $support_text ) : ?>
                <!-- H3 per gerarchia heading corretta -->
                <h3 class="floating-support__title"><?php echo esc_html( $support_text ); ?></h3>
            <?php endif; ?>
            
            <!-- ========================================
                 LISTA DEI CONTATTI DISPONIBILI
                 ======================================== -->
            <div class="floating-support__contacts">
                
                <!-- ========================================
                     LINK TELEFONO (SE CONFIGURATO)
                     ======================================== -->
                <?php if ( $support_phone ) : ?>
                    <!-- 
                    href="tel:" protocollo per aprire app telefono
                    REGEX SANITIZATION: rimuove tutto tranne numeri e +
                    -->
                    <a href="tel:<?php echo esc_attr( preg_replace('/[^+0-9]/', '', $support_phone) ); ?>" 
                       class="floating-support__contact floating-support__contact--phone">
                       
                        <!-- ========================================
                             ICONA TELEFONO SVG
                             ======================================== -->
                        <span class="floating-support__contact-icon">
                            <!-- SVG icona telefono ottimizzata -->
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M6.62 10.79C8.06 13.62 10.38 15.94 13.21 17.38L15.41 15.18C15.69 14.9 16.08 14.82 16.43 14.93C17.55 15.3 18.75 15.5 20 15.5C20.55 15.5 21 15.95 21 16.5V20C21 20.55 20.55 21 20 21C10.61 21 3 13.39 3 4C3 3.45 3.45 3 4 3H7.5C8.05 3 8.5 3.45 8.5 4C8.5 5.25 8.7 6.45 9.07 7.57C9.18 7.92 9.1 8.31 8.82 8.59L6.62 10.79Z" fill="currentColor"/>
                            </svg>
                        </span>
                        
                        <!-- ========================================
                             INFORMAZIONI CONTATTO TELEFONO
                             ======================================== -->
                        <div class="floating-support__contact-info">
                            <!-- Label descrittiva -->
                            <span class="floating-support__contact-label">Telefono</span>
                            <!-- Numero formattato per display (mantiene formattazione originale) -->
                            <span class="floating-support__contact-value"><?php echo esc_html( $support_phone ); ?></span>
                        </div>
                    </a>
                <?php endif; ?>
                
                <!-- ========================================
                     LINK EMAIL (SE CONFIGURATO)
                     ======================================== -->
                <?php if ( $support_email ) : ?>
                    <!-- href="mailto:" protocollo per aprire client email -->
                    <a href="mailto:<?php echo esc_attr( $support_email ); ?>" 
                       class="floating-support__contact floating-support__contact--email">
                       
                        <!-- ========================================
                             ICONA EMAIL SVG
                             ======================================== -->
                        <span class="floating-support__contact-icon">
                            <!-- SVG icona email ottimizzata -->
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M20 4H4C2.9 4 2 4.9 2 6V18C2 19.1 2.9 20 4 20H20C21.1 20 22 19.1 22 18V6C22 4.9 21.1 4 20 4ZM20 8L12 13L4 8V6L12 11L20 6V8Z" fill="currentColor"/>
                            </svg>
                        </span>
                        
                        <!-- ========================================
                             INFORMAZIONI CONTATTO EMAIL
                             ======================================== -->
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

<?php
// ========================================
// NOTE EDUCATIVE FINALI
// ========================================
/*
PATTERN FLOATING ACTION BUTTON IMPLEMENTATO:
- Position fixed per persistenza in viewport
- Z-index alto per stare sopra altri contenuti
- Toggle button per show/hide pannello
- Click outside to close (gestito via JavaScript)

SANITIZZAZIONE AVANZATA:
- preg_replace('/[^+0-9]/', '', $phone): rimuove tutto tranne numeri e +
- esc_attr() per attributi href
- esc_html() per contenuto testuale

PROTOCOLLI URL UTILIZZATI:
- tel: per aprire app telefono mobile
- mailto: per aprire client email default

SVG INLINE BENEFITS:
- Scalabilità perfetta su tutti i dispositivi
- Controllo CSS completo (colori, dimensioni)
- Performance superiore (no HTTP requests)
- currentColor per eredità colore

METODOLOGIA BEM AVANZATA:
- floating-support: Block principale
- floating-support__button: Element pulsante
- floating-support--bottom-right: Modifier posizione
- floating-support__contact--phone: Modifier tipo contatto

ACCESSIBILITÀ IMPLEMENTATA:
- aria-expanded per stati toggle
- aria-controls per associazioni
- aria-hidden per elementi decorativi
- Focus management via JavaScript
- Semantic HTML con link funzionali
*/
?>