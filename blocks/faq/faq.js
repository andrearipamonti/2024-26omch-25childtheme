/**
 * SCRIPT FAQ - GESTIONE ACCORDION ACCESSIBILE
 * ==========================================
 * 
 * SCOPO EDUCATIVO: Questo script implementa un sistema di FAQ (Frequently Asked Questions)
 * con pattern accordion completamente accessibile e navigabile da tastiera.
 * 
 * CONCETTI CHIAVE IMPARATI:
 * - Pattern Accordion per UI/UX
 * - Attributi ARIA per accessibilità
 * - Navigazione da tastiera (WAI-ARIA guidelines)
 * - Data attributes per configurazione
 * - Event delegation e state management
 * - Animazioni fluide con max-height
 */

// ========================================
// IIFE CON STRICT MODE
// ========================================
(function($) {
    'use strict'; // Strict mode per JavaScript più sicuro e performante

    // Inizializzazione al caricamento del DOM
    $(document).ready(function() {
        initFAQ(); // Avvia la configurazione degli accordions
    });

    /**
     * FUNZIONE DI INIZIALIZZAZIONE PRINCIPALE
     * ====================================== 
     * Configura tutti gli event listeners per FAQ accordion.
     * Gestisce sia interazione mouse che navigazione da tastiera.
     */
    function initFAQ() {
        
        // ========================================
        // EVENT LISTENER PER CLICK SULLE DOMANDE FAQ
        // ========================================
        $(document).on('click', '.faq__question', function(e) {
            e.preventDefault(); // Previene comportamento default del button/link
            
            // RACCOLTA ELEMENTI E CONTESTO
            const $button = $(this);                           // Il pulsante cliccato
            const $faqItem = $button.closest('.faq__item');    // L'item FAQ contenitore
            const $faqContainer = $button.closest('.faq');     // Il container di tutte le FAQ
            
            // ========================================
            // LETTURA CONFIGURAZIONE DA DATA ATTRIBUTES
            // ========================================
            // .data() legge attributi HTML5 data-* convertendo kebab-case in camelCase
            // data-allow-multiple="true" diventa allowMultiple
            const allowMultiple = $faqContainer.data('allow-multiple');
            
            // ========================================
            // CONTROLLO STATO CORRENTE CON ARIA
            // ========================================
            // ARIA-EXPANDED: attributo accessibility che indica se l'elemento è espanso
            // Screen reader e altri assistive technology leggono questo stato
            const isExpanded = $button.attr('aria-expanded') === 'true';
            
            // LOGICA DI TOGGLING
            if (isExpanded) {
                // Se è aperto, chiudilo
                closeFAQItem($faqItem);
            } else {
                // Se è chiuso, aprilo
                
                // ========================================
                // GESTIONE MODALITÀ ACCORDION SINGOLA
                // ========================================
                // Se non permette aperture multiple, chiudi tutti gli altri item
                if (!allowMultiple) {
                    closeAllFAQItems($faqContainer, $faqItem); // Escludi l'item corrente
                }
                
                openFAQItem($faqItem); // Apri l'item selezionato
            }
        });
        
        // ========================================
        // NAVIGAZIONE DA TASTIERA (ACCESSIBILITY)
        // ========================================
        // Implementa le WAI-ARIA Authoring Practices per accordion
        $(document).on('keydown', '.faq__question', function(e) {
            const $button = $(this);
            const $faqContainer = $button.closest('.faq');
            
            // COLLEZIONE DI TUTTI I PULSANTI FAQ NEL CONTAINER
            const $allButtons = $faqContainer.find('.faq__question');
            
            // INDEX CORRENTE: .index() trova la posizione dell'elemento nella collezione
            const currentIndex = $allButtons.index($button);
            
            // ========================================
            // SWITCH STATEMENT PER GESTIRE TASTI SPECIFICI
            // ========================================
            switch(e.key) {
                
                // FRECCIA GIÙ: vai al prossimo item (con wrap-around)
                case 'ArrowDown':
                    e.preventDefault(); // Previene lo scroll della pagina
                    
                    // MODULO OPERATOR: assicura wrap-around alla fine della lista
                    const nextIndex = (currentIndex + 1) % $allButtons.length;
                    $allButtons.eq(nextIndex).focus(); // .focus() sposta il focus
                    break;
                    
                // FRECCIA SU: vai al precedente item (con wrap-around)
                case 'ArrowUp':
                    e.preventDefault();
                    
                    // WRAP-AROUND INVERSO: se siamo a 0, va all'ultimo elemento
                    const prevIndex = (currentIndex - 1 + $allButtons.length) % $allButtons.length;
                    $allButtons.eq(prevIndex).focus();
                    break;
                    
                // HOME: vai al primo item
                case 'Home':
                    e.preventDefault();
                    $allButtons.first().focus();
                    break;
                    
                // END: vai all'ultimo item  
                case 'End':
                    e.preventDefault();
                    $allButtons.last().focus();
                    break;
            }
        });
    }

    /**
     * FUNZIONE DI APERTURA FAQ ITEM
     * =============================
     * Gestisce l'apertura di un singolo item FAQ con animazioni e accessibilità
     * 
     * @param {jQuery} $faqItem - L'elemento jQuery dell'item FAQ da aprire
     */
    function openFAQItem($faqItem) {
        
        // ========================================
        // RACCOLTA ELEMENTI FIGLI
        // ========================================
        // .find() cerca discendenti all'interno dell'elemento corrente
        const $button = $faqItem.find('.faq__question');    // Il pulsante cliccabile
        const $answer = $faqItem.find('.faq__answer');      // Container della risposta
        const $icon = $faqItem.find('.faq__icon');          // Icona indicatrice di stato

        // ========================================
        // AGGIORNAMENTO ATTRIBUTI ARIA (ACCESSIBILITY)
        // ========================================
        // ARIA-EXPANDED: informa gli screen reader dello stato espanso
        $button.attr('aria-expanded', 'true');
        
        // ARIA-HIDDEN: false rende il contenuto leggibile agli assistive technology
        $answer.attr('aria-hidden', 'false');

        // ========================================
        // AGGIORNAMENTO ICONA VISUALE
        // ========================================
        // Cambia l'icona da "+" a "−" per indicare visivamente lo stato aperto
        // .text() modifica il contenuto testuale dell'elemento
        $icon.text('−');

        // ========================================
        // ANIMAZIONE CON CALCOLO DINAMICO ALTEZZA
        // ========================================
        // SCROLLHEIGHT: altezza reale del contenuto indipendentemente da CSS
        // Navighiamo fino al contenuto interno per una misura più precisa
        const contentHeight = $answer.find('.faq__answer-content')[0].scrollHeight;
        
        // Imposta max-height per attivare la transizione CSS
        // Dalla risposta chiusa (max-height: 0) a quella aperta
        $answer.css('max-height', contentHeight + 'px');

        // ========================================
        // CUSTOM EVENT PER INTEGRAZIONE
        // ========================================
        // Permette ad altri script di reagire all'apertura della FAQ
        $faqItem.trigger('faq:opened');
    }

    /**
     * FUNZIONE DI CHIUSURA FAQ ITEM
     * =============================
     * Gestisce la chiusura di un singolo item FAQ con animazioni fluide
     * 
     * @param {jQuery} $faqItem - L'elemento jQuery dell'item FAQ da chiudere
     */
    function closeFAQItem($faqItem) {
        
        // ========================================
        // RACCOLTA ELEMENTI FIGLI
        // ========================================
        const $button = $faqItem.find('.faq__question');    // Pulsante della domanda
        const $answer = $faqItem.find('.faq__answer');      // Container risposta
        const $icon = $faqItem.find('.faq__icon');          // Icona di stato

        // ========================================
        // AGGIORNAMENTO ATTRIBUTI ARIA
        // ========================================
        // Notifica agli screen reader che l'elemento è ora chiuso
        $button.attr('aria-expanded', 'false');  // Stato: non espanso
        $answer.attr('aria-hidden', 'true');    // Contenuto: nascosto agli assistive technology

        // ========================================
        // AGGIORNAMENTO ICONA VISUALE
        // ========================================
        // Ripristina l'icona "+" per indicare che può essere riaperto
        $icon.text('+');

        // ========================================
        // ANIMAZIONE DI CHIUSURA
        // ========================================
        // Imposta max-height a 0 per attivare la transizione CSS
        // L'elemento si "ripiega" gradualmente fino a scomparire
        $answer.css('max-height', '0');

        // ========================================
        // CUSTOM EVENT PER NOTIFICA CHIUSURA
        // ========================================
        $faqItem.trigger('faq:closed');
    }

    /**
     * FUNZIONE UTILITÀ: CHIUDI TUTTI TRANNE UNO
     * =========================================
     * Implementa la logica "accordion singola" chiudendo tutti gli item 
     * eccetto quello specificato. Essenziale per il comportamento accordion.
     * 
     * @param {jQuery} $faqContainer - Il container principale delle FAQ
     * @param {jQuery} $exceptItem - L'item da NON chiudere (opzionale)
     */
    function closeAllFAQItems($faqContainer, $exceptItem = null) {
        
        // ========================================
        // ITERAZIONE SU TUTTI GLI ITEM FAQ
        // ========================================
        // .find().each() trova tutti gli item FAQ e itera su ognuno
        $faqContainer.find('.faq__item').each(function() {
            const $item = $(this); // L'item corrente nell'iterazione
            
            // ========================================
            // LOGICA DI ESCLUSIONE
            // ========================================
            // Se è specificato un item da escludere E questo è quell'item
            if ($exceptItem && $item.is($exceptItem)) {
                return; // RETURN in .each() = continue (salta questo item)
            }
            
            // ========================================
            // CONTROLLO STATO E CHIUSURA CONDIZIONALE
            // ========================================
            const $button = $item.find('.faq__question');
            
            // Chiudi solo se è attualmente aperto (ottimizzazione performance)
            if ($button.attr('aria-expanded') === 'true') {
                closeFAQItem($item);
            }
        });
    }

    /**
     * FUNZIONE UTILITÀ: APRI TUTTI
     * ============================
     * Utility per aprire tutti gli item FAQ. Utile per "Espandi tutto"
     * 
     * @param {jQuery} $faqContainer - Il container principale delle FAQ
     */
    function openAllFAQItems($faqContainer) {
        
        // Itera su tutti gli item FAQ nel container
        $faqContainer.find('.faq__item').each(function() {
            const $item = $(this);
            const $button = $item.find('.faq__question');
            
            // ========================================
            // APERTURA CONDIZIONALE
            // ========================================
            // Apri solo se è attualmente chiuso (evita animazioni inutili)
            if ($button.attr('aria-expanded') === 'false') {
                openFAQItem($item);
            }
        });
    }

    // ========================================
    // API PUBBLICA - NAMESPACE GLOBALE
    // ========================================
    // Espone funzioni nel window object per controllo programmatico esterno
    // ESEMPI DI USO:
    // FAQManager.open($('#faq-item-1'));           // Apri un FAQ specifico
    // FAQManager.close($('#faq-item-2'));         // Chiudi un FAQ specifico  
    // FAQManager.openAll($('#faq-container'));    // Apri tutte le FAQ in un container
    // FAQManager.closeAll($('#faq-container'));   // Chiudi tutte le FAQ in un container
    window.FAQManager = {
        open: openFAQItem,        // Apertura singola FAQ
        close: closeFAQItem,      // Chiusura singola FAQ
        openAll: openAllFAQItems, // Apertura massiva FAQ
        closeAll: closeAllFAQItems // Chiusura massiva FAQ
    };

})(jQuery); // Fine IIFE con jQuery passato come parametro