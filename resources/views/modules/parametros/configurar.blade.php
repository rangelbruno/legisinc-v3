@extends('components.layouts.app')

@section('title', 'Configurar: ' . $modulo->nome)

@section('content')
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <!--begin::Toolbar container-->
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <!--begin::Page title-->
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <!--begin::Title-->
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                    <i class="ki-duotone {{ $modulo->icon ?: 'ki-setting-2' }} fs-2 me-3">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    Configurar {{ $modulo->nome }}
                </h1>
                <!--end::Title-->
                <!--begin::Breadcrumb-->
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('parametros.index') }}" class="text-muted text-hover-primary">Parâmetros</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">{{ $modulo->nome }}</li>
                </ul>
                <!--end::Breadcrumb-->
            </div>
            <!--end::Page title-->
            <!--begin::Actions-->
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <!--begin::Secondary button-->
                <a href="{{ route('parametros.show', $modulo->id) }}" class="btn btn-sm btn-flex btn-secondary">
                    <i class="ki-duotone ki-arrow-left fs-3"></i>
                    Voltar
                </a>
                <!--end::Secondary button-->
            </div>
            <!--end::Actions-->
        </div>
        <!--end::Toolbar container-->
    </div>
    <!--end::Toolbar-->

    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <!--begin::Content container-->
        <div id="kt_app_content_container" class="app-container container-xxl">
            
            <!--begin::Alert-->
            @if (session('success'))
                <div class="alert alert-success d-flex align-items-center p-5 mb-10">
                    <i class="ki-duotone ki-check fs-2hx text-success me-4">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    <div class="d-flex flex-column">
                        <h5 class="mb-1">Sucesso</h5>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger d-flex align-items-center p-5 mb-10">
                    <i class="ki-duotone ki-cross-circle fs-2hx text-danger me-4">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    <div class="d-flex flex-column">
                        <h5 class="mb-1">Erro</h5>
                        <span>{{ session('error') }}</span>
                    </div>
                </div>
            @endif
            <!--end::Alert-->

            <!--begin::Row-->
            <div class="row g-6 g-xl-9">
                @forelse($submodulos as $submodulo)
                    <!--begin::Col-->
                    <div class="col-12">
                        <!--begin::Card-->
                        <div class="card card-flush mb-6">
                            <!--begin::Card header-->
                            <div class="card-header pt-8">
                                <!--begin::Card title-->
                                <div class="card-title">
                                    <h3 class="fw-bold text-gray-900">{{ $submodulo->nome }}</h3>
                                </div>
                                <!--end::Card title-->
                                <!--begin::Card toolbar-->
                                <div class="card-toolbar">
                                    <div class="d-flex align-items-center">
                                        <span class="badge badge-light-primary me-3">
                                            {{ ucfirst($submodulo->tipo) }}
                                        </span>
                                        <span class="badge badge-light-{{ $submodulo->ativo ? 'success' : 'danger' }}">
                                            {{ $submodulo->ativo ? 'Ativo' : 'Inativo' }}
                                        </span>
                                    </div>
                                </div>
                                <!--end::Card toolbar-->
                            </div>
                            <!--end::Card header-->
                            <!--begin::Card body-->
                            <div class="card-body">
                                @if($submodulo->descricao)
                                    <div class="mb-6">
                                        <p class="text-gray-600 fs-6 mb-0">{{ $submodulo->descricao }}</p>
                                    </div>
                                @endif

                                <!--begin::Form-->
                                <form action="{{ route('parametros.salvar-configuracoes', $submodulo->id) }}" method="POST" class="submodule-form">
                                    @csrf
                                    
                                    @if($submodulo->tipo === 'form')
                                        <!--begin::Form fields-->
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="fv-row mb-7">
                                                    <label class="fs-6 fw-semibold form-label mb-2">Nome da Câmara</label>
                                                    <input type="text" class="form-control form-control-solid" name="nome_camara" placeholder="Ex: Câmara Municipal de São Paulo" />
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="fv-row mb-7">
                                                    <label class="fs-6 fw-semibold form-label mb-2">Tipo de Integração</label>
                                                    <select class="form-select form-select-solid" name="tipo_integracao">
                                                        <option value="">Selecione...</option>
                                                        <option value="api">API Rest</option>
                                                        <option value="xml">XML</option>
                                                        <option value="json">JSON</option>
                                                        <option value="manual">Manual</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="fv-row mb-7">
                                                    <label class="fs-6 fw-semibold form-label mb-2">Endereço</label>
                                                    <textarea class="form-control form-control-solid" name="endereco" rows="3" placeholder="Endereço completo da câmara"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="fv-row mb-7">
                                                    <label class="fs-6 fw-semibold form-label mb-2">Qtd. Vereadores</label>
                                                    <input type="number" class="form-control form-control-solid" name="qtd_vereadores" placeholder="Ex: 21" min="1" />
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="fv-row mb-7">
                                                    <label class="fs-6 fw-semibold form-label mb-2">Qtd. Quorum</label>
                                                    <input type="number" class="form-control form-control-solid" name="qtd_quorum" placeholder="Ex: 11" min="1" />
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="fv-row mb-7">
                                                    <label class="fs-6 fw-semibold form-label mb-2">Tempo Sessão (min)</label>
                                                    <input type="number" class="form-control form-control-solid" name="tempo_sessao" placeholder="Ex: 120" min="30" />
                                                </div>
                                            </div>
                                        </div>
                                        <!--end::Form fields-->
                                    @elseif($submodulo->tipo === 'checkbox')
                                        <!--begin::Checkbox fields-->
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-check form-check-custom form-check-solid mb-5">
                                                    <input class="form-check-input" type="checkbox" name="veto_acato" id="veto_acato" />
                                                    <label class="form-check-label" for="veto_acato">
                                                        Veto (Acato/Não Acato)
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-check form-check-custom form-check-solid mb-5">
                                                    <input class="form-check-input" type="checkbox" name="iniciar_expediente" id="iniciar_expediente" checked />
                                                    <label class="form-check-label" for="iniciar_expediente">
                                                        Iniciar Expediente
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-check form-check-custom form-check-solid mb-5">
                                                    <input class="form-check-input" type="checkbox" name="abster" id="abster" checked />
                                                    <label class="form-check-label" for="abster">
                                                        Permitir Abstenção
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-check form-check-custom form-check-solid mb-5">
                                                    <input class="form-check-input" type="checkbox" name="chamada_automatica" id="chamada_automatica" />
                                                    <label class="form-check-label" for="chamada_automatica">
                                                        Chamada Automática
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-check form-check-custom form-check-solid mb-5">
                                                    <input class="form-check-input" type="checkbox" name="popup_votacao" id="popup_votacao" checked />
                                                    <label class="form-check-label" for="popup_votacao">
                                                        Pop-up de Votação
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <!--end::Checkbox fields-->
                                    @else
                                        <!--begin::Empty state-->
                                        <div class="d-flex flex-center flex-column py-10">
                                            <i class="ki-duotone ki-information-5 fs-3x text-primary mb-5">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                                <span class="path3"></span>
                                            </i>
                                            <h3 class="text-gray-800 mb-2">Configuração em Desenvolvimento</h3>
                                            <p class="text-gray-600 fs-6 mb-0">
                                                Os campos de configuração para este submódulo ainda não foram implementados.
                                            </p>
                                        </div>
                                        <!--end::Empty state-->
                                    @endif

                                    @if($submodulo->tipo !== 'custom')
                                        <!--begin::Actions-->
                                        <div class="d-flex justify-content-end pt-6">
                                            <button type="reset" class="btn btn-light me-3">
                                                Resetar
                                            </button>
                                            <button type="submit" class="btn btn-primary">
                                                <span class="indicator-label">Salvar Configurações</span>
                                                <span class="indicator-progress">Por favor aguarde...
                                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                                </span>
                                            </button>
                                        </div>
                                        <!--end::Actions-->
                                    @endif
                                </form>
                                <!--end::Form-->
                            </div>
                            <!--end::Card body-->
                        </div>
                        <!--end::Card-->
                    </div>
                    <!--end::Col-->
                @empty
                    <!--begin::Empty state-->
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body d-flex flex-center flex-column py-20">
                                <div class="text-center">
                                    <i class="ki-duotone ki-element-11 fs-4x text-primary mb-5">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                        <span class="path4"></span>
                                    </i>
                                    <h3 class="text-gray-800 mb-2">Nenhum submódulo encontrado</h3>
                                    <p class="text-gray-600 fs-6 mb-6">
                                        Este módulo ainda não possui submódulos configurados.
                                    </p>
                                    <a href="{{ route('parametros.show', $modulo->id) }}" class="btn btn-primary">
                                        <i class="ki-duotone ki-plus fs-3"></i>
                                        Criar Submódulo
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end::Empty state-->
                @endforelse
            </div>
            <!--end::Row-->
        </div>
        <!--end::Content container-->
    </div>
    <!--end::Content-->
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Form submission handling
            const forms = document.querySelectorAll('.submodule-form');
            
            forms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    const submitButton = this.querySelector('button[type="submit"]');
                    
                    // Show loading state
                    submitButton.querySelector('.indicator-label').style.display = 'none';
                    submitButton.querySelector('.indicator-progress').style.display = 'inline-block';
                    submitButton.disabled = true;
                });
            });
        });
    </script>
@endpush