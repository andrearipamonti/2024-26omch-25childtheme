/**
 * Modal Block JavaScript
 * Gestisce apertura/chiusura modal, auto-open e gestione cookie
 */

(function($) {
    'use strict';

    // Inizializza quando il DOM è pronto
    $(document).ready(function() {
        initModalBlocks();
    });

    /**
     * Inizializza tutti i blocchi modal nella pagina
     */
    function initModalBlocks() {
        $('.modal-block').each(function() {
            const $block = $(this);
            initSingleModal($block);
        });
    }

    /**
     * Inizializza una singola modal
     * @param {jQuery} $block - Il blocco modal
     */
    function initSingleModal($block) {
        const modalId = $block.data('modal-id');
        const autoOpen = $block.data('auto-open');
        const autoDelay = parseInt($block.data('auto-delay')) || 3;
        const preventReopen = $block.data('prevent-reopen');
        
        const $modal = $block.find('.modal-block__modal');
        const $trigger = $block.find('.modal-block__trigger');
        
        // Gestione click sul trigger
        $trigger.on('click', function(e) {
            e.preventDefault();
            openModal($modal, modalId);
        });
        
        // Gestione chiusura modal
        initModalCloseHandlers($modal, modalId);
        
        // Auto-open se abilitato
        if (autoOpen) {
            handleAutoOpen($modal, modalId, autoDelay, preventReopen);
        }
    }

    /**
     * Gestisce l'auto-apertura della modal
     */
    function handleAutoOpen($modal, modalId, delay, preventReopen) {
        // Controlla se la modal è stata già chiusa definitivamente
        if (preventReopen && getCookie(`modal-${modalId}-closed`)) {
            return;
        }
        
        setTimeout(function() {
            if (preventReopen && getCookie(`modal-${modalId}-closed`)) {
                return;
            }
            openModal($modal, modalId, true);
        }, delay * 1000);
    }

    /**
     * Apre una modal
     */
    function openModal($modal, modalId, isAutoOpen = false) {
        closeAllModals();
        
        if (isAutoOpen) {
            $modal.addClass('is-auto-opening');
        }
        $modal.addClass('is-open');
        $modal.attr('aria-hidden', 'false');
        $('body').addClass('modal-open');
        
        setTimeout(function() {
            const $firstFocusable = $modal.find('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])').first();
            if ($firstFocusable.length) {
                $firstFocusable.focus();
            }
        }, isAutoOpen ? 500 : 300);
        
        $modal.trigger('modal:opened', { modalId: modalId, isAutoOpen: isAutoOpen });
    }

    /**
     * Chiude una modal
     */
    function closeModal($modal, modalId, permanent = false) {
        $modal.removeClass('is-open is-auto-opening');
        $modal.attr('aria-hidden', 'true');
        
        setTimeout(function() {
            if ($('.modal-block__modal.is-open').length === 0) {
                $('body').removeClass('modal-open');
            }
        }, 300);
        
        if (permanent) {
            setCookie(`modal-${modalId}-closed`, 'true', 30);
            $modal.addClass('is-permanently-closed');
        }
        
        $modal.trigger('modal:closed', { modalId: modalId, permanent: permanent });
    }

    /**
     * Inizializza i gestori di chiusura per una modal
     */
    function initModalCloseHandlers($modal, modalId) {
        const $block = $modal.closest('.modal-block');
        const preventReopen = $block.data('prevent-reopen');
        
        // Click su overlay e pulsante chiusura
        $modal.find('.modal-block__overlay, [data-modal-close]').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            closeModal($modal, modalId, preventReopen);
        });
        
        // ESC key
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape' && $modal.hasClass('is-open')) {
                closeModal($modal, modalId, preventReopen);
            }
        });
    }

    /**
     * Chiude tutte le modal aperte
     */
    function closeAllModals() {
        $('.modal-block__modal.is-open').each(function() {
            const $modal = $(this);
            const $block = $modal.closest('.modal-block');
            const modalId = $block.data('modal-id');
            closeModal($modal, modalId, false);
        });
    }

    /**
     * Gestione cookie
     */
    function setCookie(name, value, days) {
        let expires = '';
        if (days) {
            const date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            expires = '; expires=' + date.toUTCString();
        }
        document.cookie = name + '=' + (value || '') + expires + '; path=/; SameSite=Strict';
    }

    function getCookie(name) {
        const nameEQ = name + '=';
        const ca = document.cookie.split(';');
        for (let i = 0; i < ca.length; i++) {
            let c = ca[i];
            while (c.charAt(0) === ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
        }
        return null;
    }

    // Espone funzioni globalmente
    window.ModalManager = {
        open: function(modalId) {
            const $modal = $(`#${modalId}`);
            if ($modal.length) {
                openModal($modal, modalId);
            }
        },
        close: function(modalId, permanent = false) {
            const $modal = $(`#${modalId}`);
            if ($modal.length) {
                closeModal($modal, modalId, permanent);
            }
        },
        closeAll: closeAllModals
    };

})(jQuery);