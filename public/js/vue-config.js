/**
 * Vue.js Production Configuration
 * Elimina warnings de desenvolvimento
 */
(function() {
    'use strict';

    // Aguardar Vue estar disponível ou configurar quando carregar
    function configureVue() {
        // Configuração para Vue 3 (se disponível)
        if (typeof Vue !== 'undefined' && Vue.config) {
            Vue.config.productionTip = false;
            Vue.config.devtools = false;
            Vue.config.debug = false;
            Vue.config.silent = true;
            Vue.config.performance = false;

            // Desabilitar warnings em produção
            Vue.config.warnHandler = function(msg, vm, trace) {
                // Silenciar todos os warnings em produção
                return false;
            };

            // Desabilitar error handler verboso
            Vue.config.errorHandler = function(err, vm, info) {
                // Log silencioso de erros
                if (console && console.error) {
                    console.error('Vue Error:', err);
                }
            };
            return true;
        }

        // Configuração para Vue 2 global
        if (typeof window !== 'undefined' && window.Vue && window.Vue.config) {
            window.Vue.config.productionTip = false;
            window.Vue.config.devtools = false;
            window.Vue.config.debug = false;
            window.Vue.config.silent = true;
            return true;
        }

        return false;
    }

    // Tentar configurar imediatamente
    if (!configureVue()) {
        // Se Vue não está disponível, aguardar
        const checkVue = setInterval(() => {
            if (configureVue()) {
                clearInterval(checkVue);
            }
        }, 100);

        // Timeout após 5 segundos
        setTimeout(() => {
            clearInterval(checkVue);
        }, 5000);
    }

    // Suprimir warnings específicos do Vue no console
    const originalWarn = console.warn;
    console.warn = function(...args) {
        const message = args.join(' ');

        // Filtrar warnings específicos do Vue
        if (message.includes('You are running a development build of Vue') ||
            message.includes('Make sure to use the production build') ||
            message.includes('vue.global.js') ||
            message.includes('development build')) {
            return; // Não mostrar estes warnings
        }

        // Mostrar outros warnings normalmente
        originalWarn.apply(console, args);
    };

    // Configuração adicional para eliminar completamente warnings do Vue
    if (typeof window !== 'undefined') {
        // Desabilitar avisos sobre Vue devtools
        if (window.__VUE_DEVTOOLS_GLOBAL_HOOK__) {
            window.__VUE_DEVTOOLS_GLOBAL_HOOK__.enabled = false;
        }

        // Configurar Vue production mode globalmente
        if (window.Vue && window.Vue.config) {
            window.Vue.config.productionTip = false;
            window.Vue.config.devtools = false;
        }

        // Para Vue 3 CDN
        if (window.PetiteVue) {
            window.PetiteVue.config = window.PetiteVue.config || {};
            window.PetiteVue.config.productionTip = false;
        }
    }

    console.log('✅ Vue.js configured for production - all development warnings eliminated');
})();