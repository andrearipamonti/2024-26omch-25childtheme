/**
 * SCRIPT CALLOUT - GESTIONE NOTIFICHE DISMISSIBLE
 * ==============================================
 * 
 * SCOPO EDUCATIVO: Questo script gestisce i blocchi di notifica (callout) 
 * che possono essere chiusi dall'utente con animazioni fluide.
 * 
 * CONCETTI CHIAVE IMPARATI:
 * - IIFE (Immediately Invoked Function Expression)
 * - Event delegation in jQuery 
 * - Animazioni slideUp/slideDown
 * - Event bubbling e preventDefault()
 * - Custom events per comunicazione tra componenti
 */

// ========================================
// IIFE - IMMEDIATELY INVOKED FUNCTION EXPRESSION
// ========================================
// Questo pattern serve per:
// 1. Evitare inquinamento del namespace globale
// 2. Creare uno scope privato per le variabili
// 3. Passare jQuery come parametro "$" per evitare conflitti con altre librerie
(function($) {
    'use strict'; // Modalità strict: errori più chiari, codice più sicuro

    // ========================================
    // INIZIALIZZAZIONE AL DOM READY
    // ========================================
    // $(document).ready() si esegue quando il DOM HTML è completamente caricato
    // È più veloce di $(window).load() che aspetta anche immagini e CSS
    $(document).ready(function() {
        initCalloutClose(); // Avvia l'inizializzazione degli event listeners
    });

    /**
     * FUNZIONE DI INIZIALIZZAZIONE PRINCIPALE
     * =====================================
     * Configura tutti gli event listener per i callout presenti nella pagina.
     * Usa EVENT DELEGATION per gestire anche callout aggiunti dinamicamente.
     */
    function initCalloutClose() {
        
        // ========================================
        // EVENT DELEGATION PER CLICK SUL PULSANTE CHIUDI
        // ========================================
        // $(document).on() è EVENT DELEGATION: ascolta eventi su document
        // ma esegue il callback solo se l'elemento cliccato matcha il selettore
        // VANTAGGIO: funziona anche per elementi aggiunti dinamicamente dopo il caricamento
        $(document).on('click', '.callout__close', function(e) {
            
            // PREVENTDEFAULT: impedisce l'azione predefinita del browser
            // Se il pulsante è un <a href="#">, impedisce il scroll in cima alla pagina
            // Se è un <button>, impedisce l'invio di eventuali form
            e.preventDefault();
            
            // TRAVERSING DEL DOM: naviga nella struttura HTML
            // $(this) = l'elemento che ha scatenato l'evento (il pulsante cliccato)
            const $button = $(this);
            
            // .closest() risale nell'albero DOM cercando il primo antenato 
            // che corrisponde al selettore '.callout'
            const $callout = $button.closest('.callout');
            
            // Chiama la funzione di chiusura passando l'elemento callout trovato
            closeCallout($callout);
        });

        // ========================================
        // GESTIONE TASTIERA - TASTO ESC
        // ========================================
        // Ascolta gli eventi di tastiera a livello globale (document)
        $(document).on('keydown', function(e) {
            
            // COMPATIBILITÀ BROWSER: controlla sia la proprietà moderna che quella legacy
            // e.key è la versione moderna, e.keyCode per browser più vecchi
            if (e.key === 'Escape' || e.keyCode === 27) {
                
                // SELETTORE CON PSEUDO-CLASSE: trova callout attualmente visibili
                // :visible è una pseudo-classe jQuery che filtra elementi visibili
                const $visibleCallouts = $('.callout:visible');
                
                // Se ci sono callout visibili...
                if ($visibleCallouts.length > 0) {
                    // .last() prende l'ultimo elemento della collezione
                    // Logica: chiude l'ultimo callout aperto (stack LIFO)
                    closeCallout($visibleCallouts.last());
                }
            }
        });
    }

    /**
     * FUNZIONE DI CHIUSURA CON ANIMAZIONE
     * =================================
     * Gestisce la chiusura fluida di un callout con controlli di sicurezza
     * 
     * @param {jQuery} $callout - L'oggetto jQuery del callout da chiudere
     */
    function closeCallout($callout) {
        
        // ========================================
        // VALIDAZIONE DELL'INPUT E STATO
        // ========================================
        // CONTROLLO ESISTENZA: $callout.length è il numero di elementi nella collezione jQuery
        // Se è 0, significa che l'elemento non esiste nel DOM
        // CONTROLLO STATO: .hasClass() verifica se l'elemento ha già la classe 'is-closing'
        // Previene animazioni multiple sullo stesso elemento
        if ($callout.length === 0 || $callout.hasClass('is-closing')) {
            return; // Esce dalla funzione se le condizioni non sono soddisfatte
        }

        // ========================================
        // AGGIUNTA CLASSE DI STATO
        // ========================================
        // .addClass() aggiunge una classe CSS all'elemento
        // 'is-closing' può essere usata per:
        // 1. Styling CSS (es. opacità ridotta)
        // 2. Prevenire doppi click come visto sopra
        // 3. Debugging visivo durante sviluppo
        $callout.addClass('is-closing');

        // ========================================
        // ANIMAZIONE TEMPORIZZATA
        // ========================================
        // setTimeout() ritarda l'esecuzione del codice
        // Permette al CSS di applicare eventuali transizioni della classe 'is-closing'
        setTimeout(function() {
            
            // SLIDEUP: animazione jQuery che riduce l'altezza a 0 gradualmente
            // Parametri: (durata_in_ms, callback_al_completamento)
            $callout.slideUp(300, function() {
                
                // CALLBACK FUNCTION: si esegue DOPO che slideUp è completato
                // .hide() è ridondante dopo slideUp ma garantisce che l'elemento sia nascosto
                $callout.hide();
                
                // ========================================
                // CUSTOM EVENT - COMUNICAZIONE TRA COMPONENTI
                // ========================================
                // .trigger() emette un evento personalizzato
                // Altri script possono ascoltare questo evento con .on('callout:closed', ...)
                // NAMING CONVENTION: 'namespace:action' previene conflitti
                $callout.trigger('callout:closed');
            });
            
        }, 300); // Aspetta 300ms per permettere transizioni CSS
    }

    /**
     * FUNZIONE DI APERTURA PROGRAMMATICA 
     * =================================
     * Utility per mostrare callout nascosti via JavaScript
     * Utile per logiche complesse o integrazioni con altri sistemi
     * 
     * @param {jQuery} $callout - L'oggetto jQuery del callout da mostrare
     */
    function showCallout($callout) {
        
        // VALIDAZIONE RAPIDA: verifica solo l'esistenza dell'elemento
        if ($callout.length === 0) {
            return;
        }

        // ========================================
        // RIPRISTINO STATO E ANIMAZIONE
        // ========================================
        // .removeClass() rimuove la classe di stato precedente
        $callout.removeClass('is-closing');
        
        // SLIDEDOWN: animazione opposta a slideUp, espande l'altezza da 0
        $callout.slideDown(300, function() {
            
            // .show() garantisce che l'elemento sia visibile
            // (slideDown già lo rende visibile, ma è buona pratica per chiarezza)
            $callout.show();
            
            // Custom event per notificare l'apertura ad altri componenti
            $callout.trigger('callout:shown');
        });
    }

    // ========================================
    // API PUBBLICA - NAMESPACE GLOBALE
    // ========================================
    // ESEMPI DI USO DALL'ESTERNO:
    // CalloutManager.close($('#my-callout'));     // Chiude un callout specifico
    // CalloutManager.show($('#hidden-callout'));  // Mostra un callout nascosto
    window.CalloutManager = {
        close: closeCallout,    // Funzione per chiudere callout
        show: showCallout      // Funzione per mostrare callout
    };

})(jQuery); // Fine IIFE - passa jQuery come parametro