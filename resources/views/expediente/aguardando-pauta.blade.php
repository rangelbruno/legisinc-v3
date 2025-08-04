@extends('layouts.app')

@section('title', 'Proposições Aguardando Pauta')

@section('content')
<div class="d-flex flex-column flex-column-fluid">
    <!-- Toolbar -->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    📋 Proposições Aguardando Pauta
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Home</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('expediente.index') }}" class="text-muted text-hover-primary">Expediente</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Aguardando Pauta</li>
                </ul>
            </div>
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <a href="{{ route('expediente.index') }}" class="btn btn-sm btn-light">
                    <i class="ki-duotone ki-arrow-left fs-4"></i>
                    Voltar ao Painel
                </a>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">
            
            @if($proposicoes->count() > 0)
                <!-- Cards de Estatísticas por Momento -->
                <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
                    @foreach($porMomento as $momento => $props)
                    <div class="col-md-6">
                        <div class="card card-flush bgi-no-repeat bgi-size-contain bgi-position-x-end h-md-50px" 
                             style="background-color: {{ $momento === 'EXPEDIENTE' ? '#7239EA' : '#17C653' }}; background-image:url('assets/media/patterns/vector-1.png')">
                            <div class="card-header pt-5">
                                <div class="card-title d-flex flex-column">
                                    <div class="d-flex align-items-center">
                                        <span class="fs-2hx fw-bold text-white me-2 lh-1">{{ $props->count() }}</span>
                                    </div>
                                    <span class="text-white opacity-75 pt-1 fw-semibold fs-6">
                                        {{ $momento === 'EXPEDIENTE' ? '📋 Expediente' : '⚖️ Ordem do Dia' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Proposições por Momento da Sessão -->
                @foreach($porMomento as $momento => $props)
                <div class="card card-flush mb-5">
                    <div class="card-header pt-7">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold text-dark">
                                @if($momento === 'EXPEDIENTE')
                                    📋 Expediente
                                @elseif($momento === 'ORDEM_DO_DIA')
                                    ⚖️ Ordem do Dia
                                @else
                                    ❓ {{ $momento }}
                                @endif
                            </span>
                            <span class="text-muted mt-1 fw-semibold fs-7">
                                {{ $props->count() }} {{ $props->count() === 1 ? 'proposição' : 'proposições' }}
                            </span>
                        </h3>
                        <div class="card-toolbar">
                            <span class="badge badge-{{ $momento === 'EXPEDIENTE' ? 'info' : 'primary' }} fs-8 fw-bold">
                                Aguardando Inclusão em Pauta
                            </span>
                        </div>
                    </div>
                    <div class="card-body pt-6">
                        <div class="table-responsive">
                            <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                                <thead>
                                    <tr class="fw-bold text-muted">
                                        <th class="min-w-150px">Proposição</th>
                                        <th class="min-w-140px">Autor</th>
                                        <th class="min-w-80px">Parecer</th>
                                        <th class="min-w-120px">Protocolo</th>
                                        <th class="min-w-100px text-end">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($props as $proposicao)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="symbol symbol-45px me-5">
                                                    <span class="symbol-label bg-light-{{ $momento === 'EXPEDIENTE' ? 'info' : 'primary' }} text-{{ $momento === 'EXPEDIENTE' ? 'info' : 'primary' }} fw-bold">
                                                        {{ strtoupper(substr($proposicao->tipo, 0, 2)) }}
                                                    </span>
                                                </div>
                                                <div class="d-flex justify-content-start flex-column">
                                                    <a href="{{ route('expediente.show', $proposicao) }}" class="text-dark fw-bold text-hover-primary fs-6">
                                                        {{ $proposicao->tipo_formatado }}
                                                    </a>
                                                    <span class="text-muted fw-semibold text-muted d-block fs-7">
                                                        {{ Str::limit($proposicao->ementa, 60) }}
                                                    </span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="text-dark fw-bold d-block fs-6">{{ $proposicao->autor->name }}</span>
                                            <span class="text-muted fw-semibold fs-7">{{ $proposicao->autor->roles->first()->name ?? 'N/A' }}</span>
                                        </td>
                                        <td>
                                            @if($proposicao->tem_parecer)
                                                <span class="badge badge-success fs-8 fw-bold">
                                                    <i class="ki-duotone ki-check fs-7"></i>
                                                    Com Parecer
                                                </span>
                                            @else
                                                <span class="badge badge-{{ $momento === 'ORDEM_DO_DIA' ? 'warning' : 'light' }} fs-8 fw-bold">
                                                    <i class="ki-duotone ki-information fs-7"></i>
                                                    Sem Parecer
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="text-dark fw-bold d-block fs-6">{{ $proposicao->numero_protocolo }}</span>
                                            <span class="text-muted fw-semibold fs-7">{{ $proposicao->data_protocolo->format('d/m/Y') }}</span>
                                            @if($proposicao->funcionarioProtocolo)
                                                <span class="text-muted fw-semibold fs-8 d-block">{{ $proposicao->funcionarioProtocolo->name }}</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <div class="d-flex justify-content-end flex-shrink-0">
                                                <a href="{{ route('expediente.show', $proposicao) }}" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1">
                                                    <i class="ki-duotone ki-eye fs-5"></i>
                                                </a>
                                                <button type="button" class="btn btn-icon btn-bg-light btn-active-color-success btn-sm" 
                                                        onclick="enviarParaVotacao({{ $proposicao->id }})"
                                                        title="Enviar para Votação"
                                                        @if($momento === 'ORDEM_DO_DIA' && !$proposicao->tem_parecer && str_contains($proposicao->tipo, 'projeto_'))
                                                            disabled
                                                            title="Projetos na Ordem do Dia geralmente precisam de parecer"
                                                        @endif>
                                                    <i class="ki-duotone ki-check-square fs-5"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endforeach

            @else
                <!-- Estado vazio -->
                <div class="card">
                    <div class="card-body text-center py-20">
                        <i class="ki-duotone ki-document fs-4x text-muted mb-5">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        <div class="fs-3 fw-bold text-gray-700 mb-2">Nenhuma Proposição Aguardando Pauta</div>
                        <div class="fs-6 text-muted mb-5">
                            Não há proposições protocoladas aguardando inclusão em pauta de sessão.
                        </div>
                        <a href="{{ route('expediente.index') }}" class="btn btn-primary">
                            <i class="ki-duotone ki-arrow-left fs-4"></i>
                            Voltar ao Painel do Expediente
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
function enviarParaVotacao(id) {
    if (confirm('Deseja enviar esta proposição para votação?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/expediente/proposicoes/${id}/enviar-votacao`;
        
        const csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = '{{ csrf_token() }}';
        
        form.appendChild(csrf);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endpush
@endsection