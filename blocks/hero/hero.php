<?php
/**
 * Hero Block
 * Sezione hero con titolo, testo, CTA e smooth scroll verso anchor target
 */

// Recupera i campi ACF
$hero_title = get_field('hero_title');
$hero_subtitle = get_field('hero_subtitle');
$hero_text = get_field('hero_text');
$hero_image = get_field('hero_image');
$cta_text = get_field('cta_text');
$cta_target = get_field('cta_target'); // Anchor target (es: #sezione)
$cta_external_link = get_field('cta_external_link'); // Link esterno alternativo
$hero_height = get_field('hero_height') ?: 'medium'; // small, medium, large, full
$text_alignment = get_field('text_alignment') ?: 'left'; // left, center, right
$overlay_opacity = get_field('overlay_opacity') ?: 50; // 0-100

// Ancora HTML
$anchor = '';
if ( ! empty( $block['anchor'] ) ) {
    $anchor = esc_attr( $block['anchor'] );
}

$class_name = 'hero';
if ( ! empty( $block['className'] ) ) {
    $class_name .= ' ' . $block['className'];
}

// Aggiungi classi per configurazione
$class_name .= ' hero--' . $hero_height;
$class_name .= ' hero--text-' . $text_alignment;

// Se ha immagine di sfondo
if ( $hero_image ) {
    $class_name .= ' hero--has-image';
}

// ID unico per il componente
$hero_id = uniqid('hero-');
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
    
    <!-- Indicatore scroll (opzionale) -->
    <?php if ( $cta_target && !$cta_external_link ) : ?>
        <div class="hero__scroll-indicator">
            <button type="button" class="hero__scroll-button" data-scroll-target="<?php echo esc_attr( $cta_target ); ?>" aria-label="Scorri alla sezione successiva">
                <span class="hero__scroll-arrow"></span>
            </button>
        </div>
    <?php endif; ?>
</section>
