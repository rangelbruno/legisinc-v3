@extends('layouts.app')

@section('title', 'Templates de Documentos')

@section('content')
<div class="container">
    <!--begin::Toolbar-->
    <div class="toolbar mb-5 mb-lg-7" id="kt_toolbar">
        <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
            <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                <i class="ki-duotone ki-document fs-2 me-2">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
                Templates de Documentos
            </h1>
            <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                <li class="breadcrumb-item text-muted">
                    <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Dashboard</a>
                </li>
                <li class="breadcrumb-item">
                    <span class="bullet bg-gray-500 w-5px h-2px"></span>
                </li>
                <li class="breadcrumb-item text-muted">Administração</li>
                <li class="breadcrumb-item">
                    <span class="bullet bg-gray-500 w-5px h-2px"></span>
                </li>
                <li class="breadcrumb-item text-gray-900">Templates</li>
            </ul>
        </div>
    </div>
    <!--end::Toolbar-->

    <!--begin::Stats-->
    <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
        <div class="col-md-6 col-lg-6 col-xl-6 col-xxl-3 mb-md-5 mb-xl-10">
            <div class="card card-flush bgi-no-repeat bgi-size-contain bgi-position-x-end mb-5 mb-xl-10" style="background-color: #F1416C;background-image:url('{{ asset('assets/media/patterns/vector-1.png') }}')">
                <div class="card-header pt-5">
                    <div class="card-title d-flex flex-column">
                        <div class="d-flex align-items-center">
                            <span class="fs-4 fw-semibold text-white me-1 align-self-start">{{ $tipos->count() }}</span>
                        </div>
                        <span class="text-white opacity-75 pt-1 fw-semibold fs-6">Tipos de Proposição</span>
                    </div>
                </div>
                <div class="card-body d-flex align-items-end pt-0">
                    <div class="d-flex align-items-center flex-column mt-3 w-100">
                        <div class="d-flex justify-content-between w-100 mt-auto mb-2">
                            <span class="fw-semibold fs-7 text-white opacity-75">Total</span>
                            <span class="fw-bold fs-6 text-white">{{ $tipos->count() }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-6 col-xl-6 col-xxl-3 mb-md-5 mb-xl-10">
            <div class="card card-flush bgi-no-repeat bgi-size-contain bgi-position-x-end mb-5 mb-xl-10" style="background-color: #7239EA;background-image:url('{{ asset('assets/media/patterns/vector-1.png') }}')">
                <div class="card-header pt-5">
                    <div class="card-title d-flex flex-column">
                        <div class="d-flex align-items-center">
                            <span class="fs-4 fw-semibold text-white me-1 align-self-start">{{ $tipos->filter(fn($t) => $t->hasTemplate())->count() }}</span>
                        </div>
                        <span class="text-white opacity-75 pt-1 fw-semibold fs-6">Com Template</span>
                    </div>
                </div>
                <div class="card-body d-flex align-items-end pt-0">
                    <div class="d-flex align-items-center flex-column mt-3 w-100">
                        <div class="d-flex justify-content-between w-100 mt-auto mb-2">
                            <span class="fw-semibold fs-7 text-white opacity-75">Ativos</span>
                            <span class="fw-bold fs-6 text-white">{{ $tipos->filter(fn($t) => $t->hasTemplate())->count() }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-6 col-xl-6 col-xxl-3 mb-md-5 mb-xl-10">
            <div class="card card-flush bgi-no-repeat bgi-size-contain bgi-position-x-end mb-5 mb-xl-10" style="background-color: #F1BC00;background-image:url('{{ asset('assets/media/patterns/vector-1.png') }}')">
                <div class="card-header pt-5">
                    <div class="card-title d-flex flex-column">
                        <div class="d-flex align-items-center">
                            <span class="fs-4 fw-semibold text-white me-1 align-self-start">{{ $tipos->filter(fn($t) => !$t->hasTemplate())->count() }}</span>
                        </div>
                        <span class="text-white opacity-75 pt-1 fw-semibold fs-6">Sem Template</span>
                    </div>
                </div>
                <div class="card-body d-flex align-items-end pt-0">
                    <div class="d-flex align-items-center flex-column mt-3 w-100">
                        <div class="d-flex justify-content-between w-100 mt-auto mb-2">
                            <span class="fw-semibold fs-7 text-white opacity-75">Pendentes</span>
                            <span class="fw-bold fs-6 text-white">{{ $tipos->filter(fn($t) => !$t->hasTemplate())->count() }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-6 col-xl-6 col-xxl-3 mb-md-5 mb-xl-10">
            <div class="card card-flush bgi-no-repeat bgi-size-contain bgi-position-x-end mb-5 mb-xl-10" style="background-color: #17C653;background-image:url('{{ asset('assets/media/patterns/vector-1.png') }}')">
                <div class="card-header pt-5">
                    <div class="card-title d-flex flex-column">
                        <div class="d-flex align-items-center">
                            <span class="fs-4 fw-semibold text-white me-1 align-self-start">{{ round($tipos->count() > 0 ? ($tipos->filter(fn($t) => $t->hasTemplate())->count() / $tipos->count()) * 100 : 0) }}%</span>
                        </div>
                        <span class="text-white opacity-75 pt-1 fw-semibold fs-6">Cobertura</span>
                    </div>
                </div>
                <div class="card-body d-flex align-items-end pt-0">
                    <div class="d-flex align-items-center flex-column mt-3 w-100">
                        <div class="d-flex justify-content-between w-100 mt-auto mb-2">
                            <span class="fw-semibold fs-7 text-white opacity-75">Templates</span>
                            <span class="fw-bold fs-6 text-white">{{ round($tipos->count() > 0 ? ($tipos->filter(fn($t) => $t->hasTemplate())->count() / $tipos->count()) * 100 : 0) }}%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end::Stats-->

    <div class="card">
        <div class="card-header border-0 pt-6">
            <div class="card-title">
                <div class="d-flex align-items-center position-relative my-1">
                    <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-5">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    <input type="text" data-kt-templates-table-filter="search" class="form-control form-control-solid w-250px ps-13" placeholder="Buscar tipos..." />
                </div>
            </div>
            <div class="card-toolbar">
                <div class="d-flex justify-content-end" data-kt-templates-table-toolbar="base">
                    <a href="/admin/parametros/6" class="btn btn-light-success me-3">
                        <i class="ki-duotone ki-setting-2 fs-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        Parâmetros
                    </a>
                    <button type="button" class="btn btn-light-warning me-3" onclick="regenerarTodosTemplates()">
                        <i class="ki-duotone ki-arrows-circle fs-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        Regenerar Todos
                    </button>
                    <button type="button" class="btn btn-light-primary me-3" data-bs-toggle="modal" data-bs-target="#kt_modal_help_templates">
                        <i class="ki-duotone ki-information-5 fs-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                        </i>
                        Como Usar
                    </button>
                    <a href="{{ route('templates.create') }}" class="btn btn-primary">
                        <i class="ki-duotone ki-plus fs-2"></i>
                        Novo Template
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-row-bordered align-middle">
                    <thead>
                        <tr class="fw-bold fs-6 text-gray-800">
                            <th>Tipo de Proposição</th>
                            <th>Status do Template</th>
                            <th>Última Atualização</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tipos as $tipo)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="ki-duotone ki-document fs-3 text-primary me-3">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        <div>
                                            <div class="fw-bold">{{ $tipo->nome }}</div>
                                            @if($tipo->template?->variaveis)
                                                <div class="text-muted fs-7">
                                                    {{ count($tipo->template->variaveis) }} variáveis
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($tipo->hasTemplate())
                                        <span class="badge badge-success">
                                            <i class="ki-duotone ki-check fs-7 me-1">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            Ativo
                                        </span>
                                    @else
                                        <span class="badge badge-warning">
                                            <i class="ki-duotone ki-information fs-7 me-1">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                                <span class="path3"></span>
                                            </i>
                                            Sem Template
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($tipo->template)
                                        <div>{{ $tipo->template->updated_at->format('d/m/Y H:i') }}</div>
                                        <div class="text-muted fs-7">
                                            por {{ $tipo->template->updatedBy->name ?? 'Sistema' }}
                                        </div>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('templates.editor', $tipo) }}" 
                                           class="btn btn-sm btn-primary">
                                            @if($tipo->hasTemplate())
                                                <i class="ki-duotone ki-pencil fs-6 me-1">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                Editar
                                            @else
                                                <i class="ki-duotone ki-plus fs-6 me-1">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                Criar
                                            @endif
                                        </a>
                                        
                                        <button type="button" 
                                                class="btn btn-sm btn-success"
                                                onclick="gerarComPadroesLegais({{ $tipo->id }}, '{{ $tipo->nome }}')"
                                                title="Gerar template com padrões LC 95/1998">
                                            <i class="ki-duotone ki-law fs-6 me-1">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            LC 95/1998
                                        </button>
                                    </div>
                                    
                                    <div class="btn-group ms-2" role="group">
                                        @if($tipo->hasTemplate())
                                            <a href="{{ route('api.templates.download', $tipo->template) }}?v={{ $tipo->template->updated_at->timestamp }}" 
                                               class="btn btn-sm btn-light-primary">
                                                <i class="ki-duotone ki-exit-down fs-6 me-1">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                Download
                                            </a>
                                            
                                            <button type="button" 
                                                    class="btn btn-sm btn-light-info"
                                                    onclick="validarTemplate({{ $tipo->id }}, '{{ $tipo->nome }}')"
                                                    title="Validar conformidade legal">
                                                <i class="ki-duotone ki-shield-tick fs-6 me-1">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                    <span class="path3"></span>
                                                </i>
                                                Validar
                                            </button>
                                            
                                            <button type="button" 
                                                    class="btn btn-sm btn-light-danger"
                                                    onclick="confirmarExclusaoTemplate({{ $tipo->template->id }}, '{{ $tipo->nome }}')">
                                                <i class="ki-duotone ki-trash fs-6 me-1">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                    <span class="path3"></span>
                                                    <span class="path4"></span>
                                                    <span class="path5"></span>
                                                </i>
                                                Excluir
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<!--begin::Modal - Como Usar-->
<div class="modal fade" id="kt_modal_help_templates" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-900px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold">Como Usar Templates de Documentos</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                </div>
            </div>
            <div class="modal-body py-10 px-lg-17">
                <div class="row">
                    <div class="col-12">
                        <div class="mb-10">
                            <h3 class="text-gray-900 fw-bold fs-4 mb-5">
                                <i class="ki-duotone ki-rocket fs-2 text-primary me-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                Passo a Passo
                            </h3>
                            <div class="timeline timeline-border-dashed">
                                <div class="timeline-item">
                                    <div class="timeline-line w-40px"></div>
                                    <div class="timeline-icon symbol symbol-circle symbol-40px">
                                        <div class="symbol-label bg-light">
                                            <span class="text-gray-500 fw-bold fs-8">1</span>
                                        </div>
                                    </div>
                                    <div class="timeline-content mb-10 mt-n1">
                                        <div class="mb-5 pe-3">
                                            <div class="fs-6 fw-semibold text-gray-700 mb-2">Criar Template</div>
                                            <div class="text-gray-500">Clique em "Novo Template" ou "Criar Template" para um tipo específico</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="timeline-item">
                                    <div class="timeline-line w-40px"></div>
                                    <div class="timeline-icon symbol symbol-circle symbol-40px">
                                        <div class="symbol-label bg-light">
                                            <span class="text-gray-500 fw-bold fs-8">2</span>
                                        </div>
                                    </div>
                                    <div class="timeline-content mb-10 mt-n1">
                                        <div class="mb-5 pe-3">
                                            <div class="fs-6 fw-semibold text-gray-700 mb-2">Editar no ONLYOFFICE</div>
                                            <div class="text-gray-500">Use o editor integrado para criar seu documento template</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="timeline-item">
                                    <div class="timeline-line w-40px"></div>
                                    <div class="timeline-icon symbol symbol-circle symbol-40px">
                                        <div class="symbol-label bg-light">
                                            <span class="text-gray-500 fw-bold fs-8">3</span>
                                        </div>
                                    </div>
                                    <div class="timeline-content mb-10 mt-n1">
                                        <div class="mb-5 pe-3">
                                            <div class="fs-6 fw-semibold text-gray-700 mb-2">Usar Variáveis</div>
                                            <div class="text-gray-500">Adicione variáveis como <code>${numero_proposicao}</code>, <code>${ementa}</code>, etc.</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="timeline-item">
                                    <div class="timeline-line w-40px"></div>
                                    <div class="timeline-icon symbol symbol-circle symbol-40px">
                                        <div class="symbol-label bg-light">
                                            <span class="text-gray-500 fw-bold fs-8">4</span>
                                        </div>
                                    </div>
                                    <div class="timeline-content mb-10 mt-n1">
                                        <div class="mb-5 pe-3">
                                            <div class="fs-6 fw-semibold text-gray-700 mb-2">Salvar Automaticamente</div>
                                            <div class="text-gray-500">O template é salvo automaticamente a cada alteração</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-10">
                            <h3 class="text-gray-900 fw-bold fs-4 mb-5">
                                <i class="ki-duotone ki-code fs-2 text-success me-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                </i>
                                Variáveis Disponíveis
                            </h3>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3"><code>${numero_proposicao}</code> - Número da proposição</div>
                                    <div class="mb-3"><code>${ementa}</code> - Ementa da proposição</div>
                                    <div class="mb-3"><code>${texto}</code> - Texto principal</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3"><code>${autor_nome}</code> - Nome do autor</div>
                                    <div class="mb-3"><code>${data_atual}</code> - Data atual</div>
                                    <div class="mb-3"><code>${municipio}</code> - Nome do município</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>
<!--end::Modal - Como Usar-->

@endsection

@push('styles')
<style>
    /* Reduzir espaçamento entre linhas da tabela */
    .table td {
        padding-top: 0.5rem !important;
        padding-bottom: 0.5rem !important;
    }
    
    .table th {
        padding-top: 0.75rem !important;
        padding-bottom: 0.75rem !important;
    }
</style>
@endpush

@push('scripts')
<script>
// Filtro de busca na tabela
document.querySelector('[data-kt-templates-table-filter="search"]').addEventListener('input', function(e) {
    const searchValue = e.target.value.toLowerCase();
    const tableRows = document.querySelectorAll('tbody tr');
    
    tableRows.forEach(row => {
        const tipoNome = row.querySelector('td:first-child .fw-bold').textContent.toLowerCase();
        if (tipoNome.includes(searchValue)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});

// Função para confirmar exclusão de template
function confirmarExclusaoTemplate(templateId, tipoNome) {
    Swal.fire({
        title: 'Confirmar Exclusão',
        html: `Tem certeza que deseja excluir o template do tipo:<br><strong>${tipoNome}</strong>?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sim, Excluir',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#f1416c',
        cancelButtonColor: '#7e8299',
        customClass: {
            confirmButton: 'btn btn-danger',
            cancelButton: 'btn btn-light'
        },
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Mostrar loading
            Swal.fire({
                title: 'Excluindo...',
                text: 'Aguarde enquanto o template é removido.',
                icon: 'info',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Criar formulário para enviar DELETE
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/templates/${templateId}`;
            form.style.display = 'none';
            
            // Token CSRF
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            form.appendChild(csrfToken);
            
            // Method DELETE
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'DELETE';
            form.appendChild(methodInput);
            
            // Adicionar ao DOM e submeter
            document.body.appendChild(form);
            form.submit();
        }
    });
}

// Cache-busting nos links de download já resolve o problema
// O link sempre terá o timestamp correto do banco de dados

function regenerarTodosTemplates() {
    Swal.fire({
        title: 'Regenerar Todos os Templates?',
        html: `
            <p>Esta ação irá:</p>
            <ul class="text-start">
                <li>Regenerar templates para todos os 23 tipos de proposição</li>
                <li>Aplicar os <strong>padrões legais LC 95/1998</strong></li>
                <li>Aplicar os parâmetros atualizados (cabeçalho, rodapé, formatação)</li>
                <li>Sobrescrever templates existentes</li>
            </ul>
            <p class="text-success mt-3"><strong>✅ Conformidade total com padrões jurídicos brasileiros</strong></p>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sim, Aplicar Padrões LC 95/1998',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#17c653',
        showLoaderOnConfirm: true,
        preConfirm: () => {
            return fetch('{{ route("templates.regenerar-todos") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).then(response => {
                if (!response.ok) {
                    throw new Error('Erro na regeneração');
                }
                return response;
            }).catch(error => {
                Swal.showValidationMessage(`Erro: ${error.message}`);
            });
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Templates Regenerados com Padrões Legais!',
                html: `
                    <div class="text-success mb-3">
                        <i class="ki-duotone ki-shield-tick fs-2x mb-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                        </i>
                        <p><strong>Todos os templates agora seguem:</strong></p>
                        <ul class="text-start">
                            <li>✅ LC 95/1998 - Estrutura obrigatória</li>
                            <li>✅ Numeração unificada por tipo e ano</li>
                            <li>✅ Metadados Dublin Core</li>
                            <li>✅ Formatação padronizada</li>
                            <li>✅ Acessibilidade WCAG 2.1</li>
                        </ul>
                    </div>
                `,
                icon: 'success'
            }).then(() => {
                location.reload();
            });
        }
    });
}

// Função para gerar template com padrões legais LC 95/1998
function gerarComPadroesLegais(tipoId, tipoNome) {
    Swal.fire({
        title: 'Gerar Template LC 95/1998',
        html: `
            <div class="text-center mb-4">
                <i class="ki-duotone ki-law fs-3x text-success mb-3">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
                <p>Gerar template estruturado para:</p>
                <p class="fw-bold fs-5">${tipoNome}</p>
            </div>
            <div class="alert alert-info">
                <h6>O template incluirá:</h6>
                <ul class="text-start mb-0">
                    <li>Epígrafe formatada (TIPO Nº 000/AAAA)</li>
                    <li>Ementa conforme padrões</li>
                    <li>Preâmbulo legal</li>
                    <li>Corpo articulado (Art. 1º, 2º...)</li>
                    <li>Cláusula de vigência</li>
                    <li>Variáveis dinâmicas</li>
                </ul>
            </div>
        `,
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: 'Gerar Template Estruturado',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#17c653',
        showLoaderOnConfirm: true,
        preConfirm: () => {
            return fetch(\`/admin/templates/\${tipoId}/gerar-padroes-legais\`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).then(response => {
                if (!response.ok) {
                    throw new Error('Erro na geração do template');
                }
                return response.json();
            }).catch(error => {
                Swal.showValidationMessage(\`Erro: \${error.message}\`);
            });
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.isConfirmed && result.value.success) {
            const estrutura = result.value.estrutura;
            Swal.fire({
                title: 'Template LC 95/1998 Gerado!',
                html: \`
                    <div class="text-success mb-4">
                        <i class="ki-duotone ki-shield-tick fs-2x mb-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                        </i>
                    </div>
                    <div class="text-start">
                        <p class="fw-bold">Estrutura gerada:</p>
                        <ul>
                            <li><strong>Epígrafe:</strong> \${estrutura.epigrafe}</li>
                            <li><strong>Ementa:</strong> \${estrutura.ementa}</li>
                            <li><strong>Artigos:</strong> \${estrutura.artigos} estruturados</li>
                            <li><strong>Validação:</strong> \${estrutura.validacoes}</li>
                        </ul>
                        <div class="alert alert-success mt-3">
                            ✅ <strong>Conforme LC 95/1998 e padrões técnicos</strong>
                        </div>
                    </div>
                \`,
                icon: 'success',
                showCancelButton: true,
                confirmButtonText: 'Editar Template',
                cancelButtonText: 'Fechar',
                confirmButtonColor: '#007bff'
            }).then((editResult) => {
                if (editResult.isConfirmed) {
                    window.location.href = \`/admin/templates/\${tipoId}/editor\`;
                } else {
                    location.reload();
                }
            });
        } else if (result.isConfirmed) {
            Swal.fire({
                title: 'Erro na Geração',
                text: result.value.message || 'Erro desconhecido',
                icon: 'error'
            });
        }
    });
}

// Função para validar template conforme padrões legais
function validarTemplate(tipoId, tipoNome) {
    Swal.fire({
        title: 'Validando Template...',
        text: 'Verificando conformidade com padrões legais',
        icon: 'info',
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    fetch(\`/admin/templates/\${tipoId}/validar\`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const validacao = data.validacao;
                const detalhes = data.detalhes;
                
                const statusColor = validacao.status === 'aprovado' ? 'success' : 
                                   validacao.status === 'rejeitado' ? 'danger' : 'warning';
                                   
                const statusIcon = validacao.status === 'aprovado' ? 'shield-tick' : 
                                  validacao.status === 'rejeitado' ? 'shield-cross' : 'shield';

                Swal.fire({
                    title: 'Relatório de Validação',
                    html: \`
                        <div class="text-center mb-4">
                            <i class="ki-duotone ki-\${statusIcon} fs-3x text-\${statusColor} mb-3">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                \${statusIcon === 'shield-tick' ? '<span class="path3"></span>' : ''}
                            </i>
                            <h4 class="text-\${statusColor}">\${tipoNome}</h4>
                        </div>
                        
                        <div class="row text-start">
                            <div class="col-md-6">
                                <div class="card card-bordered">
                                    <div class="card-body p-4">
                                        <h6 class="fw-bold mb-3">Conformidade Geral</h6>
                                        <div class="mb-2">
                                            <span class="badge badge-\${statusColor} fs-7">\${validacao.status.toUpperCase()}</span>
                                        </div>
                                        <div class="mb-2">
                                            <strong>Qualidade:</strong> \${validacao.qualidade_percentual}%
                                        </div>
                                        <div class="row">
                                            <div class="col-4 text-center">
                                                <div class="fs-6 fw-bold text-success">\${validacao.total_aprovado}</div>
                                                <div class="fs-7 text-muted">Aprovado</div>
                                            </div>
                                            <div class="col-4 text-center">
                                                <div class="fs-6 fw-bold text-warning">\${validacao.total_avisos}</div>
                                                <div class="fs-7 text-muted">Avisos</div>
                                            </div>
                                            <div class="col-4 text-center">
                                                <div class="fs-6 fw-bold text-danger">\${validacao.total_erros}</div>
                                                <div class="fs-7 text-muted">Erros</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card card-bordered">
                                    <div class="card-body p-4">
                                        <h6 class="fw-bold mb-3">Conformidades</h6>
                                        <div class="mb-2">
                                            \${detalhes.lc95_conforme ? '✅' : '❌'} LC 95/1998
                                        </div>
                                        <div class="mb-2">
                                            \${detalhes.estrutura_adequada ? '✅' : '❌'} Estrutura Textual
                                        </div>
                                        <div class="mb-2">
                                            \${validacao.metadados_completos ? '✅' : '❌'} Metadados
                                        </div>
                                        <div class="mb-2">
                                            \${validacao.numeracao_conforme ? '✅' : '❌'} Numeração
                                        </div>
                                        <div class="mb-2">
                                            \${validacao.acessivel ? '✅' : '❌'} Acessibilidade
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        \${validacao.status !== 'aprovado' ? \`
                            <div class="alert alert-warning mt-4">
                                <h6>Recomendações:</h6>
                                <ul class="mb-0">
                                    \${validacao.recomendacoes.map(rec => \`<li>\${rec}</li>\`).join('')}
                                </ul>
                            </div>
                        \` : \`
                            <div class="alert alert-success mt-4">
                                ✅ <strong>Template está em total conformidade com os padrões legais!</strong>
                            </div>
                        \`}
                    \`,
                    icon: validacao.status === 'aprovado' ? 'success' : 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Corrigir Template',
                    cancelButtonText: 'Fechar',
                    confirmButtonColor: '#007bff'
                }).then((result) => {
                    if (result.isConfirmed) {
                        gerarComPadroesLegais(tipoId, tipoNome);
                    }
                });
            } else {
                Swal.fire({
                    title: 'Erro na Validação',
                    text: data.message,
                    icon: 'error'
                });
            }
        })
        .catch(error => {
            Swal.fire({
                title: 'Erro na Validação',
                text: 'Erro ao conectar com o servidor: ' + error.message,
                icon: 'error'
            });
        });
}
</script>
@endpush