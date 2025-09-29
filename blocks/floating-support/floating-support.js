/**
 * SCRIPT FLOATING SUPPORT - PANNELLO AIUTO FLOTTANTE
 * ==================================================
 * 
 * SCOPO EDUCATIVO: Questo script gestisce un pannello di supporto che rimane
 * fisso nella pagina e può essere aperto/chiuso dall'utente. Include funzioni
 * avanzate come "click outside to close" e gestione focus.
 * 
 * CONCETTI CHIAVE IMPARATI:
 * - Position fixed e z-index per elementi flottanti
 * - Event delegation per elementi dinamici
 * - Click outside detection pattern
 * - State management con classi CSS
 * - Event bubbling e stopPropagation
 * - Focus management per accessibilità
 */

// ========================================
// IIFE CON STRICT MODE  
// ========================================
(function($) {
    'use strict'; // Modalità strict per codice più sicuro

    // Inizializzazione al caricamento del DOM
    $(document).ready(function() {
        initFloatingSupport();
    });

    /**
     * FUNZIONE DI INIZIALIZZAZIONE PRINCIPALE
     * ======================================
     * Configura tutti gli event listener per i pannelli di supporto flottanti.
     * Gestisce apertura, chiusura, click outside e navigazione da tastiera.
     */
    function initFloatingSupport() {
        
        // ========================================
        // EVENT LISTENER PER TRIGGER PRINCIPALE
        // ========================================
        // Il trigger è il pulsante che apre/chiude il pannello
        $(document).on('click', '.floating-support__trigger', function(e) {
            e.preventDefault(); // Previene comportamento default del link/button
            
            // ========================================
            // RACCOLTA CONTESTO E STATO CORRENTE
            // ========================================
            const $trigger = $(this); // Il pulsante cliccato
            const $floatingSupport = $trigger.closest('.floating-support'); // Il container
            
            // CONTROLLO STATO: verifica se il pannello è già aperto
            const isOpen = $floatingSupport.hasClass('is-open');
            
            // ========================================
            // LOGICA TOGGLE CON GESTIONE MULTIPLI
            // ========================================
            if (isOpen) {
                // Se è aperto, chiudilo
                closeSupportPanel($floatingSupport);
            } else {
                // Se è chiuso, prima chiudi tutti gli altri pannelli aperti
                // (comportamento comune nei sistemi multi-pannello)
                $('.floating-support.is-open').each(function() {
                    closeSupportPanel($(this));
                });
                
                // Poi apri questo pannello
                openSupportPanel($floatingSupport);
            }
        });
        
        // ========================================
        // EVENT LISTENER PER PULSANTE CHIUSURA ESPLICITA
        // ========================================
        $(document).on('click', '.floating-support__close', function(e) {
            e.preventDefault();
            
            // Trova il container del pannello e chiudilo
            const $floatingSupport = $(this).closest('.floating-support');
            closeSupportPanel($floatingSupport);
        });
        
        // ========================================
        // CLICK OUTSIDE TO CLOSE PATTERN
        // ========================================
        // Tecnica avanzata: chiudi il pannello quando l'utente clicca fuori
        $(document).on('click', function(e) {
            const $target = $(e.target); // L'elemento effettivamente cliccato
            
            // ========================================
            // CONTROLLO SE IL CLICK È "OUTSIDE"
            // ========================================
            // .closest() ritorna elementi se trova un antenato che matcha
            // Se .length è 0, significa che il click NON è dentro un floating-support
            if (!$target.closest('.floating-support').length) {
                
                // Chiudi tutti i pannelli aperti
                $('.floating-support.is-open').each(function() {
                    closeSupportPanel($(this));
                });
            }
        });
        
        // ========================================
        // CHIUSURA CON TASTO ESC (ACCESSIBILITY)
        // ========================================
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape') {
                // Chiudi tutti i pannelli aperti quando si preme ESC
                $('.floating-support.is-open').each(function() {
                    closeSupportPanel($(this));
                });
            }
        });
    }

    /**
     * FUNZIONE DI APERTURA PANNELLO
     * =============================
     * Gestisce l'apertura del pannello con animazioni e gestione focus
     * 
     * @param {jQuery} $floatingSupport - L'elemento pannello da aprire
     */
    function openSupportPanel($floatingSupport) {
        
        // ========================================
        // VALIDAZIONE INPUT
        // ========================================
        if (!$floatingSupport.length) {
            return; // Esce se l'elemento non esiste
        }
        
        // ========================================
        // AGGIORNAMENTO STATO VISIVO
        // ========================================
        // Aggiunge classe CSS per styling e animazioni
        $floatingSupport.addClass('is-open');
        
        // ========================================
        // GESTIONE FOCUS PER ACCESSIBILITÀ
        // ========================================
        // Trova il primo elemento focusabile nel pannello
        const $firstFocusable = $floatingSupport.find('a, button, input, textarea, select, [tabindex]:not([tabindex="-1"])').first();
        
        // Se trova un elemento, gli dà il focus dopo un breve delay
        if ($firstFocusable.length) {
            setTimeout(function() {
                $firstFocusable.focus(); // Sposta il focus per screen reader
            }, 300); // Aspetta che l'animazione di apertura sia completata
        }
        
        // ========================================
        // CUSTOM EVENT PER INTEGRAZIONE
        // ========================================
        $floatingSupport.trigger('floating-support:opened');
    }

    /**
     * FUNZIONE DI CHIUSURA PANNELLO
     * =============================
     * Gestisce la chiusura del pannello con cleanup dello stato
     * 
     * @param {jQuery} $floatingSupport - L'elemento pannello da chiudere
     */
    function closeSupportPanel($floatingSupport) {
        
        // Validazione input
        if (!$floatingSupport.length) {
            return;
        }
        
        // ========================================
        // AGGIORNAMENTO STATO VISIVO
        // ========================================
        // Rimuove la classe che rende visibile il pannello
        $floatingSupport.removeClass('is-open');
        
        // ========================================
        // GESTIONE FOCUS DI RITORNO
        // ========================================
        // Trova il trigger che ha aperto il pannello per ripristinare il focus
        const $trigger = $floatingSupport.find('.floating-support__trigger');
        
        // Ripristina il focus sul trigger dopo la chiusura
        if ($trigger.length) {
            setTimeout(function() {
                $trigger.focus(); // Focus di ritorno per navigazione da tastiera
            }, 300);
        }
        
        // Custom event per notificare la chiusura
        $floatingSupport.trigger('floating-support:closed');
    }

    // ========================================
    // API PUBBLICA - NAMESPACE GLOBALE
    // ========================================
    // Espone funzioni per controllo programmatico esterno
    // ESEMPI DI USO:
    // FloatingSupportManager.open($('#support-panel'));    // Apri pannello specifico
    // FloatingSupportManager.close($('#support-panel'));   // Chiudi pannello specifico
    // FloatingSupportManager.closeAll();                   // Chiudi tutti i pannelli
    window.FloatingSupportManager = {
        open: openSupportPanel,     // Apertura singolo pannello
        close: closeSupportPanel,   // Chiusura singolo pannello
        closeAll: function() {      // Utility per chiusura massiva
            $('.floating-support').each(function() {
                closeSupportPanel($(this));
            });
        }
    };

})(jQuery); // Fine IIFE