{{-- Vue.js Scripts - Production Ready --}}
{{-- Este partial centraliza o carregamento do Vue.js e elimina warnings de desenvolvimento --}}

@php
    $isDevelopment = config('app.debug', false);
    $vueVersion = $isDevelopment ? 'vue.global.js' : 'vue.global.prod.js';
@endphp

{{-- Polyfill para corrigir event listeners non-passive --}}
<script src="{{ asset('js/passive-events-polyfill.js') }}"></script>

{{-- Carregar Vue.js baseado no ambiente --}}
<script src="https://unpkg.com/vue@3/dist/{{ $vueVersion }}"></script>

{{-- Configuração de produção para eliminar warnings --}}
<script src="{{ asset('js/vue-config.js') }}"></script>

{{-- Axios (usado em muitos componentes) --}}
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>

@if($isDevelopment)
    {{-- Em desenvolvimento, mostrar qual build está sendo usado --}}
    <script>
        console.log('🔧 Vue.js Development Build carregado - warnings podem aparecer');
    </script>
@else
    {{-- Em produção, configurar para máxima performance --}}
    <script>
        // Configuração adicional para produção
        if (typeof Vue !== 'undefined') {
            Vue.config.devtools = false;
            Vue.config.productionTip = false;
        }
        console.log('🚀 Vue.js Production Build carregado - otimizado para produção');
    </script>
@endif