<?php
/**
 * TEMPLATE STAR RATING - SISTEMA VALUTAZIONI A STELLE
 * ==================================================
 * 
 * SCOPO EDUCATIVO: Questo template implementa un sistema di visualizzazione
 * di valutazioni a stelle completamente personalizzabile con diverse varianti
 * grafiche e supporto per mezze stelle.
 * 
 * CONCETTI CHIAVE IMPARATI:
 * - ACF field integration per dati dinamici
 * - Template conditionals per validazione input
 * - Calcoli matematici per stelle parziali
 * - Generazione SVG dinamica in PHP
 * - Markup semantico per accessibilità
 * - Data attributes per JavaScript enhancement
 * - Schema.org microdata per SEO
 * - Responsive design patterns
 */

// ========================================
// VALIDAZIONE CONTESTO GUTENBERG
// ========================================
// Previene accesso diretto al file PHP
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// ========================================
// RACCOLTA DATI DAI CAMPI ACF
// ========================================
// get_field() è la funzione ACF per recuperare valori dei campi personalizzati

// CAMPO PRINCIPALE: Valutazione numerica (0-5)
$rating = get_field('rating') ?: 0;
// Valore di default 0 se il campo è vuoto

// CAMPO OPZIONALE: Numero totale di recensioni
$total_reviews = get_field('total_reviews') ?: null;
// Se non specificato rimane null (non verrà mostrato)

// CAMPO OPZIONALE: Testo personalizzato della valutazione
$rating_text = get_field('rating_text') ?: '';
// Esempio: "Eccellente", "Buono", "Discreto", etc.

// CAMPO OPZIONALE: Mostra valutazione numerica
$show_numeric = get_field('show_numeric') ?: true;
// Boolean: mostra o nasconde il numero accanto alle stelle

// CAMPO SELECT: Dimensione delle stelle
$star_size = get_field('star_size') ?: 'medium';
// Opzioni: small, medium, large

// CAMPO SELECT: Stile delle stelle  
$star_style = get_field('star_style') ?: 'filled';
// Opzioni: filled, outlined, gradient

// ========================================
// VALIDAZIONE E SANITIZZAZIONE DATI
// ========================================
// Assicura che la valutazione sia nel range corretto
$rating = max(0, min(5, floatval($rating)));
// floatval() converte in numero decimale
// min(5, ...) limita il massimo a 5
// max(0, ...) limita il minimo a 0

// Sanitizza il numero di recensioni
$total_reviews = $total_reviews ? intval($total_reviews) : null;
// intval() converte in numero intero

// ========================================
// GESTIONE STANDARD GUTENBERG
// ========================================
$anchor = '';
if (!empty($block['anchor'])) {
    $anchor = esc_attr($block['anchor']);
}

// ========================================
// COSTRUZIONE CLASSI BEM
// ========================================
$class_name = 'star-rating'; // Classe base

// Aggiunta classi personalizzate da editor
if (!empty($block['className'])) {
    $class_name .= ' ' . $block['className'];
}

// ========================================
// MODIFICATORI BEM PER CONFIGURAZIONE
// ========================================
// Aggiunge modificatori per dimensione
$class_name .= ' star-rating--' . $star_size;
// Risultato: "star-rating star-rating--medium"

// Aggiunge modificatori per stile
$class_name .= ' star-rating--' . $star_style;
// Risultato: "star-rating star-rating--medium star-rating--filled"

// ========================================
// CALCOLI PER STELLE PARZIALI
// ========================================
// Separa parte intera e decimale per gestire mezze stelle
$full_stars = floor($rating);      // Stelle piene (es: 4.7 -> 4)
$partial_star = $rating - $full_stars; // Parte decimale (es: 4.7 -> 0.7)
$empty_stars = 5 - ceil($rating);  // Stelle vuote rimanenti

// ========================================
// GENERAZIONE ID UNIVOCO
// ========================================
// Per collegare elementi e gestire accessibilità
$rating_id = uniqid('star-rating-');

// ========================================
// INIZIO OUTPUT HTML
// ========================================
?>

<!-- ========================================
     CONTENITORE PRINCIPALE STAR RATING
     ======================================== -->
<!-- 
SCHEMA.ORG MICRODATA per SEO:
- itemscope: indica che contiene dati strutturati
- itemtype: specifica il tipo di oggetto (Rating)
- data-rating: attributo per JavaScript enhancement
-->
<div id="<?php echo $anchor ?>" 
     class="<?php echo $class_name ?>" 
     itemscope 
     itemtype="https://schema.org/Rating"
     data-rating="<?php echo $rating; ?>"
     data-rating-id="<?php echo $rating_id; ?>">
     
    <!-- ========================================
         CONTAINER STELLE VISUALI
         ======================================== -->
    <div class="star-rating__stars" 
         role="img" 
         aria-label="Valutazione: <?php echo $rating; ?> stelle su 5">
         
        <?php
        // ========================================
        // LOOP GENERAZIONE STELLE PIENE
        // ========================================
        for ($i = 1; $i <= $full_stars; $i++) : ?>
            <span class="star-rating__star star-rating__star--full">
                <!-- SVG STELLA PIENA -->
                <svg viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                    <!-- Percorso SVG ottimizzato per stella a 5 punte -->
                    <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z"/>
                </svg>
            </span>
        <?php endfor;
        
        // ========================================
        // STELLA PARZIALE (SE PRESENTE)
        // ========================================
        if ($partial_star > 0) : 
            // Calcola percentuale riempimento per gradiente CSS
            $fill_percentage = $partial_star * 100; ?>
            <span class="star-rating__star star-rating__star--partial" 
                  style="--fill-percentage: <?php echo $fill_percentage; ?>%">
                <!-- SVG STELLA CON GRADIENTE DINAMICO -->
                <svg viewBox="0 0 24 24" fill="url(#gradient-<?php echo $rating_id; ?>)" xmlns="http://www.w3.org/2000/svg">
                    <!-- DEFINIZIONE GRADIENTE SVG -->
                    <defs>
                        <linearGradient id="gradient-<?php echo $rating_id; ?>" x1="0%" y1="0%" x2="100%" y2="0%">
                            <!-- Parte colorata (riempita) -->
                            <stop offset="<?php echo $fill_percentage; ?>%" style="stop-color:currentColor;stop-opacity:1" />
                            <!-- Parte vuota (trasparente) -->
                            <stop offset="<?php echo $fill_percentage; ?>%" style="stop-color:currentColor;stop-opacity:0.2" />
                        </linearGradient>
                    </defs>
                    <!-- Stella con gradiente applicato -->
                    <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z"/>
                </svg>
            </span>
        <?php endif;
        
        // ========================================
        // LOOP GENERAZIONE STELLE VUOTE
        // ========================================
        for ($i = 1; $i <= $empty_stars; $i++) : ?>
            <span class="star-rating__star star-rating__star--empty">
                <!-- SVG STELLA VUOTA (OUTLINE) -->
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z"/>
                </svg>
            </span>
        <?php endfor; ?>
    </div>
    
    <!-- ========================================
         INFORMAZIONI TESTUALI DELLA VALUTAZIONE
         ======================================== -->
    <div class="star-rating__info">
        
        <?php if ($show_numeric) : ?>
            <!-- ========================================
                 VALUTAZIONE NUMERICA
                 ======================================== -->
            <!-- itemprop: Schema.org property per il valore -->
            <span class="star-rating__numeric" 
                  itemprop="ratingValue">
                <?php echo number_format($rating, 1); ?>
            </span>
            
            <!-- DENOMINATORE FISSO (su 5) -->
            <span class="star-rating__max" 
                  itemprop="bestRating">
                /5
            </span>
        <?php endif; ?>
        
        <?php if ($rating_text) : ?>
            <!-- ========================================
                 TESTO DESCRITTIVO PERSONALIZZATO
                 ======================================== -->
            <span class="star-rating__text">
                <?php echo esc_html($rating_text); ?>
            </span>
        <?php endif; ?>
        
        <?php if ($total_reviews) : ?>
            <!-- ========================================
                 NUMERO TOTALE RECENSIONI
                 ======================================== -->
            <!-- Testo con pluralizzazione italiana -->
            <span class="star-rating__reviews" 
                  itemprop="reviewCount">
                (<?php echo number_format($total_reviews); ?> 
                <?php echo $total_reviews === 1 ? 'recensione' : 'recensioni'; ?>)
            </span>
        <?php endif; ?>
    </div>
    
    <!-- ========================================
         METADATI NASCOSTI PER SCREEN READER
         ======================================== -->
    <!-- Informazioni complete per accessibilità -->
    <span class="sr-only">
        Valutazione: <?php echo $rating; ?> stelle su 5
        <?php if ($total_reviews) : ?>
            basata su <?php echo $total_reviews; ?> recensioni
        <?php endif; ?>
    </span>
</div>

<?php
// ========================================
// DOCUMENTAZIONE BEM E STRUTTURA CSS
// ========================================
/*
CLASSI BEM GENERATE:

BLOCCO BASE:
- .star-rating: Contenitore principale

ELEMENTI:
- .star-rating__stars: Container delle stelle visuali
- .star-rating__star: Singola stella
- .star-rating__info: Container informazioni testuali
- .star-rating__numeric: Valore numerico
- .star-rating__max: Denominatore (/5)
- .star-rating__text: Testo descrittivo
- .star-rating__reviews: Conteggio recensioni

MODIFICATORI STELLA:
- .star-rating__star--full: Stella completamente piena
- .star-rating__star--partial: Stella parzialmente piena
- .star-rating__star--empty: Stella vuota

MODIFICATORI DIMENSIONE:
- .star-rating--small: Stelle piccole
- .star-rating--medium: Stelle medie (default)
- .star-rating--large: Stelle grandi

MODIFICATORI STILE:
- .star-rating--filled: Stelle piene colorate
- .star-rating--outlined: Stelle con solo bordo
- .star-rating--gradient: Stelle con effetto gradiente

ATTRIBUTI DATA PER JAVASCRIPT:
- data-rating: Valore numerico per animazioni
- data-rating-id: ID univoco per identificazione

MICRODATA SCHEMA.ORG:
- itemscope + itemtype="Rating": Struttura dati SEO
- itemprop="ratingValue": Valore della valutazione
- itemprop="bestRating": Valore massimo (5)
- itemprop="reviewCount": Numero di recensioni
*/