@extends('components.layouts.app')

@section('title', 'Meus Pareceres Jurídicos')

@section('content')
<div class="d-flex flex-column flex-column-fluid">
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <!--begin::Page title-->
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    Meus Pareceres Jurídicos
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Home</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('parecer-juridico.index') }}" class="text-muted text-hover-primary">Parecer Jurídico</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Meus Pareceres</li>
                </ul>
            </div>
            <!--end::Page title-->
            
            <!--begin::Actions-->
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <a href="{{ route('parecer-juridico.index') }}" class="btn btn-sm btn-light-primary">
                    <i class="ki-duotone ki-arrow-left fs-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    Voltar para Proposições
                </a>
            </div>
            <!--end::Actions-->
        </div>
    </div>
    <!--end::Toolbar-->

    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">
            
            <!--begin::Row-->
            <div class="row gy-5 g-xl-10">
                <!--begin::Col-->
                <div class="col-xl-12">
                    <!--begin::Card-->
                    <div class="card card-flush">
                        <!--begin::Card header-->
                        <div class="card-header pt-8">
                            <div class="card-title">
                                <h2>Pareceres Emitidos</h2>
                            </div>
                            <div class="card-toolbar">
                                <!--begin::Search-->
                                <form method="GET" class="d-flex">
                                    <div class="d-flex align-items-center position-relative me-4">
                                        <i class="ki-duotone ki-magnifier fs-1 position-absolute ms-6">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        <input type="text" name="search" value="{{ request('search') }}" 
                                               class="form-control form-control-solid w-250px ps-15" 
                                               placeholder="Buscar pareceres...">
                                    </div>
                                    
                                    <select name="tipo_parecer" class="form-select form-select-solid me-4" style="width: 200px;">
                                        <option value="">Todos os Tipos</option>
                                        <option value="FAVORAVEL" {{ request('tipo_parecer') == 'FAVORAVEL' ? 'selected' : '' }}>Favorável</option>
                                        <option value="CONTRARIO" {{ request('tipo_parecer') == 'CONTRARIO' ? 'selected' : '' }}>Contrário</option>
                                        <option value="COM_EMENDAS" {{ request('tipo_parecer') == 'COM_EMENDAS' ? 'selected' : '' }}>Favorável com Emendas</option>
                                    </select>
                                    
                                    <button type="submit" class="btn btn-primary me-2">Filtrar</button>
                                    <a href="{{ route('parecer-juridico.meus-pareceres') }}" class="btn btn-light">Limpar</a>
                                </form>
                                <!--end::Search-->
                            </div>
                        </div>
                        <!--end::Card header-->
                        
                        <!--begin::Card body-->
                        <div class="card-body">
                            @if($pareceres->count() > 0)
                                <!--begin::Table-->
                                <div class="table-responsive">
                                    <table class="table align-middle table-row-dashed fs-6 gy-5">
                                        <thead>
                                            <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                                <th class="min-w-150px">Proposição</th>
                                                <th class="min-w-200px">Ementa</th>
                                                <th class="min-w-100px">Tipo Parecer</th>
                                                <th class="min-w-100px">Data Emissão</th>
                                                <th class="min-w-100px">Última Atualização</th>
                                                <th class="text-end min-w-100px">Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody class="fw-semibold text-gray-600">
                                            @foreach($pareceres as $parecer)
                                                <tr>
                                                    <td>
                                                        <div class="d-flex flex-column">
                                                            <span class="text-gray-800 text-hover-primary mb-1">
                                                                {{ $parecer->proposicao->tipo }}
                                                            </span>
                                                            @if($parecer->proposicao->numero)
                                                                <span class="text-muted fs-7">
                                                                    Nº {{ $parecer->proposicao->numero }}
                                                                </span>
                                                            @endif
                                                            @if($parecer->proposicao->numero_protocolo)
                                                                <span class="badge badge-light-info fs-8 mt-1">
                                                                    {{ $parecer->proposicao->numero_protocolo }}
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="text-gray-800">
                                                            {{ Str::limit($parecer->proposicao->ementa, 80) }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-{{ $parecer->getCorTipoParecer() }}">
                                                            {{ $parecer->getTipoParecerFormatado() }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex flex-column">
                                                            <span class="text-gray-800">
                                                                {{ $parecer->data_emissao->format('d/m/Y') }}
                                                            </span>
                                                            <span class="text-muted fs-7">
                                                                {{ $parecer->data_emissao->format('H:i') }}
                                                            </span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        @if($parecer->updated_at->gt($parecer->created_at))
                                                            <div class="d-flex flex-column">
                                                                <span class="text-gray-800">
                                                                    {{ $parecer->updated_at->format('d/m/Y') }}
                                                                </span>
                                                                <span class="text-muted fs-7">
                                                                    {{ $parecer->updated_at->format('H:i') }}
                                                                </span>
                                                            </div>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-end">
                                                        <div class="d-flex justify-content-end flex-shrink-0">
                                                            <a href="{{ route('parecer-juridico.show', $parecer) }}" 
                                                               class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1">
                                                                <i class="ki-duotone ki-eye fs-2">
                                                                    <span class="path1"></span>
                                                                    <span class="path2"></span>
                                                                    <span class="path3"></span>
                                                                </i>
                                                            </a>
                                                            
                                                            <a href="{{ route('parecer-juridico.edit', $parecer) }}" 
                                                               class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1">
                                                                <i class="ki-duotone ki-pencil fs-2">
                                                                    <span class="path1"></span>
                                                                    <span class="path2"></span>
                                                                </i>
                                                            </a>
                                                            
                                                            <a href="{{ route('parecer-juridico.pdf', $parecer) }}" 
                                                               class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1">
                                                                <i class="ki-duotone ki-printer fs-2">
                                                                    <span class="path1"></span>
                                                                    <span class="path2"></span>
                                                                    <span class="path3"></span>
                                                                    <span class="path4"></span>
                                                                    <span class="path5"></span>
                                                                </i>
                                                            </a>
                                                            
                                                            <a href="{{ route('proposicoes.show', $parecer->proposicao) }}" 
                                                               class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm">
                                                                <i class="ki-duotone ki-document fs-2">
                                                                    <span class="path1"></span>
                                                                    <span class="path2"></span>
                                                                </i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <!--end::Table-->
                                
                                <!--begin::Pagination-->
                                <div class="d-flex flex-stack flex-wrap pt-10">
                                    <div class="fs-6 fw-semibold text-gray-700">
                                        Mostrando {{ $pareceres->firstItem() }} a {{ $pareceres->lastItem() }} 
                                        de {{ $pareceres->total() }} pareceres
                                    </div>
                                    
                                    {{ $pareceres->appends(request()->query())->links() }}
                                </div>
                                <!--end::Pagination-->
                            @else
                                <!--begin::Empty state-->
                                <div class="d-flex flex-center flex-column py-10">
                                    <i class="ki-duotone ki-folder fs-5x text-gray-300">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    <div class="text-gray-500 fs-6 fw-semibold mt-4">
                                        @if(request()->hasAny(['search', 'tipo_parecer']))
                                            Nenhum parecer encontrado com os filtros aplicados
                                        @else
                                            Você ainda não emitiu nenhum parecer jurídico
                                        @endif
                                    </div>
                                    @if(!request()->hasAny(['search', 'tipo_parecer']))
                                        <div class="text-gray-400 fs-7 mt-2">
                                            <a href="{{ route('parecer-juridico.index') }}" class="text-primary">
                                                Clique aqui para ver as proposições que precisam de parecer
                                            </a>
                                        </div>
                                    @endif
                                </div>
                                <!--end::Empty state-->
                            @endif
                        </div>
                        <!--end::Card body-->
                    </div>
                    <!--end::Card-->
                </div>
                <!--end::Col-->
            </div>
            <!--end::Row-->
        </div>
    </div>
    <!--end::Content-->
</div>
@endsection