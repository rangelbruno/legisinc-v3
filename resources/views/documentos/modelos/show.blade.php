@extends('layouts.app')

@section('title', 'Visualizar Modelo')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title">{{ $modelo->nome }}</h3>
                        <div class="card-toolbar">
                            @if($modelo->ativo)
                                <span class="badge badge-success">Ativo</span>
                            @else
                                <span class="badge badge-secondary">Inativo</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h5>Informações Gerais</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td width="30%"><strong>Nome:</strong></td>
                                    <td>{{ $modelo->nome }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Descrição:</strong></td>
                                    <td>{{ $modelo->descricao ?? 'Não informada' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Tipo de Proposição:</strong></td>
                                    <td>{{ $modelo->tipoProposicao->nome ?? 'Geral' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Versão:</strong></td>
                                    <td>{{ $modelo->versao }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Arquivo:</strong></td>
                                    <td>
                                        @if($modelo->arquivo_path)
                                            <i class="fas fa-file-word text-primary"></i> {{ $modelo->arquivo_nome }}
                                            <small class="text-muted">({{ number_format($modelo->arquivo_size / 1024, 2) }} KB)</small>
                                        @else
                                            <span class="text-muted">Nenhum arquivo enviado</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Criado por:</strong></td>
                                    <td>{{ $modelo->creator->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Data de criação:</strong></td>
                                    <td>{{ $modelo->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-4">
                            <h5>Variáveis do Modelo</h5>
                            @if(!empty($variaveisFormatadas))
                                <div class="list-group">
                                    @foreach($variaveisFormatadas as $variavel)
                                        <div class="list-group-item">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <code class="text-primary">${{ $variavel['nome'] }}</code>
                                                    @if($variavel['obrigatoria'])
                                                        <span class="badge badge-light-danger ms-2">Obrigatória</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <small class="text-muted">{{ $variavel['descricao'] }}</small>
                                            @if(!empty($variavel['exemplo']))
                                                <br><small class="text-info">Exemplo: {{ $variavel['exemplo'] }}</small>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted">Nenhuma variável definida</p>
                            @endif
                        </div>
                    </div>

                    @if($modelo->instancias->count() > 0)
                        <hr>
                        <h5>Documentos Gerados ({{ $modelo->instancias->count() }})</h5>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Projeto</th>
                                        <th>Status</th>
                                        <th>Versão</th>
                                        <th>Última Modificação</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($modelo->instancias as $instancia)
                                        <tr>
                                            <td>
                                                @if($instancia->projeto)
                                                    {{ $instancia->projeto->titulo ?? 'Projeto #' . $instancia->projeto_id }}
                                                @else
                                                    <span class="text-muted">Projeto não encontrado</span>
                                                @endif
                                            </td>
                                            <td>
                                                @php
                                                    $statusData = $instancia->statusFormatado;
                                                @endphp
                                                <span class="badge {{ $statusData['classe'] }}">{{ $statusData['texto'] }}</span>
                                            </td>
                                            <td>{{ $instancia->versao }}</td>
                                            <td>{{ $instancia->updated_at->format('d/m/Y H:i') }}</td>
                                            <td>
                                                <a href="{{ route('documentos.instancias.show', $instancia) }}" 
                                                   class="btn btn-sm btn-outline-primary" title="Visualizar">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-between">
                        <div>
                            <a href="{{ route('documentos.modelos.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Voltar
                            </a>
                        </div>
                        <div>
                            @if($modelo->arquivo_path)
                                <div class="btn-group" role="group">
                                    <a href="{{ route('documentos.modelos.download', $modelo) }}?v={{ $modelo->updated_at->timestamp }}" 
                                       class="btn btn-outline-success download-btn" 
                                       title="Download do Modelo"
                                       data-modelo-id="{{ $modelo->id }}">
                                        <i class="fas fa-download"></i> Download
                                    </a>
                                    <button type="button" 
                                            class="btn btn-outline-success btn-sm update-download-btn" 
                                            title="Atualizar link de download"
                                            onclick="updateDownloadLink()"
                                            data-modelo-id="{{ $modelo->id }}">
                                        <i class="fas fa-sync-alt"></i>
                                    </button>
                                </div>
                            @endif
                            
                            {{-- ONLYOFFICE Editor Button --}}
                            @if($modelo->document_key)
                                <a href="{{ route('onlyoffice.standalone.editor.modelo', $modelo) }}" 
                                   class="btn btn-primary" 
                                   title="Editar com ONLYOFFICE em nova aba"
                                   target="_blank">
                                    <i class="fas fa-external-link-alt"></i> Editar documento
                                </a>
                            @else
                                <a href="{{ route('documentos.modelos.editor-onlyoffice', $modelo) }}" 
                                   class="btn btn-primary" 
                                   title="Criar com ONLYOFFICE">
                                    <i class="fas fa-plus"></i> Criar documento
                                </a>
                            @endif
                            
                            <a href="{{ route('documentos.modelos.edit', $modelo) }}" class="btn btn-warning">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Listener para detectar eventos do editor OnlyOffice
    window.addEventListener('message', function(event) {
        if (event.data) {
            switch(event.data.type) {
                case 'onlyoffice_editor_closed':
                    console.log('Editor OnlyOffice foi fechado, atualizando página...');
                    setTimeout(() => location.reload(), 1000);
                    break;
                case 'onlyoffice_version_changed':
                    console.log('Versão do documento alterada, preparando para atualizar...');
                    setTimeout(() => location.reload(), 3000);
                    break;
                case 'onlyoffice_document_saved':
                    console.log('Documento salvo no OnlyOffice, atualizando link de download...');
                    updateDownloadLink();
                    setTimeout(() => location.reload(), 500);
                    break;
                case 'onlyoffice_document_updated':
                    console.log('Documento atualizado no OnlyOffice, atualizando link de download...');
                    updateDownloadLink();
                    setTimeout(() => location.reload(), 1500);
                    break;
            }
        }
    });

    // Funcionalidade de atualização automática do download
    function updateDownloadLink() {
        const downloadBtn = document.querySelector('.download-btn');
        const updateBtn = document.querySelector('.update-download-btn');
        if (!downloadBtn) return;
        
        const modeloId = downloadBtn.getAttribute('data-modelo-id');
        if (!modeloId) return;
        
        // Adicionar indicador visual de atualização
        const originalIcon = downloadBtn.querySelector('i');
        const originalText = downloadBtn.innerHTML;
        const updateOriginalIcon = updateBtn ? updateBtn.innerHTML : '';
        
        downloadBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Atualizando...';
        downloadBtn.disabled = true;
        
        if (updateBtn) {
            updateBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            updateBtn.disabled = true;
        }
        
        fetch(`/admin/documentos/modelos/${modeloId}/last-update`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.timestamp) {
                    const currentUrl = new URL(downloadBtn.href);
                    const currentTimestamp = currentUrl.searchParams.get('v');
                    
                    // Só atualizar se o timestamp realmente mudou
                    if (currentTimestamp !== data.timestamp.toString()) {
                        currentUrl.searchParams.set('v', data.timestamp);
                        downloadBtn.href = currentUrl.toString();
                        
                        console.log('Download link updated with new timestamp:', data.timestamp);
                        
                        // Mostrar feedback visual rápido
                        downloadBtn.innerHTML = '<i class="fas fa-check text-success"></i> Atualizado!';
                        if (updateBtn) {
                            updateBtn.innerHTML = '<i class="fas fa-check text-success"></i>';
                        }
                        setTimeout(() => {
                            downloadBtn.innerHTML = originalText;
                            downloadBtn.disabled = false;
                            if (updateBtn) {
                                updateBtn.innerHTML = updateOriginalIcon;
                                updateBtn.disabled = false;
                            }
                        }, 1000);
                    } else {
                        downloadBtn.innerHTML = originalText;
                        downloadBtn.disabled = false;
                        if (updateBtn) {
                            updateBtn.innerHTML = updateOriginalIcon;
                            updateBtn.disabled = false;
                        }
                    }
                } else {
                    downloadBtn.innerHTML = originalText;
                    downloadBtn.disabled = false;
                    if (updateBtn) {
                        updateBtn.innerHTML = updateOriginalIcon;
                        updateBtn.disabled = false;
                    }
                }
            })
            .catch(error => {
                console.error('Error updating download link:', error);
                downloadBtn.innerHTML = originalText;
                downloadBtn.disabled = false;
                if (updateBtn) {
                    updateBtn.innerHTML = updateOriginalIcon;
                    updateBtn.disabled = false;
                }
            });
    }

    // Fallback: verificar localStorage periodicamente
    let lastChecks = {
        closed: localStorage.getItem('onlyoffice_editor_closed'),
        versionChanged: localStorage.getItem('onlyoffice_version_changed'),
        updated: localStorage.getItem('onlyoffice_document_updated'),
        saved: localStorage.getItem('onlyoffice_document_saved')
    };
    
    setInterval(function() {
        // Verificar fechamento do editor
        const currentClosed = localStorage.getItem('onlyoffice_editor_closed');
        if (currentClosed && currentClosed !== lastChecks.closed) {
            const timestamp = parseInt(currentClosed);
            if (Date.now() - timestamp < 5000) {
                console.log('Editor OnlyOffice foi fechado (localStorage), atualizando página...');
                lastChecks.closed = currentClosed;
                setTimeout(() => location.reload(), 1000);
                return;
            }
        }
        
        // Verificar mudança de versão
        const currentVersion = localStorage.getItem('onlyoffice_version_changed');
        if (currentVersion && currentVersion !== lastChecks.versionChanged) {
            const timestamp = parseInt(currentVersion);
            if (Date.now() - timestamp < 10000) {
                console.log('Versão alterada (localStorage), atualizando página...');
                lastChecks.versionChanged = currentVersion;
                setTimeout(() => location.reload(), 2000);
                return;
            }
        }
        
        // Verificar salvamento de documento (prioridade alta)
        const currentSaved = localStorage.getItem('onlyoffice_document_saved');
        if (currentSaved && currentSaved !== lastChecks.saved) {
            const timestamp = parseInt(currentSaved);
            if (Date.now() - timestamp < 5000) {
                console.log('Documento salvo (localStorage), atualizando link de download e página...');
                updateDownloadLink();
                lastChecks.saved = currentSaved;
                setTimeout(() => location.reload(), 500);
                return;
            }
        }
        
        // Verificar atualização de documento
        const currentUpdate = localStorage.getItem('onlyoffice_document_updated');
        if (currentUpdate && currentUpdate !== lastChecks.updated) {
            const timestamp = parseInt(currentUpdate);
            if (Date.now() - timestamp < 8000) {
                console.log('Documento atualizado (localStorage), atualizando link de download e página...');
                updateDownloadLink();
                lastChecks.updated = currentUpdate;
                setTimeout(() => location.reload(), 1500);
                return;
            }
        }
    }, 1000);
});
</script>
@endpush