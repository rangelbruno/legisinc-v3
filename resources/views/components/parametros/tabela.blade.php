@props([
    'id' => 'parametros-table',
    'colunas' => [],
    'dados' => null,
    'ajax' => null,
    'acoes' => true,
    'filtros' => true,
    'exportar' => true,
    'pesquisar' => true,
    'paginacao' => true,
    'ordenacao' => true,
    'selecionar' => false,
    'responsive' => true,
    'classe' => 'table-striped table-hover'
])

<div class="parametros-table-container">
    @if($filtros)
        <div class="table-filters mb-3">
            <div class="row">
                <div class="col-md-3">
                    <select class="form-control" id="filtro-status">
                        <option value="">Todos os Status</option>
                        <option value="1">Ativo</option>
                        <option value="0">Inativo</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-control" id="filtro-tipo">
                        <option value="">Todos os Tipos</option>
                        {{ $filtroTipo ?? '' }}
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="text" class="form-control" id="filtro-pesquisa" placeholder="Pesquisar...">
                </div>
                <div class="col-md-3">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-secondary" onclick="limparFiltros()">
                            <i class="fas fa-times"></i> Limpar
                        </button>
                        @if($exportar)
                            <button type="button" class="btn btn-info" onclick="exportarTabela()">
                                <i class="fas fa-download"></i> Exportar
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="table-responsive">
        <table id="{{ $id }}" class="table {{ $classe }}">
            <thead>
                <tr>
                    @if($selecionar)
                        <th width="50">
                            <input type="checkbox" id="select-all" class="form-check-input">
                        </th>
                    @endif
                    
                    @foreach($colunas as $coluna)
                        <th 
                            @if(isset($coluna['width'])) width="{{ $coluna['width'] }}" @endif
                            @if(isset($coluna['class'])) class="{{ $coluna['class'] }}" @endif
                            @if(isset($coluna['data'])) data-column="{{ $coluna['data'] }}" @endif
                        >
                            {{ $coluna['titulo'] }}
                        </th>
                    @endforeach
                    
                    @if($acoes)
                        <th width="120" class="text-center">Ações</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @if($dados && !$ajax)
                    @foreach($dados as $item)
                        <tr>
                            @if($selecionar)
                                <td>
                                    <input type="checkbox" class="form-check-input row-select" value="{{ $item->id }}">
                                </td>
                            @endif
                            
                            @foreach($colunas as $coluna)
                                <td @if(isset($coluna['class'])) class="{{ $coluna['class'] }}" @endif>
                                    @if(isset($coluna['render']))
                                        {!! $coluna['render']($item) !!}
                                    @else
                                        {{ data_get($item, $coluna['data']) }}
                                    @endif
                                </td>
                            @endforeach
                            
                            @if($acoes)
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        {{ $acoesPersonalizadas ?? '' }}
                                        <a href="#" class="btn btn-sm btn-primary" onclick="editarItem({{ $item->id }})">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger" onclick="excluirItem({{ $item->id }})">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>

    @if($selecionar)
        <div class="bulk-actions mt-3" style="display: none;">
            <div class="alert alert-info">
                <strong><span id="selected-count">0</span></strong> item(s) selecionado(s)
                <div class="btn-group ml-3">
                    <button type="button" class="btn btn-sm btn-success" onclick="ativarSelecionados()">
                        <i class="fas fa-check"></i> Ativar
                    </button>
                    <button type="button" class="btn btn-sm btn-warning" onclick="desativarSelecionados()">
                        <i class="fas fa-times"></i> Desativar
                    </button>
                    <button type="button" class="btn btn-sm btn-danger" onclick="excluirSelecionados()">
                        <i class="fas fa-trash"></i> Excluir
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>

@push('styles')
    <link href="{{ url('assets/plugins/table/datatable/datatables.css') }}" rel="stylesheet" />
    <style>
        .parametros-table-container {
            background: #fff;
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .table-filters {
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 6px;
        }
        
        .parametros-table-container .table th {
            font-weight: 600;
            background-color: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
        }
        
        .parametros-table-container .table td {
            vertical-align: middle;
        }
        
        .btn-group .btn {
            margin-right: 0.25rem;
        }
        
        .btn-group .btn:last-child {
            margin-right: 0;
        }
        
        .bulk-actions {
            position: sticky;
            bottom: 0;
            z-index: 10;
        }
        
        @media (max-width: 768px) {
            .table-filters .col-md-3 {
                margin-bottom: 0.5rem;
            }
            
            .btn-group {
                flex-direction: column;
                width: 100%;
            }
            
            .btn-group .btn {
                width: 100%;
                margin-bottom: 0.25rem;
                margin-right: 0;
            }
        }
    </style>
@endpush

@push('scripts')
    <script src="{{ url('assets/plugins/table/datatable/datatables.js') }}"></script>
    <script>
        $(document).ready(function() {
            const tableConfig = {
                @if($responsive) responsive: true, @endif
                @if($ordenacao) ordering: true, @endif
                @if($pesquisar) searching: true, @endif
                @if($paginacao) paging: true, @endif
                pageLength: 25,
                lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Todos"]],
                language: {
                    url: '/assets/plugins/table/datatable/pt-BR.json'
                },
                dom: 'Bfrtip',
                buttons: [
                    @if($exportar)
                    'excel', 'pdf', 'print'
                    @endif
                ],
                @if($ajax)
                ajax: {
                    url: "{{ $ajax }}",
                    type: 'GET',
                    beforeSend: function(xhr) {
                        @auth
                        xhr.setRequestHeader('Authorization', 'Bearer ' + "{{ session('token') }}");
                        @endauth
                    },
                    error: function(xhr, error, thrown) {
                        console.error('Erro ao carregar dados:', error);
                        if (typeof swal !== 'undefined') {
                            swal('Erro!', 'Erro ao carregar dados da tabela.', 'error');
                        }
                    }
                },
                columns: [
                    @if($selecionar)
                    { data: null, orderable: false, searchable: false, render: function(data, type, row) {
                        return '<input type="checkbox" class="form-check-input row-select" value="' + row.id + '">';
                    }},
                    @endif
                    
                    @foreach($colunas as $coluna)
                    {
                        data: "{{ $coluna['data'] }}",
                        @if(isset($coluna['orderable'])) orderable: {{ $coluna['orderable'] ? 'true' : 'false' }}, @endif
                        @if(isset($coluna['searchable'])) searchable: {{ $coluna['searchable'] ? 'true' : 'false' }}, @endif
                        @if(isset($coluna['render']))
                        render: function(data, type, row) {
                            {!! $coluna['render'] !!}
                        }
                        @endif
                    },
                    @endforeach
                    
                    @if($acoes)
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        className: 'text-center',
                        render: function(data, type, row) {
                            return '<div class="btn-group" role="group">' +
                                   '<a href="#" class="btn btn-sm btn-primary" onclick="editarItem(' + row.id + ')"><i class="fas fa-edit"></i></a>' +
                                   '<button type="button" class="btn btn-sm btn-danger" onclick="excluirItem(' + row.id + ')"><i class="fas fa-trash"></i></button>' +
                                   '</div>';
                        }
                    }
                    @endif
                ]
                @endif
            };
            
            const table = $('#{{ $id }}').DataTable(tableConfig);
            
            @if($filtros)
            // Filtros personalizados
            $('#filtro-status').on('change', function() {
                table.column('status:name').search(this.value).draw();
            });
            
            $('#filtro-tipo').on('change', function() {
                table.column('tipo:name').search(this.value).draw();
            });
            
            $('#filtro-pesquisa').on('keyup', function() {
                table.search(this.value).draw();
            });
            @endif
            
            @if($selecionar)
            // Seleção em massa
            $('#select-all').on('change', function() {
                $('.row-select').prop('checked', this.checked);
                updateBulkActions();
            });
            
            $(document).on('change', '.row-select', function() {
                updateBulkActions();
            });
            
            function updateBulkActions() {
                const selected = $('.row-select:checked').length;
                $('#selected-count').text(selected);
                
                if (selected > 0) {
                    $('.bulk-actions').show();
                } else {
                    $('.bulk-actions').hide();
                }
                
                // Atualizar checkbox "select all"
                const total = $('.row-select').length;
                $('#select-all').prop('indeterminate', selected > 0 && selected < total);
                $('#select-all').prop('checked', selected === total);
            }
            @endif
        });
        
        function limparFiltros() {
            @if($filtros)
            $('#filtro-status').val('');
            $('#filtro-tipo').val('');
            $('#filtro-pesquisa').val('');
            $('#{{ $id }}').DataTable().search('').columns().search('').draw();
            @endif
        }
        
        function exportarTabela() {
            // Implementar lógica de exportação
            console.log('Exportar tabela');
        }
        
        function editarItem(id) {
            // Implementar lógica de edição
            console.log('Editar item:', id);
        }
        
        function excluirItem(id) {
            if (typeof swal !== 'undefined') {
                swal({
                    title: 'Tem certeza?',
                    text: "Você não poderá reverter isso!",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'SIM',
                    cancelButtonText: 'NÃO',
                }).then(function(result) {
                    if (result.value) {
                        // Implementar lógica de exclusão
                        console.log('Excluir item:', id);
                    }
                });
            } else {
                if (confirm('Tem certeza que deseja excluir este item?')) {
                    // Implementar lógica de exclusão
                    console.log('Excluir item:', id);
                }
            }
        }
        
        @if($selecionar)
        function ativarSelecionados() {
            const selecionados = $('.row-select:checked').map(function() {
                return this.value;
            }).get();
            
            console.log('Ativar selecionados:', selecionados);
        }
        
        function desativarSelecionados() {
            const selecionados = $('.row-select:checked').map(function() {
                return this.value;
            }).get();
            
            console.log('Desativar selecionados:', selecionados);
        }
        
        function excluirSelecionados() {
            const selecionados = $('.row-select:checked').map(function() {
                return this.value;
            }).get();
            
            if (typeof swal !== 'undefined') {
                swal({
                    title: 'Tem certeza?',
                    text: `Você irá excluir ${selecionados.length} item(s)!`,
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'SIM',
                    cancelButtonText: 'NÃO',
                }).then(function(result) {
                    if (result.value) {
                        console.log('Excluir selecionados:', selecionados);
                    }
                });
            }
        }
        @endif
    </script>
@endpush