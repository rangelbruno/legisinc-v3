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
            <div class="card card-flush bgi-no-repeat bgi-size-contain bgi-position-x-end h-md-50 mb-5 mb-xl-10" style="background-color: #F1416C;background-image:url('{{ asset('assets/media/patterns/vector-1.png') }}')">
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
            <div class="card card-flush bgi-no-repeat bgi-size-contain bgi-position-x-end h-md-50 mb-5 mb-xl-10" style="background-color: #7239EA;background-image:url('{{ asset('assets/media/patterns/vector-1.png') }}')">
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
            <div class="card card-flush bgi-no-repeat bgi-size-contain bgi-position-x-end h-md-50 mb-5 mb-xl-10" style="background-color: #F1BC00;background-image:url('{{ asset('assets/media/patterns/vector-1.png') }}')">
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
            <div class="card card-flush bgi-no-repeat bgi-size-contain bgi-position-x-end h-md-50 mb-5 mb-xl-10" style="background-color: #17C653;background-image:url('{{ asset('assets/media/patterns/vector-1.png') }}')">
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
                                    <a href="{{ route('templates.editor', $tipo) }}" 
                                       class="btn btn-sm btn-primary"
                                       target="_blank">
                                        @if($tipo->hasTemplate())
                                            <i class="ki-duotone ki-pencil fs-6 me-1">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            Editar Template
                                        @else
                                            <i class="ki-duotone ki-plus fs-6 me-1">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            Criar Template
                                        @endif
                                    </a>
                                    
                                    @if($tipo->hasTemplate())
                                        <a href="{{ route('api.templates.download', $tipo->template) }}?v={{ $tipo->template->updated_at->timestamp }}" 
                                           class="btn btn-sm btn-light-primary ms-2">
                                            <i class="ki-duotone ki-exit-down fs-6 me-1">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            Download
                                        </a>
                                        
                                        <button type="button" 
                                                class="btn btn-sm btn-light-danger ms-2"
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
</script>
@endpush