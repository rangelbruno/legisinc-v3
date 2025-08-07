@extends('components.layouts.app')

@section('title', $title ?? 'Mesa Diretora')

@section('content')
<!--begin::Content wrapper-->
<div class="d-flex flex-column flex-column-fluid">
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <!--begin::Page title-->
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    Mesa Diretora
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Home</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('parlamentares.index') }}" class="text-muted text-hover-primary">Parlamentares</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Mesa Diretora</li>
                </ul>
            </div>
            <!--end::Page title-->
        </div>
    </div>
    <!--end::Toolbar-->
    
    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">
            
            @if(isset($error))
                <div class="alert alert-danger">
                    <i class="ki-duotone ki-cross-circle fs-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    {{ $error }}
                </div>
            @endif
            
            @if($mesaDiretora->count() > 0)
                <!--begin::Row-->
                <div class="row g-6 g-xl-9 mb-6 mb-xl-9">
                    @foreach($mesaDiretora as $membro)
                        <!--begin::Col-->
                        <div class="col-md-6 col-xxl-4">
                            <!--begin::Card-->
                            <div class="card">
                                <!--begin::Card body-->
                                <div class="card-body d-flex flex-center flex-column pt-12 p-9">
                                    <!--begin::Avatar-->
                                    <div class="symbol symbol-65px symbol-circle mb-5">
                                        <img src="{{ asset('assets/media/avatars/blank.png') }}" alt="image" />
                                        <div class="bg-success position-absolute border border-4 border-body h-15px w-15px rounded-circle translate-middle start-100 top-100 ms-n3 mt-n3"></div>
                                    </div>
                                    <!--end::Avatar-->
                                    <!--begin::Name-->
                                    <a href="{{ route('parlamentares.show', $membro['id']) }}" class="fs-4 text-gray-800 text-hover-primary fw-bold mb-0">
                                        {{ $membro['nome'] }}
                                    </a>
                                    <!--end::Name-->
                                    <!--begin::Position-->
                                    <div class="fw-semibold text-gray-400 mb-6">{{ $membro['cargo_mesa'] }}</div>
                                    <!--end::Position-->
                                    <!--begin::Info-->
                                    <div class="d-flex flex-center flex-wrap">
                                        <!--begin::Stats-->
                                        <div class="border border-gray-300 border-dashed rounded min-w-80px py-3 px-4 mx-2 mb-3">
                                            <div class="fs-6 fw-bold text-gray-700">{{ $membro['partido'] }}</div>
                                            <div class="fw-semibold text-gray-400">Partido</div>
                                        </div>
                                        <!--end::Stats-->
                                        <!--begin::Stats-->
                                        <div class="border border-gray-300 border-dashed rounded min-w-80px py-3 px-4 mx-2 mb-3">
                                            <div class="fs-6 fw-bold text-gray-700">{{ $membro['mandato_mesa']['ano_inicio'] ?? '2023' }}</div>
                                            <div class="fw-semibold text-gray-400">Mandato</div>
                                        </div>
                                        <!--end::Stats-->
                                    </div>
                                    <!--end::Info-->
                                </div>
                                <!--end::Card body-->
                            </div>
                            <!--end::Card-->
                        </div>
                        <!--end::Col-->
                    @endforeach
                </div>
                <!--end::Row-->
                
                <!--begin::Card-->
                <div class="card">
                    <!--begin::Card header-->
                    <div class="card-header border-0 pt-6">
                        <!--begin::Card title-->
                        <div class="card-title">
                            <h2>Composição da Mesa Diretora</h2>
                        </div>
                        <!--end::Card title-->
                    </div>
                    <!--end::Card header-->
                    <!--begin::Card body-->
                    <div class="card-body py-4">
                        <!--begin::Table-->
                        <div class="table-responsive">
                            <table class="table align-middle table-row-dashed fs-6 gy-5">
                                <!--begin::Table head-->
                                <thead>
                                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                        <th class="min-w-125px">Cargo na Mesa</th>
                                        <th class="min-w-125px">Parlamentar</th>
                                        <th class="min-w-125px">Partido</th>
                                        <th class="min-w-125px">Mandato</th>
                                        <th class="text-end min-w-100px">Ações</th>
                                    </tr>
                                </thead>
                                <!--end::Table head-->
                                <!--begin::Table body-->
                                <tbody class="text-gray-600 fw-semibold">
                                    @foreach($mesaDiretora as $membro)
                                        <tr>
                                            <td>
                                                <span class="badge badge-light-primary fs-7 fw-bold">{{ $membro['cargo_mesa'] }}</span>
                                            </td>
                                            <td class="d-flex align-items-center">
                                                <!--begin::Avatar-->
                                                <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                                                    <div class="symbol-label">
                                                        <img src="{{ asset('assets/media/avatars/blank.png') }}" alt="{{ $membro['nome'] }}" class="w-100" />
                                                    </div>
                                                </div>
                                                <!--end::Avatar-->
                                                <!--begin::User details-->
                                                <div class="d-flex flex-column">
                                                    <a href="{{ route('parlamentares.show', $membro['id']) }}" class="text-gray-800 text-hover-primary mb-1">
                                                        {{ $membro['nome'] }}
                                                    </a>
                                                    <span>{{ $membro['cargo_parlamentar'] }}</span>
                                                </div>
                                                <!--begin::User details-->
                                            </td>
                                            <td>
                                                <span class="badge badge-light-primary">{{ $membro['partido'] }}</span>
                                            </td>
                                            <td>
                                                {{ $membro['mandato_mesa']['ano_inicio'] ?? '2023' }} - {{ $membro['mandato_mesa']['ano_fim'] ?? '2024' }}
                                            </td>
                                            <td class="text-end">
                                                <a href="{{ route('parlamentares.show', $membro['id']) }}" class="btn btn-light btn-active-light-primary btn-sm">
                                                    Ver Detalhes
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <!--end::Table body-->
                            </table>
                        </div>
                        <!--end::Table-->
                    </div>
                    <!--end::Card body-->
                </div>
                <!--end::Card-->
            @else
                <!--begin::Card-->
                <div class="card">
                    <!--begin::Card body-->
                    <div class="card-body text-center">
                        <img src="{{ asset('assets/media/illustrations/sketchy-1/5.png') }}" class="mw-300px">
                        <div class="fs-1 fw-bolder text-dark mb-4">Mesa Diretora não configurada</div>
                        <div class="fs-6">Ainda não há membros configurados para a Mesa Diretora.</div>
                    </div>
                    <!--end::Card body-->
                </div>
                <!--end::Card-->
            @endif
        </div>
    </div>
    <!--end::Content-->
</div>
<!--end::Content wrapper-->
@endsection