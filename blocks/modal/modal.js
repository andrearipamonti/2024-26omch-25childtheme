/**
 * SCRIPT MODAL - SISTEMA COMPLETO DI DIALOG OVERLAY
 * ================================================
 * 
 * SCOPO EDUCATIVO: Questo script implementa un sistema completo di modal dialog
 * con funzionalità avanzate: auto-apertura, gestione cookie, focus trapping,
 * trigger multipli e accessibilità completa secondo le linee guida WAI-ARIA.
 * 
 * CONCETTI CHIAVE IMPARATI:
 * - Modal dialog pattern (WAI-ARIA)
 * - Cookie management per persistenza stato
 * - Focus trapping per accessibilità
 * - Event delegation e namespace eventi
 * - Auto-open con timer e condizioni
 * - Overlay click e ESC key handling
 * - Cross-component communication
 * - State management con data attributes
 */

// ========================================
// IIFE CON STRICT MODE
// ========================================
(function($) {
    'use strict'; // Modalità strict per JavaScript più sicuro

    // Inizializzazione al caricamento del DOM
    $(document).ready(function() {
        initModalBlocks();
    });

    /**
     * FUNZIONE DI INIZIALIZZAZIONE PRINCIPALE
     * ======================================
     * Scansiona la pagina per tutti i blocchi modal e li configura.
     * Gestisce sia modal auto-aperte che trigger esterni.
     */
    function initModalBlocks() {
        
        // ========================================
        // ITERAZIONE SU TUTTI I BLOCCHI MODAL
        // ========================================
        // Trova ogni blocco modal presente nella pagina
        $('.modal-block').each(function() {
            const $block = $(this); // Il blocco contenitore corrente
            
            // ========================================
            // LETTURA CONFIGURAZIONE DA DATA ATTRIBUTES
            // ========================================
            // Legge la configurazione dall'HTML data-* attributes
            const modalId = $block.data('modal-id');                    // ID univoco modal
            const $modal = $block.find('.modal-block__modal');          // L'elemento modal interno
            const autoOpen = $block.data('auto-open');                  // Se auto-aprire
            const autoDelay = parseInt($block.data('auto-delay')) || 3000; // Delay auto-apertura
            
            // ========================================
            // VALIDAZIONE CONFIGURAZIONE
            // ========================================
            if (!modalId) {
                console.warn('Modal block missing modal-id'); // Debug per sviluppatori
                return; // Salta questo blocco se configurazione invalida
            }
            
            // ========================================
            // SETUP MODAL ID E ACCESSIBILITÀ
            // ========================================
            // Imposta l'ID HTML per permettere riferimenti via CSS/JS
            $modal.attr('id', modalId);
            
            // Inizializza tutti i gestori di eventi per la chiusura
            initModalCloseHandlers($modal, modalId);
            
            // ========================================
            // GESTIONE AUTO-APERTURA CON COOKIE
            // ========================================
            if (autoOpen) {
                
                // ========================================
                // CONTROLLO COOKIE PER EVITARE RIAPERTURE
                // ========================================
                // Verifica se l'utente ha già chiuso permanentemente questa modal
                if (!getCookie(`modal-${modalId}-closed`)) {
                    
                    // ========================================
                    // TIMER AUTO-APERTURA
                    // ========================================
                    // Ritarda l'apertura per permettere caricamento completo pagina
                    setTimeout(function() {
                        openModal($modal, modalId);
                    }, autoDelay); // Delay configurabile via data-auto-delay
                }
            }
        });
        
        // ========================================
        // GESTIONE TRIGGER INTERNI DEL BLOCCO
        // ========================================
        // Gestisce i pulsanti trigger all'interno dello stesso blocco modal
        $(document).on('click', '.modal-block__trigger', function(e) {
            e.preventDefault(); // Previene comportamento default del button/link
            
            // Legge quale modal aprire dall'attributo data-modal-target
            const modalId = $(this).data('modal-target');
            const $modal = $('#' + modalId); // Trova modal per ID
            
            // Se la modal esiste, aprila
            if ($modal.length) {
                openModal($modal, modalId);
            }
        });
        
        // ========================================
        // GESTIONE TRIGGER ESTERNI
        // ========================================
        // Configura pulsanti esterni che possono aprire modal specifiche
        // Esempio HTML: <button data-modal-trigger="my-modal-id">Apri Modal</button>
        $(document).on('click', '[data-modal-trigger]', function(e) {
            e.preventDefault(); // Previene comportamento default del button/link
            
            // Legge quale modal aprire dall'attributo data
            const modalId = $(this).data('modal-trigger');
            const $modal = $('#' + modalId); // Trova modal per ID
            
            // Se la modal esiste, aprila
            if ($modal.length) {
                openModal($modal, modalId);
            }
        });
    }



    /**
     * FUNZIONE DI APERTURA MODAL
     * ==========================
     * Gestisce l'apertura di una modal con setup completo di accessibilità,
     * focus management e stato visuale.
     * 
     * @param {jQuery} $modal - L'elemento modal da aprire
     * @param {string} modalId - L'ID univoco della modal
     * @param {boolean} isAutoOpen - Se l'apertura è automatica o manuale
     */
    function openModal($modal, modalId, isAutoOpen = false) {
        
        // ========================================
        // CHIUSURA MODALI ESISTENTI
        // ========================================
        // Previene sovrapposizioni: chiude altre modal aperte prima di aprire questa
        closeAllModals();
        
        // ========================================
        // AGGIORNAMENTO STATO VISUALE
        // ========================================
        // Distingue visivamente tra apertura automatica e manuale
        if (isAutoOpen) {
            $modal.addClass('is-auto-opening'); // Classe per styling specifico auto-open
        }
        
        // ========================================
        // ATTIVAZIONE MODAL
        // ========================================
        $modal.addClass('is-open');              // Classe principale per visibilità
        $modal.attr('aria-hidden', 'false');    // Accessibilità: contenuto disponibile a screen reader
        $('body').addClass('modal-open');       // Previene scroll di background
        
        // ========================================
        // GESTIONE FOCUS PER ACCESSIBILITÀ
        // ========================================
        // Ritarda il focus per permettere alle animazioni CSS di completarsi
        setTimeout(function() {
            
            // ========================================
            // RICERCA PRIMO ELEMENTO FOCUSABILE
            // ========================================
            // Selettore complesso per trovare tutti gli elementi interattivi
            const $firstFocusable = $modal.find('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])').first();
            
            // Se trova un elemento focusabile, sposta il focus lì
            if ($firstFocusable.length) {
                $firstFocusable.focus(); // Focus trapping iniziale
            }
            
        }, isAutoOpen ? 500 : 300); // Delay maggiore per auto-open per evitare disturbi
        
        // ========================================
        // CUSTOM EVENT CON METADATA
        // ========================================
        // Emette evento con dati aggiuntivi per integrazioni esterne
        $modal.trigger('modal:opened', { 
            modalId: modalId, 
            isAutoOpen: isAutoOpen 
        });
    }

    /**
     * FUNZIONE DI CHIUSURA MODAL
     * ==========================
     * Gestisce la chiusura di una modal con cleanup completo di stato,
     * gestione cookie per persistenza e ripristino focus.
     * 
     * @param {jQuery} $modal - L'elemento modal da chiudere
     * @param {string} modalId - L'ID univoco della modal
     * @param {boolean} permanent - Se salvare la chiusura nei cookie
     */
    function closeModal($modal, modalId, permanent = false) {
        
        // ========================================
        // RIMOZIONE STATO VISUALE
        // ========================================
        // Rimuove tutte le classi di stato attivo
        $modal.removeClass('is-open is-auto-opening');
        
        // ========================================
        // AGGIORNAMENTO ACCESSIBILITÀ
        // ========================================
        // Nasconde il contenuto agli screen reader
        $modal.attr('aria-hidden', 'true');
        
        // ========================================
        // CLEANUP BODY CLASS CON DELAY
        // ========================================
        // Rimuove la classe modal-open dal body solo se non ci sono altre modal aperte
        setTimeout(function() {
            if ($('.modal-block__modal.is-open').length === 0) {
                $('body').removeClass('modal-open'); // Ripristina scroll di background
            }
        }, 300); // Delay per sincronizzare con animazioni CSS
        
        // ========================================
        // GESTIONE PERSISTENZA CON COOKIE
        // ========================================
        if (permanent) {
            // ========================================
            // SALVATAGGIO STATO NEI COOKIE
            // ========================================
            // Salva cookie per 30 giorni per ricordare che l'utente ha chiuso questa modal
            setCookie(`modal-${modalId}-closed`, 'true', 30);
            
            // Aggiunge classe per possibili stili specifici
            $modal.addClass('is-permanently-closed');
        }
        
        // ========================================
        // CUSTOM EVENT CON METADATA
        // ========================================
        $modal.trigger('modal:closed', { 
            modalId: modalId, 
            permanent: permanent 
        });
    }

    /**
     * CONFIGURAZIONE EVENT HANDLERS DI CHIUSURA
     * =========================================
     * Imposta tutti i meccanismi di chiusura per una modal specifica:
     * click overlay, pulsante close, tasto ESC.
     * 
     * @param {jQuery} $modal - L'elemento modal
     * @param {string} modalId - L'ID della modal
     */
    function initModalCloseHandlers($modal, modalId) {
        
        // ========================================
        // LETTURA CONFIGURAZIONE PERSISTENZA
        // ========================================
        const $block = $modal.closest('.modal-block');
        const preventReopen = $block.data('prevent-reopen'); // Se salvare chiusura nei cookie
        
        // ========================================
        // CLICK OVERLAY E PULSANTI CHIUSURA
        // ========================================
        // Selettore multiplo: overlay di sfondo E pulsanti con data-modal-close
        $modal.find('.modal-block__overlay, [data-modal-close]').on('click', function(e) {
            e.preventDefault();    // Previene comportamento default
            e.stopPropagation();  // Impedisce bubbling ad elementi parent
            
            // Chiude la modal, salvando stato se configurato
            closeModal($modal, modalId, preventReopen);
        });
        
        // ========================================
        // TASTO ESC PER CHIUSURA RAPIDA
        // ========================================
        // Event listener globale per intercettare ESC
        $(document).on('keydown', function(e) {
            
            // ========================================
            // CONTROLLO CONDIZIONI DI CHIUSURA
            // ========================================
            // Chiudi solo se:
            // 1. Il tasto premuto è ESC
            // 2. Questa specifica modal è attualmente aperta
            if (e.key === 'Escape' && $modal.hasClass('is-open')) {
                closeModal($modal, modalId, preventReopen);
            }
        });
    }

    /**
     * FUNZIONE UTILITÀ: CHIUDI TUTTE LE MODAL
     * =======================================
     * Utility per chiudere tutte le modal aperte contemporaneamente.
     * Utile per prevenire sovrapposizioni o per reset globale.
     */
    function closeAllModals() {
        
        // ========================================
        // ITERAZIONE SU TUTTE LE MODAL APERTE
        // ========================================
        // Trova tutte le modal con classe "is-open" e le chiude
        $('.modal-block__modal.is-open').each(function() {
            const $modal = $(this);                               // Modal corrente
            const $block = $modal.closest('.modal-block');       // Blocco contenitore  
            const modalId = $block.data('modal-id');             // ID per identificazione
            
            // ========================================
            // CHIUSURA NON PERMANENTE
            // ========================================
            // false = non salvare nei cookie (chiusura temporanea)
            // Usato quando si vuole solo fare spazio per aprire un'altra modal
            closeModal($modal, modalId, false);
        });
    }

    /**
     * FUNZIONE GESTIONE COOKIE - SCRITTURA
     * ====================================
     * Salva un cookie con nome, valore e scadenza specificati.
     * Usata per ricordare le modal chiuse permanentemente dall'utente.
     * 
     * @param {string} name - Nome del cookie
     * @param {string} value - Valore da salvare
     * @param {number} days - Giorni di validità del cookie
     */
    function setCookie(name, value, days) {
        
        // ========================================
        // CALCOLO SCADENZA COOKIE
        // ========================================
        let expires = ''; // Stringa vuota = cookie di sessione (scade alla chiusura browser)
        
        if (days) {
            // ========================================
            // CREAZIONE DATA DI SCADENZA
            // ========================================
            const date = new Date();                                        // Data corrente
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000)); // Aggiunge giorni in millisecondi
            expires = '; expires=' + date.toUTCString();                   // Formato UTC per header HTTP
        }
        
        // ========================================
        // SCRITTURA COOKIE CON SICUREZZA
        // ========================================
        // FORMAT: name=value; expires=data; path=percorso; SameSite=policy
        document.cookie = name + '=' + (value || '') + expires + '; path=/; SameSite=Strict';
        //                                                         ↑        ↑
        //                                                    Disponibile   Protezione
        //                                                    su tutto il   CSRF/XSS
        //                                                    sito
    }

    /**
     * FUNZIONE GESTIONE COOKIE - LETTURA
     * ==================================
     * Legge il valore di un cookie specifico dalla stringa cookie del browser.
     * Implementa parsing manuale della stringa cookie.
     * 
     * @param {string} name - Nome del cookie da cercare
     * @returns {string|null} - Valore del cookie o null se non trovato
     */
    function getCookie(name) {
        
        // ========================================
        // PREPARAZIONE PATTERN DI RICERCA
        // ========================================
        const nameEQ = name + '='; // Pattern "nome=" per identificare il cookie
        
        // ========================================
        // PARSING STRINGA COOKIE
        // ========================================
        // document.cookie restituisce tutti i cookie come stringa "name1=value1; name2=value2; ..."
        const ca = document.cookie.split(';'); // Divide la stringa sui punti e virgola
        
        // ========================================
        // ITERAZIONE E RICERCA
        // ========================================
        for (let i = 0; i < ca.length; i++) {
            let c = ca[i]; // Cookie corrente (es: " name=value")
            
            // ========================================
            // RIMOZIONE SPAZI INIZIALI
            // ========================================
            // I cookie possono avere spazi dopo il ";" nel formato "name1=value1; name2=value2"
            while (c.charAt(0) === ' ') {
                c = c.substring(1, c.length); // Rimuove caratteri spazio dall'inizio
            }
            
            // ========================================
            // CONTROLLO MATCH E ESTRAZIONE VALORE
            // ========================================
            // .indexOf() cerca la posizione del pattern. 0 significa "inizia con"
            if (c.indexOf(nameEQ) === 0) {
                // Trovato! Estrae solo il valore (dopo "nome=")
                return c.substring(nameEQ.length, c.length);
            }
        }
        
        // Cookie non trovato
        return null;
    }

    // ========================================
    // API PUBBLICA - NAMESPACE GLOBALE
    // ========================================
    // Espone un'interfaccia pubblica per controllo programmatico delle modal
    // da parte di altri script, plugin o integrazioni esterne.
    window.ModalManager = {
        
        // ========================================
        // APERTURA MODAL PER ID
        // ========================================
        open: function(modalId) {
            // Template literal per costruire selettore ID
            const $modal = $(`#${modalId}`);
            
            // Validazione esistenza prima di procedere
            if ($modal.length) {
                openModal($modal, modalId);
            }
        },
        
        // ========================================
        // CHIUSURA MODAL PER ID
        // ========================================
        close: function(modalId, permanent = false) {
            const $modal = $(`#${modalId}`);
            
            if ($modal.length) {
                closeModal($modal, modalId, permanent);
            }
        },
        
        // ========================================
        // CHIUSURA MASSIVA
        // ========================================
        closeAll: closeAllModals // Riferimento diretto alla funzione utility
    };

})(jQuery); // Fine IIFE - Sistema modal completo