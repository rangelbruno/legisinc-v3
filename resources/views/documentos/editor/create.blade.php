@extends('components.layouts.app')

@section('title', 'Editor de Documentos')

@push('styles')
<style>
.editor-container {
    min-height: 500px;
    border: 1px solid #e1e5e9;
    border-radius: 0.475rem;
}

.editor-menu {
    border-bottom: 1px solid #e1e5e9;
    padding: 0.75rem 1rem;
    background: #f8f9fa;
    border-radius: 0.475rem 0.475rem 0 0;
}

.editor-content {
    padding: 1rem;
    min-height: 400px;
    max-height: 600px;
    overflow-y: auto;
}

.editor-content .ProseMirror {
    outline: none;
    font-family: 'Times New Roman', serif;
    font-size: 12pt;
    line-height: 1.6;
}

.variable-tag {
    background: #e8f4fd;
    color: #1976d2;
    padding: 2px 6px;
    border-radius: 4px;
    font-weight: 500;
    border: 1px solid #bbdefb;
    cursor: pointer;
}

.variable-tag:hover {
    background: #bbdefb;
}

.variable-sidebar {
    max-height: 600px;
    overflow-y: auto;
}

.variable-item {
    cursor: pointer;
    transition: all 0.2s;
}

.variable-item:hover {
    background: #f8f9fa;
    transform: translateX(5px);
}
</style>
@endpush

@section('content')
<!--begin::Content wrapper-->
<div class="d-flex flex-column flex-column-fluid">
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                    Editor de Documentos
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('documentos.editor.index') }}" class="text-muted text-hover-primary">Editor</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Novo Documento</li>
                </ul>
            </div>
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <a href="{{ route('documentos.editor.index') }}" class="btn btn-sm fw-bold btn-secondary">
                    <i class="ki-duotone ki-arrow-left fs-2"></i>
                    Voltar
                </a>
                <button type="button" id="btn_save_draft" class="btn btn-sm fw-bold btn-light-primary">
                    <i class="ki-duotone ki-save fs-2"></i>
                    Salvar Rascunho
                </button>
                <button type="button" id="btn_export" class="btn btn-sm fw-bold btn-primary">
                    <i class="ki-duotone ki-export fs-2"></i>
                    Exportar Documento
                </button>
            </div>
        </div>
    </div>
    <!--end::Toolbar-->

    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">
            
            <div class="row g-5">
                <!--begin::Main Editor-->
                <div class="col-xl-8">
                    <!--begin::Card-->
                    <div class="card">
                        <!--begin::Header-->
                        <div class="card-header">
                            <h3 class="card-title">
                                <input type="text" id="document_title" class="form-control form-control-flush fw-bold fs-4" 
                                       placeholder="Título do documento..." value="{{ $modelo ? $modelo->nome : 'Novo Documento' }}">
                            </h3>
                            <div class="card-toolbar">
                                <div class="d-flex align-items-center gap-3">
                                    @if($modelo)
                                        <span class="badge badge-light-{{ $modelo->tipoProposicao->cor ?? 'primary' }}">
                                            {{ $modelo->tipoProposicao->nome ?? 'Modelo Geral' }}
                                        </span>
                                    @endif
                                    <select id="formato_exportacao" class="form-select form-select-sm w-auto">
                                        <option value="docx">Word (.docx)</option>
                                        <option value="pdf">PDF (.pdf)</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <!--end::Header-->

                        <!--begin::Body-->
                        <div class="card-body p-0">
                            <!--begin::Editor-->
                            <div class="editor-container">
                                <!--begin::Menu-->
                                <div class="editor-menu">
                                    <div class="btn-toolbar" role="toolbar">
                                        <div class="btn-group me-2" role="group">
                                            <button type="button" class="btn btn-sm btn-light" data-action="bold" title="Negrito">
                                                <i class="ki-duotone ki-text-bold fs-3"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-light" data-action="italic" title="Itálico">
                                                <i class="ki-duotone ki-text-italic fs-3"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-light" data-action="underline" title="Sublinhado">
                                                <i class="ki-duotone ki-text-underline fs-3"></i>
                                            </button>
                                        </div>
                                        <div class="btn-group me-2" role="group">
                                            <button type="button" class="btn btn-sm btn-light" data-action="left" title="Alinhar à esquerda">
                                                <i class="ki-duotone ki-text-align-left fs-3"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-light" data-action="center" title="Centralizar">
                                                <i class="ki-duotone ki-text-align-center fs-3"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-light" data-action="right" title="Alinhar à direita">
                                                <i class="ki-duotone ki-text-align-right fs-3"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-light" data-action="justify" title="Justificar">
                                                <i class="ki-duotone ki-text-align-justify fs-3"></i>
                                            </button>
                                        </div>
                                        <div class="btn-group me-2" role="group">
                                            <button type="button" class="btn btn-sm btn-light" data-action="bulletList" title="Lista">
                                                <i class="ki-duotone ki-bullets fs-3"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-light" data-action="orderedList" title="Lista numerada">
                                                <i class="ki-duotone ki-numbers fs-3"></i>
                                            </button>
                                        </div>
                                        <div class="btn-group" role="group">
                                            <select class="form-select form-select-sm" data-action="heading" style="width: auto;">
                                                <option value="paragraph">Parágrafo</option>
                                                <option value="1">Título 1</option>
                                                <option value="2">Título 2</option>
                                                <option value="3">Título 3</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Menu-->

                                <!--begin::Content-->
                                <div class="editor-content" id="editor"></div>
                                <!--end::Content-->
                            </div>
                            <!--end::Editor-->
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Card-->
                </div>
                <!--end::Main Editor-->

                <!--begin::Sidebar-->
                <div class="col-xl-4">
                    <!--begin::Variables Card-->
                    <div class="card mb-5">
                        <div class="card-header">
                            <h3 class="card-title">Variáveis Disponíveis</h3>
                            <div class="card-toolbar">
                                <button type="button" class="btn btn-sm btn-light" data-bs-toggle="collapse" data-bs-target="#variables_help">
                                    <i class="ki-duotone ki-question fs-2"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body variable-sidebar">
                            <div class="collapse" id="variables_help">
                                <div class="notice d-flex bg-light-info rounded border-info border border-dashed p-4 mb-4">
                                    <div class="d-flex flex-stack flex-grow-1">
                                        <div class="fw-semibold">
                                            <div class="fs-7 text-gray-700">
                                                Clique em uma variável para inseri-la no documento. 
                                                As variáveis serão substituídas automaticamente pelos valores reais na exportação.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="variables_list">
                                @if($variaveisData && count($variaveisData) > 0)
                                    @foreach($variaveisData as $nome => $valor)
                                        <div class="variable-item d-flex align-items-center p-3 border border-gray-300 border-dashed rounded mb-2" 
                                             data-variable="{{ $nome }}" data-value="{{ $valor }}">
                                            <div class="flex-grow-1">
                                                <div class="fw-bold text-gray-900">${{ $nome }}</div>
                                                <div class="text-muted fs-7">{{ Str::limit($valor, 30) }}</div>
                                            </div>
                                            <i class="ki-duotone ki-plus fs-3 text-primary"></i>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="text-center py-5">
                                        <div class="text-muted fs-6">Selecione um modelo para ver as variáveis disponíveis</div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <!--end::Variables Card-->

                    <!--begin::Options Card-->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Configurações</h3>
                        </div>
                        <div class="card-body">
                            <!--begin::Model Selection-->
                            <div class="mb-5">
                                <label class="form-label fw-semibold">Modelo Base</label>
                                <select id="modelo_select" class="form-select" data-control="select2" data-placeholder="Selecione um modelo">
                                    <option value="">Documento em branco</option>
                                    @foreach($modelos as $modeloOption)
                                        <option value="{{ $modeloOption->id }}" 
                                                {{ $modelo && $modelo->id == $modeloOption->id ? 'selected' : '' }}>
                                            {{ $modeloOption->nome }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <!--end::Model Selection-->

                            <!--begin::Project Selection-->
                            <div class="mb-5">
                                <label class="form-label fw-semibold">Projeto Associado</label>
                                <select id="projeto_select" class="form-select" data-control="select2" data-placeholder="Selecione um projeto">
                                    <option value="">Nenhum projeto</option>
                                    @foreach($projetos as $projetoOption)
                                        <option value="{{ $projetoOption->id }}" 
                                                {{ $projeto && $projeto->id == $projetoOption->id ? 'selected' : '' }}>
                                            {{ $projetoOption->numero_proposicao ?? $projetoOption->titulo }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <!--end::Project Selection-->

                            <!--begin::Actions-->
                            <div class="d-flex flex-column gap-2">
                                <button type="button" id="btn_load_template" class="btn btn-sm btn-light-primary w-100">
                                    <i class="ki-duotone ki-file-down fs-2"></i>
                                    Carregar Conteúdo do Modelo
                                </button>
                                <button type="button" id="btn_refresh_variables" class="btn btn-sm btn-light-success w-100">
                                    <i class="ki-duotone ki-arrows-circle fs-2"></i>
                                    Atualizar Variáveis
                                </button>
                            </div>
                            <!--end::Actions-->
                        </div>
                    </div>
                    <!--end::Options Card-->
                </div>
                <!--end::Sidebar-->
            </div>

        </div>
    </div>
    <!--end::Content-->
</div>
<!--end::Content wrapper-->

<!--begin::Export Modal-->
<div class="modal fade" id="export_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Exportar Documento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="export_form">
                    <div class="mb-4">
                        <label class="form-label">Título do Documento</label>
                        <input type="text" id="export_title" class="form-control" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Formato de Exportação</label>
                        <select id="export_format" class="form-select" required>
                            <option value="docx">Microsoft Word (.docx)</option>
                            <option value="pdf">Adobe PDF (.pdf)</option>
                        </select>
                    </div>
                    <div class="notice d-flex bg-light-warning rounded border-warning border border-dashed p-4">
                        <div class="d-flex flex-stack flex-grow-1">
                            <div class="fw-semibold">
                                <div class="fs-7 text-gray-700">
                                    O documento será processado e as variáveis serão substituídas automaticamente pelos valores atuais.
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" id="btn_confirm_export" class="btn btn-primary">
                    <span class="indicator-label">Exportar Documento</span>
                    <span class="indicator-progress">Processando...
                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>
<!--end::Export Modal-->

@endsection

@push('scripts')
<!-- TipTap Dependencies via unpkg -->
<script src="https://unpkg.com/@tiptap/core@2.1.13/dist/index.umd.js"></script>
<script src="https://unpkg.com/@tiptap/starter-kit@2.1.13/dist/index.umd.js"></script>
<script src="https://unpkg.com/@tiptap/extension-text-align@2.1.13/dist/index.umd.js"></script>
<script src="https://unpkg.com/@tiptap/extension-underline@2.1.13/dist/index.umd.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar Select2
    $('[data-control="select2"]').select2();

    // Aguardar carregamento das dependências
    let checkTipTap = setInterval(() => {
        if (typeof window.Editor !== 'undefined' && 
            typeof window.StarterKit !== 'undefined' && 
            typeof window.TextAlign !== 'undefined' && 
            typeof window.Underline !== 'undefined') {
            
            clearInterval(checkTipTap);
            initEditor();
        }
    }, 100);

    function initEditor() {
        // Inicializar TipTap Editor
        const editor = new window.Editor({
            element: document.querySelector('#editor'),
            extensions: [
                window.StarterKit.configure({
                    heading: {
                        levels: [1, 2, 3]
                    }
                }),
                window.TextAlign.configure({
                    types: ['heading', 'paragraph']
                }),
                window.Underline
            ],
            content: `
                <h1 style="text-align: center;">TÍTULO DO DOCUMENTO</h1>
                <p></p>
                <p><strong>Autor:</strong> \${autor_nome} - \${autor_cargo}</p>
                <p><strong>Data:</strong> \${data_criacao}</p>
                <p></p>
                <p><strong>EMENTA:</strong> \${ementa}</p>
                <p></p>
                <p>Conteúdo do documento...</p>
            `,
            editorProps: {
                attributes: {
                    class: 'prose prose-sm sm:prose lg:prose-lg xl:prose-2xl mx-auto focus:outline-none',
                },
            },
        });

        // Toolbar actions
        document.querySelectorAll('[data-action]').forEach(button => {
            button.addEventListener('click', (e) => {
                const action = e.currentTarget.getAttribute('data-action');
                
                switch(action) {
                    case 'bold':
                        editor.chain().focus().toggleBold().run();
                        break;
                    case 'italic':
                        editor.chain().focus().toggleItalic().run();
                        break;
                    case 'underline':
                        editor.chain().focus().toggleUnderline().run();
                        break;
                    case 'left':
                        editor.chain().focus().setTextAlign('left').run();
                        break;
                    case 'center':
                        editor.chain().focus().setTextAlign('center').run();
                        break;
                    case 'right':
                        editor.chain().focus().setTextAlign('right').run();
                        break;
                    case 'justify':
                        editor.chain().focus().setTextAlign('justify').run();
                        break;
                    case 'bulletList':
                        editor.chain().focus().toggleBulletList().run();
                        break;
                    case 'orderedList':
                        editor.chain().focus().toggleOrderedList().run();
                        break;
                }
            });
        });

        // Heading selector
        document.querySelector('[data-action="heading"]').addEventListener('change', (e) => {
            const level = e.target.value;
            if (level === 'paragraph') {
                editor.chain().focus().setParagraph().run();
            } else {
                editor.chain().focus().toggleHeading({ level: parseInt(level) }).run();
            }
        });

        // Variable insertion
        document.addEventListener('click', function(e) {
            if (e.target.closest('.variable-item')) {
                const variableItem = e.target.closest('.variable-item');
                const variable = variableItem.getAttribute('data-variable');
                
                editor.chain().focus().insertContent(`\${${variable}}`).run();
            }
        });

        // Export functionality
        window.editorInstance = editor; // Make editor available globally for export
    }


    // Model selection change
    document.getElementById('modelo_select').addEventListener('change', function() {
        const modeloId = this.value;
        if (modeloId) {
            loadModelVariables(modeloId);
        } else {
            document.getElementById('variables_list').innerHTML = `
                <div class="text-center py-5">
                    <div class="text-muted fs-6">Selecione um modelo para ver as variáveis disponíveis</div>
                </div>
            `;
        }
    });

    // Project selection change
    document.getElementById('projeto_select').addEventListener('change', function() {
        refreshVariables();
    });

    // Load template content
    document.getElementById('btn_load_template').addEventListener('click', function() {
        const modeloId = document.getElementById('modelo_select').value;
        if (!modeloId) {
            Swal.fire('Atenção', 'Selecione um modelo primeiro.', 'warning');
            return;
        }
        
        // Aqui você carregaria o conteúdo do modelo
        Swal.fire('Sucesso', 'Conteúdo do modelo carregado!', 'success');
    });

    // Refresh variables
    document.getElementById('btn_refresh_variables').addEventListener('click', function() {
        refreshVariables();
    });

    // Export button
    document.getElementById('btn_export').addEventListener('click', function() {
        document.getElementById('export_title').value = document.getElementById('document_title').value;
        document.getElementById('export_format').value = document.getElementById('formato_exportacao').value;
        
        const modal = new bootstrap.Modal(document.getElementById('export_modal'));
        modal.show();
    });

    // Confirm export
    document.getElementById('btn_confirm_export').addEventListener('click', function() {
        const button = this;
        const title = document.getElementById('export_title').value;
        const format = document.getElementById('export_format').value;
        const content = window.editorInstance ? window.editorInstance.getHTML() : '';
        const modeloId = document.getElementById('modelo_select').value;
        const projetoId = document.getElementById('projeto_select').value;

        if (!title.trim()) {
            Swal.fire('Erro', 'Digite um título para o documento.', 'error');
            return;
        }

        button.setAttribute('data-kt-indicator', 'on');
        button.disabled = true;

        // Collect variables data
        const variaveis = {};
        document.querySelectorAll('.variable-item').forEach(item => {
            const variable = item.getAttribute('data-variable');
            const value = item.getAttribute('data-value');
            variaveis[variable] = value;
        });

        fetch('{{ route("documentos.editor.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                modelo_id: modeloId,
                projeto_id: projetoId,
                titulo: title,
                conteudo: content,
                variaveis: variaveis,
                formato_exportacao: format
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('export_modal')).hide();
                
                // Download file
                window.location.href = data.download_url;
                
                Swal.fire('Sucesso', data.message, 'success');
            } else {
                Swal.fire('Erro', data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Erro', 'Erro ao exportar documento.', 'error');
        })
        .finally(() => {
            button.removeAttribute('data-kt-indicator');
            button.disabled = false;
        });
    });

    // Functions
    function loadModelVariables(modeloId) {
        fetch(`{{ route('documentos.editor.index') }}/variaveis/${modeloId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateVariablesList(data.variaveis);
                }
            })
            .catch(error => {
                console.error('Error loading variables:', error);
            });
    }

    function refreshVariables() {
        const modeloId = document.getElementById('modelo_select').value;
        const projetoId = document.getElementById('projeto_select').value;
        
        if (!modeloId) return;

        const url = projetoId ? 
            '{{ route("documentos.editor.index") }}/preencher-variaveis' : 
            `{{ route('documentos.editor.index') }}/variaveis/${modeloId}`;

        const requestData = projetoId ? {
            modelo_id: modeloId,
            projeto_id: projetoId
        } : {};

        fetch(url, {
            method: projetoId ? 'POST' : 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: projetoId ? JSON.stringify(requestData) : null
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateVariablesList(data.variaveis);
            }
        })
        .catch(error => {
            console.error('Error refreshing variables:', error);
        });
    }

    function updateVariablesList(variaveis) {
        const container = document.getElementById('variables_list');
        
        if (!variaveis || Object.keys(variaveis).length === 0) {
            container.innerHTML = `
                <div class="text-center py-5">
                    <div class="text-muted fs-6">Nenhuma variável disponível</div>
                </div>
            `;
            return;
        }

        let html = '';
        Object.entries(variaveis).forEach(([nome, valor]) => {
            html += `
                <div class="variable-item d-flex align-items-center p-3 border border-gray-300 border-dashed rounded mb-2" 
                     data-variable="${nome}" data-value="${valor || ''}">
                    <div class="flex-grow-1">
                        <div class="fw-bold text-gray-900">\${${nome}}</div>
                        <div class="text-muted fs-7">${valor ? valor.substring(0, 30) + (valor.length > 30 ? '...' : '') : 'Valor padrão'}</div>
                    </div>
                    <i class="ki-duotone ki-plus fs-3 text-primary"></i>
                </div>
            `;
        });
        
        container.innerHTML = html;
    }
});
</script>
@endpush