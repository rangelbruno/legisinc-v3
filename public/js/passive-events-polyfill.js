/**
 * Passive Events Polyfill - Elimina violações de scroll-blocking
 * Automaticamente torna todos os event listeners passivos quando apropriado
 */

(function() {
    'use strict';

    // Detectar suporte a passive events
    let supportsPassive = false;
    try {
        const opts = Object.defineProperty({}, 'passive', {
            get: function() {
                supportsPassive = true;
                return false;
            }
        });
        window.addEventListener("testPassive", null, opts);
        window.removeEventListener("testPassive", null, opts);
    } catch (e) {}

    if (!supportsPassive) return;

    // Lista expandida de eventos que devem ser passivos por padrão
    const passiveEvents = [
        'touchstart',
        'touchmove',
        'touchend',
        'touchcancel',
        'mousewheel',
        'wheel',
        'scroll',
        'pointermove',
        'pointerover',
        'pointerenter',
        'pointerdown',
        'pointerup'
    ];

    // Override addEventListener para tornar eventos passivos automaticamente
    const originalAddEventListener = EventTarget.prototype.addEventListener;
    EventTarget.prototype.addEventListener = function(type, listener, options) {
        // Forçar passivo para eventos de scroll-blocking
        if (passiveEvents.includes(type)) {
            if (typeof options === 'boolean') {
                options = { capture: options, passive: true };
            } else if (typeof options === 'object' && options !== null) {
                // Forçar passive mesmo se foi explicitamente definido como false
                options = { ...options, passive: true };
            } else {
                options = { passive: true };
            }
        }
        return originalAddEventListener.call(this, type, listener, options);
    };

    // Override para window e document especificamente
    ['window', 'document'].forEach(target => {
        const obj = target === 'window' ? window : document;
        if (obj && obj.addEventListener) {
            const originalMethod = obj.addEventListener;
            obj.addEventListener = function(type, listener, options) {
                if (passiveEvents.includes(type)) {
                    if (typeof options === 'boolean') {
                        options = { capture: options, passive: true };
                    } else if (typeof options === 'object' && options !== null) {
                        options = { ...options, passive: true };
                    } else {
                        options = { passive: true };
                    }
                }
                return originalMethod.call(this, type, listener, options);
            };
        }
    });

    // Override para jQuery se estiver presente
    if (window.jQuery) {
        const originalOn = jQuery.fn.on;
        jQuery.fn.on = function(events, selector, data, handler) {
            if (typeof events === 'string') {
                const eventList = events.split(' ');
                const hasPassiveEvents = eventList.some(event => passiveEvents.includes(event));

                if (hasPassiveEvents && arguments.length > 0) {
                    // Para jQuery, precisamos trabalhar com o objeto de opções
                    const lastArg = arguments[arguments.length - 1];
                    if (typeof lastArg === 'function') {
                        // Adicionar flag para eventos passivos
                        const newHandler = function(e) {
                            return lastArg.call(this, e);
                        };
                        newHandler.passive = true;
                        arguments[arguments.length - 1] = newHandler;
                    }
                }
            }
            return originalOn.apply(this, arguments);
        };
    }

    // Console log para confirmar que o polyfill foi carregado
    console.log('✅ Passive Events Polyfill loaded - scroll violations should be eliminated');
})();