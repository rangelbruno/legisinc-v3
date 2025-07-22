@extends('components.layouts.app')

@section('title', 'Editar Modelo')

@section('content')
<!--begin::Content wrapper-->
<div class="d-flex flex-column flex-column-fluid">
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                    Editar Modelo: {{ $modelo->nome }}
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('documentos.modelos.index') }}" class="text-muted text-hover-primary">Modelos</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Editar</li>
                </ul>
            </div>
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <a href="{{ route('documentos.modelos.show', $modelo) }}" class="btn btn-sm fw-bold btn-secondary">
                    <i class="ki-duotone ki-arrow-left fs-2"></i>
                    Voltar
                </a>
            </div>
        </div>
    </div>
    <!--end::Toolbar-->

    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">
            
            @if($errors->any())
                <div class="alert alert-danger d-flex align-items-center p-5 mb-10">
                    <i class="ki-duotone ki-shield-cross fs-2hx text-danger me-4">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    <div class="d-flex flex-column">
                        <h4 class="mb-1 text-danger">Erro ao salvar!</h4>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <!--begin::Form-->
            <form id="kt_modelo_edit_form" class="form" action="{{ route('documentos.modelos.update', $modelo) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <!--begin::Card-->
                <div class="card">
                    <!--begin::Card header-->
                    <div class="card-header">
                        <div class="card-title">
                            <h2>Informações do Modelo</h2>
                        </div>
                        <div class="card-toolbar">
                            @if($modelo->ativo)
                                <span class="badge badge-success">Ativo</span>
                            @else
                                <span class="badge badge-secondary">Inativo</span>
                            @endif
                        </div>
                    </div>
                    <!--end::Card header-->

                    <!--begin::Card body-->
                    <div class="card-body pt-9 pb-0">
                        <!--begin::Row-->
                        <div class="row mb-7">
                            <!--begin::Label-->
                            <label class="col-lg-4 fw-semibold text-muted">Nome do Modelo</label>
                            <!--end::Label-->
                            <!--begin::Col-->
                            <div class="col-lg-8">
                                <input type="text" name="nome" class="form-control form-control-lg form-control-solid" 
                                       placeholder="Ex: Modelo de Projeto de Lei Ordinária" 
                                       value="{{ old('nome', $modelo->nome) }}" required />
                                <div class="form-text">Digite um nome descritivo para o modelo</div>
                            </div>
                            <!--end::Col-->
                        </div>
                        <!--end::Row-->

                        <!--begin::Row-->
                        <div class="row mb-7">
                            <!--begin::Label-->
                            <label class="col-lg-4 fw-semibold text-muted">Descrição</label>
                            <!--end::Label-->
                            <!--begin::Col-->
                            <div class="col-lg-8">
                                <textarea name="descricao" class="form-control form-control-lg form-control-solid" 
                                          rows="3" placeholder="Descrição opcional do modelo">{{ old('descricao', $modelo->descricao) }}</textarea>
                                <div class="form-text">Descreva o propósito e uso do modelo</div>
                            </div>
                            <!--end::Col-->
                        </div>
                        <!--end::Row-->

                        <!--begin::Row-->
                        <div class="row mb-7">
                            <!--begin::Label-->
                            <label class="col-lg-4 fw-semibold text-muted">Tipo de Proposição</label>
                            <!--end::Label-->
                            <!--begin::Col-->
                            <div class="col-lg-8">
                                <select name="tipo_proposicao_id" class="form-select form-select-lg form-select-solid" data-control="select2" data-placeholder="Selecione um tipo">
                                    <option value="">Modelo Geral (todos os tipos)</option>
                                    @foreach($tiposProposicao as $tipo)
                                        <option value="{{ $tipo->id }}" {{ (old('tipo_proposicao_id', $modelo->tipo_proposicao_id) == $tipo->id) ? 'selected' : '' }}>
                                            {{ $tipo->nome }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="form-text">Associe o modelo a um tipo específico ou deixe como geral</div>
                            </div>
                            <!--end::Col-->
                        </div>
                        <!--end::Row-->

                        <!--begin::Row-->
                        <div class="row mb-7">
                            <!--begin::Label-->
                            <label class="col-lg-4 fw-semibold text-muted">Status</label>
                            <!--end::Label-->
                            <!--begin::Col-->
                            <div class="col-lg-8">
                                <div class="form-check form-switch form-check-custom form-check-solid">
                                    <input class="form-check-input" type="checkbox" name="ativo" value="1" id="ativo_switch" {{ old('ativo', $modelo->ativo) ? 'checked' : '' }} />
                                    <label class="form-check-label fw-semibold" for="ativo_switch">
                                        Modelo ativo e disponível para uso
                                    </label>
                                </div>
                                <div class="form-text">Desmarque para desativar temporariamente o modelo</div>
                            </div>
                            <!--end::Col-->
                        </div>
                        <!--end::Row-->

                        <!--begin::Current File Info-->
                        @if($modelo->arquivo_path)
                        <div class="row mb-7">
                            <!--begin::Label-->
                            <label class="col-lg-4 fw-semibold text-muted">Arquivo Atual</label>
                            <!--end::Label-->
                            <!--begin::Col-->
                            <div class="col-lg-8">
                                <div class="d-flex align-items-center p-3 bg-light rounded">
                                    <i class="ki-duotone ki-file-added fs-2x text-success me-3">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    <div class="flex-grow-1">
                                        <div class="fw-bold text-gray-800">{{ $modelo->arquivo_nome }}</div>
                                        <div class="text-muted fs-7">{{ number_format($modelo->arquivo_size / 1024, 2) }} KB</div>
                                    </div>
                                    <div>
                                        <a href="{{ route('documentos.modelos.download', $modelo) }}" class="btn btn-sm btn-light-success">
                                            <i class="ki-duotone ki-down fs-4">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            Download
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <!--end::Col-->
                        </div>
                        @endif
                        <!--end::Current File Info-->

                        <!--begin::Row-->
                        <div class="row mb-7">
                            <!--begin::Label-->
                            <label class="col-lg-4 fw-semibold text-muted">{{ $modelo->arquivo_path ? 'Substituir Arquivo' : 'Upload de Arquivo' }}</label>
                            <!--end::Label-->
                            <!--begin::Col-->
                            <div class="col-lg-8">
                                <!--begin::Dropzone-->
                                <div class="dropzone" id="kt_dropzone_modelo">
                                    <div class="dz-message needsclick">
                                        <i class="ki-duotone ki-file-up fs-3x text-primary">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        <div class="ms-4">
                                            <h3 class="fs-5 fw-bold text-gray-900 mb-1">
                                                {{ $modelo->arquivo_path ? 'Arraste um novo arquivo ou clique para substituir' : 'Arraste o arquivo aqui ou clique para selecionar' }}
                                            </h3>
                                            <span class="fs-7 fw-semibold text-gray-500">Apenas arquivos .docx são aceitos (máximo 10MB)</span>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Dropzone-->
                                
                                <!--begin::Input oculto para fallback-->
                                <input type="file" name="arquivo" id="arquivo_fallback" accept=".docx" style="display: none;" />
                                <!--end::Input oculto-->
                                
                                <div class="form-text">
                                    {{ $modelo->arquivo_path ? 'Opcional: Apenas envie um novo arquivo se desejar substituir o atual.' : 'O arquivo deve ser um documento Word (.docx) com variáveis no formato ${nome_variavel}' }}
                                </div>
                            </div>
                            <!--end::Col-->
                        </div>
                        <!--end::Row-->

                        <!--begin::Current Variables-->
                        @if(!empty($variaveisFormatadas))
                        <div class="row mb-7">
                            <!--begin::Label-->
                            <label class="col-lg-4 fw-semibold text-muted">Variáveis Atuais</label>
                            <!--end::Label-->
                            <!--begin::Col-->
                            <div class="col-lg-8">
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($variaveisFormatadas as $variavel)
                                        <span class="badge badge-light-primary fs-7">${{ $variavel['nome'] }}</span>
                                    @endforeach
                                </div>
                            </div>
                            <!--end::Col-->
                        </div>
                        @endif
                        <!--end::Current Variables-->

                        <!--begin::Variáveis detectadas (para novo arquivo)-->
                        <div class="row mb-7" id="variaveis_detectadas" style="display: none;">
                            <!--begin::Label-->
                            <label class="col-lg-4 fw-semibold text-muted">Novas Variáveis Detectadas</label>
                            <!--end::Label-->
                            <!--begin::Col-->
                            <div class="col-lg-8">
                                <div class="alert alert-info d-flex align-items-center p-5">
                                    <i class="ki-duotone ki-information-5 fs-2hx text-info me-4">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                    </i>
                                    <div class="d-flex flex-column">
                                        <h4 class="mb-1 text-info">Variáveis do novo arquivo</h4>
                                        <span>As variáveis encontradas no novo documento aparecerão aqui</span>
                                    </div>
                                </div>
                                <div id="lista_variaveis"></div>
                            </div>
                            <!--end::Col-->
                        </div>
                        <!--end::Variáveis detectadas-->

                        <!--begin::Version Info-->
                        <div class="row mb-7">
                            <!--begin::Label-->
                            <label class="col-lg-4 fw-semibold text-muted">Informações de Versão</label>
                            <!--end::Label-->
                            <!--begin::Col-->
                            <div class="col-lg-8">
                                <div class="table-responsive">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td width="30%"><strong>Versão atual:</strong></td>
                                            <td>{{ $modelo->versao }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Criado por:</strong></td>
                                            <td>{{ $modelo->creator->name ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Data de criação:</strong></td>
                                            <td>{{ $modelo->created_at->format('d/m/Y H:i') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Última atualização:</strong></td>
                                            <td>{{ $modelo->updated_at->format('d/m/Y H:i') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <!--end::Col-->
                        </div>
                        <!--end::Version Info-->
                    </div>
                    <!--end::Card body-->

                    <!--begin::Actions-->
                    <div class="card-footer d-flex justify-content-end py-6 px-9">
                        <button type="reset" class="btn btn-light btn-active-light-primary me-2">Resetar</button>
                        <button type="submit" class="btn btn-primary" id="kt_modelo_edit_submit">
                            <span class="indicator-label">Salvar Alterações</span>
                            <span class="indicator-progress">Salvando...
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                        </button>
                    </div>
                    <!--end::Actions-->
                </div>
                <!--end::Card-->
            </form>
            <!--end::Form-->
        </div>
    </div>
    <!--end::Content-->
</div>
<!--end::Content wrapper-->
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar Select2
    $('[data-control="select2"]').select2();

    // Configurar Dropzone
    try {
        const dropzone = new Dropzone("#kt_dropzone_modelo", {
            url: "#", // não usado, só para inicializar
            paramName: "arquivo",
            maxFiles: 1,
            maxFilesize: 10, // MB
            acceptedFiles: ".docx",
            addRemoveLinks: true,
            autoProcessQueue: false,
            dictDefaultMessage: "Arraste o arquivo aqui ou clique para selecionar",
            dictRemoveFile: "Remover arquivo",
            dictFileTooBig: "Arquivo muito grande (máximo 10MB)",
            dictInvalidFileType: "Apenas arquivos .docx são aceitos",
            
            init: function() {
                const dz = this;
                
                // Quando um arquivo for adicionado
                this.on("addedfile", function(file) {
                    // Simular detecção de variáveis
                    setTimeout(() => {
                        document.getElementById('variaveis_detectadas').style.display = 'block';
                        document.getElementById('lista_variaveis').innerHTML = `
                            <div class="d-flex flex-wrap gap-2">
                                <span class="badge badge-light-primary">numero_proposicao</span>
                                <span class="badge badge-light-primary">tipo_proposicao</span>
                                <span class="badge badge-light-primary">ementa</span>
                                <span class="badge badge-light-primary">autor_nome</span>
                                <span class="badge badge-light-primary">data_criacao</span>
                            </div>
                        `;
                    }, 1000);
                });

                // Quando o arquivo for removido
                this.on("removedfile", function(file) {
                    document.getElementById('variaveis_detectadas').style.display = 'none';
                });

                // Interceptar o submit do formulário
                document.getElementById('kt_modelo_edit_form').addEventListener('submit', function(e) {
                    if (dz.getAcceptedFiles().length > 0) {
                        // Criar um novo FormData com o arquivo do Dropzone
                        const formData = new FormData(this);
                        formData.delete('arquivo'); // remover o input file original
                        formData.append('arquivo', dz.getAcceptedFiles()[0]);
                        
                        // Você pode aqui fazer upload via AJAX se necessário
                        // Por ora, deixamos o formulário seguir o fluxo normal
                    }
                });
            }
        });
    } catch (error) {
        console.log('Dropzone não inicializado, usando input file padrão');
        // Fallback para input file normal
        document.getElementById('arquivo_fallback').style.display = 'block';
        document.getElementById('kt_dropzone_modelo').style.display = 'none';
    }

    // Validação do formulário
    const form = document.getElementById('kt_modelo_edit_form');
    const submitButton = document.getElementById('kt_modelo_edit_submit');
    
    form.addEventListener('submit', function() {
        submitButton.setAttribute('data-kt-indicator', 'on');
        submitButton.disabled = true;
    });
});
</script>
@endpush