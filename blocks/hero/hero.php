<?php
/**
 * TEMPLATE PHP HERO - SEZIONE BANNER PRINCIPALE
 * ============================================
 * 
 * SCOPO EDUCATIVO: Questo template genera una sezione hero (banner principale)
 * con immagine di sfondo, contenuto sovrapposto, CTA configurabili e smooth scroll.
 * Implementa pattern UI moderni con overlay, responsive design e accessibility.
 * 
 * CONCETTI CHIAVE IMPARATI:
 * - Hero section pattern per landing pages
 * - Gestione immagini di sfondo con overlay
 * - CTA (Call To Action) multipli con comportamenti diversi
 * - Campi ACF complessi (image, select, range)
 * - Logica condizionale complessa per UI variants
 * - Classi CSS dinamiche per responsive design
 * - Data attributes per configurazione JavaScript
 * - Semantic HTML5 (section, h1, etc.)
 * - Inline CSS per valori dinamici
 */

// ========================================
// DOCUMENTAZIONE COMPONENTE
// ========================================
// Hero Section: sezione principale di una pagina, solitamente above-the-fold
// Contiene: titolo principale, sottotitolo, testo descrittivo, CTA, immagine sfondo

// ========================================
// RECUPERO CAMPI ACF COMPLESSI
// ========================================
// Contenuto principale
$hero_title = get_field('hero_title');           // Titolo principale (H1)
$hero_subtitle = get_field('hero_subtitle');     // Sottotitolo introduttivo
$hero_text = get_field('hero_text');             // Testo descrittivo (WYSIWYG)
$hero_image = get_field('hero_image');           // Immagine di sfondo (ACF Image field)

// Call To Action (CTA)
$cta_text = get_field('cta_text');               // Testo del pulsante CTA
$cta_target = get_field('cta_target');           // Anchor target per smooth scroll (es: #about)
$cta_external_link = get_field('cta_external_link'); // Link esterno alternativo

// ========================================
// CAMPI DI CONFIGURAZIONE CON DEFAULTS
// ========================================
// Campo select per altezza hero
$hero_height = get_field('hero_height') ?: 'medium'; 
// Opzioni: small, medium, large, full (100vh)

// Campo select per allineamento testo
$text_alignment = get_field('text_alignment') ?: 'left'; 
// Opzioni: left, center, right

// Campo range per opacità overlay (0-100)
$overlay_opacity = get_field('overlay_opacity') ?: 50; 
// Permette di controllare la leggibilità del testo sull'immagine

// ========================================
// GESTIONE STANDARD GUTENBERG
// ========================================
$anchor = '';
if ( ! empty( $block['anchor'] ) ) {
    $anchor = esc_attr( $block['anchor'] );
}

// ========================================
// COSTRUZIONE CLASSI CSS DINAMICHE COMPLESSE
// ========================================
$class_name = 'hero'; // Classe base BEM

// Classi personalizzate da editor
if ( ! empty( $block['className'] ) ) {
    $class_name .= ' ' . $block['className'];
}

// ========================================
// MODIFICATORI BEM PER CONFIGURAZIONE
// ========================================
// Modificatore per altezza (controlla min-height via CSS)
$class_name .= ' hero--' . $hero_height;

// Modificatore per allineamento testo
$class_name .= ' hero--text-' . $text_alignment;

// ========================================
// CLASSE CONDIZIONALE PER IMMAGINE
// ========================================
// Se ha immagine di sfondo, aggiunge modificatore per styling specifico
if ( $hero_image ) {
    $class_name .= ' hero--has-image';
}

// ========================================
// ID UNIVOCO PER COMPONENTE
// ========================================
$hero_id = uniqid('hero-');

// ========================================
// INIZIO OUTPUT HTML
// ========================================
?>

<section id="<?php echo $anchor ?>" class="<?php echo $class_name ?>" data-hero-id="<?php echo $hero_id; ?>">
    <?php if ( $hero_image ) : ?>
        <div class="hero__background">
            <img src="<?php echo esc_url( $hero_image['url'] ); ?>" alt="<?php echo esc_attr( $hero_image['alt'] ?: $hero_title ); ?>" class="hero__background-image">
            <div class="hero__overlay" style="opacity: <?php echo $overlay_opacity / 100; ?>"></div>
        </div>
    <?php endif; ?>
    
    <div class="hero__container">
        <div class="hero__content">
            <?php if ( $hero_subtitle ) : ?>
                <p class="hero__subtitle"><?php echo esc_html( $hero_subtitle ); ?></p>
            <?php endif; ?>
            
            <?php if ( $hero_title ) : ?>
                <h1 class="hero__title"><?php echo esc_html( $hero_title ); ?></h1>
            <?php endif; ?>
            
            <?php if ( $hero_text ) : ?>
                <div class="hero__text">
                    <?php echo wp_kses_post( $hero_text ); ?>
                </div>
            <?php endif; ?>
            
            <?php if ( $cta_text && ($cta_target || $cta_external_link) ) : ?>
                <div class="hero__cta">
                    <?php if ( $cta_external_link ) : ?>
                        <a href="<?php echo esc_url( $cta_external_link ); ?>" class="hero__cta-button hero__cta-button--external">
                            <?php echo esc_html( $cta_text ); ?>
                            <span class="hero__cta-icon" aria-hidden="true">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M7 17L17 7M17 7H7M17 7V17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                        </a>
                    <?php else : ?>
                        <button type="button" class="hero__cta-button hero__cta-button--scroll" data-scroll-target="<?php echo esc_attr( $cta_target ); ?>">
                            <?php echo esc_html( $cta_text ); ?>
                            <span class="hero__cta-icon" aria-hidden="true">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M7 13L12 18L17 13M7 6L12 11L17 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                        </button>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- ========================================
         INDICATORE SCROLL ANIMATO (OPZIONALE)
         ======================================== -->
    <!-- Mostrato solo se ha target interno (non link esterno) -->
    <?php if ( $cta_target && !$cta_external_link ) : ?>
        <div class="hero__scroll-indicator">
            <!-- 
            BUTTON ACCESSIBILE PER SCROLL:
            - aria-label per descrizione completa
            - data-scroll-target per JavaScript
            - type="button" previene submit form
            -->
            <button type="button" 
                    class="hero__scroll-button" 
                    data-scroll-target="<?php echo esc_attr( $cta_target ); ?>" 
                    aria-label="Scorri alla sezione successiva">
                <!-- Freccia animata CSS pura (no SVG per performance) -->
                <span class="hero__scroll-arrow"></span>
            </button>
        </div>
    <?php endif; ?>
    
</section>

<?php
// ========================================
// NOTE EDUCATIVE FINALI
// ========================================
/*
PATTERN HERO SECTION IMPLEMENTATO:
- Above-the-fold content per massimo impatto
- Immagine di sfondo full-width con overlay controllabile
- Gerarchia heading corretta (H1 per SEO)
- CTA prominente con azioni alternative
- Responsive design con container flessibili

CAMPI ACF UTILIZZATI:
- hero_title (text): Titolo principale
- hero_subtitle (text): Sottotitolo
- hero_text (wysiwyg): Contenuto rich text
- hero_image (image): Immagine di sfondo
- cta_text (text): Testo pulsante
- cta_target (text): Anchor per smooth scroll
- cta_external_link (url): Link esterno alternativo
- hero_height (select): Altezza sezione
- text_alignment (select): Allineamento testo
- overlay_opacity (range): Opacità overlay

LOGICA CONDIZIONALE COMPLESSA:
- Mostra elementi solo se hanno contenuto
- CTA alterna tra link esterno e scroll interno
- Overlay solo se presente immagine
- Indicatore scroll solo per target interni

SICUREZZA IMPLEMENTATA:
- esc_url() per URL sanitization
- esc_attr() per attributi HTML
- esc_html() per contenuto textuale
- wp_kses_post() per rich text sicuro

ACCESSIBILITÀ FEATURES:
- Semantic HTML5 structure
- aria-label per elementi interattivi
- aria-hidden per icone decorative
- Alt text dinamico per immagini
- Focus indicators via CSS

PERFORMANCE OPTIMIZATIONS:
- SVG inline per icone (no HTTP requests)
- Conditional loading di elementi
- CSS classes per styling efficiente
- Minimal inline CSS solo per valori dinamici
*/
?>
