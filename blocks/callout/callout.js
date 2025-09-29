/**
 * Callout Block JavaScript
 * Gestisce la funzionalità di chiusura del callout
 */

(function($) {
    'use strict';

    // Inizializza quando il DOM è pronto
    $(document).ready(function() {
        initCalloutClose();
    });

    /**
     * Inizializza la funzionalità di chiusura dei callout
     */
    function initCalloutClose() {
        // Gestisce il click sul pulsante di chiusura
        $(document).on('click', '.callout__close', function(e) {
            e.preventDefault();
            
            const $button = $(this);
            const $callout = $button.closest('.callout');
            
            closeCallout($callout);
        });

        // Gestisce la chiusura con la tastiera (ESC)
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape' || e.keyCode === 27) {
                const $visibleCallouts = $('.callout:visible');
                if ($visibleCallouts.length > 0) {
                    closeCallout($visibleCallouts.last());
                }
            }
        });
    }

    /**
     * Chiude un callout con animazione
     * @param {jQuery} $callout - L'elemento callout da chiudere
     */
    function closeCallout($callout) {
        if ($callout.length === 0 || $callout.hasClass('is-closing')) {
            return;
        }

        // Aggiunge la classe per l'animazione di chiusura
        $callout.addClass('is-closing');

        // Dopo l'animazione, nasconde completamente l'elemento
        setTimeout(function() {
            $callout.slideUp(300, function() {
                $callout.hide();
                
                // Trigger custom event per eventuali callback
                $callout.trigger('callout:closed');
            });
        }, 300);
    }

    /**
     * Mostra un callout nascosto (utility function)
     * @param {jQuery} $callout - L'elemento callout da mostrare
     */
    function showCallout($callout) {
        if ($callout.length === 0) {
            return;
        }

        $callout.removeClass('is-closing');
        $callout.slideDown(300, function() {
            $callout.show();
            
            // Trigger custom event per eventuali callback
            $callout.trigger('callout:shown');
        });
    }

    // Espone le funzioni globalmente per uso esterno se necessario
    window.CalloutManager = {
        close: closeCallout,
        show: showCallout
    };

})(jQuery);