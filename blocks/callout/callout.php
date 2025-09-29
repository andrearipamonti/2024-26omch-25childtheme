<?php
/**
 * TEMPLATE PHP CALLOUT - BLOCCO NOTIFICA DISMISSIBLE
 * =================================================
 * 
 * SCOPO EDUCATIVO: Questo template PHP genera l'HTML per un blocco callout
 * (notifica/avviso) che può essere chiuso dall'utente. Integra Advanced Custom Fields (ACF)
 * per la gestione dei contenuti e utilizza le API di WordPress per blocchi Gutenberg.
 * 
 * CONCETTI CHIAVE IMPARATI:
 * - Template PHP per blocchi Gutenberg personalizzati
 * - Integrazione con Advanced Custom Fields (ACF)
 * - Sanitizzazione e validazione dell'output (Security)
 * - Attributi HTML per accessibilità (ARIA)
 * - Generazione di classi CSS dinamiche
 * - Gestione di anchor e className da Gutenberg
 * - Escape delle variabili per sicurezza XSS
 */

// ========================================
// DOCUMENTAZIONE E RIFERIMENTI ESTERNI
// ========================================
// Cosa è un callout? https://www.w3schools.com/howto/howto_js_callout.asp 
// Pattern UI comune per notifiche, avvisi, messaggi importanti

// ========================================
// RECUPERO DATI DA ADVANCED CUSTOM FIELDS (ACF)
// ========================================
// get_field() è la funzione principale di ACF per recuperare i valori dei campi
// Questi campi sono configurati nell'admin di WordPress tramite ACF
$title = get_field('callout_title');    // Campo testo: Titolo del callout
$text = get_field('callout_text');      // Campo textarea: Contenuto del messaggio  
$link = get_field('callout_link');      // Campo link: Link opzionale (con URL, target, title)

// ========================================
// GESTIONE ATTRIBUTI GUTENBERG STANDARD
// ========================================
// WordPress Gutenberg permette di assegnare ID personalizzato (anchor) e classi CSS

// ANCHOR HTML: permette di linkare direttamente a questo blocco (#mio-callout)
$anchor = '';
if ( ! empty( $block['anchor'] ) ) {
    // esc_attr() SANITIZZA l'attributo per prevenire XSS attacks
    $anchor = esc_attr( $block['anchor'] );
}

// ========================================
// COSTRUZIONE CLASSI CSS DINAMICHE
// ========================================
// Inizia con la classe base del componente
$class_name = 'callout';

// AGGIUNTA CLASSI PERSONALIZZATE DA GUTENBERG
// L'editor permette di aggiungere classi CSS personalizzate
if ( ! empty( $block['className'] ) ) {
    $class_name .= ' ' . $block['className']; // Concatenazione con spazio
}

// ========================================
// INIZIO OUTPUT HTML
// ========================================
?>

<!-- ========================================
     CONTENITORE PRINCIPALE DEL CALLOUT
     ======================================== -->
<div id="<?php echo $anchor ?>" class="<?php echo $class_name ?>">
    
    <!-- ========================================
         PULSANTE DI CHIUSURA (SEMPRE PRESENTE)
         ======================================== -->
    <!-- type="button" previene l'invio di form se il callout è dentro un form -->
    <!-- aria-label fornisce descrizione per screen reader -->
    <button class="callout__close" type="button" aria-label="Chiudi callout">
        <!-- &times; è l'entity HTML per il simbolo × (moltiplicazione) -->
        <span class="callout__close-icon">&times;</span>
    </button>
    
    <!-- ========================================
         CONTENUTO CONDIZIONALE: TITOLO
         ======================================== -->
    <!-- Controlla se il campo titolo ha un valore prima di mostrarlo -->
    <?php if ( $title ) : ?>
        <!-- esc_html() ESCAPA il contenuto per prevenire XSS -->
        <!-- Converte caratteri speciali in entità HTML sicure -->
        <h2 class="callout__title"><?php echo esc_html( $title ); ?></h2>
    <?php endif; ?>
    
    <!-- ========================================
         CONTENUTO CONDIZIONALE: TESTO PRINCIPALE
         ======================================== -->
    <?php if ( $text ) : ?>
        <!-- Wrapper in tag <p> per semantica corretta -->
        <p class="callout__text"><?php echo esc_html( $text ); ?></p>
    <?php endif; ?>
    
    <!-- ========================================
         CONTENUTO CONDIZIONALE: LINK OPZIONALE
         ======================================== -->
    <?php if ( $link ) : ?>
        <!-- CAMPO LINK ACF: restituisce un array con 'url', 'target', 'title' -->
        <a class="callout__link" 
           href="<?php echo esc_url( $link['url'] ); ?>" 
           target="<?php echo esc_attr( $link['target'] ); ?>">
            <!-- 
            esc_url() SANITIZZA URLs per prevenire javascript: e altri protocolli pericolosi
            esc_attr() SANITIZZA attributi HTML per prevenire injection
            esc_html() SANITIZZA il contenuto testuale
            -->
            <?php echo esc_html( $link['title'] ); ?>
        </a>
    <?php endif; ?>
    
</div>

<?php
// ========================================
// NOTE EDUCATIVE FINALI
// ========================================
/*
PATTERN DI SICUREZZA UTILIZZATI:
- esc_html(): Escape di contenuto testuale
- esc_url(): Validazione e sanitizzazione URL
- esc_attr(): Escape di attributi HTML
- Controlli condizionali per evitare output vuoto

METODOLOGIA BEM CSS:
- .callout: Block (componente principale)
- .callout__title: Element (elemento del componente)
- .callout__close: Element (pulsante di chiusura)

ACCESSIBILITÀ IMPLEMENTATA:
- aria-label per descrizione pulsante chiusura
- Struttura semantica con heading h2
- Focus management gestito via JavaScript

INTEGRAZIONE WORDPRESS:
- Compatibile con Gutenberg editor
- Supporta anchor personalizzato
- Supporta classi CSS aggiuntive
- Integrato con ACF per gestione contenuti
*/
?>