@extends('components.layouts.app')

@section('title', 'Editor de Template - Relatório PDF')

@section('content')
<!--begin::Post-->
<div class="post d-flex flex-column-fluid" id="kt_post">
    <!--begin::Container-->
    <div id="kt_content_container" class="container-xxl">
        <!--begin::Header-->
        <div class="d-flex flex-wrap flex-stack mb-6">
            <!--begin::Title-->
            <h3 class="fw-bold my-2">
                Editor de Template - Relatório PDF
                <span class="fs-6 text-gray-500 fw-semibold ms-1">Personalize o layout do relatório de produtividade</span>
            </h3>
            <!--end::Title-->
            <!--begin::Actions-->
            <div class="d-flex flex-wrap my-2">
                <button type="button" class="btn btn-sm btn-light-warning me-3" id="btn-preview">
                    <i class="ki-duotone ki-eye fs-4">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                    </i>
                    Preview
                </button>
                
                <button type="button" class="btn btn-sm btn-light-info me-3" id="btn-backups">
                    <i class="ki-duotone ki-folder fs-4">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    Backups
                </button>
                
                <button type="button" class="btn btn-sm btn-light-danger me-3" id="btn-reset">
                    <i class="ki-duotone ki-arrows-circle fs-4">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    Resetar
                </button>
                
                <button type="button" class="btn btn-sm btn-primary" id="btn-salvar">
                    <i class="ki-duotone ki-check fs-4">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    Salvar Template
                </button>
            </div>
            <!--end::Actions-->
        </div>
        <!--end::Header-->

        <!--begin::Row-->
        <div class="row g-5">
            <!--begin::Editor-->
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header">
                        <h3 class="card-title">Editor HTML/CSS</h3>
                        <div class="card-toolbar">
                            <div class="badge badge-light-primary">Template Blade</div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <textarea id="template-editor" name="template_content" style="height: 600px; width: 100%;">{{ $conteudoTemplate }}</textarea>
                    </div>
                </div>
            </div>
            <!--end::Editor-->

            <!--begin::Preview-->
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header">
                        <h3 class="card-title">Preview do PDF</h3>
                        <div class="card-toolbar">
                            <button type="button" class="btn btn-sm btn-light-primary" id="btn-refresh-preview">
                                <i class="ki-duotone ki-arrows-circle fs-4">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                Atualizar
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div id="preview-container" style="height: 600px; overflow-y: auto; background: white; border: 1px solid #e1e3ea;">
                            <div class="d-flex align-items-center justify-content-center h-100 text-muted">
                                <div class="text-center">
                                    <i class="ki-duotone ki-eye fs-3x mb-3">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                    </i>
                                    <div>Clique em "Preview" para visualizar</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Preview-->
        </div>
        <!--end::Row-->

        <!--begin::Help Section-->
        <div class="row mt-5">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Ajuda - Variáveis Disponíveis</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h5>Dados do Relatório:</h5>
                                <ul class="list-unstyled">
                                    <li><code>${{ '$dados[\'data_inicio\']' }}</code> - Data de início</li>
                                    <li><code>${{ '$dados[\'data_fim\']' }}</code> - Data de fim</li>
                                    <li><code>${{ '$dados[\'total_geral\']' }}</code> - Total de proposições</li>
                                    <li><code>${{ '$dados[\'periodo\']' }}</code> - Tipo de período</li>
                                </ul>
                                
                                <h5>Dados por Usuário:</h5>
                                <ul class="list-unstyled">
                                    <li><code>@foreach($dados['dados_por_usuario'] as $user)</code></li>
                                    <li><code>${{ '$user[\'nome\']' }}</code> - Nome do usuário</li>
                                    <li><code>${{ '$user[\'aprovadas\']' }}</code> - Proposições aprovadas</li>
                                    <li><code>${{ '$user[\'devolvidas\']' }}</code> - Proposições devolvidas</li>
                                    <li><code>${{ '$user[\'retornadas\']' }}</code> - Proposições retornadas</li>
                                    <li><code>${{ '$user[\'total\']' }}</code> - Total do usuário</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h5>Lista de Proposições:</h5>
                                <ul class="list-unstyled">
                                    <li><code>@foreach($dados['proposicoes'] as $prop)</code></li>
                                    <li><code>${{ '$prop->id' }}</code> - ID da proposição</li>
                                    <li><code>${{ '$prop->tipo' }}</code> - Tipo da proposição</li>
                                    <li><code>${{ '$prop->titulo' }}</code> - Título</li>
                                    <li><code>${{ '$prop->status' }}</code> - Status atual</li>
                                    <li><code>${{ '$prop->autor->name' }}</code> - Nome do autor</li>
                                    <li><code>${{ '$prop->updated_at->format(\'d/m/Y\')' }}</code> - Data</li>
                                </ul>
                                
                                <h5>Funções Úteis:</h5>
                                <ul class="list-unstyled">
                                    <li><code>${{ 'now()->format(\'d/m/Y H:i:s\')' }}</code> - Data atual</li>
                                    <li><code>array_sum(array_column($dados['dados_por_usuario'], 'aprovadas'))</code> - Soma aprovadas</li>
                                    <li><code>count($dados['dados_por_usuario'])</code> - Contagem de usuários</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--end::Help Section-->
    </div>
    <!--end::Container-->
</div>
<!--end::Post-->

<!-- Modal Backups -->
<div class="modal fade" id="modal-backups" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Gerenciar Backups</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="backups-list">
                    <div class="text-center">
                        <div class="spinner-border" role="status">
                            <span class="sr-only">Carregando...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- CodeMirror -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/codemirror.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/codemirror.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/xml/xml.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/css/css.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/htmlmixed/htmlmixed.min.js"></script>

<script>
$(document).ready(function() {
    // Inicializar CodeMirror
    const editor = CodeMirror.fromTextArea(document.getElementById('template-editor'), {
        lineNumbers: true,
        mode: 'htmlmixed',
        theme: 'default',
        lineWrapping: true,
        autoCloseTags: true,
        matchBrackets: true,
        indentUnit: 4,
        tabSize: 4,
        extraKeys: {
            "Ctrl-S": function() {
                $('#btn-salvar').click();
            },
            "Ctrl-P": function() {
                $('#btn-preview').click();
            }
        }
    });

    // Preview do template
    $('#btn-preview, #btn-refresh-preview').on('click', function() {
        const templateContent = editor.getValue();
        
        $.ajax({
            url: '{{ route("admin.templates.preview") }}',
            type: 'POST',
            data: {
                template_content: templateContent,
                _token: '{{ csrf_token() }}'
            },
            beforeSend: function() {
                $('#preview-container').html(`
                    <div class="d-flex align-items-center justify-content-center h-100">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Carregando...</span>
                        </div>
                    </div>
                `);
            },
            success: function(response) {
                if (response.success) {
                    $('#preview-container').html(response.html);
                } else {
                    $('#preview-container').html(`
                        <div class="alert alert-danger m-3">
                            <h6>Erro no Preview:</h6>
                            <pre>${response.message}</pre>
                        </div>
                    `);
                }
            },
            error: function() {
                $('#preview-container').html(`
                    <div class="alert alert-danger m-3">
                        Erro ao gerar preview. Verifique a sintaxe do template.
                    </div>
                `);
            }
        });
    });

    // Salvar template
    $('#btn-salvar').on('click', function() {
        const templateContent = editor.getValue();
        
        $.ajax({
            url: '{{ route("admin.templates.salvar-pdf") }}',
            type: 'POST',
            data: {
                template_content: templateContent,
                _token: '{{ csrf_token() }}'
            },
            beforeSend: function() {
                $('#btn-salvar').prop('disabled', true).html(`
                    <span class="spinner-border spinner-border-sm" role="status"></span>
                    Salvando...
                `);
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire('Sucesso!', response.message, 'success');
                } else {
                    Swal.fire('Erro!', response.message, 'error');
                }
            },
            error: function() {
                Swal.fire('Erro!', 'Erro ao salvar template', 'error');
            },
            complete: function() {
                $('#btn-salvar').prop('disabled', false).html(`
                    <i class="ki-duotone ki-check fs-4">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    Salvar Template
                `);
            }
        });
    });

    // Gerenciar backups
    $('#btn-backups').on('click', function() {
        $('#modal-backups').modal('show');
        carregarBackups();
    });

    function carregarBackups() {
        $.ajax({
            url: '{{ route("admin.templates.listar-backups") }}',
            type: 'GET',
            success: function(backups) {
                let html = '';
                if (backups.length === 0) {
                    html = '<div class="text-center text-muted">Nenhum backup encontrado</div>';
                } else {
                    html = '<div class="table-responsive"><table class="table table-striped">';
                    html += '<thead><tr><th>Arquivo</th><th>Data</th><th>Tamanho</th><th>Ações</th></tr></thead><tbody>';
                    
                    backups.forEach(function(backup) {
                        html += `<tr>
                            <td>${backup.filename}</td>
                            <td>${backup.created_at}</td>
                            <td>${backup.size}</td>
                            <td>
                                <button class="btn btn-sm btn-light-primary" onclick="restaurarBackup('${backup.filename}')">
                                    Restaurar
                                </button>
                            </td>
                        </tr>`;
                    });
                    
                    html += '</tbody></table></div>';
                }
                $('#backups-list').html(html);
            }
        });
    }

    // Restaurar backup
    window.restaurarBackup = function(filename) {
        Swal.fire({
            title: 'Restaurar Backup?',
            text: 'Isso irá substituir o template atual.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sim, restaurar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route("admin.templates.restaurar-backup") }}',
                    type: 'POST',
                    data: {
                        backup_file: filename,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Sucesso!', response.message, 'success');
                            location.reload();
                        } else {
                            Swal.fire('Erro!', response.message, 'error');
                        }
                    }
                });
            }
        });
    };

    // Resetar template
    $('#btn-reset').on('click', function() {
        Swal.fire({
            title: 'Resetar Template?',
            text: 'Isso irá restaurar o template padrão e criar um backup do atual.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sim, resetar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route("admin.templates.resetar") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Sucesso!', response.message, 'success');
                            location.reload();
                        } else {
                            Swal.fire('Erro!', response.message, 'error');
                        }
                    }
                });
            }
        });
    });

    // Auto-preview ao carregar
    setTimeout(function() {
        $('#btn-preview').click();
    }, 500);
});
</script>
@endpush