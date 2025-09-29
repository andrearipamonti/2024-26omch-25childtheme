/**
 * Floating Support Block JavaScript
 * Gestisce l'apertura/chiusura del panel di supporto
 */

(function($) {
    'use strict';

    // Inizializza quando il DOM è pronto
    $(document).ready(function() {
        initFloatingSupport();
    });

    /**
     * Inizializza la funzionalità del floating support
     */
    function initFloatingSupport() {
        // Gestisce il click sul pulsante floating
        $(document).on('click', '.floating-support__button', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const $button = $(this);
            const $floatingSupport = $button.closest('.floating-support');
            const $panel = $floatingSupport.find('.floating-support__panel');
            const isExpanded = $button.attr('aria-expanded') === 'true';
            
            if (isExpanded) {
                closeSupportPanel($floatingSupport);
            } else {
                openSupportPanel($floatingSupport);
            }
        });

        // Chiude il panel quando si clicca fuori
        $(document).on('click', function(e) {
            const $target = $(e.target);
            
            if (!$target.closest('.floating-support').length) {
                $('.floating-support').each(function() {
                    const $floatingSupport = $(this);
                    const $button = $floatingSupport.find('.floating-support__button');
                    
                    if ($button.attr('aria-expanded') === 'true') {
                        closeSupportPanel($floatingSupport);
                    }
                });
            }
        });

        // Gestione tastiera - ESC chiude il panel
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape' || e.keyCode === 27) {
                $('.floating-support').each(function() {
                    const $floatingSupport = $(this);
                    const $button = $floatingSupport.find('.floating-support__button');
                    
                    if ($button.attr('aria-expanded') === 'true') {
                        closeSupportPanel($floatingSupport);
                        $button.focus();
                    }
                });
            }
        });
    }

    /**
     * Apre il panel di supporto
     * @param {jQuery} $floatingSupport - Il container del floating support
     */
    function openSupportPanel($floatingSupport) {
        const $button = $floatingSupport.find('.floating-support__button');
        const $panel = $floatingSupport.find('.floating-support__panel');

        // Chiudi altri panel aperti
        $('.floating-support').not($floatingSupport).each(function() {
            closeSupportPanel($(this));
        });

        // Aggiorna attributi ARIA
        $button.attr('aria-expanded', 'true');
        $panel.attr('aria-hidden', 'false');

        // Trigger evento personalizzato
        $floatingSupport.trigger('floating-support:opened');
    }

    /**
     * Chiude il panel di supporto
     * @param {jQuery} $floatingSupport - Il container del floating support
     */
    function closeSupportPanel($floatingSupport) {
        const $button = $floatingSupport.find('.floating-support__button');
        const $panel = $floatingSupport.find('.floating-support__panel');

        // Aggiorna attributi ARIA
        $button.attr('aria-expanded', 'false');
        $panel.attr('aria-hidden', 'true');

        // Trigger evento personalizzato
        $floatingSupport.trigger('floating-support:closed');
    }

    // Espone funzioni globalmente per uso esterno
    window.FloatingSupportManager = {
        open: openSupportPanel,
        close: closeSupportPanel,
        closeAll: function() {
            $('.floating-support').each(function() {
                closeSupportPanel($(this));
            });
        }
    };

})(jQuery);