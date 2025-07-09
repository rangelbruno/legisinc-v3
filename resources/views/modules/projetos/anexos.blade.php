<x-layouts.app title="Anexos do Projeto">
    <div class="d-flex flex-column flex-column-fluid">
        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                    <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                        Anexos
                    </h1>
                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-400 w-5px h-2px"></span>
                        </li>
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('projetos.index') }}" class="text-muted text-hover-primary">Projetos</a>
                        </li>
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-400 w-5px h-2px"></span>
                        </li>
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('projetos.show', $projeto->id) }}" class="text-muted text-hover-primary">{{ $projeto->numero_completo ?? 'Projeto' }}</a>
                        </li>
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-400 w-5px h-2px"></span>
                        </li>
                        <li class="breadcrumb-item text-muted">Anexos</li>
                    </ul>
                </div>
                <div class="d-flex align-items-center gap-2 gap-lg-3">
                    <a href="{{ route('projetos.show', $projeto->id) }}" class="btn btn-light-primary btn-sm">
                        <i class="ki-duotone ki-arrow-left fs-3">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        Voltar ao Projeto
                    </a>
                    
                    @if($projeto->podeAnexarArquivos())
                    <button type="button" class="btn btn-primary btn-sm" 
                            data-bs-toggle="modal" 
                            data-bs-target="#modal_upload_anexo">
                        <i class="ki-duotone ki-plus fs-3">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        Adicionar Anexo
                    </button>
                    @endif
                </div>
            </div>
        </div>

        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div id="kt_app_content_container" class="app-container container-xxl">
                
                <div class="row g-5 g-xl-8">
                    <!-- Informações do Projeto -->
                    <div class="col-xl-4">
                        <div class="card card-flush mb-5">
                            <div class="card-header">
                                <div class="card-title">
                                    <h3>Informações do Projeto</h3>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="mb-6">
                                    <label class="fs-6 fw-semibold text-gray-600 mb-2">Título</label>
                                    <div class="fs-6 fw-bold text-gray-900">{{ $projeto->titulo }}</div>
                                </div>
                                
                                <div class="mb-6">
                                    <label class="fs-6 fw-semibold text-gray-600 mb-2">Número</label>
                                    <div class="fs-6 fw-bold text-gray-900">{{ $projeto->numero_completo ?? 'Não numerado' }}</div>
                                </div>
                                
                                <div class="mb-6">
                                    <label class="fs-6 fw-semibold text-gray-600 mb-2">Status</label>
                                    <div>
                                        @php
                                            $statusColor = match($projeto->status) {
                                                'rascunho' => 'secondary',
                                                'protocolado' => 'primary',
                                                'em_tramitacao' => 'warning',
                                                'na_comissao' => 'info',
                                                'aprovado' => 'success',
                                                'rejeitado' => 'danger',
                                                'arquivado' => 'dark',
                                                default => 'secondary'
                                            };
                                        @endphp
                                        <div class="badge badge-light-{{ $statusColor }} fw-bold fs-6 px-4 py-3">
                                            {{ $projeto->status_formatado ?? ucfirst($projeto->status) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Estatísticas de Anexos -->
                        <div class="card card-flush">
                            <div class="card-header">
                                <div class="card-title">
                                    <h3>Estatísticas</h3>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="mb-6">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="fs-6 fw-semibold text-gray-600">Total de Anexos</span>
                                        <span class="fs-6 fw-bold text-gray-900">{{ $projeto->anexos->count() }}</span>
                                    </div>
                                </div>
                                
                                @php
                                    $totalTamanho = $projeto->anexos->sum('tamanho') ?: 0;
                                    $tamanhoFormatado = $totalTamanho > 0 ? 
                                        number_format($totalTamanho / 1024 / 1024, 2) . ' MB' : 
                                        '0 MB';
                                @endphp
                                <div class="mb-6">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="fs-6 fw-semibold text-gray-600">Tamanho Total</span>
                                        <span class="fs-6 fw-bold text-gray-900">{{ $tamanhoFormatado }}</span>
                                    </div>
                                </div>
                                
                                <div class="mb-6">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="fs-6 fw-semibold text-gray-600">Públicos</span>
                                        <span class="fs-6 fw-bold text-success">{{ $projeto->anexos->where('publico', true)->count() }}</span>
                                    </div>
                                </div>
                                
                                <div class="mb-6">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="fs-6 fw-semibold text-gray-600">Privados</span>
                                        <span class="fs-6 fw-bold text-warning">{{ $projeto->anexos->where('publico', false)->count() }}</span>
                                    </div>
                                </div>
                                
                                <!-- Tipos de Arquivo -->
                                <div class="separator my-4"></div>
                                <div class="mb-3">
                                    <span class="fs-6 fw-semibold text-gray-600">Tipos de Arquivo</span>
                                </div>
                                @php
                                    $tiposContagem = $projeto->anexos->groupBy('tipo')->map->count();
                                @endphp
                                @foreach($tiposContagem as $tipo => $count)
                                <div class="mb-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="fs-7 text-gray-600">{{ ucfirst($tipo) }}</span>
                                        <span class="fs-7 fw-bold text-gray-900">{{ $count }}</span>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Lista de Anexos -->
                    <div class="col-xl-8">
                        <div class="card card-flush">
                            <div class="card-header">
                                <div class="card-title">
                                    <h3>Arquivos Anexados</h3>
                                </div>
                                <div class="card-toolbar">
                                    <div class="btn-group" role="group">
                                        <input type="radio" class="btn-check" name="view_mode" id="view_list" value="list" checked />
                                        <label class="btn btn-outline btn-outline-secondary btn-sm" for="view_list">
                                            <i class="ki-duotone ki-row-horizontal fs-3"></i>
                                        </label>
                                        
                                        <input type="radio" class="btn-check" name="view_mode" id="view_grid" value="grid" />
                                        <label class="btn btn-outline btn-outline-secondary btn-sm" for="view_grid">
                                            <i class="ki-duotone ki-element-4 fs-3"></i>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <!-- Filtros -->
                                <div class="d-flex align-items-center mb-5">
                                    <div class="d-flex align-items-center position-relative my-1 me-5">
                                        <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-4">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        <input type="text" id="search_anexos" class="form-control form-control-solid w-250px ps-14" placeholder="Buscar arquivo..." />
                                    </div>
                                    
                                    <select class="form-select form-select-solid w-150px me-5" id="filter_tipo">
                                        <option value="">Todos os tipos</option>
                                        <option value="documento">Documentos</option>
                                        <option value="imagem">Imagens</option>
                                        <option value="planilha">Planilhas</option>
                                        <option value="apresentacao">Apresentações</option>
                                        <option value="outro">Outros</option>
                                    </select>
                                    
                                    <select class="form-select form-select-solid w-150px" id="filter_visibilidade">
                                        <option value="">Todos</option>
                                        <option value="1">Públicos</option>
                                        <option value="0">Privados</option>
                                    </select>
                                </div>

                                <!-- Lista de Anexos -->
                                <div id="anexos_container">
                                    @forelse($projeto->anexos->sortByDesc('created_at') as $anexo)
                                    <div class="anexo-item d-flex align-items-center bg-light-primary rounded p-4 mb-4" 
                                         data-nome="{{ strtolower($anexo->nome_original) }}" 
                                         data-tipo="{{ $anexo->tipo }}" 
                                         data-publico="{{ $anexo->publico ? '1' : '0' }}">
                                        
                                        <!-- Ícone do Arquivo -->
                                        <div class="symbol symbol-50px me-4">
                                            @if($anexo->isImage())
                                                <div class="symbol-label bg-light-success text-success">
                                                    <i class="ki-duotone ki-picture fs-2">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                    </i>
                                                </div>
                                            @elseif($anexo->isPdf())
                                                <div class="symbol-label bg-light-danger text-danger">
                                                    <i class="ki-duotone ki-file fs-2">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                    </i>
                                                </div>
                                            @elseif($anexo->isDocument())
                                                <div class="symbol-label bg-light-info text-info">
                                                    <i class="ki-duotone ki-document fs-2">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                    </i>
                                                </div>
                                            @else
                                                <div class="symbol-label bg-light-warning text-warning">
                                                    <i class="ki-duotone ki-file-up fs-2">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                    </i>
                                                </div>
                                            @endif
                                        </div>
                                        
                                        <!-- Informações do Arquivo -->
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center mb-2">
                                                <a href="#" class="text-gray-900 fw-bold text-hover-primary fs-6 me-3" 
                                                   onclick="downloadAnexo({{ $anexo->id }})">
                                                    {{ $anexo->nome_original }}
                                                </a>
                                                
                                                <div class="badge badge-light-{{ $anexo->publico ? 'success' : 'warning' }} fs-8 me-2">
                                                    {{ $anexo->publico ? 'Público' : 'Privado' }}
                                                </div>
                                                
                                                <div class="badge badge-light-primary fs-8">
                                                    {{ ucfirst($anexo->tipo) }}
                                                </div>
                                            </div>
                                            
                                            @if($anexo->descricao)
                                            <div class="text-gray-600 fs-7 mb-2">{{ $anexo->descricao }}</div>
                                            @endif
                                            
                                            <div class="d-flex align-items-center text-gray-500 fs-7">
                                                <span class="me-3">{{ $anexo->getTamanhoFormatado() }}</span>
                                                <span class="me-3">•</span>
                                                <span class="me-3">{{ $anexo->uploadedBy->name ?? 'Sistema' }}</span>
                                                <span class="me-3">•</span>
                                                <span>{{ $anexo->created_at->format('d/m/Y H:i') }}</span>
                                            </div>
                                        </div>
                                        
                                        <!-- Ações -->
                                        <div class="ms-auto">
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-light-primary btn-sm" 
                                                        onclick="downloadAnexo({{ $anexo->id }})"
                                                        title="Download">
                                                    <i class="ki-duotone ki-cloud-download fs-3">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                    </i>
                                                </button>
                                                
                                                @if($anexo->isImage())
                                                <button type="button" class="btn btn-light-success btn-sm" 
                                                        onclick="previewAnexo({{ $anexo->id }})"
                                                        title="Visualizar">
                                                    <i class="ki-duotone ki-eye fs-3">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                        <span class="path3"></span>
                                                    </i>
                                                </button>
                                                @endif
                                                
                                                <button type="button" class="btn btn-light-warning btn-sm" 
                                                        onclick="editAnexo({{ $anexo->id }})"
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#modal_edit_anexo"
                                                        title="Editar">
                                                    <i class="ki-duotone ki-pencil fs-3">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                    </i>
                                                </button>
                                                
                                                @if($projeto->podeAnexarArquivos())
                                                <button type="button" class="btn btn-light-danger btn-sm" 
                                                        onclick="deleteAnexo({{ $anexo->id }})"
                                                        title="Excluir">
                                                    <i class="ki-duotone ki-trash fs-3">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                        <span class="path3"></span>
                                                        <span class="path4"></span>
                                                        <span class="path5"></span>
                                                    </i>
                                                </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    @empty
                                    <div class="text-center py-10">
                                        <div class="text-gray-500 fs-6 mb-5">
                                            Nenhum anexo encontrado para este projeto.
                                        </div>
                                        @if($projeto->podeAnexarArquivos())
                                        <button type="button" class="btn btn-primary" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#modal_upload_anexo">
                                            <i class="ki-duotone ki-plus fs-3">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            Adicionar Primeiro Anexo
                                        </button>
                                        @endif
                                    </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Upload Anexo -->
    @if($projeto->podeAnexarArquivos())
    <div class="modal fade" id="modal_upload_anexo" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-650px">
            <div class="modal-content">
                <form id="form_upload_anexo" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h2>Adicionar Anexo</h2>
                        <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                            <i class="ki-duotone ki-cross fs-1">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                        </div>
                    </div>
                    <div class="modal-body">
                        <div class="fv-row mb-7">
                            <label class="required fs-6 fw-semibold mb-2">Arquivo</label>
                            <input type="file" class="form-control form-control-solid" name="arquivo" required 
                                   accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png,.gif,.txt" />
                            <div class="form-text">Tipos permitidos: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, JPG, PNG, GIF, TXT. Tamanho máximo: 50MB</div>
                        </div>
                        
                        <div class="fv-row mb-7">
                            <label class="required fs-6 fw-semibold mb-2">Tipo</label>
                            <select class="form-select form-select-solid" name="tipo" required>
                                <option value="">Selecione o tipo</option>
                                <option value="documento">Documento</option>
                                <option value="imagem">Imagem</option>
                                <option value="planilha">Planilha</option>
                                <option value="apresentacao">Apresentação</option>
                                <option value="outro">Outro</option>
                            </select>
                        </div>
                        
                        <div class="fv-row mb-7">
                            <label class="fs-6 fw-semibold mb-2">Descrição</label>
                            <textarea class="form-control form-control-solid" name="descricao" rows="3" placeholder="Descrição do arquivo (opcional)"></textarea>
                        </div>
                        
                        <div class="fv-row mb-7">
                            <div class="form-check form-check-custom form-check-solid">
                                <input class="form-check-input" type="checkbox" name="publico" value="1" id="anexo_publico" checked />
                                <label class="form-check-label fw-semibold" for="anexo_publico">
                                    Arquivo público (visível para todos)
                                </label>
                            </div>
                        </div>
                        
                        <!-- Progress Bar -->
                        <div class="progress progress-sm mb-3" id="upload_progress" style="display: none;">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary" id="btn_upload">
                            <span class="indicator-label">Fazer Upload</span>
                            <span class="indicator-progress">
                                Enviando... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    <!-- Modal Editar Anexo -->
    <div class="modal fade" id="modal_edit_anexo" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-650px">
            <div class="modal-content">
                <form id="form_edit_anexo">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit_anexo_id" name="anexo_id" />
                    <div class="modal-header">
                        <h2>Editar Anexo</h2>
                        <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                            <i class="ki-duotone ki-cross fs-1">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                        </div>
                    </div>
                    <div class="modal-body">
                        <div class="fv-row mb-7">
                            <label class="fs-6 fw-semibold mb-2">Nome do Arquivo</label>
                            <input type="text" class="form-control form-control-solid" id="edit_nome_original" readonly />
                        </div>
                        
                        <div class="fv-row mb-7">
                            <label class="required fs-6 fw-semibold mb-2">Tipo</label>
                            <select class="form-select form-select-solid" id="edit_tipo" name="tipo" required>
                                <option value="documento">Documento</option>
                                <option value="imagem">Imagem</option>
                                <option value="planilha">Planilha</option>
                                <option value="apresentacao">Apresentação</option>
                                <option value="outro">Outro</option>
                            </select>
                        </div>
                        
                        <div class="fv-row mb-7">
                            <label class="fs-6 fw-semibold mb-2">Descrição</label>
                            <textarea class="form-control form-control-solid" id="edit_descricao" name="descricao" rows="3" placeholder="Descrição do arquivo (opcional)"></textarea>
                        </div>
                        
                        <div class="fv-row mb-7">
                            <div class="form-check form-check-custom form-check-solid">
                                <input class="form-check-input" type="checkbox" id="edit_publico" name="publico" value="1" />
                                <label class="form-check-label fw-semibold" for="edit_publico">
                                    Arquivo público (visível para todos)
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Preview -->
    <div class="modal fade" id="modal_preview" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 id="preview_title">Visualizar Arquivo</h2>
                    <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                        <i class="ki-duotone ki-cross fs-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </div>
                </div>
                <div class="modal-body d-flex justify-content-center align-items-center">
                    <div id="preview_content"></div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        let anexos = @json($projeto->anexos->keyBy('id'));
        
        document.addEventListener('DOMContentLoaded', function() {
            // Filtros
            setupFilters();
            
            // Upload form
            setupUploadForm();
            
            // Edit form
            setupEditForm();
            
            // Auto-detect file type
            setupFileTypeDetection();
        });
        
        function setupFilters() {
            const searchInput = document.getElementById('search_anexos');
            const tipoFilter = document.getElementById('filter_tipo');
            const visibilidadeFilter = document.getElementById('filter_visibilidade');
            
            [searchInput, tipoFilter, visibilidadeFilter].forEach(element => {
                if (element) {
                    element.addEventListener('input', filterAnexos);
                    element.addEventListener('change', filterAnexos);
                }
            });
        }
        
        function filterAnexos() {
            const searchTerm = document.getElementById('search_anexos').value.toLowerCase();
            const tipoFilter = document.getElementById('filter_tipo').value;
            const visibilidadeFilter = document.getElementById('filter_visibilidade').value;
            
            const items = document.querySelectorAll('.anexo-item');
            
            items.forEach(item => {
                const nome = item.getAttribute('data-nome');
                const tipo = item.getAttribute('data-tipo');
                const publico = item.getAttribute('data-publico');
                
                const matchSearch = !searchTerm || nome.includes(searchTerm);
                const matchTipo = !tipoFilter || tipo === tipoFilter;
                const matchVisibilidade = !visibilidadeFilter || publico === visibilidadeFilter;
                
                if (matchSearch && matchTipo && matchVisibilidade) {
                    item.style.display = 'flex';
                } else {
                    item.style.display = 'none';
                }
            });
        }
        
        function setupUploadForm() {
            const form = document.getElementById('form_upload_anexo');
            if (!form) return;
            
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                const btnUpload = document.getElementById('btn_upload');
                const progressBar = document.getElementById('upload_progress');
                
                // Mostrar progress
                btnUpload.setAttribute('data-kt-indicator', 'on');
                btnUpload.disabled = true;
                progressBar.style.display = 'block';
                
                // Upload com progress
                const xhr = new XMLHttpRequest();
                
                xhr.upload.addEventListener('progress', function(e) {
                    if (e.lengthComputable) {
                        const percentComplete = (e.loaded / e.total) * 100;
                        progressBar.querySelector('.progress-bar').style.width = percentComplete + '%';
                    }
                });
                
                xhr.addEventListener('load', function() {
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response.success) {
                            alert('Anexo enviado com sucesso!');
                            location.reload();
                        } else {
                            alert('Erro: ' + (response.message || 'Erro desconhecido'));
                        }
                    } catch (e) {
                        alert('Erro na resposta do servidor');
                    }
                    
                    // Reset form
                    btnUpload.removeAttribute('data-kt-indicator');
                    btnUpload.disabled = false;
                    progressBar.style.display = 'none';
                });
                
                xhr.addEventListener('error', function() {
                    alert('Erro na conexão. Tente novamente.');
                    btnUpload.removeAttribute('data-kt-indicator');
                    btnUpload.disabled = false;
                    progressBar.style.display = 'none';
                });
                
                xhr.open('POST', `/projetos/{{ $projeto->id }}/anexos`);
                xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                xhr.send(formData);
            });
        }
        
        function setupEditForm() {
            const form = document.getElementById('form_edit_anexo');
            if (!form) return;
            
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const anexoId = document.getElementById('edit_anexo_id').value;
                const formData = new FormData(this);
                const data = Object.fromEntries(formData);
                
                fetch(`/anexos/${anexoId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(data)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Anexo atualizado com sucesso!');
                        location.reload();
                    } else {
                        alert('Erro: ' + (data.message || 'Erro desconhecido'));
                    }
                })
                .catch(error => {
                    alert('Erro na conexão. Tente novamente.');
                });
            });
        }
        
        function setupFileTypeDetection() {
            const fileInput = document.querySelector('input[name="arquivo"]');
            const tipoSelect = document.querySelector('select[name="tipo"]');
            
            if (fileInput && tipoSelect) {
                fileInput.addEventListener('change', function() {
                    const file = this.files[0];
                    if (!file) return;
                    
                    const extension = file.name.split('.').pop().toLowerCase();
                    
                    // Auto-detect tipo
                    if (['jpg', 'jpeg', 'png', 'gif'].includes(extension)) {
                        tipoSelect.value = 'imagem';
                    } else if (['pdf', 'doc', 'docx', 'txt'].includes(extension)) {
                        tipoSelect.value = 'documento';
                    } else if (['xls', 'xlsx'].includes(extension)) {
                        tipoSelect.value = 'planilha';
                    } else if (['ppt', 'pptx'].includes(extension)) {
                        tipoSelect.value = 'apresentacao';
                    } else {
                        tipoSelect.value = 'outro';
                    }
                });
            }
        }
        
        function downloadAnexo(anexoId) {
            window.open(`/anexos/${anexoId}/download`, '_blank');
        }
        
        function previewAnexo(anexoId) {
            const anexo = anexos[anexoId];
            if (!anexo) return;
            
            document.getElementById('preview_title').textContent = anexo.nome_original;
            
            const content = document.getElementById('preview_content');
            content.innerHTML = `<img src="/anexos/${anexoId}/preview" style="max-width: 100%; max-height: 80vh;" alt="${anexo.nome_original}" />`;
            
            const modal = new bootstrap.Modal(document.getElementById('modal_preview'));
            modal.show();
        }
        
        function editAnexo(anexoId) {
            const anexo = anexos[anexoId];
            if (!anexo) return;
            
            document.getElementById('edit_anexo_id').value = anexoId;
            document.getElementById('edit_nome_original').value = anexo.nome_original;
            document.getElementById('edit_tipo').value = anexo.tipo;
            document.getElementById('edit_descricao').value = anexo.descricao || '';
            document.getElementById('edit_publico').checked = anexo.publico;
        }
        
        function deleteAnexo(anexoId) {
            const anexo = anexos[anexoId];
            if (!anexo) return;
            
            if (!confirm(`Tem certeza que deseja excluir o arquivo "${anexo.nome_original}"? Esta ação não pode ser desfeita.`)) {
                return;
            }
            
            fetch(`/anexos/${anexoId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Anexo excluído com sucesso!');
                    location.reload();
                } else {
                    alert('Erro ao excluir anexo: ' + (data.message || 'Erro desconhecido'));
                }
            })
            .catch(error => {
                alert('Erro na conexão. Tente novamente.');
                console.error('Erro:', error);
            });
        }
    </script>
    @endpush
</x-layouts.app>