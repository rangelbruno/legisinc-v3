@extends('components.layouts.app')

@section('title', 'Configurar Templates - Assinatura e QR Code')

@section('content')
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <!--begin::Toolbar container-->
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <!--begin::Page title-->
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <!--begin::Title-->
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                    <i class="ki-duotone ki-security-user fs-2 me-3">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    Configurar Assinatura Digital e QR Code
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
                        <a href="{{ route('parametros.show', $moduloId) }}" class="text-muted text-hover-primary">Templates</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-gray-900">Assinatura e QR Code</li>
                </ul>
                <!--end::Breadcrumb-->
            </div>
            <!--end::Page title-->
        </div>
        <!--end::Toolbar container-->
    </div>
    <!--end::Toolbar-->

    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <!--begin::Content container-->
        <div id="kt_app_content_container" class="app-container container-xxl">
            
            @if(session('success'))
                <div class="alert alert-success d-flex align-items-center p-5 mb-10">
                    <i class="ki-duotone ki-shield-tick fs-2hx text-success me-4">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    <div class="d-flex flex-column">
                        <h4 class="mb-1 text-success">Sucesso!</h4>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger d-flex align-items-center p-5 mb-10">
                    <i class="ki-duotone ki-shield-cross fs-2hx text-danger me-4">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                    </i>
                    <div class="d-flex flex-column">
                        <h4 class="mb-1 text-danger">Erro!</h4>
                        @foreach($errors->all() as $error)
                            <span>{{ $error }}</span>
                        @endforeach
                    </div>
                </div>
            @endif

            <!--begin::Row-->
            <div class="row g-5 g-xl-10">
                <!--begin::Col-->
                <div class="col-xl-6">
                    <!--begin::Card-->
                    <div class="card card-flush h-xl-100">
                        <!--begin::Header-->
                        <div class="card-header pt-7">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold text-gray-900">Configurações de Assinatura Digital</span>
                                <span class="text-gray-500 mt-1 fw-semibold fs-6">Configure onde e como a assinatura digital aparecerá</span>
                            </h3>
                        </div>
                        <!--end::Header-->
                        <!--begin::Body-->
                        <div class="card-body pt-5">
                            <form action="{{ route('parametros.templates.assinatura-qrcode.store') }}" method="POST">
                                @csrf
                                
                                <!--begin::Input group-->
                                <div class="fv-row mb-7">
                                    <label class="form-label fw-semibold text-gray-900 fs-6">Posição da Assinatura</label>
                                    <select name="assinatura_posicao" class="form-select form-select-lg" data-control="select2">
                                        <option value="rodape_esquerda" {{ $configuracoes['assinatura_posicao'] === 'rodape_esquerda' ? 'selected' : '' }}>Rodapé - Esquerda</option>
                                        <option value="rodape_centro" {{ $configuracoes['assinatura_posicao'] === 'rodape_centro' ? 'selected' : '' }}>Rodapé - Centro</option>
                                        <option value="rodape_direita" {{ $configuracoes['assinatura_posicao'] === 'rodape_direita' ? 'selected' : '' }}>Rodapé - Direita</option>
                                        <option value="final_documento_esquerda" {{ $configuracoes['assinatura_posicao'] === 'final_documento_esquerda' ? 'selected' : '' }}>Final do Documento - Esquerda</option>
                                        <option value="final_documento_centro" {{ $configuracoes['assinatura_posicao'] === 'final_documento_centro' ? 'selected' : '' }}>Final do Documento - Centro</option>
                                        <option value="final_documento_direita" {{ $configuracoes['assinatura_posicao'] === 'final_documento_direita' ? 'selected' : '' }}>Final do Documento - Direita</option>
                                        <option value="pagina_separada" {{ $configuracoes['assinatura_posicao'] === 'pagina_separada' ? 'selected' : '' }}>Página Separada</option>
                                    </select>
                                    <div class="form-text">Escolha onde a assinatura digital será posicionada no documento</div>
                                </div>
                                <!--end::Input group-->

                                <!--begin::Input group-->
                                <div class="fv-row mb-7">
                                    <label class="form-label fw-semibold text-gray-900 fs-6">Texto da Assinatura</label>
                                    <textarea name="assinatura_texto" class="form-control form-control-lg" rows="4" placeholder="Digite o texto que acompanha a assinatura">{{ $configuracoes['assinatura_texto'] ?? "Documento assinado digitalmente por:\n{autor_nome}\n{autor_cargo}\nEm {data_assinatura}" }}</textarea>
                                    <div class="form-text">
                                        Use as variáveis: <code>{autor_nome}</code>, <code>{autor_cargo}</code>, <code>{data_assinatura}</code>
                                    </div>
                                </div>
                                <!--end::Input group-->

                                <!--begin::Input group-->
                                <div class="fv-row mb-7">
                                    <div class="form-check form-switch form-check-custom form-check-success form-check-solid">
                                        <input class="form-check-input" type="checkbox" name="assinatura_apenas_protocolo" value="1" 
                                               {{ ($configuracoes['assinatura_apenas_protocolo'] ?? '1') == '1' ? 'checked' : '' }}>
                                        <label class="form-check-label fw-semibold text-gray-900 fs-6">
                                            Mostrar apenas após protocolo
                                        </label>
                                    </div>
                                    <div class="form-text">A assinatura digital só aparece no documento após ser protocolado</div>
                                </div>
                                <!--end::Input group-->

                                
                                <button type="submit" class="btn btn-primary">
                                    <i class="ki-duotone ki-check fs-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Salvar Configurações
                                </button>
                            </form>
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Card-->
                </div>
                <!--end::Col-->

                <!--begin::Col-->
                <div class="col-xl-6">
                    <!--begin::Card-->
                    <div class="card card-flush h-xl-100">
                        <!--begin::Header-->
                        <div class="card-header pt-7">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold text-gray-900">Configurações do QR Code</span>
                                <span class="text-gray-500 mt-1 fw-semibold fs-6">Configure onde e como o QR Code aparecerá</span>
                            </h3>
                        </div>
                        <!--end::Header-->
                        <!--begin::Body-->
                        <div class="card-body pt-5">
                            <form action="{{ route('parametros.templates.assinatura-qrcode.store') }}" method="POST">
                                @csrf
                                
                                <!--begin::Input group-->
                                <div class="fv-row mb-7">
                                    <label class="form-label fw-semibold text-gray-900 fs-6">Posição do QR Code</label>
                                    <select name="qrcode_posicao" class="form-select form-select-lg" data-control="select2">
                                        <option value="rodape_esquerda" {{ $configuracoes['qrcode_posicao'] === 'rodape_esquerda' ? 'selected' : '' }}>Rodapé - Esquerda</option>
                                        <option value="rodape_centro" {{ $configuracoes['qrcode_posicao'] === 'rodape_centro' ? 'selected' : '' }}>Rodapé - Centro</option>
                                        <option value="rodape_direita" {{ $configuracoes['qrcode_posicao'] === 'rodape_direita' ? 'selected' : '' }}>Rodapé - Direita</option>
                                        <option value="cabecalho_esquerda" {{ $configuracoes['qrcode_posicao'] === 'cabecalho_esquerda' ? 'selected' : '' }}>Cabeçalho - Esquerda</option>
                                        <option value="cabecalho_direita" {{ $configuracoes['qrcode_posicao'] === 'cabecalho_direita' ? 'selected' : '' }}>Cabeçalho - Direita</option>
                                        <option value="final_documento_esquerda" {{ $configuracoes['qrcode_posicao'] === 'final_documento_esquerda' ? 'selected' : '' }}>Final do Documento - Esquerda</option>
                                        <option value="final_documento_centro" {{ $configuracoes['qrcode_posicao'] === 'final_documento_centro' ? 'selected' : '' }}>Final do Documento - Centro</option>
                                        <option value="final_documento_direita" {{ $configuracoes['qrcode_posicao'] === 'final_documento_direita' ? 'selected' : '' }}>Final do Documento - Direita</option>
                                        <option value="lateral_direita" {{ $configuracoes['qrcode_posicao'] === 'lateral_direita' ? 'selected' : '' }}>Lateral Direita (Margem)</option>
                                        <option value="desabilitado" {{ $configuracoes['qrcode_posicao'] === 'desabilitado' ? 'selected' : '' }}>Não Exibir QR Code</option>
                                    </select>
                                    <div class="form-text">Escolha onde o QR Code será posicionado no documento</div>
                                </div>
                                <!--end::Input group-->

                                <!--begin::Input group-->
                                <div class="fv-row mb-7">
                                    <label class="form-label fw-semibold text-gray-900 fs-6">Tamanho do QR Code (pixels)</label>
                                    <input type="number" name="qrcode_tamanho" class="form-control form-control-lg" 
                                           value="{{ $configuracoes['qrcode_tamanho'] ?? '100' }}" min="50" max="300" step="10">
                                    <div class="form-text">Tamanho do QR Code entre 50 e 300 pixels</div>
                                </div>
                                <!--end::Input group-->

                                <!--begin::Input group-->
                                <div class="fv-row mb-7">
                                    <label class="form-label fw-semibold text-gray-900 fs-6">Texto do QR Code</label>
                                    <textarea name="qrcode_texto" class="form-control form-control-lg" rows="3" placeholder="Digite o texto que acompanha o QR Code">{{ $configuracoes['qrcode_texto'] ?? "Consulte este documento online:\nProtocolo: {numero_protocolo}" }}</textarea>
                                    <div class="form-text">
                                        Use as variáveis: <code>{numero_protocolo}</code>, <code>{numero_proposicao}</code>
                                    </div>
                                </div>
                                <!--end::Input group-->

                                <!--begin::Input group-->
                                <div class="fv-row mb-7">
                                    <label class="form-label fw-semibold text-gray-900 fs-6">URL do QR Code</label>
                                    <input type="text" name="qrcode_url_formato" class="form-control form-control-lg" 
                                           value="{{ $configuracoes['qrcode_url_formato'] ?? '{base_url}/proposicoes/consulta/{numero_protocolo}' }}"
                                           placeholder="{base_url}/proposicoes/consulta/{numero_protocolo}">
                                    <div class="form-text">
                                        Use as variáveis: <code>{base_url}</code>, <code>{numero_protocolo}</code>, <code>{numero_proposicao}</code>
                                    </div>
                                </div>
                                <!--end::Input group-->

                                <!--begin::Input group-->
                                <div class="fv-row mb-7">
                                    <div class="form-check form-switch form-check-custom form-check-success form-check-solid">
                                        <input class="form-check-input" type="checkbox" name="qrcode_apenas_protocolo" value="1" 
                                               {{ ($configuracoes['qrcode_apenas_protocolo'] ?? '1') == '1' ? 'checked' : '' }}>
                                        <label class="form-check-label fw-semibold text-gray-900 fs-6">
                                            Mostrar apenas após protocolo
                                        </label>
                                    </div>
                                    <div class="form-text">O QR Code só aparece no documento após ser protocolado</div>
                                </div>
                                <!--end::Input group-->

                                
                                <button type="submit" class="btn btn-primary">
                                    <i class="ki-duotone ki-check fs-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Salvar Configurações
                                </button>
                            </form>
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Card-->
                </div>
                <!--end::Col-->
            </div>
            <!--end::Row-->

            <!--begin::Preview Row-->
            <div class="row g-5 g-xl-10 mt-5">
                <div class="col-xl-12">
                    <!--begin::Card-->
                    <div class="card card-flush">
                        <!--begin::Header-->
                        <div class="card-header pt-7">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold text-gray-900">Preview das Configurações</span>
                                <span class="text-gray-500 mt-1 fw-semibold fs-6">Visualize como ficará a assinatura e QR Code no documento</span>
                            </h3>
                        </div>
                        <!--end::Header-->
                        <!--begin::Body-->
                        <div class="card-body pt-5">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="text-gray-900 fw-bold mb-3">Preview da Assinatura Digital:</h6>
                                    <div class="border border-2 border-success rounded p-4 bg-light-success">
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="fas fa-certificate text-success me-2"></i>
                                            <span class="fw-bold text-success">Assinatura Digital</span>
                                        </div>
                                        <div class="text-gray-700 fs-7">
                                            {{ str_replace(['{autor_nome}', '{autor_cargo}', '{data_assinatura}'], 
                                               ['João Silva', 'Vereador', date('d/m/Y H:i:s')], 
                                               $configuracoes['assinatura_texto'] ?? 'Documento assinado digitalmente por: {autor_nome}') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-gray-900 fw-bold mb-3">Preview do QR Code:</h6>
                                    <div class="border border-2 border-info rounded p-4 bg-light-info text-center">
                                        <div class="d-flex align-items-center justify-content-center mb-2">
                                            <i class="fas fa-qrcode text-info me-2"></i>
                                            <span class="fw-bold text-info">Verificação Online</span>
                                        </div>
                                        <div class="bg-white d-inline-block p-2 rounded mb-2" style="width: {{ $configuracoes['qrcode_tamanho'] ?? 100 }}px; height: {{ $configuracoes['qrcode_tamanho'] ?? 100 }}px;">
                                            <div class="d-flex align-items-center justify-content-center h-100">
                                                <i class="fas fa-qrcode text-gray-600" style="font-size: {{ (($configuracoes['qrcode_tamanho'] ?? 100) * 0.6) }}px;"></i>
                                            </div>
                                        </div>
                                        <div class="text-gray-700 fs-8">
                                            {{ str_replace(['{numero_protocolo}', '{numero_proposicao}'], 
                                               ['0001/2025', '123'], 
                                               $configuracoes['qrcode_texto'] ?? 'Consulte online: {numero_protocolo}') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Card-->
                </div>
            </div>
            <!--end::Preview Row-->

        </div>
        <!--end::Content container-->
    </div>
    <!--end::Content-->
@endsection

@section('scripts')
    <script>
        // Initialize Select2
        $(document).ready(function() {
            $('[data-control="select2"]').select2();
        });
    </script>
@endsection