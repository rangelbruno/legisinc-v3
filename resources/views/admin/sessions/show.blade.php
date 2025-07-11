@extends('components.layouts.app')

@section('title', "Sessão #{$session['numero']}/{$session['ano']}")

@section('content')
<!--begin::Content wrapper-->
<div class="d-flex flex-column flex-column-fluid">
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <!--begin::Page title-->
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    {{ $session['numero'] }}ª Sessão {{ $session['tipo_descricao'] }} de {{ $session['ano'] }}
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Home</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('admin.sessions.index') }}" class="text-muted text-hover-primary">Sessões</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Detalhes</li>
                </ul>
            </div>
            <!--end::Page title-->
            <!--begin::Actions-->
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <a href="{{ route('admin.sessions.edit', $session['id']) }}" class="btn btn-sm fw-bold btn-primary">
                    <i class="ki-duotone ki-pencil fs-2"></i>
                    Editar Sessão
                </a>
            </div>
            <!--end::Actions-->
        </div>
    </div>
    <!--end::Toolbar-->
    
    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">
            
            @if(session('success'))
                <div class="alert alert-success">
                    <i class="ki-duotone ki-check-circle fs-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">
                    <i class="ki-duotone ki-cross-circle fs-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    {{ session('error') }}
                </div>
            @endif
            
            <!--begin::Layout-->
            <div class="d-flex flex-column flex-lg-row">
                <!--begin::Sidebar-->
                <div class="flex-column flex-lg-row-auto w-lg-250px w-xl-350px mb-10">
                    <!--begin::Card-->
                    <div class="card mb-5 mb-xl-8">
                        <!--begin::Card body-->
                        <div class="card-body">
                            <!--begin::Summary-->
                            <div class="d-flex flex-center flex-column py-5">
                                <!--begin::Avatar-->
                                <div class="symbol symbol-100px symbol-circle mb-7">
                                    <i class="ki-duotone ki-calendar-8 fs-1 text-primary">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                        <span class="path4"></span>
                                        <span class="path5"></span>
                                        <span class="path6"></span>
                                    </i>
                                </div>
                                <!--end::Avatar-->
                                
                                <!--begin::Name-->
                                <a href="#" class="fs-3 text-gray-800 text-hover-primary fw-bold mb-3">
                                    {{ $session['numero'] }}ª Sessão {{ $session['tipo_descricao'] }}
                                </a>
                                <!--end::Name-->
                                
                                <!--begin::Position-->
                                <div class="mb-9">
                                    <!--begin::Badge-->
                                    @if($session['status'] == 'preparacao')
                                        <div class="badge badge-lg badge-light-warning d-inline">Preparação</div>
                                    @elseif($session['status'] == 'agendada')
                                        <div class="badge badge-lg badge-light-primary d-inline">Agendada</div>
                                    @elseif($session['status'] == 'exportada')
                                        <div class="badge badge-lg badge-light-success d-inline">Exportada</div>
                                    @elseif($session['status'] == 'concluida')
                                        <div class="badge badge-lg badge-light-dark d-inline">Concluída</div>
                                    @endif
                                    <!--end::Badge-->
                                </div>
                                <!--end::Position-->
                            </div>
                            <!--end::Summary-->
                            
                            <!--begin::Details toggle-->
                            <div class="d-flex flex-stack fs-4 py-3">
                                <div class="fw-bold rotate collapsible" data-bs-toggle="collapse" href="#kt_user_view_details" role="button" aria-expanded="false" aria-controls="kt_user_view_details">
                                    Detalhes
                                    <span class="ms-2 rotate-180">
                                        <i class="ki-duotone ki-down fs-3"></i>
                                    </span>
                                </div>
                                <span data-bs-toggle="tooltip" data-bs-trigger="hover" title="Editar detalhes da sessão">
                                    <a href="{{ route('admin.sessions.edit', $session['id']) }}" class="btn btn-sm btn-light-primary">
                                        <i class="ki-duotone ki-pencil fs-2"></i>
                                    </a>
                                </span>
                            </div>
                            <!--end::Details toggle-->
                            
                            <div class="separator"></div>
                            
                            <!--begin::Details content-->
                            <div id="kt_user_view_details" class="collapse show">
                                <div class="pb-5 fs-6">
                                    <!--begin::Details item-->
                                    <div class="fw-bold mt-5">Data</div>
                                    <div class="text-gray-600">{{ \Carbon\Carbon::parse($session['data'])->format('d/m/Y') }}</div>
                                    <!--begin::Details item-->
                                    
                                    <!--begin::Details item-->
                                    <div class="fw-bold mt-5">Horário</div>
                                    <div class="text-gray-600">
                                        {{ $session['hora'] }}
                                        @if($session['hora_inicial'] && $session['hora_final'])
                                            <br><small class="text-muted">({{ $session['hora_inicial'] }} - {{ $session['hora_final'] }})</small>
                                        @endif
                                    </div>
                                    <!--begin::Details item-->
                                    
                                    <!--begin::Details item-->
                                    <div class="fw-bold mt-5">Tipo</div>
                                    <div class="text-gray-600">{{ $session['tipo_descricao'] }}</div>
                                    <!--begin::Details item-->
                                    
                                    <!--begin::Details item-->
                                    <div class="fw-bold mt-5">Total de Matérias</div>
                                    <div class="text-gray-600">{{ $session['total_materias'] ?? 0 }} matérias</div>
                                    <!--begin::Details item-->
                                    
                                    @if($session['observacoes'])
                                        <!--begin::Details item-->
                                        <div class="fw-bold mt-5">Observações</div>
                                        <div class="text-gray-600">{{ $session['observacoes'] }}</div>
                                        <!--begin::Details item-->
                                    @endif
                                </div>
                            </div>
                            <!--end::Details content-->
                        </div>
                        <!--end::Card body-->
                    </div>
                    <!--end::Card-->
                    
                    <!--begin::Export Actions-->
                    @if(count($matters) > 0)
                        <div class="card">
                            <div class="card-header">
                                <div class="card-title">
                                    <h3 class="fw-bold">Exportações</h3>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="d-flex flex-column gap-3">
                                    <button class="btn btn-light-primary" onclick="generateXML('expediente')">
                                        <i class="ki-duotone ki-document-copy fs-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        Gerar Expediente
                                    </button>
                                    <button class="btn btn-light-info" onclick="generateXML('ordem_do_dia')">
                                        <i class="ki-duotone ki-file-down fs-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        Gerar Ordem do Dia
                                    </button>
                                </div>
                                
                                @if(count($exports) > 0)
                                    <div class="separator my-5"></div>
                                    <div class="fs-6 fw-bold mb-3">Histórico de Exportações</div>
                                    @foreach($exports as $export)
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="symbol symbol-30px me-3">
                                                <i class="ki-duotone ki-check-circle fs-1 text-success">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="fw-bold">{{ ucfirst(str_replace('_', ' ', $export['document_type'])) }}</div>
                                                <div class="text-muted fs-7">{{ \Carbon\Carbon::parse($export['exported_at'])->format('d/m/Y H:i') }}</div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    @endif
                    <!--end::Export Actions-->
                </div>
                <!--end::Sidebar-->
                
                <!--begin::Content-->
                <div class="flex-lg-row-fluid ms-lg-15">
                    <!--begin::Tab content-->
                    <div class="tab-content" id="myTabContent">
                        <!--begin::Tab pane-->
                        <div class="tab-pane fade show active" id="kt_user_view_overview_tab" role="tabpanel">
                            <!--begin::Card-->
                            <div class="card pt-4 mb-6 mb-xl-9">
                                <!--begin::Card header-->
                                <div class="card-header border-0">
                                    <!--begin::Card title-->
                                    <div class="card-title">
                                        <h2>Matérias da Sessão</h2>
                                    </div>
                                    <!--end::Card title-->
                                    
                                    <!--begin::Card toolbar-->
                                    <div class="card-toolbar">
                                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_add_matter">
                                            <i class="ki-duotone ki-plus fs-2"></i>
                                            Adicionar Matéria
                                        </button>
                                    </div>
                                    <!--end::Card toolbar-->
                                </div>
                                <!--end::Card header-->
                                
                                <!--begin::Card body-->
                                <div class="card-body pt-0 pb-5">
                                    <!--begin::Table-->
                                    <div class="table-responsive">
                                        <table class="table align-middle table-row-dashed gy-5">
                                            <thead class="border-bottom border-gray-200 fs-7 fw-bold">
                                                <tr class="text-start text-muted text-uppercase gs-0">
                                                    <th class="min-w-250px">Matéria</th>
                                                    <th class="min-w-150px">Tipo</th>
                                                    <th class="min-w-150px">Fase</th>
                                                    <th class="min-w-150px">Autor</th>
                                                    <th class="text-end min-w-70px">Ações</th>
                                                </tr>
                                            </thead>
                                            <tbody class="fs-6 fw-semibold text-gray-600">
                                                @forelse($matters as $matter)
                                                    <tr>
                                                        <td>
                                                            <div class="d-flex flex-column">
                                                                <span class="text-dark fw-bold">{{ $matter['tipo_descricao'] }} {{ $matter['numero'] }}/{{ $matter['ano'] }}</span>
                                                                <span class="text-muted">{{ $matter['descricao'] }}</span>
                                                                <small class="text-muted">Assunto: {{ $matter['assunto'] }}</small>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <span class="badge badge-light-info">{{ $matter['tipo_descricao'] }}</span>
                                                        </td>
                                                        <td>
                                                            <span class="badge badge-light-primary">{{ $matter['fase_descricao'] }}</span>
                                                        </td>
                                                        <td>{{ $matter['autor_nome'] }}</td>
                                                        <td class="text-end">
                                                            <a href="#" class="btn btn-sm btn-light btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                                                Ações
                                                                <i class="ki-duotone ki-down fs-5 m-0"></i>
                                                            </a>
                                                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
                                                                <div class="menu-item px-3">
                                                                    <a href="#" class="menu-link px-3" onclick="editMatter({{ json_encode($matter) }})">
                                                                        Editar
                                                                    </a>
                                                                </div>
                                                                <div class="menu-item px-3">
                                                                    <a href="#" class="menu-link px-3 text-danger" onclick="removeMatter({{ $matter['id'] }})">
                                                                        Remover
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="5" class="text-center py-10">
                                                            <div class="d-flex flex-column flex-center">
                                                                <img src="{{ asset('assets/media/illustrations/sketchy-1/5.png') }}" class="mw-250px">
                                                                <div class="fs-1 fw-bolder text-dark mb-4">Nenhuma matéria adicionada.</div>
                                                                <div class="fs-6">Adicione matérias para poder gerar os documentos da sessão.</div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                    <!--end::Table-->
                                </div>
                                <!--end::Card body-->
                            </div>
                            <!--end::Card-->
                        </div>
                        <!--end::Tab pane-->
                    </div>
                    <!--end::Tab content-->
                </div>
                <!--end::Content-->
            </div>
            <!--end::Layout-->
        </div>
    </div>
    <!--end::Content-->
</div>
<!--end::Content wrapper-->

<!--begin::Modal - Add Matter-->
<div class="modal fade" id="kt_modal_add_matter" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <form class="form" action="#" id="kt_modal_add_matter_form">
                <div class="modal-header">
                    <h2 class="fw-bold" id="modal_title">Adicionar Matéria</h2>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-kt-matter-modal-action="close">
                        <i class="ki-duotone ki-cross fs-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </div>
                </div>
                
                <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                    <div class="row mb-6">
                        <div class="col-md-6">
                            <div class="fv-row mb-7">
                                <label class="required fw-semibold fs-6 mb-2">Tipo de Matéria</label>
                                <select name="tipo_id" class="form-select form-select-solid" required>
                                    <option value="">Selecione o tipo</option>
                                    @foreach($tipos_materia as $id => $nome)
                                        <option value="{{ $id }}">{{ $nome }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="fv-row mb-7">
                                <label class="required fw-semibold fs-6 mb-2">Fase de Tramitação</label>
                                <select name="fase_id" class="form-select form-select-solid" required>
                                    <option value="">Selecione a fase</option>
                                    @foreach($fases_tramitacao as $id => $nome)
                                        <option value="{{ $id }}">{{ $nome }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-6">
                        <div class="col-md-6">
                            <div class="fv-row mb-7">
                                <label class="required fw-semibold fs-6 mb-2">Número</label>
                                <input type="text" name="numero" class="form-control form-control-solid" placeholder="Ex: 001" required />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="fv-row mb-7">
                                <label class="required fw-semibold fs-6 mb-2">Ano</label>
                                <select name="ano" class="form-select form-select-solid" required>
                                    @for($year = date('Y'); $year >= date('Y') - 5; $year--)
                                        <option value="{{ $year }}" {{ $year == date('Y') ? 'selected' : '' }}>{{ $year }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="fv-row mb-7">
                        <label class="required fw-semibold fs-6 mb-2">Descrição</label>
                        <input type="text" name="descricao" class="form-control form-control-solid" placeholder="Descrição da matéria" required />
                    </div>
                    
                    <div class="fv-row mb-7">
                        <label class="required fw-semibold fs-6 mb-2">Assunto</label>
                        <input type="text" name="assunto" class="form-control form-control-solid" placeholder="Assunto principal" required />
                    </div>
                    
                    <div class="fv-row mb-7">
                        <label class="required fw-semibold fs-6 mb-2">Autor</label>
                        <select name="autor_id" class="form-select form-select-solid" required>
                            <option value="">Selecione o autor</option>
                            @foreach($parlamentares as $parlamentar)
                                <option value="{{ $parlamentar['id'] }}">{{ $parlamentar['nome'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="row mb-6">
                        <div class="col-md-6">
                            <div class="fv-row mb-7">
                                <label class="fw-semibold fs-6 mb-2">Regime de Tramitação</label>
                                <select name="regime_id" class="form-select form-select-solid">
                                    <option value="">Selecione (opcional)</option>
                                    @foreach($regimes_tramitacao as $id => $nome)
                                        <option value="{{ $id }}">{{ $nome }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="fv-row mb-7">
                                <label class="fw-semibold fs-6 mb-2">Quorum</label>
                                <select name="quorum_id" class="form-select form-select-solid">
                                    <option value="">Selecione (opcional)</option>
                                    @foreach($tipos_quorum as $id => $nome)
                                        <option value="{{ $id }}">{{ $nome }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-kt-matter-modal-action="cancel">Cancelar</button>
                    <button type="submit" class="btn btn-primary" data-kt-matter-modal-action="submit">
                        <span class="indicator-label">Salvar</span>
                        <span class="indicator-progress">Aguarde...
                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!--end::Modal - Add Matter-->

@push('scripts')
<script>
const sessionId = {{ $session['id'] }};
let editingMatterId = null;

// Add/Edit Matter Modal
document.addEventListener('DOMContentLoaded', function() {
    const modal = new bootstrap.Modal(document.getElementById('kt_modal_add_matter'));
    const form = document.getElementById('kt_modal_add_matter_form');
    const submitButton = form.querySelector('[data-kt-matter-modal-action="submit"]');
    const cancelButton = form.querySelector('[data-kt-matter-modal-action="cancel"]');
    const closeButton = form.querySelector('[data-kt-matter-modal-action="close"]');
    
    // Close modal handlers
    cancelButton.addEventListener('click', () => modal.hide());
    closeButton.addEventListener('click', () => modal.hide());
    
    // Form submit
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());
        
        // Remove empty values
        Object.keys(data).forEach(key => {
            if (data[key] === '') {
                delete data[key];
            }
        });
        
        submitButton.disabled = true;
        submitButton.querySelector('.indicator-label').style.display = 'none';
        submitButton.querySelector('.indicator-progress').style.display = 'inline-block';
        
        const url = editingMatterId 
            ? `{{ route('admin.sessions.update-matter', ['sessionId' => $session['id'], 'matterId' => ':matterId']) }}`.replace(':matterId', editingMatterId)
            : `{{ route('admin.sessions.add-matter', $session['id']) }}`;
        
        const method = editingMatterId ? 'PUT' : 'POST';
        
        fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                modal.hide();
                location.reload();
            } else {
                alert('Erro: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Erro ao salvar matéria');
        })
        .finally(() => {
            submitButton.disabled = false;
            submitButton.querySelector('.indicator-label').style.display = 'inline-block';
            submitButton.querySelector('.indicator-progress').style.display = 'none';
        });
    });
    
    // Reset modal when hidden
    document.getElementById('kt_modal_add_matter').addEventListener('hidden.bs.modal', function() {
        form.reset();
        editingMatterId = null;
        document.getElementById('modal_title').textContent = 'Adicionar Matéria';
    });
});

function editMatter(matter) {
    editingMatterId = matter.id;
    document.getElementById('modal_title').textContent = 'Editar Matéria';
    
    const form = document.getElementById('kt_modal_add_matter_form');
    
    // Fill form with matter data
    Object.keys(matter).forEach(key => {
        const input = form.querySelector(`[name="${key}"]`);
        if (input) {
            input.value = matter[key] || '';
        }
    });
    
    const modal = new bootstrap.Modal(document.getElementById('kt_modal_add_matter'));
    modal.show();
}

function removeMatter(matterId) {
    if (!confirm('Tem certeza que deseja remover esta matéria da sessão?')) {
        return;
    }
    
    fetch(`{{ route('admin.sessions.remove-matter', ['sessionId' => $session['id'], 'matterId' => ':matterId']) }}`.replace(':matterId', matterId), {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Erro: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Erro ao remover matéria');
    });
}

function generateXML(documentType) {
    fetch(`{{ route('admin.sessions.generate-xml', $session['id']) }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            document_type: documentType
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Open preview in new window/tab
            const url = `{{ route('admin.sessions.preview-xml', $session['id']) }}?document_type=${documentType}`;
            window.open(url, '_blank');
        } else {
            alert('Erro: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Erro ao gerar XML');
    });
}
</script>
@endpush
@endsection