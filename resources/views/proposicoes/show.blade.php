@extends('components.layouts.app')

@section('title', 'Visualizar Proposição')

@section('content')
<style>
/* Estilos otimizados para botões OnlyOffice */
.btn-onlyoffice {
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease;
    border-radius: 8px;
    font-weight: 600;
    padding: 12px 20px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.btn-onlyoffice:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.btn-onlyoffice .fas {
    transition: transform 0.3s ease;
}

.btn-onlyoffice:hover .fas {
    transform: scale(1.1);
}

/* Variações de cores para diferentes contextos */
.btn-onlyoffice.btn-primary {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    border: none;
}

.btn-onlyoffice.btn-outline-primary {
    border: 2px solid #007bff;
    background: rgba(0, 123, 255, 0.05);
}

.btn-onlyoffice.btn-outline-primary:hover {
    background: #007bff;
    color: white;
}

.btn-onlyoffice.btn-outline-warning {
    border: 2px solid #ffc107;
    background: rgba(255, 193, 7, 0.05);
}

.btn-onlyoffice.btn-outline-warning:hover {
    background: #ffc107;
    color: #212529;
}

/* Espaçamento melhorado para botões em grid */
.d-grid .btn-onlyoffice {
    margin-bottom: 8px;
}

.d-grid .btn-onlyoffice:last-child {
    margin-bottom: 0;
}

/* Estilos otimizados para botão de assinatura */
.btn-assinatura {
    font-weight: 600;
    padding: 12px 20px;
    border-radius: 8px;
}

.btn-assinatura:hover {
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

.btn-assinatura .fas {
    /* Removido animações que podem interferir com o clique */
}

/* Estilo específico para botão de assinatura success */
.btn-assinatura.btn-success {
    background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
    border: none;
}

.btn-assinatura.btn-success:hover {
    background: linear-gradient(135deg, #218838 0%, #1c7430 100%);
}

/* Espaçamento melhorado para botões de assinatura em grid */
.d-grid .btn-assinatura {
    margin-bottom: 8px;
    position: relative;
    z-index: 1;
    display: inline-block;
}

.d-grid .btn-assinatura:last-child {
    margin-bottom: 0;
}
</style>
<style>

.d-grid .btn-assinatura:last-child {
    margin-bottom: 0;
}
</style>

<style>

.d-grid .btn-assinatura:last-child {
    margin-bottom: 0;
}

/* Estilo melhorado para botão Assinar Documento */
.btn-assinatura-melhorado {
    background: linear-gradient(135deg, #198754 0%, #146c43 100%);
    border: none;
    border-radius: 10px;
    transition: all 0.3s ease;
    font-weight: 600;
    position: relative;
    overflow: hidden;
}

.btn-assinatura-melhorado:hover {
    background: linear-gradient(135deg, #157347 0%, #0f5132 100%);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(21, 115, 71, 0.4);
}

.btn-assinatura-melhorado:hover .fw-bold {
    color: #ffffff !important;
}

.btn-assinatura-melhorado:hover .text-muted {
    color: #e8f5e8 !important;
}

.btn-assinatura-melhorado:hover .ki-duotone {
    color: #ffffff !important;
}

.btn-assinatura-melhorado:active {
    transform: translateY(0);
    box-shadow: 0 2px 10px rgba(21, 115, 71, 0.3);
}
</style>

<div id="proposicao-app" v-cloak>
    <!--begin::Container-->
    <div class="container-fluid">
        <!--begin::Page Header-->
        <div class="row mb-4">
            <div class="col-12">
                <!--begin::Back Button-->
                <div class="mb-3">
                    <a :href="getBackUrl()" class="btn btn-secondary w-100">
                        <i class="ki-duotone ki-arrow-left fs-3 me-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        @{{ getBackButtonText() }}
                    </a>
                </div>
                <!--end::Back Button-->
                
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-gradient-primary text-white py-4">
                        <div class="d-flex align-items-start justify-content-between w-100">
                            <div class="flex-grow-0">
                                <h1 class="mb-1 fw-bold text-white d-flex align-items-center">
                                    <i class="ki-duotone ki-document fs-1 text-white me-3">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    @{{ proposicao.tipo?.toUpperCase() || 'PROPOSIÇÃO' }} #@{{ proposicao.id }}
                                </h1>
                                <p class="mb-0 text-white-75 fs-6 ms-9">
                                    Criado em @{{ formatDate(proposicao.created_at) }}
                                </p>
                            </div>
                            <div class="flex-grow-0 text-end">
                                <div class="mb-1">
                                    <span :class="getStatusBadgeClass(proposicao.status)" class="badge fs-5 px-4 py-2">
                                        @{{ getStatusText(proposicao.status) }}
                                    </span>
                                </div>
                                <div>
                                    <small class="text-white-75">
                                        Atualizado há @{{ getTimeAgo(proposicao.updated_at) }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--end::Page Header-->

        <!--begin::Main Content-->
        <div class="row g-4">
            <!--begin::Left Column - Content-->
            <div class="col-lg-8">
                <!--begin::Informações Básicas Card-->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-light">
                        <div class="d-flex align-items-center justify-content-between w-100">
                            <div class="flex-grow-0">
                                <h3 class="card-title m-0">
                                    <i class="ki-duotone ki-information-4 fs-2 text-primary me-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                    </i>
                                    Informações da Proposição
                                </h3>
                            </div>
                            <div class="flex-grow-0 d-flex align-items-center">
                                <span class="text-muted fs-7 me-3">Sincronização automática</span>
                                <div class="spinner-border spinner-border-sm text-primary" v-show="loading" role="status">
                                    <span class="visually-hidden">Carregando...</span>
                                </div>
                                <i v-show="!loading" class="ki-duotone ki-check-circle fs-2 text-success" title="Dados atualizados">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-50px me-3">
                                        <div class="symbol-label bg-light-primary">
                                            <i class="ki-duotone ki-profile-user fs-2 text-primary">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                                <span class="path3"></span>
                                                <span class="path4"></span>
                                            </i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="text-muted fs-7">Autor</div>
                                        <div class="fw-bold fs-6">@{{ proposicao.autor?.name || 'N/A' }}</div>
                                        <div class="text-muted fs-8">@{{ proposicao.autor?.email || '' }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-50px me-3">
                                        <div class="symbol-label bg-light-info">
                                            <i class="ki-duotone ki-code fs-2 text-info">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="text-muted fs-7">Protocolo</div>
                                        <div class="fw-bold fs-6" :class="proposicao.numero_protocolo ? 'text-success' : 'text-warning'">
                                            @{{ proposicao.numero_protocolo || '[Aguardando]' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-50px me-3">
                                        <div class="symbol-label bg-light-success">
                                            <i class="ki-duotone ki-file fs-2 text-success">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="text-muted fs-7">Documentos</div>
                                        <div class="fw-bold fs-6">
                                            <span v-if="proposicao.has_arquivo" class="badge badge-light-success me-1">DOCX</span>
                                            <span v-if="proposicao.has_pdf" class="badge badge-light-danger">PDF</span>
                                            <span v-if="!proposicao.has_arquivo && !proposicao.has_pdf" class="text-muted">Nenhum</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end::Informações Básicas Card-->

                <!--begin::Ementa Card-->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-light">
                        <h3 class="card-title m-0">
                            <i class="ki-duotone ki-notepad-edit fs-2 text-warning me-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            Ementa
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="fs-5 text-gray-800 lh-lg" v-if="proposicao.ementa">
                            @{{ proposicao.ementa }}
                        </div>
                        <div class="text-muted fst-italic" v-else>
                            Ementa não definida
                        </div>
                    </div>
                </div>
                <!--end::Ementa Card-->

                <!--begin::Anexos Card-->
                <div class="card shadow-sm border-0 mb-4" v-if="proposicao.anexos && proposicao.anexos.length > 0">
                    <div class="card-header bg-light">
                        <h3 class="card-title m-0">
                            <i class="ki-duotone ki-paper-clip fs-2 text-info me-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            Anexos (@{{ proposicao.total_anexos || proposicao.anexos.length }})
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Arquivo</th>
                                        <th>Tamanho</th>
                                        <th>Data Upload</th>
                                        <th class="text-end">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(anexo, index) in proposicao.anexos" :key="index">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i :class="getFileIcon(anexo.extensao)" class="fs-2x me-2"></i>
                                                <div>
                                                    <div class="fw-bold">@{{ anexo.nome_original }}</div>
                                                    <small class="text-muted">@{{ anexo.extensao.toUpperCase() }}</small>
                                                </div>
                                            </div>
            </td>
                                        <td>@{{ formatFileSize(anexo.tamanho) }}</a></td>
                                        <td>@{{ formatDate(anexo.uploaded_at) }}</a></td>
                                        <td class="text-end">
                                            <a :href="`/proposicoes/${proposicao.id}/anexo/${index}/download`" 
                                               class="btn btn-sm btn-light-primary me-1">
                                                <i class="ki-duotone ki-download fs-4">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                                                                Download</a><a :href="`/proposicoes/${proposicao.id}/anexo/${index}/view`" 
                                               target="_blank"
                                               class="btn btn-sm btn-light-info"
                                               v-if="['pdf', 'jpg', 'jpeg', 'png'].includes(anexo.extensao.toLowerCase())">
                                                <i class="ki-duotone ki-eye fs-4">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                    <span class="path3"></span>
                                                </i>
                                                Visualizar</a></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!--end::Anexos Card-->

                <!--begin::Conteúdo Card-->
                <div class="card shadow-sm border-0 content-card">
                    <div class="card-header bg-light">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <h3 class="card-title m-0">
                                    <i class="ki-duotone ki-document fs-2 text-success me-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Conteúdo da Proposição
                                </h3>
                                <!-- Badge de origem do conteúdo -->
                                <span v-if="proposicao.conteudo_origem" 
                                      :class="proposicao.conteudo_origem === 'onlyoffice' ? 'badge-light-success' : 'badge-light-primary'"
                                      class="badge ms-2">
                                    <i :class="proposicao.conteudo_origem === 'onlyoffice' ? 'ki-duotone ki-file-up' : 'ki-duotone ki-data'">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    @{{ proposicao.conteudo_origem === 'onlyoffice' ? 'OnlyOffice' : 'Database' }}
                                    <span v-if="proposicao.conteudo_timestamp" 
                                          class="text-muted ms-1">
                                        (@{{ formatTimestamp(proposicao.conteudo_timestamp) }})
                                    </span>
                                </span>
                            </div>
                            <div class="d-flex align-items-center" v-if="proposicao.conteudo && proposicao.conteudo.length > 500">
                                <button 
                                    @click="toggleContent" 
                                    class="btn btn-sm btn-light-primary">
                                    <i :class="showFullContent ? 'ki-duotone ki-up' : 'ki-duotone ki-down'" class="fs-3">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    @{{ showFullContent ? 'Mostrar Menos' : 'Mostrar Mais' }}
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Notificação de origem do conteúdo -->
                        <div v-if="proposicao.conteudo_origem === 'onlyoffice'" 
                             class="alert alert-light-success d-flex align-items-center p-5 mb-4">
                            <i class="ki-duotone ki-check-circle fs-2hx text-success me-4">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <div class="d-flex flex-column">
                                <h5 class="mb-1">Conteúdo atualizado do OnlyOffice</h5>
                                <span class="fs-7 fw-normal text-muted">
                                    Este conteúdo foi extraído em tempo real do arquivo salvo no OnlyOffice.
                                    <span v-if="proposicao.conteudo_timestamp">
                                        Última modificação: @{{ formatTimestamp(proposicao.conteudo_timestamp) }}
                                    </span>
                                </span>
                            </div>
                        </div>
                        
                        <div class="content-display" v-if="proposicao.conteudo">
                            <div v-if="proposicao.conteudo.length <= 500" class="fs-6 text-gray-700 lh-lg">
                                @{{ proposicao.conteudo }}
                            </div>
                            <div v-else>
                                <div v-show="!showFullContent" class="fs-6 text-gray-700 lh-lg">
                                    @{{ proposicao.conteudo.substring(0, 500) }}...
                                </div>
                                <div v-show="showFullContent" class="fs-6 text-gray-700 lh-lg">
                                    @{{ proposicao.conteudo }}
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-center py-5">
                            <i class="ki-duotone ki-file-added fs-3x text-muted mb-3">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <div class="text-muted">Nenhum conteúdo disponível</div>
                            <small class="text-muted">Adicione conteúdo editando a proposição</small>
                        </div>
                    </div>
                </div>
                <!--end::Conteúdo Card-->
            </div>
            <!--end::Left Column-->

            <!--begin::Right Column - Actions-->
            <div class="col-lg-4">
                <!--begin::Ações Card-->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-light">
                        <h3 class="card-title m-0">
                            <i class="ki-duotone ki-gear fs-2 text-primary me-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            Ações Disponíveis
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-3">
                            <!--begin::Status Messages-->
                            <div v-if="proposicao.status === 'em_revisao'" class="alert alert-info d-flex align-items-center">
                                <i class="ki-duotone ki-search-list fs-2x text-info me-3">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                                <div>
                                    <h6 class="alert-heading mb-1">Documento em Análise</h6>
                                    <p class="mb-0 small">O Legislativo está revisando este documento. Você será notificado quando houver atualizações.</p>
                                </div>
                            </div>
                            
                            <div v-if="proposicao.status === 'enviado_legislativo' && userRole !== 'LEGISLATIVO'" class="alert alert-warning d-flex align-items-center">
                                <i class="ki-duotone ki-time fs-2x text-warning me-3">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                <div>
                                    <h6 class="alert-heading mb-1">Aguardando Análise</h6>
                                    <p class="mb-0 small">Documento enviado ao Legislativo e aguardando início da revisão.</p>
                                </div>
                            </div>
                            
                            <div v-if="proposicao.status === 'enviado_legislativo' && userRole === 'LEGISLATIVO'" class="alert alert-info d-flex align-items-center">
                                <i class="ki-duotone ki-search-list fs-2x text-info me-3">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                                <div>
                                    <h6 class="alert-heading mb-1">Documento Recebido para Análise</h6>
                                    <p class="mb-0 small">Este documento foi enviado para sua revisão. Você pode editar ou tomar uma decisão.</p>
                                </div>
                            </div>
                            
                            <div v-if="proposicao.status === 'aprovado'" class="alert alert-success d-flex align-items-center">
                                <i class="ki-duotone ki-check-circle fs-2x text-success me-3">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                <div>
                                    <h6 class="alert-heading mb-1">Documento Aprovado</h6>
                                    <p class="mb-0 small">Este documento foi aprovado pelo Legislativo e está pronto para assinatura.</p>
                                </div>
                            </div>
                            
                            <div v-if="proposicao.status === 'devolvido_edicao'" class="alert alert-warning d-flex align-items-center">
                                <i class="ki-duotone ki-arrow-left fs-2x text-warning me-3">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                <div>
                                    <h6 class="alert-heading mb-1">Devolvido para Correções</h6>
                                    <p class="mb-0 small">O Legislativo solicitou ajustes neste documento. Por favor, revise as observações.</p>
                                </div>
                            </div>
                            <!--end::Status Messages-->
                            
                            <!--begin::OnlyOffice Editor-->
                            <div v-if="canEdit()">
                                <a 
                                    :href="getEditorUrl()"
                                    class="btn btn-primary btn-lg w-100 d-flex align-items-center justify-content-center"
                                    style="min-height: 50px;">
                                    <i class="ki-duotone ki-file-edit fs-2 me-3">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    <div class="text-start">
                                        <div class="fw-bold">@{{ getEditorButtonText() }}</div>
                                        <small class="text-white-75">Editor OnlyOffice</small>
                                    </div>
                                </a>
                            </div>
                            <!--end::OnlyOffice Editor-->

                            <!--begin::Enviar para Legislativo-->
                            <div v-if="canSendToLegislative()">
                                <form 
                                    :action="'/proposicoes/' + proposicao.id + '/enviar-legislativo'" 
                                    method="POST" 
                                    @submit="confirmSendToLegislative">
                                    @csrf
                                    @method('PUT')
                                    <button 
                                        type="submit" 
                                        class="btn btn-success btn-lg w-100 d-flex align-items-center justify-content-center"
                                        style="min-height: 50px;">
                                        <i class="ki-duotone ki-send fs-2 me-3">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        <div class="text-start">
                                            <div class="fw-bold">Enviar para Legislativo</div>
                                            <small class="text-white-75">Submeter para análise</small>
                                        </div>
                                    </button>
                                </form>
                            </div>
                            <!--end::Enviar para Legislativo-->

                            <!--begin::Status Actions for Legislative-->
                            <div v-if="canUpdateStatus()">
                                <div class="separator my-3"></div>
                                <h6 class="text-muted mb-3">Ações do Legislativo</h6>
                                
                                <!--begin::Edit Document Button-->
                                <a 
                                    :href="'/proposicoes/' + proposicao.id + '/onlyoffice/editor'"
                                    class="btn btn-primary w-100 mb-2">
                                    <i class="ki-duotone ki-file-edit fs-3 me-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Revisar Documento
                                </a>
                                <!--end::Edit Document Button-->
                                
                                <form 
                                    :action="'/proposicoes/' + proposicao.id + '/status'" 
                                    method="POST" 
                                    @submit="updateStatusAction"
                                    class="mb-2">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="aprovado">
                                    <button type="submit" class="btn btn-success w-100">
                                        <i class="ki-duotone ki-check fs-3 me-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        Aprovar Proposição
                                    </button>
                                </form>

                                <form 
                                    :action="'/proposicoes/' + proposicao.id + '/status'" 
                                    method="POST" 
                                    @submit="updateStatusAction"
                                    class="mb-2">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="devolvido_edicao">
                                    <button type="submit" class="btn btn-warning w-100">
                                        <i class="ki-duotone ki-arrow-left fs-3 me-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        Devolver ao Parlamentar
                                    </button>
                                </form>

                                <form 
                                    :action="'/proposicoes/' + proposicao.id + '/status'" 
                                    method="POST" 
                                    @submit="updateStatusAction">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="reprovado">
                                    <button type="submit" class="btn btn-danger w-100">
                                        <i class="ki-duotone ki-cross fs-3 me-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        Reprovar Proposição
                                    </button>
                                </form>
                            </div>
                            <!--end::Status Actions-->

                            <!--begin::Sign Document-->
                            <div v-if="canSign()">
                                <div class="separator my-3"></div>
                                <a 
                                    :href="'/proposicoes/' + proposicao.id + '/assinatura-digital'"
                                    class="btn btn-light-success btn-lg w-100 d-flex align-items-center justify-content-center btn-assinatura-melhorado"
                                    style="min-height: 50px;">
                                    <i class="ki-duotone ki-signature fs-2 me-3">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    <div class="text-start">
                                        <div class="fw-bold">Assinar Documento</div>
                                        <small class="text-muted">Assinatura digital com certificado</small>
                                    </div></a></div>
                            <!--end::Sign Document-->

                            <!--begin::View PDF-->
                            <div v-if="proposicao.has_pdf && canViewPDF()">
                                <a 
                                    :href="'/proposicoes/' + proposicao.id + '/pdf'"
                                    target="_blank"
                                    class="btn btn-light-secondary w-100 mb-3">
                                    <i class="ki-duotone ki-file fs-3 me-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Visualizar PDF
                                </a>
                            </div>
                            <!--end::View PDF-->

                            <!--begin::Delete Proposition Complete-->
                            <div v-if="podeExcluirDocumento()">
                                <button 
                                    type="button" 
                                    @click="confirmarExclusaoDocumento" 
                                    class="btn btn-light-danger w-100 mb-3">
                                    <i class="ki-duotone ki-trash fs-3 me-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                        <span class="path4"></span>
                                        <span class="path5"></span>
                                    </i>
                                    Excluir Proposição
                                    <div class="fs-7 text-muted">Remove completamente do sistema</div>
                                </button>
                            </div>
                            <!--end::Delete Proposition Complete-->

                            
                            <!--begin::Refresh-->
                            <div class="separator my-3"></div>
                            <button 
                                @click="forceRefresh" 
                                class="btn btn-light btn-sm w-100"
                                :disabled="loading" type="button">
                                <i class="ki-duotone ki-arrows-circle fs-4 me-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                @{{ loading ? 'Atualizando...' : 'Atualizar Dados' }}
                            </button>
                            
                            <!--begin::Separator-->
                            <div class="separator my-3"></div>
                            <!--end::Separator-->
                            
                            <!--begin::Back to List Button-->
                            <a :href="getBackUrl()" class="btn btn-secondary w-100">
                                <i class="ki-duotone ki-arrow-left fs-3 me-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                @{{ getBackButtonText() }}
                            </a>
                            <!--end::Back to List Button-->
                        </div>
                    </div>
                </div>
                <!--end::Ações Card-->

                <!--begin::Histórico Card-->
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-light">
                        <h3 class="card-title m-0">
                            <i class="ki-duotone ki-time fs-2 text-info me-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            Histórico de Tramitação
                        </h3>
                    </div>
                    <div class="card-body p-6">
                        <div class="timeline timeline-enhanced">
                            <div v-for="(evento, index) in timeline" :key="index" class="timeline-item mb-6">
                                <div class="timeline-line" v-if="index < timeline.length - 1"></div>
                                <div class="timeline-icon">
                                    <div :class="'badge badge-circle badge-' + evento.color + ' shadow-sm'">
                                        <i :class="evento.icon + ' fs-4'" style="color: white;">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </div>
                                </div>
                                <div class="timeline-content ms-3">
                                    <div class="timeline-item-wrapper bg-white rounded p-4 border">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h5 class="mb-0 fw-bolder text-gray-800">@{{ evento.title }}</h5>
                                            <span class="badge badge-light-info fs-8 px-3 py-1">
                                                @{{ formatDateTime(evento.date) }}
                                            </span>
                                        </div>
                                        <p class="text-gray-600 mb-0 fs-6 lh-lg">@{{ evento.description }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end::Histórico Card-->
            </div>
            <!--end::Right Column-->
        </div>
        <!--end::Main Content-->
    </div>
    <!--end::Container-->

    <!--begin::Toast Notifications-->
    <div class="position-fixed top-0 end-0 p-3" style="z-index: 1080">
        <div 
            v-for="(toast, index) in toasts" 
            :key="index"
            :class="'toast align-items-center text-white bg-' + toast.type + ' border-0'"
            role="alert" 
            :class="{ show: toast.show }"
            style="min-width: 300px;">
            <div class="d-flex">
                <div class="toast-body">
                    <i :class="toast.icon + ' me-2'"></i>
                    @{{ toast.message }}
                </div>
                <button 
                    type="button" 
                    class="btn-close btn-close-white me-2 m-auto" 
                    @click="removeToast(index)">
                </button>
            </div>
        </div>
    </div>
    <!--end::Toast Notifications-->
</div>

<!-- Vue 3 -->
<script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>

<style>
[v-cloak] { display: none; }

.bg-gradient-primary {
    background: linear-gradient(135deg, #3699FF 0%, #0BB783 100%);
}

.content-display {
    max-height: none;
    overflow: visible;
    white-space: pre-wrap;
    word-wrap: break-word;
}

/* Enhanced Timeline Styles */
.timeline-enhanced {
    position: relative;
    padding-left: 0;
}

.timeline-enhanced .timeline-item {
    position: relative;
    display: flex;
    align-items: flex-start;
    padding-left: 0;
}

.timeline-enhanced .timeline-item:last-child {
    margin-bottom: 0 !important;
}

.timeline-enhanced .timeline-line {
    position: absolute;
    left: 21px;
    top: 44px;
    bottom: -1.5rem;
    width: 2px;
    background: #E1E3EA;
    border-radius: 1px;
    z-index: 0;
}

.timeline-enhanced .timeline-icon {
    position: relative;
    z-index: 2;
    flex-shrink: 0;
}

.timeline-enhanced .timeline-icon .badge-circle {
    width: 44px;
    height: 44px;
    border: 3px solid #fff;
}

.timeline-enhanced .timeline-content {
    flex: 1;
    min-width: 0;
    margin-top: -2px;
}

.timeline-enhanced .timeline-item-wrapper {
    background: #ffffff;
    border: 1px solid #e9ecef;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

/* Legacy timeline support */
.timeline {
    position: relative;
}

.timeline-item {
    position: relative;
    padding-bottom: 2rem;
    display: flex;
    align-items: flex-start;
}

.timeline-item:last-child {
    padding-bottom: 0;
}

.timeline-line {
    position: absolute;
    left: 19px;
    top: 38px;
    bottom: -2rem;
    width: 2px;
    background: #E1E3EA;
}

.timeline-icon {
    position: relative;
    z-index: 1;
    flex-shrink: 0;
}

.timeline-content {
    flex: 1;
}

.badge-circle {
    width: 44px !important;
    height: 44px !important;
    border-radius: 50% !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    position: relative;
    overflow: hidden;
    min-width: 44px !important;
    max-width: 44px !important;
    padding: 0 !important;
}

/* Enhanced badge with better visual hierarchy */
.timeline-enhanced .badge-circle {
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.timeline-enhanced .badge-circle::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(45deg, rgba(255,255,255,0.1), rgba(255,255,255,0.3));
    border-radius: 50%;
    pointer-events: none;
}

/* Icon centering fix */
.badge-circle i {
    position: relative;
    z-index: 1;
    line-height: 1;
}

.badge-circle .ki-duotone {
    display: flex;
    align-items: center;
    justify-content: center;
}

.toast {
    margin-bottom: 0.5rem;
}

.btn:hover {
    transform: translateY(-1px);
    transition: all 0.2s ease;
}

.card {
    transition: box-shadow 0.15s ease;
}

.card:hover {
    box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.1) !important;
}

/* SweetAlert2 Modal Customizado para Exclusão */
.swal2-large-modal {
    max-width: 800px !important;
    width: 95% !important;
}

@media (max-width: 768px) {
    .swal2-large-modal {
        width: 100% !important;
        margin: 0.5rem !important;
    }
}

.swal2-large-modal .swal2-html-container {
    max-height: 600px !important;
    overflow-y: auto !important;
}

.swal2-large-modal .alert {
    margin-bottom: 0 !important;
}

.swal2-large-modal .btn-lg {
    font-size: 1.1rem !important;
    padding: 0.75rem 1.5rem !important;
}
</style>

<script>
const { createApp } = Vue;

createApp({
    data() {
        return {
            proposicao: @json($proposicao ?? null),
            userRole: '{{ strtoupper(auth()->user()->getRoleNames()->first() ?? "guest") }}',
            userId: {{ auth()->user()->id ?? 0 }},
            csrfToken: '{{ csrf_token() }}',
            loading: false,
            lastUpdate: null,
            pollingInterval: null,
            showFullContent: false,
            toasts: [],
            timeline: []
        }
    },
    
    mounted() {
        console.log('User Role:', this.userRole);
        console.log('User ID:', this.userId);
        console.log('Proposicao Status:', this.proposicao?.status);
        console.log('Can Update Status:', this.canUpdateStatus());
        console.log('Can Legislative Edit:', this.canLegislativeEdit());
        
        this.setupFetch();
        
        // Clean initial data from Blade template
        this.cleanProposicaoData();
        
        // Generate timeline with initial data from Blade
        this.generateTimeline();
        
        // Start polling for updates using web routes
        this.startPolling();
    },
    
    beforeUnmount() {
        this.stopPolling();
    },
    
    methods: {
        setupFetch() {
            // Configure default headers for fetch requests
            this.defaultHeaders = {
                'X-CSRF-TOKEN': this.csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            };
        },

        logPDFClick() {
            // Log específico para debug quando usuário clica para visualizar PDF
            const proposicaoId = this.proposicao?.id || 'N/A';
            const status = this.proposicao?.status || 'N/A';
            const hasPdf = this.proposicao?.has_pdf || false;
            
            console.log('🔴 DEBUG PDF: Usuário clicou para visualizar PDF', {
                proposicao_id: proposicaoId,
                status: status,
                has_pdf: hasPdf,
                url: `/proposicoes/${proposicaoId}/pdf`,
                timestamp: new Date().toISOString(),
                user_action: 'click_visualizar_pdf'
            });
            
            // Se debug logger estiver ativo, registrar ação também lá
            if (window.debugLoggerApp && window.debugLoggerApp._instance) {
                const debugInstance = window.debugLoggerApp._instance.proxy;
                if (debugInstance.isRecording) {
                    debugInstance.logAction('user_interaction', `Clique em "Visualizar PDF" - Proposição ${proposicaoId}`, {
                        proposicao_id: proposicaoId,
                        status: status,
                        has_pdf: hasPdf,
                        url: `/proposicoes/${proposicaoId}/pdf`,
                        action_type: 'pdf_view_request'
                    });
                }
            }
        },

        cleanProposicaoData() {
            // Simplificar a limpeza dos dados - apenas garantir que temos valores válidos
            if (!this.proposicao) return;
            
            let ementa = this.proposicao.ementa || '';
            let conteudo = this.proposicao.conteudo || '';
            
            // Fallbacks simples para dados vazios
            if (!ementa || ementa === 'Criado pelo Parlamentar') {
                ementa = 'Moção em elaboração';
            }
            
            if (!conteudo) {
                conteudo = 'Conteúdo em elaboração pelo parlamentar';
            }
            
            // Atualizar apenas se necessário
            this.proposicao.ementa = ementa;
            this.proposicao.conteudo = conteudo;
        },
        
        async makeRequest(url, options = {}) {
            const config = {
                credentials: 'same-origin',
                headers: { ...this.defaultHeaders, ...(options.headers || {}) },
                ...options
            };
            
            try {
                const response = await fetch(url, config);
                
                if (response.status === 401 || response.status === 403) {
                    this.showToast('Sessão expirada. Recarregando página...', 'warning', 'ki-duotone ki-warning');
                    setTimeout(() => window.location.reload(), 2000);
                    return null;
                }
                
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                
                return await response.json();
            } catch (error) {
                console.error('Request error:', error);
                throw error;
            }
        },
        
        async loadProposicao() {
            if (!this.proposicao?.id) return;
            
            this.loading = true;
            try {
                // Use traditional Laravel web route to get fresh data
                const data = await this.makeRequest(`/proposicoes/${this.proposicao.id}/dados-frescos`);
                if (data && data.success) {
                    // Store previous status to check for changes
                    const previousStatus = this.proposicao.status;
                    
                    // Update only the necessary fields to maintain reactivity
                    Object.assign(this.proposicao, data.proposicao);
                    this.lastUpdate = new Date().toISOString();
                    this.generateTimeline();
                    
                    // If status changed, show notification
                    if (previousStatus !== this.proposicao.status) {
                        this.showToast(
                            `Status atualizado para: ${this.getStatusText(this.proposicao.status)}`, 
                            'info', 
                            'ki-duotone ki-information'
                        );
                    }
                    
                    // Force Vue reactivity update
                    this.$nextTick(() => {
                        this.$forceUpdate();
                    });
                }
            } catch (error) {
                console.error('Erro ao carregar proposição:', error);
                // Silent fail for background updates - only show error on manual refresh
                if (this.loading) {
                    this.showToast('Erro ao atualizar dados', 'warning', 'ki-duotone ki-warning');
                }
            } finally {
                this.loading = false;
            }
        },
        
        startPolling() {
            // Polling inteligente a cada 30 segundos
            this.pollingInterval = setInterval(() => {
                if (!document.hidden) { // Só atualiza se a aba estiver visível
                    this.checkForUpdates();
                }
            }, 30000);
        },
        
        stopPolling() {
            if (this.pollingInterval) {
                clearInterval(this.pollingInterval);
                this.pollingInterval = null;
            }
        },
        
        async checkForUpdates() {
            if (!this.proposicao?.id || this.loading) return;
            
            // Capturar estado anterior para comparação
            const conteudoAnterior = this.proposicao.conteudo;
            const timestampAnterior = this.proposicao.ultima_modificacao;
            
            // Recarregar dados
            await this.loadProposicao();
            
            // Verificar se houve mudança no conteúdo
            if (this.proposicao.conteudo !== conteudoAnterior) {
                // Mostrar notificação de sincronização
                this.showToast(
                    'Conteúdo atualizado automaticamente via OnlyOffice', 
                    'success', 
                    'ki-duotone ki-arrows-loop'
                );
                
                // Adicionar efeito visual temporário
                this.highlightContentUpdate();
            }
        },

        highlightContentUpdate() {
            // Destacar temporariamente a seção de conteúdo
            const contentCard = document.querySelector('.content-card');
            if (contentCard) {
                contentCard.classList.add('border-success');
                contentCard.style.transition = 'all 0.3s ease';
                
                setTimeout(() => {
                    contentCard.classList.remove('border-success');
                }, 3000);
            }
        },
        
        async forceRefresh() {
            await this.loadProposicao();
            this.showToast('Dados atualizados manualmente', 'info', 'ki-duotone ki-arrows-circle');
        },
        
        generateTimeline() {
            if (!this.proposicao) return;
            
            const events = [];
            
            // Evento de criação
            events.push({
                title: 'Proposição Criada',
                description: `Por ${this.proposicao.autor?.name || 'N/A'}`,
                date: this.proposicao.created_at,
                icon: 'ki-duotone ki-plus',
                color: 'primary'
            });
            
            // Eventos baseados no status
            const status = this.proposicao.status;
            
            if (['em_edicao', 'enviado_legislativo', 'em_revisao', 'aprovado', 'reprovado', 'devolvido_edicao'].includes(status)) {
                events.push({
                    title: 'Em Edição',
                    description: 'Conteúdo sendo elaborado',
                    date: this.proposicao.updated_at,
                    icon: 'ki-duotone ki-pencil',
                    color: 'warning'
                });
            }
            
            if (['enviado_legislativo', 'em_revisao', 'aprovado', 'reprovado'].includes(status)) {
                events.push({
                    title: 'Enviado ao Legislativo',
                    description: 'Para análise técnica',
                    date: this.proposicao.updated_at,
                    icon: 'ki-duotone ki-send',
                    color: 'info'
                });
            }
            
            if (['em_revisao', 'aprovado', 'reprovado'].includes(status)) {
                events.push({
                    title: 'Em Revisão',
                    description: 'Análise do Legislativo',
                    date: this.proposicao.updated_at,
                    icon: 'ki-duotone ki-search-list',
                    color: 'primary'
                });
            }
            
            if (status === 'aprovado') {
                events.push({
                    title: 'Aprovado',
                    description: 'Proposição aprovada',
                    date: this.proposicao.updated_at,
                    icon: 'ki-duotone ki-check-circle',
                    color: 'success'
                });
                
                if (this.proposicao.numero_protocolo) {
                    events.push({
                        title: 'Protocolado',
                        description: `Nº ${this.proposicao.numero_protocolo}`,
                        date: this.proposicao.updated_at,
                        icon: 'ki-duotone ki-hashtag',
                        color: 'success'
                    });
                }
            }
            
            if (status === 'reprovado') {
                events.push({
                    title: 'Reprovado',
                    description: 'Proposição não aprovada',
                    date: this.proposicao.updated_at,
                    icon: 'ki-duotone ki-cross-circle',
                    color: 'danger'
                });
            }
            
            if (status === 'devolvido_edicao') {
                events.push({
                    title: 'Devolvido para Edição',
                    description: 'Necessita ajustes',
                    date: this.proposicao.updated_at,
                    icon: 'ki-duotone ki-arrow-left',
                    color: 'warning'
                });
            }
            
            this.timeline = events.reverse();
        },
        
        // Utility Methods
        toggleContent() {
            this.showFullContent = !this.showFullContent;
        },

        formatTimestamp(timestamp) {
            if (!timestamp) return '';
            
            const date = new Date(timestamp * 1000); // Convert from Unix timestamp
            const now = new Date();
            const diffMs = now - date;
            const diffMins = Math.floor(diffMs / 60000);
            const diffHours = Math.floor(diffMs / 3600000);
            const diffDays = Math.floor(diffMs / 86400000);
            
            if (diffMins < 1) return 'agora';
            if (diffMins < 60) return `${diffMins}min atrás`;
            if (diffHours < 24) return `${diffHours}h atrás`;
            if (diffDays < 7) return `${diffDays}d atrás`;
            
            return date.toLocaleDateString('pt-BR', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        },
        
        getFileIcon(extension) {
            const ext = (extension || '').toLowerCase();
            const icons = {
                'pdf': 'ki-duotone ki-file-pdf text-danger',
                'doc': 'ki-duotone ki-file-word text-primary',
                'docx': 'ki-duotone ki-file-word text-primary',
                'xls': 'ki-duotone ki-file-excel text-success',
                'xlsx': 'ki-duotone ki-file-excel text-success',
                'jpg': 'ki-duotone ki-file-image text-info',
                'jpeg': 'ki-duotone ki-file-image text-info',
                'png': 'ki-duotone ki-file-image text-info'
            };
            return icons[ext] || 'ki-duotone ki-file text-muted';
        },
        
        formatFileSize(bytes) {
            if (!bytes || bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
        },
        
        formatDate(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            return date.toLocaleDateString('pt-BR', { 
                day: '2-digit', 
                month: '2-digit', 
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        },
        
        canEdit() {
            if (!this.proposicao) return false;
            const isOwner = this.proposicao.autor_id === this.userId;
            return (isOwner || this.userRole === 'PARLAMENTAR') && 
                   ['rascunho', 'em_edicao', 'devolvido_edicao'].includes(this.proposicao.status);
        },
        
        canDelete() {
            if (!this.proposicao) return false;
            const isOwner = this.proposicao.autor_id === this.userId;
            return (isOwner || this.userRole === 'PARLAMENTAR') && 
                   ['rascunho', 'em_edicao'].includes(this.proposicao.status);
        },
        
        canSendToLegislative() {
            if (!this.proposicao) return false;
            const isOwner = this.proposicao.autor_id === this.userId;
            return (isOwner || this.userRole === 'PARLAMENTAR') && 
                   ['rascunho', 'em_edicao', 'devolvido_edicao'].includes(this.proposicao.status) &&
                   this.proposicao.ementa && this.proposicao.conteudo;
        },
        
        canUpdateStatus() {
            const result = this.userRole === 'LEGISLATIVO' && 
                          ['em_revisao', 'enviado_legislativo'].includes(this.proposicao?.status);
            console.log('canUpdateStatus check:', {
                userRole: this.userRole,
                isLegislativo: this.userRole === 'LEGISLATIVO',
                proposicaoStatus: this.proposicao?.status,
                validStatuses: ['em_revisao', 'enviado_legislativo'].includes(this.proposicao?.status),
                result: result
            });
            return result;
        },
        
        canLegislativeEdit() {
            return this.userRole === 'LEGISLATIVO' && 
                   ['em_revisao', 'enviado_legislativo'].includes(this.proposicao.status);
        },
        
        canSign() {
            if (!this.proposicao) return false;
            const isOwner = this.proposicao.autor_id === this.userId;
            // Botão só aparece quando status é exatamente 'aprovado'
            const canSignStatuses = ['aprovado'];
            const hasPermission = isOwner || this.userRole === 'PARLAMENTAR';
            const statusAllowed = canSignStatuses.includes(this.proposicao.status);
            
            console.log('canSign check:', {
                status: this.proposicao.status,
                statusAllowed,
                isOwner,
                userRole: this.userRole,
                hasPermission,
                result: statusAllowed && hasPermission
            });
            
            return statusAllowed && hasPermission;
        },
        
        canViewPDF() {
            if (!this.proposicao) return false;
            
            // PDF só pode ser visualizado após aprovação pelo Legislativo (para todos os usuários)
            const allowedStatuses = ['aprovado', 'aprovado_assinatura'];
            return allowedStatuses.includes(this.proposicao.status);
        },
        
        getEditorUrl() {
            if (!this.proposicao) return '#';
            const isOwner = this.proposicao.autor_id === this.userId;
            return isOwner || this.userRole === 'PARLAMENTAR' 
                ? `/proposicoes/${this.proposicao.id}/onlyoffice/editor-parlamentar`
                : `/proposicoes/${this.proposicao.id}/onlyoffice/editor`;
        },
        
        getEditorButtonText() {
            if (!this.proposicao) return 'Editar';
            return this.proposicao.conteudo ? 'Continuar Editando' : 'Adicionar Conteúdo';
        },
        
        getBackUrl() {
            return this.userRole === 'LEGISLATIVO' ? '/proposicoes/legislativo' : '/proposicoes/minhas-proposicoes';
        },
        
        getBackButtonText() {
            return this.userRole === 'LEGISLATIVO' ? 'Voltar ao Legislativo' : 'Voltar para Minhas Proposições';
        },
        
        getStatusBadgeClass(status) {
            const classes = {
                'rascunho': 'badge-warning',
                'em_edicao': 'badge-warning',
                'enviado_legislativo': 'badge-secondary',
                'em_revisao': 'badge-primary',
                'aguardando_aprovacao_autor': 'badge-primary',
                'devolvido_edicao': 'badge-warning',
                'retornado_legislativo': 'badge-info',
                'aprovado': 'badge-success',
                'aprovado_assinatura': 'badge-primary',
                'assinado': 'badge-success',
                'enviado_protocolo': 'badge-info',
                'protocolado': 'badge-primary',
                'reprovado': 'badge-danger'
            };
            return classes[status] || 'badge-secondary';
        },
        
        getStatusIcon(status) {
            const icons = {
                'rascunho': 'ki-duotone ki-pencil',
                'em_edicao': 'ki-duotone ki-pencil',
                'enviado_legislativo': 'ki-duotone ki-send',
                'em_revisao': 'ki-duotone ki-search-list',
                'aguardando_aprovacao_autor': 'ki-duotone ki-time',
                'devolvido_edicao': 'ki-duotone ki-arrow-left',
                'retornado_legislativo': 'ki-duotone ki-arrow-right',
                'aprovado': 'ki-duotone ki-check-circle',
                'reprovado': 'ki-duotone ki-cross-circle'
            };
            return icons[status] || 'ki-duotone ki-question';
        },
        
        getStatusText(status) {
            const texts = {
                'rascunho': 'Rascunho',
                'em_edicao': 'Em Edição',
                'enviado_legislativo': 'Enviado ao Legislativo',
                'em_revisao': 'Em Revisão',
                'aguardando_aprovacao_autor': 'Aguardando Aprovação',
                'devolvido_edicao': 'Devolvido para Edição',
                'retornado_legislativo': 'Retornado do Legislativo',
                'aprovado': 'Aprovado',
                'aprovado_assinatura': 'Aguardando Assinatura',
                'assinado': 'Assinado',
                'enviado_protocolo': 'Enviado ao Protocolo',
                'protocolado': 'Protocolado',
                'reprovado': 'Reprovado'
            };
            return texts[status] || 'Status Desconhecido';
        },
        
        formatDate(dateString) {
            if (!dateString) return 'N/A';
            return new Date(dateString).toLocaleDateString('pt-BR');
        },
        
        formatDateTime(dateString) {
            if (!dateString) return 'N/A';
            return new Date(dateString).toLocaleString('pt-BR');
        },
        
        getTimeAgo(dateString) {
            if (!dateString) return 'N/A';
            const now = new Date();
            const date = new Date(dateString);
            const diffInMinutes = Math.floor((now - date) / 1000 / 60);
            
            if (diffInMinutes < 1) return 'agora mesmo';
            if (diffInMinutes < 60) return `${diffInMinutes} min atrás`;
            if (diffInMinutes < 1440) return `${Math.floor(diffInMinutes / 60)}h atrás`;
            return `${Math.floor(diffInMinutes / 1440)}d atrás`;
        },
        
        // Event Handlers
        async confirmSendToLegislative(event) {
            event.preventDefault(); // Prevent default form submission
            
            try {
                const result = await Swal.fire({
                    title: '📤 Enviar para o Legislativo',
                    html: `
                        <div class="text-start">
                            <h5 class="mb-4">Deseja confirmar o envio desta proposição?</h5>
                            
                            <div class="mb-3">
                                <strong>Resumo:</strong>
                            </div>
                            
                            <div class="bg-light rounded p-3 mb-3">
                                <div class="mb-2">
                                    <strong>Tipo:</strong> ${this.proposicao.tipo || 'Moção'}
                                </div>
                                <div class="mb-2">
                                    <strong>Autor:</strong> ${this.proposicao.autor?.name || 'Parlamentar'}
                                </div>
                                <div class="mb-0">
                                    <strong>Ementa:</strong> "${this.proposicao.ementa}"
                                </div>
                            </div>
                            
                            <div class="alert alert-warning d-flex align-items-center mb-0">
                                <span class="me-2">⚠️</span>
                                <span>Após o envio, não será possível editar esta proposição.</span>
                            </div>
                        </div>
                    `,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: '📤 Confirmar envio',
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#6c757d',
                    width: '550px',
                    customClass: {
                        popup: 'swal2-modern',
                        confirmButton: 'btn btn-primary',
                        cancelButton: 'btn btn-secondary'
                    },
                    buttonsStyling: false
                });

                if (result.isConfirmed) {
                    // Show loading state
                    Swal.fire({
                        title: 'Enviando...',
                        html: 'Processando envio para o Legislativo...',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        willOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Submit the form
                    await this.submitToLegislative(event.target.closest('form'));
                }
            } catch (error) {
                console.error('Erro ao enviar para legislativo:', error);
                this.showErrorAlert('Erro inesperado ao processar solicitação');
            }
        },

        async submitToLegislative(form) {
            try {
                const formData = new FormData(form);
                // FormData will already include _method=PUT from @method('PUT') directive
                
                // Para FormData, precisamos remover o Content-Type header
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin',
                    headers: {
                        'X-CSRF-TOKEN': this.csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                        // NÃO incluir Content-Type para FormData
                    }
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                
                const data = await response.json();

                if (data && data.success) {
                    // Update proposição data immediately
                    if (data.proposicao) {
                        Object.assign(this.proposicao, data.proposicao);
                        this.generateTimeline();
                    } else {
                        // If no proposicao data returned, force reload from server
                        this.proposicao.status = 'enviado_legislativo';
                        this.generateTimeline();
                    }
                    
                    // Success notification
                    await Swal.fire({
                        title: 'Sucesso!',
                        html: `
                            <div class="text-center">
                                <div class="mb-3">
                                    <i class="ki-duotone ki-check-circle fs-3x text-success">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                </div>
                                <p class="mb-2">Proposição enviada com sucesso para o Legislativo!</p>
                                <small class="text-muted">O status foi atualizado automaticamente</small>
                            </div>
                        `,
                        icon: 'success',
                        confirmButtonText: 'Continuar',
                        confirmButtonColor: '#198754',
                        customClass: {
                            confirmButton: 'btn btn-success'
                        },
                        buttonsStyling: false
                    });

                    // Show success toast
                    this.showToast(
                        'Proposição enviada para análise do Legislativo', 
                        'success', 
                        'ki-duotone ki-send'
                    );
                    
                    // Force a complete refresh after a short delay to ensure server has processed
                    setTimeout(async () => {
                        await this.loadProposicao();
                        // Force Vue to re-render the interface
                        this.$forceUpdate();
                    }, 500);
                    
                } else {
                    throw new Error(data?.message || 'Erro ao enviar proposição');
                }
            } catch (error) {
                console.error('Erro no envio:', error);
                this.showErrorAlert(error.message || 'Erro ao enviar proposição para o Legislativo');
            }
        },

        showErrorAlert(message) {
            Swal.fire({
                title: 'Erro!',
                html: `
                    <div class="text-center">
                        <div class="mb-3">
                            <i class="ki-duotone ki-cross-circle fs-3x text-danger">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                        </div>
                        <p class="mb-2">${message}</p>
                        <small class="text-muted">Tente novamente ou contate o suporte técnico</small>
                    </div>
                `,
                icon: 'error',
                confirmButtonText: 'Fechar',
                confirmButtonColor: '#dc3545',
                customClass: {
                    confirmButton: 'btn btn-danger'
                },
                buttonsStyling: false
            });
        },
        
        async confirmDeleteProposicao() {
            try {
                const result = await Swal.fire({
                    title: 'Excluir Documento',
                    html: `
                        <div class="text-center">
                            <div class="mb-4">
                                <i class="ki-duotone ki-trash fs-3x text-danger mb-3">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                    <span class="path5"></span>
                                </i>
                                <h5 class="mb-2 text-danger">Tem certeza que deseja excluir?</h5>
                            </div>
                            <div class="bg-light-danger rounded p-3 mb-3">
                                <div class="fw-bold text-danger mb-1">Ementa:</div>
                                <div class="text-dark">"${this.proposicao.ementa}"</div>
                            </div>
                            <div class="text-warning fs-7">
                                <i class="ki-duotone ki-information-5 me-1">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                                Esta ação é irreversível e excluirá todos os dados
                            </div>
                        </div>
                    `,
                    showCancelButton: true,
                    confirmButtonText: '<i class="ki-duotone ki-trash me-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>Sim, Excluir',
                    cancelButtonText: '<i class="ki-duotone ki-cross me-2"><span class="path1"></span><span class="path2"></span></i>Cancelar',
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    customClass: {
                        confirmButton: 'btn btn-danger',
                        cancelButton: 'btn btn-secondary'
                    },
                    buttonsStyling: false,
                    width: '450px',
                    allowOutsideClick: false,
                    allowEscapeKey: false
                });

                if (result.isConfirmed) {
                    await this.deleteProposicao();
                }
            } catch (error) {
                console.error('Erro ao confirmar exclusão:', error);
                this.showErrorAlert('Erro inesperado ao processar solicitação');
            }
        },
        
        async deleteProposicao() {
            try {
                // Show loading state
                Swal.fire({
                    title: 'Excluindo...',
                    html: 'Processando exclusão do documento...',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    willOpen: () => {
                        Swal.showLoading();
                    }
                });

                const response = await this.makeRequest(`/proposicoes/${this.proposicao.id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });

                if (response && response.success) {
                    // Success notification
                    await Swal.fire({
                        title: 'Documento Excluído!',
                        html: `
                            <div class="text-center">
                                <div class="mb-3">
                                    <i class="ki-duotone ki-check-circle fs-3x text-success">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                </div>
                                <p class="mb-2">O documento foi excluído com sucesso!</p>
                                <small class="text-muted">Você será redirecionado para a lista de proposições</small>
                            </div>
                        `,
                        icon: 'success',
                        confirmButtonText: 'Continuar',
                        confirmButtonColor: '#198754',
                        customClass: {
                            confirmButton: 'btn btn-success'
                        },
                        buttonsStyling: false
                    });

                    // Redirect to propositions list
                    window.location.href = '/proposicoes';
                } else {
                    throw new Error(response?.message || 'Erro ao excluir documento');
                }
            } catch (error) {
                console.error('Erro na exclusão:', error);
                this.showErrorAlert(error.message || 'Erro ao excluir o documento');
            }
        },
        
        async updateStatusAction(event) {
            event.preventDefault();
            
            const form = event.target.closest('form');
            const status = form.querySelector('input[name="status"]').value;
            
            const config = this.getStatusActionConfig(status);
            
            try {
                const result = await Swal.fire({
                    title: config.title,
                    html: config.html,
                    icon: config.icon,
                    showCancelButton: true,
                    confirmButtonText: config.confirmText,
                    cancelButtonText: 'Cancelar',
                    customClass: {
                        confirmButton: `btn btn-lg ${config.confirmButtonClass}`,
                        cancelButton: 'btn btn-lg btn-secondary'
                    },
                    buttonsStyling: false
                });

                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Processando...',
                        text: 'Atualizando status da proposição',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    
                    // Fazer requisição AJAX em vez de submit tradicional
                    await this.submitStatusForm(form);
                }
            } catch (error) {
                console.error('Erro ao processar ação:', error);
                this.showErrorAlert('Erro inesperado ao processar ação');
            }
        },
        
        async submitStatusForm(form) {
            try {
                const formData = new FormData(form);
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });
                
                const result = await response.json();
                
                if (result.success) {
                    // Atualizar o status da proposição localmente
                    this.proposicao.status = result.novo_status;
                    
                    // Fechar o loading
                    Swal.close();
                    
                    // Mostrar mensagem de sucesso
                    await Swal.fire({
                        title: 'Sucesso!',
                        text: result.message,
                        icon: 'success',
                        confirmButtonText: 'OK',
                        customClass: {
                            confirmButton: 'btn btn-success'
                        },
                        buttonsStyling: false
                    });
                    
                } else {
                    throw new Error(result.message || 'Erro ao atualizar status');
                }
            } catch (error) {
                console.error('Erro na requisição AJAX:', error);
                Swal.close();
                this.showErrorAlert(error.message || 'Erro ao atualizar status da proposição');
            }
        },
        
        getStatusActionConfig(status) {
            const configs = {
                'aprovado': {
                    title: 'Aprovar Proposição',
                    html: `
                        <div class="text-center mb-3">
                            <i class="ki-duotone ki-check-circle fs-3x text-success mb-3">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <p class="mb-2"><strong>Você está aprovando esta proposição.</strong></p>
                            <p class="mb-0 text-muted small">O documento será liberado para assinatura digital pelo Parlamentar.</p>
                        </div>
                    `,
                    icon: 'success',
                    confirmText: 'Sim, Aprovar',
                    confirmButtonClass: 'btn-success'
                },
                'devolvido_edicao': {
                    title: 'Devolver ao Parlamentar',
                    html: `
                        <div class="text-center mb-3">
                            <i class="ki-duotone ki-arrow-left fs-3x text-warning mb-3">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <p class="mb-2"><strong>Você está devolvendo esta proposição para correções.</strong></p>
                            <p class="mb-0 text-muted small">O Parlamentar poderá fazer ajustes e reenviar para nova análise.</p>
                        </div>
                    `,
                    icon: 'warning',
                    confirmText: 'Sim, Devolver',
                    confirmButtonClass: 'btn-warning'
                },
                'reprovado': {
                    title: 'Reprovar Proposição',
                    html: `
                        <div class="text-center mb-3">
                            <i class="ki-duotone ki-cross-circle fs-3x text-danger mb-3">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <p class="mb-2"><strong>Você está reprovando esta proposição.</strong></p>
                            <p class="mb-0 text-muted small"><span class="text-danger">Atenção:</span> Esta ação é definitiva e arquivará a proposição.</p>
                        </div>
                    `,
                    icon: 'error',
                    confirmText: 'Sim, Reprovar',
                    confirmButtonClass: 'btn-danger'
                }
            };
            
            return configs[status] || {
                title: 'Confirmar Ação',
                html: 'Deseja confirmar esta ação?',
                icon: 'question',
                confirmText: 'Confirmar',
                confirmButtonClass: 'btn-primary'
            };
        },
        
        // Toast System
        showToast(message, type = 'success', icon = 'ki-duotone ki-check') {
            const toast = {
                message,
                type,
                icon,
                show: false
            };
            
            this.toasts.push(toast);
            
            // Show toast with small delay
            setTimeout(() => {
                toast.show = true;
            }, 100);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                this.removeToast(this.toasts.indexOf(toast));
            }, 5000);
        },
        
        removeToast(index) {
            if (this.toasts[index]) {
                this.toasts[index].show = false;
                setTimeout(() => {
                    this.toasts.splice(index, 1);
                }, 300);
            }
        },

        // Novos métodos para exclusão de documento
        podeExcluirDocumento() {
            // Usuários do Legislativo NÃO podem excluir proposições
            if (this.userRole === 'LEGISLATIVO') {
                return false;
            }
            
            // Verificar se a proposição está em um status que permite exclusão
            const statusPermitidos = ['aprovado', 'aprovado_assinatura', 'retornado_legislativo', 'rascunho', 'em_edicao'];
            return statusPermitidos.includes(this.proposicao?.status);
        },

        async confirmarExclusaoDocumento() {
            try {
                // Mapear status para texto legível
                const statusTexto = {
                    'rascunho': 'Rascunho',
                    'em_edicao': 'Em Edição',
                    'aprovado': 'Aprovado',
                    'aprovado_assinatura': 'Aguardando Assinatura',
                    'retornado_legislativo': 'Retornado do Legislativo'
                };
                
                // Mapear tipo para texto legível
                const tipoTexto = this.proposicao.tipo === 'mocao' ? 'Moção' : 
                                  this.proposicao.tipo ? this.proposicao.tipo.charAt(0).toUpperCase() + this.proposicao.tipo.slice(1) : 
                                  'Proposição';
                
                const result = await Swal.fire({
                    title: '',
                    html: `
                        <div class="text-center" style="padding: 10px;">
                            <div class="mb-4">
                                <div class="bg-light-warning rounded p-4 mb-4" style="border: 2px solid #ffc107;">
                                    <i class="ki-duotone ki-information-5 fs-3x text-warning mb-3">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                    </i>
                                    <h5 class="text-dark fw-bold mb-3">Você está prestes a excluir esta proposição</h5>
                                    <p class="text-dark fs-6 mb-0">
                                        Ao confirmar, <strong>todos os documentos, arquivos e informações</strong> desta proposição 
                                        serão <strong>permanentemente removidos</strong> do sistema.
                                    </p>
                                </div>
                            </div>
                            
                            <div class="bg-light rounded p-4 mb-4" style="border-left: 4px solid #dc3545;">
                                <div class="text-start">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="ki-duotone ki-document fs-2 text-primary me-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        <span class="fw-bold text-dark">Proposição que será excluída:</span>
                                    </div>
                                    <div class="ms-4">
                                        <div class="text-dark fs-6 fw-semibold mb-2">"${this.proposicao.ementa}"</div>
                                        <div class="d-flex gap-3">
                                            <span class="badge badge-light-primary">📝 ${tipoTexto}</span>
                                            <span class="badge badge-light-info">📊 ${statusTexto[this.proposicao.status] || this.proposicao.status}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="alert alert-danger d-flex align-items-center mb-0" style="background-color: #fff5f5; border: 1px solid #f1416c;">
                                <div class="text-center w-100">
                                    <i class="ki-duotone ki-shield-cross fs-3x text-danger mb-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                    </i>
                                    <div class="fw-bold text-danger fs-5 mb-2">⚠️ ATENÇÃO IMPORTANTE</div>
                                    <div class="text-danger fs-6">
                                        <strong>Esta ação NÃO pode ser desfeita!</strong><br>
                                        <span class="text-dark">Após a exclusão, não será possível recuperar esta proposição.</span><br>
                                        <em class="text-muted fs-7">Certifique-se de que realmente deseja excluir antes de confirmar.</em>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `,
                    showCancelButton: true,
                    confirmButtonText: '<i class="ki-duotone ki-trash fs-3 me-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>Sim, Excluir Proposição',
                    cancelButtonText: '<i class="ki-duotone ki-check fs-3 me-2"><span class="path1"></span><span class="path2"></span></i>Não, Manter Proposição',
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    customClass: {
                        confirmButton: 'btn btn-danger btn-lg',
                        cancelButton: 'btn btn-secondary btn-lg',
                        popup: 'swal2-large-modal'
                    },
                    buttonsStyling: false,
                    width: '800px',
                    padding: '2rem',
                    allowOutsideClick: false,
                    allowEscapeKey: false
                });

                if (result.isConfirmed) {
                    await this.excluirDocumento();
                }
            } catch (error) {
                console.error('Erro ao confirmar exclusão de proposição:', error);
                this.showToast('Erro ao exibir confirmação', 'error');
            }
        },

        async excluirDocumento() {
            // Mostrar loading
            Swal.fire({
                title: 'Excluindo Proposição...',
                html: 'Removendo proposição permanentemente do sistema...',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            try {
                const response = await fetch(`/proposicoes/${this.proposicao.id}/excluir-documento`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken,
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    // Fechar loading
                    Swal.close();

                    // Mostrar sucesso final
                    await Swal.fire({
                        title: 'Proposição Excluída!',
                        html: `
                            <div class="text-center">
                                <i class="ki-duotone ki-check-circle fs-3x text-success mb-3">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                <p class="mb-2">${data.message}</p>
                                <p class="fs-7 text-muted">Redirecionando para a listagem de proposições...</p>
                            </div>
                        `,
                        icon: 'success',
                        confirmButtonText: 'OK',
                        timer: 3000,
                        customClass: {
                            confirmButton: 'btn btn-success'
                        },
                        buttonsStyling: false
                    });

                    // Redirecionar para a listagem
                    if (data.redirect_url) {
                        window.location.href = data.redirect_url;
                    } else {
                        window.location.href = '/proposicoes';
                    }

                } else {
                    Swal.close();
                    
                    // Mostrar erro específico
                    await Swal.fire({
                        title: 'Erro ao Excluir',
                        text: data.message || 'Não foi possível excluir a proposição.',
                        icon: 'error',
                        confirmButtonText: 'OK',
                        customClass: {
                            confirmButton: 'btn btn-danger'
                        },
                        buttonsStyling: false
                    });
                }

            } catch (error) {
                console.error('Erro na requisição de exclusão:', error);
                Swal.close();
                
                // Mostrar erro de conexão
                await Swal.fire({
                    title: 'Erro de Conexão',
                    text: 'Erro de conexão ao excluir proposição: ' + error.message,
                    icon: 'error',
                    confirmButtonText: 'OK',
                    customClass: {
                        confirmButton: 'btn btn-danger'
                    },
                    buttonsStyling: false
                });
            }
        }
    }
}).mount('#proposicao-app');

// 🔧 Fix v2.0: Verificação de autenticação antes de navegar
function verificarAutenticacaoENavegar(url) {
    console.log('🔍 Verificando autenticação antes de navegar para:', url);
    
    // Mostrar loading
    Swal.fire({
        title: 'Verificando acesso...',
        html: '<div class="spinner-border text-primary" role="status"></div>',
        allowOutsideClick: false,
        showConfirmButton: false,
        didOpen: () => Swal.showLoading()
    });
    
    // Testar acesso com fetch
    fetch(url, {
        method: 'GET',
        credentials: 'include',
        headers: {
            'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        Swal.close();
        
        if (response.status === 200 && !response.url.includes('/login')) {
            // ✅ Sucesso - navegar
            window.location.href = url;
        } else if (response.url.includes('/login') || response.status === 302) {
            // 🔐 Sessão expirada
            Swal.fire({
                title: 'Sessão Expirada',
                html: `<div class="text-center">
                    <i class="fas fa-exclamation-triangle text-warning fa-3x mb-3"></i>
                    <p class="mb-3">Sua sessão expirou. Você precisa fazer login novamente.</p>
                    <p class="small text-muted">Você será redirecionado para a página de login.</p>
                </div>`,
                icon: null,
                confirmButtonText: 'Fazer Login',
                confirmButtonColor: '#007bff'
            }).then(() => window.location.href = '/login');
        } else if (response.status === 403) {
            // ❌ Sem permissão
            Swal.fire({
                title: 'Acesso Negado',
                html: `<div class="text-center">
                    <i class="fas fa-ban text-danger fa-3x mb-3"></i>
                    <p>Você não tem permissão para assinar esta proposição.</p>
                </div>`,
                icon: null,
                confirmButtonText: 'OK',
                confirmButtonColor: '#dc3545'
            });
        } else {
            // 🚨 Outro erro
            Swal.fire({
                title: 'Erro de Acesso',
                text: `Erro ${response.status}: Não foi possível acessar a página de assinatura.`,
                icon: 'error',
                confirmButtonText: 'OK'
            });
        }
    })
    .catch(error => {
        Swal.close();
        console.error('🚨 Erro na requisição:', error);
        // Fallback: tentar navegação direta
        window.location.href = url;
    });
}

</script>
@endsection