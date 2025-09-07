@extends('components.layouts.app')

@section('title', 'Debug PDF - Proposição ' . $proposicao->id)

@section('content')
<div class="d-flex flex-column flex-column-fluid">
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    🔧 Debug PDF - Proposição {{ $proposicao->id }}
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Home</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('proposicoes.show', $proposicao->id) }}" class="text-muted text-hover-primary">Proposição {{ $proposicao->id }}</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Debug PDF</li>
                </ul>
            </div>
        </div>
    </div>
    <!--end::Toolbar-->

    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">

            <div class="row">
                <div class="col-lg-8">
                    <!--begin::Debug Info-->
                    <div class="card card-flush">
                        <div class="card-header">
                            <div class="card-title">
                                <h2>🔍 Informações de Debug do PDF</h2>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-row-dashed table-row-gray-300 gy-5">
                                    <tbody>
                                        <tr>
                                            <td class="fw-bold text-muted">ID da Proposição:</td>
                                            <td>{{ $debugInfo['proposicao_id'] }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold text-muted">Status:</td>
                                            <td>
                                                <span class="badge badge-light-primary">{{ $debugInfo['status'] }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold text-muted">Autor ID:</td>
                                            <td>{{ $debugInfo['autor_id'] }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold text-muted">Arquivo RTF/DOCX:</td>
                                            <td>
                                                @if($debugInfo['arquivo_path'])
                                                    <span class="badge badge-light-info">{{ $debugInfo['arquivo_path'] }}</span>
                                                    @if($debugInfo['has_arquivo'])
                                                        <span class="badge badge-success ms-2">✓ Existe</span>
                                                    @else
                                                        <span class="badge badge-danger ms-2">✗ Não encontrado</span>
                                                    @endif
                                                @else
                                                    <span class="text-muted">Nenhum arquivo fonte</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold text-muted">Arquivo PDF:</td>
                                            <td>
                                                @if($debugInfo['arquivo_pdf_path'])
                                                    <span class="badge badge-light-danger">{{ $debugInfo['arquivo_pdf_path'] }}</span>
                                                    @if($debugInfo['has_pdf'])
                                                        <span class="badge badge-success ms-2">✓ Existe</span>
                                                    @else
                                                        <span class="badge badge-danger ms-2">✗ Não encontrado</span>
                                                    @endif
                                                @else
                                                    <span class="text-muted">Nenhum PDF gerado</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold text-muted">PDF Gerado em:</td>
                                            <td>
                                                @if($debugInfo['pdf_gerado_em'])
                                                    {{ \Carbon\Carbon::parse($debugInfo['pdf_gerado_em'])->format('d/m/Y H:i:s') }}
                                                    <small class="text-muted">({{ \Carbon\Carbon::parse($debugInfo['pdf_gerado_em'])->diffForHumans() }})</small>
                                                @else
                                                    <span class="text-muted">Nunca gerado</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold text-muted">PDF Oficial (detectado):</td>
                                            <td>
                                                @if($debugInfo['pdf_oficial_path'])
                                                    <span class="badge badge-light-success">{{ $debugInfo['pdf_oficial_path'] }}</span>
                                                @else
                                                    <span class="text-muted">Nenhum PDF oficial detectado</span>
                                                @endif
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!--end::Debug Info-->

                    <!--begin::Storage Files-->
                    @if(count($debugInfo['storage_files']) > 0)
                    <div class="card card-flush mt-5">
                        <div class="card-header">
                            <div class="card-title">
                                <h3>📁 Arquivos no Storage</h3>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-row-dashed table-row-gray-300 gy-3">
                                    <thead>
                                        <tr class="fw-bold fs-6 text-gray-800">
                                            <th>Arquivo</th>
                                            <th>Tamanho</th>
                                            <th>Última Modificação</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($debugInfo['storage_files'] as $file)
                                        <tr>
                                            <td>
                                                <span class="badge badge-light">{{ basename($file) }}</span>
                                            </td>
                                            <td>{{ \Illuminate\Support\Facades\Storage::size($file) ? round(\Illuminate\Support\Facades\Storage::size($file) / 1024, 2) . ' KB' : 'N/A' }}</td>
                                            <td>{{ \Carbon\Carbon::createFromTimestamp(\Illuminate\Support\Facades\Storage::lastModified($file))->format('d/m/Y H:i:s') }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endif
                    <!--end::Storage Files-->
                </div>

                <div class="col-lg-4">
                    <!--begin::Actions-->
                    <div class="card card-flush">
                        <div class="card-header">
                            <div class="card-title">
                                <h3>🎯 Ações de Debug</h3>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="d-flex flex-column gap-3">
                                <a href="{{ route('proposicoes.show', $proposicao->id) }}" 
                                   class="btn btn-light-primary w-100">
                                    <i class="ki-duotone ki-arrow-left fs-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Voltar para Proposição
                                </a>

                                @if($debugInfo['has_pdf'])
                                <a href="{{ route('proposicoes.serve-pdf', $proposicao->id) }}" 
                                   target="_blank"
                                   class="btn btn-light-danger w-100">
                                    <i class="ki-duotone ki-file-down fs-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Abrir PDF
                                </a>
                                @endif

                                <button class="btn btn-light-info w-100" onclick="window.initializeDebugLogger()">
                                    <i class="ki-duotone ki-setting-2 fs-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Ativar Debug Logger
                                </button>

                                <button class="btn btn-light-warning w-100" onclick="refreshDebugInfo()">
                                    <i class="ki-duotone ki-arrows-circle fs-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Atualizar Informações
                                </button>
                            </div>
                        </div>
                    </div>
                    <!--end::Actions-->

                    <!--begin::Instructions-->
                    <div class="card card-flush mt-5">
                        <div class="card-header">
                            <div class="card-title">
                                <h3>📋 Como Usar</h3>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="text-muted fs-6">
                                <p><strong>1.</strong> Ative o Debug Logger clicando no botão acima</p>
                                <p><strong>2.</strong> Inicie a gravação no painel de debug</p>
                                <p><strong>3.</strong> Teste a visualização do PDF</p>
                                <p><strong>4.</strong> Analise os logs capturados para identificar problemas</p>
                                
                                <div class="alert alert-primary mt-3">
                                    <strong>URL de acesso:</strong><br>
                                    <code>/proposicoes/{{ $proposicao->id }}/pdf-debug</code>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end::Instructions-->
                </div>
            </div>
        </div>
    </div>
    <!--end::Content-->
</div>

<script>
function refreshDebugInfo() {
    location.reload();
}

// Log específico quando página de debug carrega
console.log('🔧 DEBUG PDF: Página de debug carregada', {
    proposicao_id: {{ $proposicao->id }},
    timestamp: new Date().toISOString(),
    debug_info: @json($debugInfo)
});
</script>
@endsection