@extends('components.layouts.app')

@section('title', 'Partidos Brasileiros Disponíveis')

@section('content')
<!--begin::Content wrapper-->
<div class="d-flex flex-column flex-column-fluid">
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <!--begin::Page title-->
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    Partidos Brasileiros
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Home</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('partidos.index') }}" class="text-muted text-hover-primary">Partidos</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Partidos Brasileiros</li>
                </ul>
            </div>
            <!--end::Page title-->
            <!--begin::Actions-->
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <a href="{{ route('partidos.index') }}" class="btn btn-sm fw-bold btn-light">
                    <i class="ki-duotone ki-arrow-left fs-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    Voltar
                </a>
                <button type="button" id="carregar-partidos-btn" class="btn btn-sm fw-bold btn-primary">
                    <i class="ki-duotone ki-refresh fs-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    Carregar Lista
                </button>
            </div>
            <!--end::Actions-->
        </div>
    </div>
    <!--end::Toolbar-->

    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">
            
            <!--begin::Info Card-->
            <div class="card mb-5">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <i class="ki-duotone ki-information fs-1 text-primary me-3">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                        </i>
                        <div>
                            <h5 class="mb-1">Base de Dados de Partidos Brasileiros</h5>
                            <span class="text-muted">Lista completa dos principais partidos políticos do Brasil com suas informações básicas.</span>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Info Card-->

            <!--begin::Card-->
            <div class="card">
                <!--begin::Card header-->
                <div class="card-header border-0 pt-6">
                    <div class="card-title">
                        <h3 class="fw-bold m-0">Partidos Políticos Brasileiros</h3>
                    </div>
                </div>
                <!--end::Card header-->
                <!--begin::Card body-->
                <div class="card-body py-4">
                    <div id="loading" class="text-center py-10" style="display: none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Carregando...</span>
                        </div>
                        <div class="mt-3">Carregando lista de partidos...</div>
                    </div>

                    <div id="partidos-container">
                        <div class="text-center py-20">
                            <i class="ki-duotone ki-flag fs-3x text-gray-400 mb-3">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <h4 class="text-gray-800 mb-3">Lista de Partidos Disponível</h4>
                            <span class="text-gray-600 mb-5">Clique em "Carregar Lista" para visualizar todos os partidos brasileiros disponíveis.</span>
                        </div>
                    </div>
                </div>
                <!--end::Card body-->
            </div>
            <!--end::Card-->
        </div>
    </div>
    <!--end::Content-->
</div>
<!--end::Content wrapper-->

<script>
document.addEventListener('DOMContentLoaded', function() {
    const carregarBtn = document.getElementById('carregar-partidos-btn');
    const loading = document.getElementById('loading');
    const container = document.getElementById('partidos-container');

    carregarBtn.addEventListener('click', function() {
        carregarBtn.disabled = true;
        carregarBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Carregando...';
        loading.style.display = 'block';
        container.innerHTML = '';

        fetch('/api/partidos/brasileiros')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    renderizarPartidos(data.data);
                } else {
                    mostrarErro('Erro ao carregar lista de partidos.');
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                mostrarErro('Erro de conexão ao carregar partidos.');
            })
            .finally(() => {
                carregarBtn.disabled = false;
                carregarBtn.innerHTML = '<i class="ki-duotone ki-refresh fs-2"><span class="path1"></span><span class="path2"></span></i> Carregar Lista';
                loading.style.display = 'none';
            });
    });

    function renderizarPartidos(partidos) {
        let html = `
            <div class="row g-5">
        `;

        partidos.forEach(partido => {
            html += `
                <div class="col-md-6 col-lg-4">
                    <div class="card card-hover h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex align-items-center mb-3">
                                <div class="symbol symbol-50px me-3">
                                    <div class="symbol-label bg-light-primary text-primary fs-2 fw-bold">
                                        ${partido.sigla.substring(0, 2)}
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-bold fs-5 text-gray-800">${partido.sigla}</div>
                                    <div class="badge badge-light-info fs-8">Nº ${partido.numero}</div>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="text-gray-700 mb-2">${partido.nome}</h6>
                            </div>
                            <div class="mt-auto">
                                <button type="button" class="btn btn-light-primary btn-sm w-100" 
                                        onclick="criarPartido('${partido.sigla}', '${partido.nome}', '${partido.numero}', '${partido.presidente || ''}', '${partido.fundacao || ''}', '${partido.site || ''}')">
                                    <i class="ki-duotone ki-plus fs-4">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Criar Partido
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });

        html += `</div>`;
        container.innerHTML = html;
    }

    function mostrarErro(mensagem) {
        container.innerHTML = `
            <div class="text-center py-20">
                <i class="ki-duotone ki-cross-circle fs-3x text-danger mb-3">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
                <h4 class="text-gray-800 mb-3">Erro ao Carregar</h4>
                <span class="text-gray-600">${mensagem}</span>
            </div>
        `;
    }

    window.criarPartido = function(sigla, nome, numero, presidente, fundacao, site) {
        const url = new URL('{{ route("partidos.create") }}');
        url.searchParams.set('sigla', sigla);
        url.searchParams.set('nome', nome);
        url.searchParams.set('numero', numero);
        if (presidente) url.searchParams.set('presidente', presidente);
        if (fundacao) url.searchParams.set('fundacao', fundacao);
        if (site) url.searchParams.set('site', site);
        
        window.location.href = url.toString();
    };
});
</script>

@endsection