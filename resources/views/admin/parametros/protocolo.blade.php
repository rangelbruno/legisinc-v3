<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Configuração de Numeração de Processos
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.parametros.protocolo.update') }}" id="formParametros">
                @csrf
                @method('PUT')

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h3 class="text-lg font-semibold mb-4">Formato de Numeração</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Formato do Número -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Formato do Número de Processo
                                </label>
                                <input type="text" 
                                    name="parametros[protocolo.formato_numero_processo]" 
                                    value="{{ $parametros['protocolo.formato_numero_processo']->valor ?? '{TIPO}/{ANO}/{SEQUENCIAL}' }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                <p class="mt-1 text-sm text-gray-500">
                                    Variáveis: {TIPO}, {ANO}, {SEQUENCIAL}, {MES}, {DIA}
                                </p>
                            </div>

                            <!-- Dígitos do Sequencial -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Quantidade de Dígitos do Sequencial
                                </label>
                                <select name="parametros[protocolo.digitos_sequencial]" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                    @foreach(['3' => '3 dígitos (001)', '4' => '4 dígitos (0001)', '5' => '5 dígitos (00001)', '6' => '6 dígitos (000001)'] as $value => $label)
                                        <option value="{{ $value }}" {{ ($parametros['protocolo.digitos_sequencial']->valor ?? '4') == $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Prefixo -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Prefixo (Opcional)
                                </label>
                                <input type="text" 
                                    name="parametros[protocolo.prefixo_processo]" 
                                    value="{{ $parametros['protocolo.prefixo_processo']->valor ?? '' }}"
                                    maxlength="10"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                <p class="mt-1 text-sm text-gray-500">Ex: PROC-</p>
                            </div>

                            <!-- Sufixo -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Sufixo (Opcional)
                                </label>
                                <input type="text" 
                                    name="parametros[protocolo.sufixo_processo]" 
                                    value="{{ $parametros['protocolo.sufixo_processo']->valor ?? '' }}"
                                    maxlength="10"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                <p class="mt-1 text-sm text-gray-500">Ex: -CM</p>
                            </div>
                        </div>

                        <!-- Preview -->
                        <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                            <h4 class="font-semibold mb-2">Exemplo de Numeração:</h4>
                            <div id="previewNumero" class="text-lg font-mono text-blue-600"></div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h3 class="text-lg font-semibold mb-4">Opções de Controle</h3>
                        
                        <div class="space-y-4">
                            <!-- Reiniciar Anualmente -->
                            <div class="flex items-center justify-between">
                                <div>
                                    <label class="text-sm font-medium text-gray-700">
                                        Reiniciar Sequencial Anualmente
                                    </label>
                                    <p class="text-sm text-gray-500">O sequencial reinicia em 1 a cada novo ano</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="hidden" name="parametros[protocolo.reiniciar_sequencial_anualmente]" value="0">
                                    <input type="checkbox" 
                                        name="parametros[protocolo.reiniciar_sequencial_anualmente]" 
                                        value="1"
                                        {{ ($parametros['protocolo.reiniciar_sequencial_anualmente']->valor ?? '1') == '1' ? 'checked' : '' }}
                                        class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                </label>
                            </div>

                            <!-- Sequencial por Tipo -->
                            <div class="flex items-center justify-between">
                                <div>
                                    <label class="text-sm font-medium text-gray-700">
                                        Sequencial Separado por Tipo
                                    </label>
                                    <p class="text-sm text-gray-500">Cada tipo (PL, PEC, etc) terá numeração independente</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="hidden" name="parametros[protocolo.sequencial_por_tipo]" value="0">
                                    <input type="checkbox" 
                                        name="parametros[protocolo.sequencial_por_tipo]" 
                                        value="1"
                                        {{ ($parametros['protocolo.sequencial_por_tipo']->valor ?? '1') == '1' ? 'checked' : '' }}
                                        class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                </label>
                            </div>

                            <!-- Permitir Número Manual -->
                            <div class="flex items-center justify-between">
                                <div>
                                    <label class="text-sm font-medium text-gray-700">
                                        Permitir Número Manual
                                    </label>
                                    <p class="text-sm text-gray-500">Permite inserção manual do número além do automático</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="hidden" name="parametros[protocolo.permitir_numero_manual]" value="0">
                                    <input type="checkbox" 
                                        name="parametros[protocolo.permitir_numero_manual]" 
                                        value="1"
                                        {{ ($parametros['protocolo.permitir_numero_manual']->valor ?? '0') == '1' ? 'checked' : '' }}
                                        class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h3 class="text-lg font-semibold mb-4">Inserção no Documento</h3>
                        
                        <div class="space-y-4">
                            <!-- Inserir no Documento -->
                            <div class="flex items-center justify-between">
                                <div>
                                    <label class="text-sm font-medium text-gray-700">
                                        Inserir Número no Documento
                                    </label>
                                    <p class="text-sm text-gray-500">Insere automaticamente o número de processo no documento</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="hidden" name="parametros[protocolo.inserir_numero_documento]" value="0">
                                    <input type="checkbox" 
                                        name="parametros[protocolo.inserir_numero_documento]" 
                                        value="1"
                                        {{ ($parametros['protocolo.inserir_numero_documento']->valor ?? '1') == '1' ? 'checked' : '' }}
                                        class="sr-only peer"
                                        id="inserirDocumento">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                </label>
                            </div>

                            <!-- Posição no Documento -->
                            <div id="posicaoDocumentoDiv">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Posição do Número no Documento
                                </label>
                                <select name="parametros[protocolo.posicao_numero_documento]" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                    @foreach([
                                        'cabecalho' => 'No cabeçalho do documento',
                                        'rodape' => 'No rodapé do documento',
                                        'primeira_pagina' => 'Na primeira página (canto superior direito)',
                                        'marca_dagua' => 'Como marca d\'água',
                                        'nao_inserir' => 'Não inserir no documento'
                                    ] as $value => $label)
                                        <option value="{{ $value }}" {{ ($parametros['protocolo.posicao_numero_documento']->valor ?? 'cabecalho') == $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Próximos Números -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h3 class="text-lg font-semibold mb-4">Próximos Números Disponíveis</h3>
                        
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            @foreach($proximosNumeros as $tipo => $numero)
                                <div class="p-3 bg-gray-50 rounded">
                                    <div class="text-sm font-medium text-gray-600">{{ $tipo }}</div>
                                    <div class="text-lg font-mono">{{ $numero }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Botões de Ação -->
                <div class="mt-6 flex justify-between">
                    <button type="button" 
                        onclick="if(confirm('Restaurar valores padrão?')) { window.location.href='{{ route('admin.parametros.protocolo.restaurar') }}' }"
                        class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600">
                        Restaurar Padrões
                    </button>
                    
                    <div class="space-x-2">
                        <button type="button" 
                            id="btnTestar"
                            class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">
                            Testar Formato
                        </button>
                        
                        <button type="submit" 
                            class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                            Salvar Configurações
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        // Atualizar preview
        function atualizarPreview() {
            const formato = document.querySelector('[name="parametros[protocolo.formato_numero_processo]"]').value;
            const digitos = document.querySelector('[name="parametros[protocolo.digitos_sequencial]"]').value;
            const prefixo = document.querySelector('[name="parametros[protocolo.prefixo_processo]"]').value;
            const sufixo = document.querySelector('[name="parametros[protocolo.sufixo_processo]"]').value;
            
            let sequencial = '1'.padStart(digitos, '0');
            let exemplo = formato
                .replace('{TIPO}', 'PL')
                .replace('{ANO}', new Date().getFullYear())
                .replace('{SEQUENCIAL}', sequencial)
                .replace('{MES}', String(new Date().getMonth() + 1).padStart(2, '0'))
                .replace('{DIA}', String(new Date().getDate()).padStart(2, '0'));
            
            document.getElementById('previewNumero').textContent = prefixo + exemplo + sufixo;
        }

        // Atualizar preview ao carregar
        atualizarPreview();

        // Atualizar preview ao mudar campos
        document.querySelectorAll('[name^="parametros[protocolo."]').forEach(input => {
            input.addEventListener('change', atualizarPreview);
            input.addEventListener('keyup', atualizarPreview);
        });

        // Controlar visibilidade da posição no documento
        document.getElementById('inserirDocumento').addEventListener('change', function() {
            document.getElementById('posicaoDocumentoDiv').style.display = this.checked ? 'block' : 'none';
        });

        // Testar formato
        document.getElementById('btnTestar').addEventListener('click', function() {
            const formato = document.querySelector('[name="parametros[protocolo.formato_numero_processo]"]').value;
            const digitos = document.querySelector('[name="parametros[protocolo.digitos_sequencial]"]').value;
            const prefixo = document.querySelector('[name="parametros[protocolo.prefixo_processo]"]').value;
            const sufixo = document.querySelector('[name="parametros[protocolo.sufixo_processo]"]').value;
            
            fetch('{{ route("admin.parametros.protocolo.testar") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ formato, digitos, prefixo, sufixo })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Formato válido! Exemplo: ' + data.exemplo);
                } else {
                    alert('Formato inválido: ' + data.message);
                }
            });
        });
    </script>
    @endpush
</x-app-layout>