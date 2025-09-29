/**
 * Hero Block JavaScript
 * Gestisce il smooth scroll verso gli anchor target
 */

(function($) {
    'use strict';

    // Inizializza quando il DOM è pronto
    $(document).ready(function() {
        initHeroScroll();
    });

    /**
     * Inizializza la funzionalità di smooth scroll del hero
     */
    function initHeroScroll() {
        // Gestisce il click sui pulsanti di scroll
        $(document).on('click', '.hero__cta-button--scroll, .hero__scroll-button', function(e) {
            e.preventDefault();
            
            const $button = $(this);
            const target = $button.data('scroll-target');
            
            if (target) {
                smoothScrollTo(target);
                
                // Trigger evento personalizzato
                $button.trigger('hero:scroll-initiated', { target: target });
            }
        });

        // Gestione tastiera per i pulsanti di scroll
        $(document).on('keydown', '.hero__cta-button--scroll, .hero__scroll-button', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                $(this).click();
            }
        });
    }

    /**
     * Effettua smooth scroll verso un target
     * @param {string} target - Il selettore CSS del target (es: '#sezione')
     */
    function smoothScrollTo(target) {
        // Assicurati che il target inizi con #
        if (!target.startsWith('#') && !target.startsWith('.')) {
            target = '#' + target;
        }

        const $target = $(target);
        
        if ($target.length === 0) {
            console.warn('Hero scroll target not found:', target);
            return;
        }

        // Calcola offset per header fissi
        const headerOffset = getHeaderOffset();
        const targetOffset = $target.offset().top - headerOffset;
        
        // Smooth scroll
        $('html, body').animate({
            scrollTop: targetOffset
        }, {
            duration: 800,
            easing: 'swing',
            complete: function() {
                // Focus sull'elemento target per accessibilità
                $target.attr('tabindex', '-1').focus();
                
                // Trigger evento di completamento
                $(document).trigger('hero:scroll-completed', { 
                    target: target,
                    element: $target[0]
                });
            }
        });
    }

    /**
     * Calcola l'offset dell'header se fisso
     * @returns {number} L'altezza dell'header fisso
     */
    function getHeaderOffset() {
        // Cerca header comuni
        const $fixedHeaders = $('header, .header, .navbar').filter(function() {
            return $(this).css('position') === 'fixed';
        });
        
        if ($fixedHeaders.length > 0) {
            return $fixedHeaders.first().outerHeight() || 0;
        }
        
        // Offset predefinito per sicurezza
        return 80;
    }

    // Espone funzioni globalmente per uso esterno
    window.HeroManager = {
        scrollTo: smoothScrollTo,
        getHeaderOffset: getHeaderOffset
    };

    // Gestisce scroll automatico da URL hash al caricamento pagina
    $(window).on('load', function() {
        const hash = window.location.hash;
        if (hash && hash.length > 1) {
            setTimeout(function() {
                smoothScrollTo(hash);
            }, 500);
        }
    });

})(jQuery);