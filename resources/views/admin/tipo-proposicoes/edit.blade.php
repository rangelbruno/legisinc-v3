@extends('components.layouts.app')

@section('title', 'Editar Tipo de Proposição')

@section('content')
<!--begin::Toolbar-->
<div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
    <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
        <!--begin::Page title-->
        <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
            <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                Editar Tipo: {{ $tipoProposicao->nome }}
            </h1>
            <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                <li class="breadcrumb-item text-muted">
                    <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Dashboard</a>
                </li>
                <li class="breadcrumb-item">
                    <span class="bullet bg-gray-500 w-5px h-2px"></span>
                </li>
                <li class="breadcrumb-item text-muted">Admin</li>
                <li class="breadcrumb-item">
                    <span class="bullet bg-gray-500 w-5px h-2px"></span>
                </li>
                <li class="breadcrumb-item text-muted">
                    <a href="{{ route('admin.tipo-proposicoes.index') }}" class="text-muted text-hover-primary">Tipos de Proposição</a>
                </li>
                <li class="breadcrumb-item">
                    <span class="bullet bg-gray-500 w-5px h-2px"></span>
                </li>
                <li class="breadcrumb-item text-muted">Editar</li>
            </ul>
        </div>
        <!--end::Page title-->
        <!--begin::Actions-->
        <div class="d-flex align-items-center gap-2 gap-lg-3">
            <a href="{{ route('admin.tipo-proposicoes.index') }}" class="btn btn-sm fw-bold btn-secondary">
                <i class="ki-duotone ki-arrow-left fs-2"></i>
                Voltar
            </a>
        </div>
        <!--end::Actions-->
    </div>
</div>
<!--end::Toolbar-->

<!--begin::Content-->
<div id="kt_app_content" class="app-content flex-column-fluid">
    <div id="kt_app_content_container" class="app-container container-xxl">
        <!--begin::Form-->
        <form id="kt_tipo_form" class="form d-flex flex-column flex-lg-row" method="POST" action="{{ route('admin.tipo-proposicoes.update', $tipoProposicao) }}">
            @csrf
            @method('PUT')
            <!--begin::Aside column-->
            <div class="d-flex flex-column gap-7 gap-lg-10 w-100 w-lg-300px mb-7 me-lg-10">
                <!--begin::Thumbnail settings-->
                <div class="card card-flush py-4">
                    <!--begin::Card header-->
                    <div class="card-header">
                        <!--begin::Card title-->
                        <div class="card-title">
                            <h2>Visualização</h2>
                        </div>
                        <!--end::Card title-->
                    </div>
                    <!--end::Card header-->
                    <!--begin::Card body-->
                    <div class="card-body text-center pt-0">
                        <!--begin::Image input-->
                        <div class="image-input image-input-empty image-input-outline image-input-placeholder mb-3" data-kt-image-input="true">
                            <!--begin::Preview existing avatar-->
                            <div class="image-input-wrapper w-150px h-150px" id="icon-preview">
                                <div class="d-flex align-items-center justify-content-center h-100">
                                    <i class="ki-duotone {{ $tipoProposicao->icone }} fs-4x text-{{ $tipoProposicao->cor }}" id="preview-icon">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                </div>
                            </div>
                            <!--end::Preview existing avatar-->
                        </div>
                        <!--end::Image input-->
                        <!--begin::Description-->
                        <div class="text-muted fs-7">Preview do ícone selecionado</div>
                        <!--end::Description-->
                    </div>
                    <!--end::Card body-->
                </div>
                <!--end::Thumbnail settings-->

                <!--begin::Status-->
                <div class="card card-flush py-4">
                    <!--begin::Card header-->
                    <div class="card-header">
                        <!--begin::Card title-->
                        <div class="card-title">
                            <h2>Status</h2>
                        </div>
                        <!--end::Card title-->
                        <!--begin::Card toolbar-->
                        <div class="card-toolbar">
                            <div class="rounded-circle {{ $tipoProposicao->ativo ? 'bg-success' : 'bg-danger' }} w-15px h-15px" id="kt_tipo_status"></div>
                        </div>
                        <!--end::Card toolbar-->
                    </div>
                    <!--end::Card header-->
                    <!--begin::Card body-->
                    <div class="card-body pt-0">
                        <!--begin::Select2-->
                        <select class="form-select mb-2" name="ativo" data-control="select2" data-hide-search="true" data-placeholder="Selecione um status">
                            <option value="1" {{ $tipoProposicao->ativo ? 'selected' : '' }}>Ativo</option>
                            <option value="0" {{ !$tipoProposicao->ativo ? 'selected' : '' }}>Inativo</option>
                        </select>
                        <!--end::Select2-->
                        <!--begin::Description-->
                        <div class="text-muted fs-7">Define se o tipo estará disponível para uso.</div>
                        <!--end::Description-->
                    </div>
                    <!--end::Card body-->
                </div>
                <!--end::Status-->

                <!--begin::Template-->
                <div class="card card-flush py-4">
                    <!--begin::Card header-->
                    <div class="card-header">
                        <!--begin::Card title-->
                        <div class="card-title">
                            <h2>Template</h2>
                        </div>
                        <!--end::Card title-->
                    </div>
                    <!--end::Card header-->
                    <!--begin::Card body-->
                    <div class="card-body pt-0">
                        <!--begin::Select2-->
                        <select class="form-select mb-2" data-kt-ecommerce-catalog-add-category="template" data-placeholder="Selecione um template" name="template_padrao">
                            <option></option>
                            <option value="template_lei" {{ $tipoProposicao->template_padrao == 'template_lei' ? 'selected' : '' }}>Template de Lei</option>
                            <option value="template_requerimento" {{ $tipoProposicao->template_padrao == 'template_requerimento' ? 'selected' : '' }}>Template de Requerimento</option>
                        </select>
                        <!--end::Select2-->
                        <!--begin::Description-->
                        <div class="text-muted fs-7 mt-2">Template padrão para este tipo de proposição.</div>
                        <!--end::Description-->
                    </div>
                    <!--end::Card body-->
                </div>
                <!--end::Template-->
            </div>
            <!--end::Aside column-->

            <!--begin::Main column-->
            <div class="d-flex flex-column flex-row-fluid gap-7 gap-lg-10">
                <!--begin::General options-->
                <div class="card card-flush py-4">
                    <!--begin::Card header-->
                    <div class="card-header">
                        <div class="card-title">
                            <h2>Informações Gerais</h2>
                        </div>
                    </div>
                    <!--end::Card header-->
                    <!--begin::Card body-->
                    <div class="card-body pt-0">
                        <!--begin::Input group-->
                        <div class="mb-10 fv-row">
                            <!--begin::Label-->
                            <label class="required form-label">Nome do Tipo</label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <input type="text" name="nome" class="form-control mb-2" placeholder="Ex: Projeto de Lei Ordinária" value="{{ old('nome', $tipoProposicao->nome) }}" />
                            <!--end::Input-->
                            <!--begin::Description-->
                            <div class="text-muted fs-7">Nome completo que será exibido para os usuários.</div>
                            <!--end::Description-->
                            @error('nome')
                                <div class="text-danger fs-7 mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <!--end::Input group-->

                        <!--begin::Input group-->
                        <div class="mb-10 fv-row">
                            <!--begin::Label-->
                            <label class="required form-label">Código</label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <input type="text" name="codigo" class="form-control mb-2" placeholder="Ex: projeto_lei_ordinaria" value="{{ old('codigo', $tipoProposicao->codigo) }}" />
                            <!--end::Input-->
                            <!--begin::Description-->
                            <div class="text-muted fs-7">Código único (apenas letras minúsculas, números e underscores).</div>
                            <!--end::Description-->
                            @error('codigo')
                                <div class="text-danger fs-7 mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <!--end::Input group-->

                        <!--begin::Input group-->
                        <div class="mb-10 fv-row">
                            <!--begin::Label-->
                            <label class="form-label">Descrição</label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <textarea name="descricao" class="form-control mb-2" rows="3" placeholder="Descrição detalhada do tipo de proposição...">{{ old('descricao', $tipoProposicao->descricao) }}</textarea>
                            <!--end::Input-->
                            <!--begin::Description-->
                            <div class="text-muted fs-7">Descrição opcional que será exibida como ajuda.</div>
                            <!--end::Description-->
                            @error('descricao')
                                <div class="text-danger fs-7 mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <!--end::Input group-->

                        <!--begin::Input group-->
                        <div class="row mb-10">
                            <!--begin::Col-->
                            <div class="col-md-6 fv-row">
                                <!--begin::Label-->
                                <label class="required form-label">Ícone</label>
                                <!--end::Label-->
                                <!--begin::Select2-->
                                <select class="form-select mb-2" name="icone" data-control="select2" data-placeholder="Selecione um ícone" id="icone-select">
                                    <option></option>
                                    @foreach($iconesDisponiveis as $icone => $nome)
                                        <option value="{{ $icone }}" {{ old('icone', $tipoProposicao->icone) == $icone ? 'selected' : '' }}>{{ $nome }}</option>
                                    @endforeach
                                </select>
                                <!--end::Select2-->
                                @error('icone')
                                    <div class="text-danger fs-7 mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <!--end::Col-->
                            <!--begin::Col-->
                            <div class="col-md-6 fv-row">
                                <!--begin::Label-->
                                <label class="required form-label">Cor</label>
                                <!--end::Label-->
                                <!--begin::Select2-->
                                <select class="form-select mb-2" name="cor" data-control="select2" data-placeholder="Selecione uma cor" id="cor-select">
                                    <option></option>
                                    @foreach($coresDisponiveis as $cor => $nome)
                                        <option value="{{ $cor }}" {{ old('cor', $tipoProposicao->cor) == $cor ? 'selected' : '' }}>{{ $nome }}</option>
                                    @endforeach
                                </select>
                                <!--end::Select2-->
                                @error('cor')
                                    <div class="text-danger fs-7 mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <!--end::Col-->
                        </div>
                        <!--end::Input group-->

                        <!--begin::Input group-->
                        <div class="mb-10 fv-row">
                            <!--begin::Label-->
                            <label class="required form-label">Ordem</label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <input type="number" name="ordem" class="form-control mb-2" placeholder="Ordem de exibição" value="{{ old('ordem', $tipoProposicao->ordem) }}" min="0" />
                            <!--end::Input-->
                            <!--begin::Description-->
                            <div class="text-muted fs-7">Ordem de exibição na lista (menor número aparece primeiro).</div>
                            <!--end::Description-->
                            @error('ordem')
                                <div class="text-danger fs-7 mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <!--end::Input group-->
                    </div>
                    <!--end::Card body-->
                </div>
                <!--end::General options-->

                <!--begin::Advanced options-->
                <div class="card card-flush py-4">
                    <!--begin::Card header-->
                    <div class="card-header">
                        <div class="card-title">
                            <h2>Configurações Avançadas</h2>
                        </div>
                    </div>
                    <!--end::Card header-->
                    <!--begin::Card body-->
                    <div class="card-body pt-0">
                        <!--begin::Input group-->
                        <div class="mb-10 fv-row">
                            <!--begin::Label-->
                            <label class="form-label">Configurações (JSON)</label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <textarea name="configuracoes" class="form-control mb-2" rows="5" placeholder='{"exemplo": "valor"}'>{{ old('configuracoes', $tipoProposicao->configuracoes ? json_encode($tipoProposicao->configuracoes, JSON_PRETTY_PRINT) : '') }}</textarea>
                            <!--end::Input-->
                            <!--begin::Description-->
                            <div class="text-muted fs-7">Configurações específicas em formato JSON (opcional).</div>
                            <!--end::Description-->
                            @error('configuracoes')
                                <div class="text-danger fs-7 mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <!--end::Input group-->
                    </div>
                    <!--end::Card body-->
                </div>
                <!--end::Advanced options-->

                <div class="d-flex justify-content-end">
                    <!--begin::Button-->
                    <a href="{{ route('admin.tipo-proposicoes.index') }}" id="kt_tipo_cancel" class="btn btn-light me-5">Cancelar</a>
                    <!--end::Button-->
                    <!--begin::Button-->
                    <button type="submit" id="kt_tipo_submit" class="btn btn-primary">
                        <span class="indicator-label">Atualizar</span>
                        <span class="indicator-progress">Aguarde...
                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                    </button>
                    <!--end::Button-->
                </div>
            </div>
            <!--end::Main column-->
        </form>
        <!--end::Form-->
    </div>
</div>
<!--end::Content-->

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Update icon preview
    const iconeSelect = document.getElementById('icone-select');
    const corSelect = document.getElementById('cor-select');
    const previewIcon = document.getElementById('preview-icon');
    
    function updatePreview() {
        const selectedIcon = iconeSelect.value || 'ki-document';
        const selectedCor = corSelect.value || 'primary';
        
        previewIcon.className = `ki-duotone ${selectedIcon} fs-4x text-${selectedCor}`;
    }
    
    iconeSelect.addEventListener('change', updatePreview);
    corSelect.addEventListener('change', updatePreview);

    // Form validation
    const form = document.getElementById('kt_tipo_form');
    const submitButton = document.getElementById('kt_tipo_submit');
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Show loading state
        submitButton.setAttribute('data-kt-indicator', 'on');
        submitButton.disabled = true;
        
        // Validate required fields
        const nome = document.querySelector('input[name="nome"]').value.trim();
        const codigo = document.querySelector('input[name="codigo"]').value.trim();
        const icone = document.querySelector('select[name="icone"]').value;
        const cor = document.querySelector('select[name="cor"]').value;
        
        if (!nome || !codigo || !icone || !cor) {
            // Hide loading state
            submitButton.removeAttribute('data-kt-indicator');
            submitButton.disabled = false;
            
            Swal.fire({
                text: "Por favor, preencha todos os campos obrigatórios.",
                icon: "warning",
                buttonsStyling: false,
                confirmButtonText: "Ok",
                customClass: {
                    confirmButton: "btn btn-primary"
                }
            });
            return;
        }
        
        // Validate JSON if provided
        const configuracoes = document.querySelector('textarea[name="configuracoes"]').value.trim();
        if (configuracoes) {
            try {
                JSON.parse(configuracoes);
            } catch (e) {
                // Hide loading state
                submitButton.removeAttribute('data-kt-indicator');
                submitButton.disabled = false;
                
                Swal.fire({
                    text: "O campo configurações deve conter um JSON válido.",
                    icon: "error",
                    buttonsStyling: false,
                    confirmButtonText: "Ok",
                    customClass: {
                        confirmButton: "btn btn-primary"
                    }
                });
                return;
            }
        }
        
        // Submit form
        form.submit();
    });
});
</script>
@endpush
@endsection