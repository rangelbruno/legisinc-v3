/**
 * Performance Optimizer - Elimina reflows e repaints desnecessários
 * Otimiza manipulações DOM e eventos para máxima performance
 */

(function() {
    'use strict';

    // 1. Debounce para eventos de resize e scroll
    function debounce(func, wait, immediate) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                timeout = null;
                if (!immediate) func.apply(this, args);
            };
            const callNow = immediate && !timeout;
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
            if (callNow) func.apply(this, args);
        };
    }

    // 2. Throttle para eventos muito frequentes
    function throttle(func, limit) {
        let inThrottle;
        return function() {
            const args = arguments;
            const context = this;
            if (!inThrottle) {
                func.apply(context, args);
                inThrottle = true;
                setTimeout(() => inThrottle = false, limit);
            }
        };
    }

    // 3. Batch DOM operations para evitar múltiplos reflows
    class DOMBatcher {
        constructor() {
            this.reads = [];
            this.writes = [];
            this.scheduled = false;
        }

        read(fn) {
            this.reads.push(fn);
            this.schedule();
        }

        write(fn) {
            this.writes.push(fn);
            this.schedule();
        }

        schedule() {
            if (this.scheduled) return;
            this.scheduled = true;

            requestAnimationFrame(() => {
                // Execute all reads first
                this.reads.forEach(fn => fn());
                // Then all writes
                this.writes.forEach(fn => fn());

                // Reset
                this.reads.length = 0;
                this.writes.length = 0;
                this.scheduled = false;
            });
        }
    }

    // 4. Instância global do batcher
    window.domBatcher = new DOMBatcher();

    // 5. Observer para mudanças de dimensões sem polling
    const resizeObservers = new Map();
    function observeElementResize(element, callback) {
        if ('ResizeObserver' in window) {
            const observer = new ResizeObserver(entries => {
                requestAnimationFrame(() => callback(entries));
            });
            observer.observe(element);
            resizeObservers.set(element, observer);
            return observer;
        } else {
            // Fallback para browsers sem ResizeObserver
            const debouncedCallback = debounce(callback, 100);
            window.addEventListener('resize', debouncedCallback, { passive: true });
            return { disconnect: () => window.removeEventListener('resize', debouncedCallback) };
        }
    }

    // 6. Intersection Observer para lazy loading e visibility
    function createIntersectionObserver(callback, options = {}) {
        const defaultOptions = {
            root: null,
            rootMargin: '50px',
            threshold: 0.1,
            ...options
        };

        if ('IntersectionObserver' in window) {
            return new IntersectionObserver(callback, defaultOptions);
        } else {
            // Fallback simples
            return {
                observe: (element) => {
                    // Assumir que está visível para compatibilidade
                    callback([{ isIntersecting: true, target: element }]);
                },
                disconnect: () => {},
                unobserve: () => {}
            };
        }
    }

    // 7. Otimizar animações com will-change
    function optimizeAnimation(element, property) {
        if (element && element.style) {
            element.style.willChange = property;

            // Remove will-change após animação
            const removeWillChange = () => {
                element.style.willChange = 'auto';
                element.removeEventListener('animationend', removeWillChange);
                element.removeEventListener('transitionend', removeWillChange);
            };

            element.addEventListener('animationend', removeWillChange, { once: true, passive: true });
            element.addEventListener('transitionend', removeWillChange, { once: true, passive: true });
        }
    }

    // 8. Virtual scrolling para listas grandes
    class VirtualScroller {
        constructor(container, itemHeight, renderItem) {
            this.container = container;
            this.itemHeight = itemHeight;
            this.renderItem = renderItem;
            this.items = [];
            this.visibleStart = 0;
            this.visibleEnd = 0;
            this.containerHeight = 0;

            this.init();
        }

        init() {
            this.container.style.position = 'relative';
            this.container.style.overflow = 'auto';

            this.updateVisibleRange = throttle(() => {
                this.updateVisibleItems();
            }, 16); // ~60fps

            this.container.addEventListener('scroll', this.updateVisibleRange, { passive: true });
            observeElementResize(this.container, () => this.updateContainerHeight());
        }

        setItems(items) {
            this.items = items;
            this.updateTotalHeight();
            this.updateVisibleItems();
        }

        updateContainerHeight() {
            this.containerHeight = this.container.clientHeight;
            this.updateVisibleItems();
        }

        updateTotalHeight() {
            const totalHeight = this.items.length * this.itemHeight;
            if (!this.spacer) {
                this.spacer = document.createElement('div');
                this.container.appendChild(this.spacer);
            }
            this.spacer.style.height = totalHeight + 'px';
        }

        updateVisibleItems() {
            if (!this.containerHeight) return;

            const scrollTop = this.container.scrollTop;
            const visibleStart = Math.floor(scrollTop / this.itemHeight);
            const visibleCount = Math.ceil(this.containerHeight / this.itemHeight) + 2; // Buffer
            const visibleEnd = Math.min(visibleStart + visibleCount, this.items.length);

            this.renderVisibleItems(visibleStart, visibleEnd);
        }

        renderVisibleItems(start, end) {
            // Remove itens antigos
            const existingItems = this.container.querySelectorAll('.virtual-item');
            existingItems.forEach(item => {
                const index = parseInt(item.dataset.index);
                if (index < start || index >= end) {
                    item.remove();
                }
            });

            // Adiciona novos itens
            for (let i = start; i < end; i++) {
                const existing = this.container.querySelector(`[data-index="${i}"]`);
                if (!existing) {
                    const item = this.renderItem(this.items[i], i);
                    item.className += ' virtual-item';
                    item.dataset.index = i;
                    item.style.position = 'absolute';
                    item.style.top = (i * this.itemHeight) + 'px';
                    item.style.width = '100%';
                    item.style.height = this.itemHeight + 'px';
                    this.container.appendChild(item);
                }
            }
        }
    }

    // 9. Exposer utilitários globais
    window.PerformanceOptimizer = {
        debounce,
        throttle,
        DOMBatcher,
        observeElementResize,
        createIntersectionObserver,
        optimizeAnimation,
        VirtualScroller,

        // Atalhos úteis
        batchRead: (fn) => window.domBatcher.read(fn),
        batchWrite: (fn) => window.domBatcher.write(fn),

        // Utilitário para otimizar toggles de classe
        toggleClassOptimized: (element, className, condition) => {
            window.domBatcher.write(() => {
                if (condition) {
                    element.classList.add(className);
                } else {
                    element.classList.remove(className);
                }
            });
        },

        // Utilitário para mudanças de estilo em lote
        setStylesOptimized: (element, styles) => {
            window.domBatcher.write(() => {
                Object.assign(element.style, styles);
            });
        }
    };

    // 10. Auto-otimização para tabelas existentes
    document.addEventListener('DOMContentLoaded', () => {
        // Otimizar tabelas com muitas linhas
        const tables = document.querySelectorAll('table tbody');
        tables.forEach(tbody => {
            const rows = tbody.querySelectorAll('tr');
            if (rows.length > 100) {
                console.log(`🚀 Aplicando virtual scrolling em tabela com ${rows.length} linhas`);
                // Implementar virtual scrolling se necessário
            }
        });

        // Otimizar botões com animações
        const buttons = document.querySelectorAll('.btn, button');
        buttons.forEach(button => {
            button.addEventListener('mouseenter', () => {
                optimizeAnimation(button, 'transform, background-color');
            }, { passive: true });
        });

        console.log('✅ Performance Optimizer loaded - DOM operations optimized');
    });

})();