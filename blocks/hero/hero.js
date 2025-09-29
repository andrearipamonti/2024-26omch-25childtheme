/**
 * SCRIPT HERO - SMOOTH SCROLL NAVIGATION
 * =====================================
 * 
 * SCOPO EDUCATIVO: Questo script implementa una navigazione fluida (smooth scroll)
 * per i pulsanti hero che puntano a sezioni specifiche della pagina. Include
 * calcolo automatico dell'offset per header fissi e gestione accessibilità.
 * 
 * CONCETTI CHIAVE IMPARATI:
 * - Smooth scroll animation con jQuery
 * - Data attributes per configurazione
 * - Keyboard event handling per accessibilità
 * - Custom events per comunicazione componenti
 * - Offset calculation per layout responsive
 * - Cross-browser smooth scrolling
 */

// ========================================
// IIFE CON STRICT MODE
// ========================================
(function($) {
    'use strict'; // Modalità strict per prevenire errori comuni

    // Inizializzazione al caricamento del DOM
    $(document).ready(function() {
        initHeroScroll();
    });

    /**
     * FUNZIONE DI INIZIALIZZAZIONE PRINCIPALE
     * ======================================
     * Configura gli event listener per i pulsanti di scroll del hero.
     * Gestisce sia interazione mouse che navigazione da tastiera.
     */
    function initHeroScroll() {
        
        // ========================================
        // EVENT LISTENER PER CLICK SUI PULSANTI SCROLL
        // ========================================
        // SELETTORE MULTIPLO: gestisce diverse classi di pulsanti hero
        $(document).on('click', '.hero__cta-button--scroll, .hero__scroll-button', function(e) {
            e.preventDefault(); // Previene comportamento default del button/link
            
            const $button = $(this); // Il pulsante cliccato
            
            // ========================================
            // LETTURA CONFIGURAZIONE DA DATA ATTRIBUTE
            // ========================================
            // .data() legge l'attributo data-scroll-target dall'HTML
            // Esempio HTML: <button data-scroll-target="#about-section">Scopri di più</button>
            const target = $button.data('scroll-target');
            
            // ========================================
            // VALIDAZIONE E ESECUZIONE SCROLL
            // ========================================
            if (target) {
                smoothScrollTo(target); // Esegue lo scroll fluido
                
                // ========================================
                // CUSTOM EVENT CON DATI AGGIUNTIVI
                // ========================================
                // .trigger() con secondo parametro passa dati all'evento
                // Altri script possono ascoltare: $button.on('hero:scroll-initiated', function(e, data) {...})
                $button.trigger('hero:scroll-initiated', { target: target });
            }
        });

        // ========================================
        // GESTIONE TASTIERA PER ACCESSIBILITÀ
        // ========================================
        // Implementa le WAI-ARIA guidelines per navigazione da tastiera
        $(document).on('keydown', '.hero__cta-button--scroll, .hero__scroll-button', function(e) {
            
            // ========================================
            // CONTROLLO TASTI SPECIFICI
            // ========================================
            // ENTER: tasto standard per attivazione
            // SPACE: tasto alternativo per pulsanti (standard HTML)  
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault(); // Previene scroll di pagina con SPACE
                
                // SIMULAZIONE CLICK: triggera gli stessi eventi del mouse
                $(this).click(); // Riutilizza la logica dell'event handler click
            }
        });
    }

    /**
     * FUNZIONE SMOOTH SCROLL PRINCIPALE
     * ================================
     * Esegue uno scroll fluido verso un elemento target della pagina,
     * calcolando automaticamente offset per header fissi.
     * 
     * @param {string} target - Il selettore CSS del target (es: '#about-section')
     */
    function smoothScrollTo(target) {
        
        // ========================================
        // NORMALIZZAZIONE SELETTORE
        // ========================================
        // Assicura che il target abbia il prefisso corretto per CSS selectors
        if (!target.startsWith('#') && !target.startsWith('.')) {
            target = '#' + target; // Aggiunge # se manca
        }

        // ========================================
        // VALIDAZIONE TARGET NEL DOM
        // ========================================
        const $target = $(target); // Cerca l'elemento nel DOM
        
        // Se l'elemento non esiste, logga un warning e interrompe
        if ($target.length === 0) {
            console.warn('Hero scroll target not found:', target);
            return; // Exit early se target non trovato
        }

        // ========================================
        // CALCOLO POSIZIONE CON OFFSET DINAMICO
        // ========================================
        // Ottiene l'offset da header fissi o altri elementi flottanti
        const headerOffset = getHeaderOffset();
        
        // .offset().top restituisce la posizione assoluta dell'elemento rispetto al documento
        const targetOffset = $target.offset().top - headerOffset;
        
        // ========================================
        // ANIMAZIONE SMOOTH SCROLL
        // ========================================
        // Anima sia html che body per compatibilità cross-browser
        // (alcuni browser animano html, altri body)
        $('html, body').animate({
            scrollTop: targetOffset // Posizione finale dello scroll
        }, {
            duration: 800,        // Durata animazione in millisecondi
            easing: 'swing',      // Tipo di easing (accelerazione/decelerazione)
            
            // ========================================
            // CALLBACK AL COMPLETAMENTO
            // ========================================
            complete: function() {
                
                // ========================================
                // GESTIONE FOCUS PER ACCESSIBILITÀ
                // ========================================
                // Imposta tabindex temporaneo per permettere il focus programmatico
                // Gli screen reader seguiranno lo scroll e leggeranno il contenuto
                $target.attr('tabindex', '-1').focus();
                
                // ========================================
                // CUSTOM EVENT DI COMPLETAMENTO
                // ========================================
                $(document).trigger('hero:scroll-completed', { 
                    target: target,      // Il selettore usato
                    element: $target[0]  // L'elemento DOM nativo per altre integrazioni
                });
            }
        });
    }

    /**
     * FUNZIONE CALCOLO OFFSET HEADER
     * ==============================
     * Calcola automaticamente l'altezza di eventuali header fissi
     * per posizionare correttamente lo scroll target.
     * 
     * @returns {number} L'altezza in pixel dell'header fisso
     */
    function getHeaderOffset() {
        
        // ========================================
        // RICERCA AUTOMATICA HEADER FISSI
        // ========================================
        // Seleziona elementi header comuni e filtra solo quelli con position:fixed
        const $fixedHeaders = $('header, .header, .navbar').filter(function() {
            // .css('position') legge la proprietà CSS computata finale
            return $(this).css('position') === 'fixed';
        });
        
        // ========================================
        // CALCOLO ALTEZZA SE TROVATO HEADER FISSO
        // ========================================
        if ($fixedHeaders.length > 0) {
            // ========================================
            // MISURAZIONE ALTEZZA HEADER
            // ========================================
            // .outerHeight() include padding e border (più accurato di .height())
            // .first() prende il primo header se ce ne sono multipli
            // || 0 fallback nel caso outerHeight() restituisca undefined/null
            return $fixedHeaders.first().outerHeight() || 0;
        }
        
        // ========================================
        // FALLBACK OFFSET PREDEFINITO
        // ========================================
        // Se non trova header fissi, usa un offset standard
        // 80px è un valore common per header tipici
        return 80;
    }

    // ========================================
    // API PUBBLICA - NAMESPACE GLOBALE  
    // ========================================
    // Espone funzioni utili per uso da altri script
    // ESEMPI DI USO:
    // HeroManager.scrollTo('#contact');              // Scroll a sezione specifica
    // const offset = HeroManager.getHeaderOffset();  // Ottieni altezza header
    window.HeroManager = {
        scrollTo: smoothScrollTo,         // Funzione di scroll fluido
        getHeaderOffset: getHeaderOffset  // Funzione calcolo offset
    };

    // ========================================
    // GESTIONE HASH URL AL CARICAMENTO PAGINA
    // ========================================
    // Se l'utente arriva alla pagina con un hash nell'URL (es: esempio.com#contatti)
    // esegue automaticamente lo scroll fluido alla sezione target
    $(window).on('load', function() {
        
        // WINDOW.LOCATION.HASH: proprietà del browser che contiene la parte dopo #
        const hash = window.location.hash; // Es: "#about-section"
        
        // Verifica che ci sia un hash valido (non vuoto e più lungo di solo "#")
        if (hash && hash.length > 1) {
            
            // ========================================
            // DELAY PER COMPLETAMENTO CARICAMENTO
            // ========================================
            // setTimeout ritarda lo scroll per permettere a:
            // 1. CSS di applicarsi completamente
            // 2. Immagini di caricarsi e calcolare altezze corrette
            // 3. Altri script di inizializzarsi
            setTimeout(function() {
                smoothScrollTo(hash); // Esegue scroll fluido al target
            }, 500); // 500ms di delay
        }
    });

})(jQuery); // Fine IIFE