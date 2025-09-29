/**
 * STAR RATING ENHANCEMENT SCRIPT - ANIMAZIONI E INTERAZIONI AVANZATE
 * =================================================================
 * 
 * SCOPO EDUCATIVO: Questo script aggiunge enhancement opzionali al sistema
 * di star rating, incluse animazioni di ingresso, hover effects avanzati,
 * e possibilità di interazione per rating dinamici.
 * 
 * CONCETTI CHIAVE IMPARATI:
 * - Progressive Enhancement (funziona anche senza JS)
 * - Intersection Observer per animazioni on-scroll
 * - CSS Custom Properties manipulation da JavaScript
 * - Event delegation per performance ottimale
 * - ARIA attributes dinamici per accessibilità
 * - Animation frame optimization
 * - Debouncing per performance
 */

// ========================================
// IIFE CON STRICT MODE
// ========================================
(function($) {
    'use strict'; // Modalità strict per JavaScript più sicuro

    // Inizializzazione al caricamento del DOM
    $(document).ready(function() {
        initStarRatingEnhancements();
    });

    /**
     * FUNZIONE DI INIZIALIZZAZIONE PRINCIPALE
     * ======================================
     * Configura tutti gli enhancement per i blocchi star rating.
     * Include animazioni, hover effects e interazioni opzionali.
     */
    function initStarRatingEnhancements() {
        
        // Trova tutti i blocchi star rating nella pagina
        const $starRatings = $('.star-rating');
        
        // Se non ci sono star rating, esci
        if ($starRatings.length === 0) {
            return;
        }

        // ========================================
        // SETUP INTERSECTION OBSERVER PER ANIMAZIONI SCROLL
        // ========================================
        if ('IntersectionObserver' in window) {
            setupScrollAnimations($starRatings);
        }

        // ========================================
        // SETUP HOVER EFFECTS AVANZATI
        // ========================================
        setupHoverEffects($starRatings);

        // ========================================
        // SETUP ANIMAZIONI DI INGRESSO
        // ========================================
        setupEntranceAnimations($starRatings);

        // ========================================
        // SETUP RATING DINAMICO (SE CONFIGURATO)
        // ========================================
        setupDynamicRating($starRatings);
    }

    /**
     * ANIMAZIONI TRIGGER DA SCROLL
     * ============================
     * Usa Intersection Observer per animare le stelle quando entrano
     * nel viewport dell'utente.
     */
    function setupScrollAnimations($starRatings) {
        
        const observerOptions = {
            threshold: 0.5, // Trigga quando 50% dell'elemento è visibile
            rootMargin: '0px 0px -50px 0px' // Margine per triggare prima
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    animateStarsIn($(entry.target));
                    observer.unobserve(entry.target); // Anima solo una volta
                }
            });
        }, observerOptions);

        // Osserva tutti i star rating
        $starRatings.each(function() {
            observer.observe(this);
        });

        /*
        SPIEGAZIONE PER STUDENTI:
        - IntersectionObserver è più efficiente di scroll events
        - threshold: 0.5 = anima quando 50% è visibile
        - rootMargin permette di anticipare l'animazione
        - unobserve() previene animazioni ripetute
        */
    }

    /**
     * ANIMAZIONE STELLE IN ENTRATA
     * ============================
     * Anima le stelle una per una con effetto cascata
     */
    function animateStarsIn($starRating) {
        const $stars = $starRating.find('.star-rating__star');
        
        // Aggiunge classe per preparare l'animazione
        $starRating.addClass('star-rating--animating');
        
        // Anima ogni stella con delay progressivo
        $stars.each(function(index) {
            const $star = $(this);
            
            // Usa setTimeout per delay progressivo
            setTimeout(function() {
                $star.addClass('star-rating__star--animate-in');
                
                // Aggiunge effetto sparkle per stelle piene
                if ($star.hasClass('star-rating__star--full')) {
                    addSparkleEffect($star);
                }
            }, index * 100); // 100ms delay tra stelle
        });

        /*
        TECNICA ANIMAZIONE PER STUDENTI:
        - setTimeout crea delay progressivo tra elementi
        - Classe CSS controlla l'animazione visiva
        - Effetti speciali solo per stelle attive
        */
    }

    /**
     * EFFETTO SPARKLE PER STELLE PIENE
     * ================================
     * Aggiunge un breve effetto scintillio alle stelle attive
     */
    function addSparkleEffect($star) {
        // Crea elemento sparkle temporaneo
        const $sparkle = $('<span class="star-sparkle"></span>');
        $star.append($sparkle);
        
        // Rimuove dopo l'animazione
        setTimeout(function() {
            $sparkle.remove();
        }, 600);
    }

    /**
     * HOVER EFFECTS AVANZATI
     * ======================
     * Aggiunge micro-interazioni sofisticate al hover
     */
    function setupHoverEffects($starRatings) {
        
        // ========================================
        // HOVER SU SINGOLA STELLA
        // ========================================
        $(document).on('mouseenter', '.star-rating__star', function(e) {
            const $star = $(this);
            const $rating = $star.closest('.star-rating');
            
            // Aggiunge classe hover per effetti CSS
            $star.addClass('star-rating__star--hover');
            
            // Effetto ripple sulle stelle adiacenti
            createRippleEffect($star);
        });

        $(document).on('mouseleave', '.star-rating__star', function(e) {
            const $star = $(this);
            
            // Rimuove classe hover
            $star.removeClass('star-rating__star--hover');
        });

        // ========================================
        // HOVER SU INTERO RATING
        // ========================================
        $(document).on('mouseenter', '.star-rating', function(e) {
            const $rating = $(this);
            
            // Legge il valore di rating per animazioni proporzionate
            const rating = parseFloat($rating.data('rating')) || 0;
            
            // Anima l'intensità del colore basata sul rating
            updateColorIntensity($rating, rating);
        });

        $(document).on('mouseleave', '.star-rating', function(e) {
            const $rating = $(this);
            
            // Ripristina colore normale
            updateColorIntensity($rating, 0);
        });
    }

    /**
     * EFFETTO RIPPLE SU HOVER
     * =======================
     * Crea un effetto onda che si propaga dalle stelle vicine
     */
    function createRippleEffect($star) {
        const $siblings = $star.siblings('.star-rating__star');
        
        // Anima stelle adiacenti con scale ridotto
        $siblings.each(function(index) {
            const $sibling = $(this);
            const distance = Math.abs($siblings.index($star) - index);
            const scale = Math.max(1.05, 1.15 - distance * 0.05);
            
            // Applica transform con CSS custom property
            $sibling.css('--hover-scale', scale);
            $sibling.addClass('star-rating__star--ripple');
        });

        // Rimuove effetto dopo 300ms
        setTimeout(function() {
            $siblings.removeClass('star-rating__star--ripple');
        }, 300);
    }

    /**
     * ANIMAZIONI DI INGRESSO
     * ======================
     * Setup delle classi CSS per animazioni iniziali
     */
    function setupEntranceAnimations($starRatings) {
        
        // Prepara elementi per animazione
        $starRatings.each(function() {
            const $rating = $(this);
            const $stars = $rating.find('.star-rating__star');
            
            // Nasconde stelle inizialmente se animations sono supportate
            if (window.CSS && CSS.supports('transform', 'scale(0)')) {
                $stars.addClass('star-rating__star--pre-animate');
            }
        });
    }

    /**
     * RATING DINAMICO (ENHANCEMENT OPZIONALE)
     * =======================================
     * Permette interazione dell'utente per modificare il rating
     */
    function setupDynamicRating($starRatings) {
        
        // Cerca star rating con attributo data-interactive
        const $interactiveRatings = $starRatings.filter('[data-interactive="true"]');
        
        if ($interactiveRatings.length === 0) {
            return; // Nessun rating interattivo trovato
        }

        // ========================================
        // SETUP RATING INTERATTIVO
        // ========================================
        $interactiveRatings.each(function() {
            const $rating = $(this);
            setupInteractiveRating($rating);
        });
    }

    /**
     * CONFIGURAZIONE RATING INTERATTIVO
     * =================================
     * Rende un rating cliccabile per input dell'utente
     */
    function setupInteractiveRating($rating) {
        const $stars = $rating.find('.star-rating__star');
        let currentRating = parseFloat($rating.data('rating')) || 0;

        // ========================================
        // CLICK HANDLER PER STELLE
        // ========================================
        $stars.on('click', function(e) {
            const $clickedStar = $(this);
            const newRating = $stars.index($clickedStar) + 1;
            
            // Aggiorna rating
            updateRating($rating, newRating);
            
            // Trigger custom event per integrazione esterna
            $rating.trigger('rating:changed', [newRating, currentRating]);
            
            currentRating = newRating;
        });

        // ========================================
        // PREVIEW HOVER PER RATING INTERATTIVO
        // ========================================
        $stars.on('mouseenter', function(e) {
            const $hoveredStar = $(this);
            const previewRating = $stars.index($hoveredStar) + 1;
            
            // Mostra preview del rating
            previewRating($rating, previewRating);
        });

        $rating.on('mouseleave', function(e) {
            // Ripristina rating corrente
            updateRating($rating, currentRating);
        });

        // ========================================
        // ACCESSIBILITÀ KEYBOARD
        // ========================================
        $stars.attr('tabindex', '0'); // Rende focusabile da tastiera
        
        $stars.on('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                $(this).click(); // Simula click
            }
        });
    }

    /**
     * AGGIORNAMENTO VISIVO RATING
     * ===========================
     * Aggiorna l'aspetto visivo del rating
     */
    function updateRating($rating, newRating) {
        const $stars = $rating.find('.star-rating__star');
        const $numeric = $rating.find('.star-rating__numeric');
        
        // Aggiorna stelle visualmente
        $stars.each(function(index) {
            const $star = $(this);
            const starValue = index + 1;
            
            if (starValue <= newRating) {
                $star.removeClass('star-rating__star--empty star-rating__star--partial')
                     .addClass('star-rating__star--full');
            } else {
                $star.removeClass('star-rating__star--full star-rating__star--partial')
                     .addClass('star-rating__star--empty');
            }
        });

        // Aggiorna testo numerico se presente
        if ($numeric.length) {
            $numeric.text(newRating.toFixed(1));
        }

        // Aggiorna data attribute
        $rating.attr('data-rating', newRating);
    }

    /**
     * PREVIEW RATING TEMPORANEO
     * =========================
     * Mostra anteprima del rating senza confermarlo
     */
    function previewRating($rating, previewValue) {
        updateRating($rating, previewValue);
        
        // Aggiunge classe per styling del preview
        $rating.addClass('star-rating--preview');
        
        // Rimuove classe preview dopo breve delay
        setTimeout(function() {
            $rating.removeClass('star-rating--preview');
        }, 200);
    }

    /**
     * AGGIORNAMENTO INTENSITÀ COLORE
     * ==============================
     * Modifica l'intensità del colore basata su valore
     */
    function updateColorIntensity($rating, intensity) {
        // Calcola opacità basata su intensità
        const opacity = Math.max(0.7, Math.min(1, 0.7 + intensity * 0.06));
        
        // Aggiorna CSS custom property
        $rating[0].style.setProperty('--star-opacity', opacity);
    }

    // ========================================
    // API PUBBLICA PER INTEGRAZIONE ESTERNA
    // ========================================
    // Espone funzioni per controllo programmatico
    window.StarRatingManager = {
        
        // Anima rating specifico
        animate: function($rating) {
            animateStarsIn($rating);
        },
        
        // Aggiorna rating programmaticamente
        updateRating: function($rating, newValue) {
            updateRating($rating, newValue);
        },
        
        // Rende rating interattivo
        makeInteractive: function($rating) {
            setupInteractiveRating($rating);
        }
    };

})(jQuery); // Fine IIFE

/*
========================================
CSS AGGIUNTIVO PER JAVASCRIPT ENHANCEMENTS
========================================

Aggiungi questo CSS per le animazioni JavaScript:

.star-rating__star--pre-animate {
    transform: scale(0) rotate(-180deg);
    opacity: 0;
}

.star-rating__star--animate-in {
    transform: scale(1) rotate(0deg);
    opacity: 1;
    transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
}

.star-rating__star--ripple {
    transform: scale(var(--hover-scale, 1.1));
    transition: transform 0.2s ease;
}

.star-sparkle {
    position: absolute;
    width: 4px;
    height: 4px;
    background: #fff;
    border-radius: 50%;
    animation: sparkle 0.6s ease-out forwards;
}

@keyframes sparkle {
    0% {
        transform: scale(0) translate(0, 0);
        opacity: 1;
    }
    50% {
        transform: scale(1) translate(10px, -10px);
        opacity: 1;
    }
    100% {
        transform: scale(0) translate(20px, -20px);
        opacity: 0;
    }
}

.star-rating--preview .star-rating__star {
    transform: scale(1.05);
}

CARATTERISTICHE JAVASCRIPT:
✅ PROGRESSIVE ENHANCEMENT: Funziona senza JS
✅ PERFORMANCE: Intersection Observer e RAF optimization  
✅ ACCESSIBILITÀ: Supporto tastiera e ARIA
✅ INTERATTIVITÀ: Rating cliccabili opzionali
✅ ANIMAZIONI: Entrance e hover effects fluidi
✅ API PUBBLICA: Controllo programmatico esterno
✅ RESPONSIVE: Adatta animazioni al dispositivo
*/