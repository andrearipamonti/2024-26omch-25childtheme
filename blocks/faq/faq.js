/**
 * FAQ Block JavaScript
 * Gestisce la funzionalità accordion per le FAQ
 */

(function($) {
    'use strict';

    // Inizializza quando il DOM è pronto
    $(document).ready(function() {
        initFAQ();
    });

    /**
     * Inizializza la funzionalità FAQ
     */
    function initFAQ() {
        // Gestisce il click sui pulsanti delle domande
        $(document).on('click', '.faq__question', function(e) {
            e.preventDefault();
            
            const $button = $(this);
            const $faqItem = $button.closest('.faq__item');
            const $faqContainer = $button.closest('.faq');
            const $answer = $faqItem.find('.faq__answer');
            const isExpanded = $button.attr('aria-expanded') === 'true';
            const allowMultiple = $faqContainer.data('allow-multiple') === true;
            
            // Se non permette apertura multipla, chiudi tutti gli altri
            if (!allowMultiple && !isExpanded) {
                closeAllFAQItems($faqContainer, $faqItem);
            }
            
            // Toggle dell'item corrente
            if (isExpanded) {
                closeFAQItem($faqItem);
            } else {
                openFAQItem($faqItem);
            }
        });

        // Gestione tastiera (Enter e Spazio)
        $(document).on('keydown', '.faq__question', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                $(this).click();
            }
        });

        // Chiusura con ESC (chiude tutti gli item aperti)
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape' || e.keyCode === 27) {
                $('.faq__question[aria-expanded="true"]').each(function() {
                    closeFAQItem($(this).closest('.faq__item'));
                });
            }
        });
    }

    /**
     * Apre un singolo item FAQ
     * @param {jQuery} $faqItem - L'elemento .faq__item da aprire
     */
    function openFAQItem($faqItem) {
        const $button = $faqItem.find('.faq__question');
        const $answer = $faqItem.find('.faq__answer');
        const $icon = $faqItem.find('.faq__icon');

        // Aggiorna attributi ARIA
        $button.attr('aria-expanded', 'true');
        $answer.attr('aria-hidden', 'false');

        // Cambia icona
        $icon.text('−');

        // Calcola altezza necessaria
        const contentHeight = $answer.find('.faq__answer-content')[0].scrollHeight;
        $answer.css('max-height', contentHeight + 'px');

        // Trigger evento personalizzato
        $faqItem.trigger('faq:opened');
    }

    /**
     * Chiude un singolo item FAQ
     * @param {jQuery} $faqItem - L'elemento .faq__item da chiudere
     */
    function closeFAQItem($faqItem) {
        const $button = $faqItem.find('.faq__question');
        const $answer = $faqItem.find('.faq__answer');
        const $icon = $faqItem.find('.faq__icon');

        // Aggiorna attributi ARIA
        $button.attr('aria-expanded', 'false');
        $answer.attr('aria-hidden', 'true');

        // Cambia icona
        $icon.text('+');

        // Chiudi con animazione
        $answer.css('max-height', '0');

        // Trigger evento personalizzato
        $faqItem.trigger('faq:closed');
    }

    /**
     * Chiude tutti gli item FAQ eccetto quello specificato
     * @param {jQuery} $faqContainer - Il container .faq
     * @param {jQuery} $exceptItem - L'item da NON chiudere (opzionale)
     */
    function closeAllFAQItems($faqContainer, $exceptItem = null) {
        $faqContainer.find('.faq__item').each(function() {
            const $item = $(this);
            
            // Se è l'item da escludere, salta
            if ($exceptItem && $item.is($exceptItem)) {
                return;
            }
            
            const $button = $item.find('.faq__question');
            if ($button.attr('aria-expanded') === 'true') {
                closeFAQItem($item);
            }
        });
    }

    /**
     * Apre tutti gli item FAQ
     * @param {jQuery} $faqContainer - Il container .faq
     */
    function openAllFAQItems($faqContainer) {
        $faqContainer.find('.faq__item').each(function() {
            const $item = $(this);
            const $button = $item.find('.faq__question');
            
            if ($button.attr('aria-expanded') === 'false') {
                openFAQItem($item);
            }
        });
    }

    // Espone funzioni globalmente per uso esterno
    window.FAQManager = {
        open: openFAQItem,
        close: closeFAQItem,
        openAll: openAllFAQItems,
        closeAll: closeAllFAQItems
    };

})(jQuery);