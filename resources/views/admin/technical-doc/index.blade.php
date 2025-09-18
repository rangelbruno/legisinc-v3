@extends('components.layouts.app')

@section('title', 'Documentação Técnica do Sistema')

@section('content')
<div class="d-flex flex-column flex-column-fluid">
    <!--begin::Toolbar-->
    <div id="kt_toolbar" class="toolbar py-2">
        <div id="kt_toolbar_container" class="container-fluid d-flex align-items-center">
            <div class="flex-grow-1 flex-shrink-0 me-5">
                <!--begin::Page title-->
                <div data-kt-swapper="true" data-kt-swapper-mode="prepend"
                     data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}"
                     class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
                    <h1 class="d-flex align-items-center text-dark fw-bolder my-1 fs-3">
                        <i class="ki-duotone ki-code fs-1 text-primary me-3">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                            <span class="path4"></span>
                        </i>
                        Documentação Técnica
                    </h1>
                </div>
                <!--end::Page title-->
            </div>
        </div>
    </div>
    <!--end::Toolbar-->

    <!--begin::Post-->
    <div class="post d-flex flex-column-fluid" id="kt_post">
        <div id="kt_content_container" class="container-fluid">

            <!--begin::Card-->
            <div class="card mb-8">
                <div class="card-body p-8">
                    <div class="d-flex align-items-center mb-6">
                        <i class="ki-duotone ki-information-5 fs-1 text-primary me-3">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                        </i>
                        <div>
                            <h3 class="text-gray-900 fw-bolder mb-1">Sistema LegisInc v2.3</h3>
                            <p class="text-gray-600 mb-0">Documentação técnica dos módulos principais do sistema legislativo</p>
                        </div>
                    </div>

                    <div class="separator separator-dashed my-6"></div>

                    <div class="text-gray-700">
                        <p class="mb-4">Esta página apresenta a arquitetura e fluxos dos principais módulos do sistema:</p>
                        <ul class="mb-0">
                            <li><strong>Arquivos envolvidos:</strong> Controllers, Models, Views, Services</li>
                            <li><strong>Rotas e endpoints:</strong> Mapeamento completo das APIs</li>
                            <li><strong>Fluxos visuais:</strong> Diagramas Mermaid interativos</li>
                            <li><strong>Integração entre módulos:</strong> Como os componentes se comunicam</li>
                        </ul>
                    </div>
                </div>
            </div>
            <!--end::Card-->

            <!--begin::Modules Grid-->
            <div class="row g-6 g-xl-9">
                @foreach($modules as $key => $module)
                <div class="col-md-6 col-xl-3">
                    <div class="card h-100 cursor-pointer" onclick="window.location.href='{{ route('admin.technical-doc.module', $key) }}'">
                        <div class="card-body p-6 text-center">
                            <div class="mb-4">
                                <i class="ki-duotone {{ $module['icon'] }} fs-3x text-{{ $module['color'] }}">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                            </div>

                            <h4 class="text-gray-900 fw-bolder mb-3">{{ $module['name'] }}</h4>

                            <p class="text-gray-600 mb-4 fs-6">{{ $module['description'] }}</p>

                            <div class="d-flex justify-content-center mb-4">
                                <div class="badge badge-light-{{ $module['color'] }} fw-bold">
                                    {{ count($module['controllers']) }} Controllers
                                </div>
                            </div>

                            <div class="text-center">
                                <span class="btn btn-sm btn-light-{{ $module['color'] }} fw-bold">
                                    Ver Detalhes
                                    <i class="ki-duotone ki-arrow-right fs-5 ms-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <!--end::Modules Grid-->

        </div>
    </div>
    <!--end::Post-->
</div>

<style>
.card.cursor-pointer:hover {
    transform: translateY(-2px);
    transition: transform 0.2s ease-in-out;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
}
</style>
@endsection