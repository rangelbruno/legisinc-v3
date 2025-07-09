<x-layouts.app title="Versões do Projeto">
    <div class="d-flex flex-column flex-column-fluid">
        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                    <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                        Histórico de Versões
                    </h1>
                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-400 w-5px h-2px"></span>
                        </li>
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('projetos.index') }}" class="text-muted text-hover-primary">Projetos</a>
                        </li>
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-400 w-5px h-2px"></span>
                        </li>
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('projetos.show', $projeto->id) }}" class="text-muted text-hover-primary">{{ $projeto->numero_completo ?? 'Projeto' }}</a>
                        </li>
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-400 w-5px h-2px"></span>
                        </li>
                        <li class="breadcrumb-item text-muted">Versões</li>
                    </ul>
                </div>
                <div class="d-flex align-items-center gap-2 gap-lg-3">
                    <a href="{{ route('projetos.show', $projeto->id) }}" class="btn btn-light-primary btn-sm">
                        <i class="ki-duotone ki-arrow-left fs-3">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        Voltar ao Projeto
                    </a>
                    
                    @if($projeto->podeEditarConteudo())
                    <a href="{{ route('projetos.editor', $projeto->id) }}" class="btn btn-primary btn-sm" target="_blank">
                        <i class="ki-duotone ki-pencil fs-3">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        Editor
                    </a>
                    @endif
                </div>
            </div>
        </div>

        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div id="kt_app_content_container" class="app-container container-xxl">
                
                <!-- Info do Projeto -->
                <div class="card card-flush mb-5">
                    <div class="card-body d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h2 class="mb-2">{{ $projeto->titulo }}</h2>
                            <div class="text-muted">{{ $projeto->numero_completo }} - {{ $projeto->ementa }}</div>
                        </div>
                        <div class="text-end">
                            <div class="fs-2hx fw-bold text-primary">{{ $projeto->version_atual }}</div>
                            <div class="text-muted fs-6">Versão Atual</div>
                        </div>
                    </div>
                </div>

                <!-- Timeline de Versões -->
                <div class="card card-flush">
                    <div class="card-header">
                        <div class="card-title">
                            <h3>Histórico de Alterações</h3>
                        </div>
                        <div class="card-toolbar">
                            <div class="text-muted fs-6">
                                {{ $projeto->versions->count() }} versão(ões)
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        @forelse($projeto->versions->sortByDesc('version_number') as $version)
                        <div class="timeline">
                            <div class="timeline-item">
                                <div class="timeline-line w-40px"></div>
                                
                                <div class="timeline-icon symbol symbol-circle symbol-40px">
                                    @if($version->is_current)
                                        <div class="symbol-label bg-success">
                                            <i class="ki-duotone ki-check fs-2 text-white">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                        </div>
                                    @else
                                        <div class="symbol-label bg-light-primary text-primary fw-bold">
                                            {{ $version->version_number }}
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="timeline-content mb-10 mt-n1">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="pe-3">
                                            <div class="fs-5 fw-semibold mb-2">
                                                Versão {{ $version->version_number }}
                                                @if($version->is_current)
                                                    <span class="badge badge-light-success ms-2">Atual</span>
                                                @endif
                                            </div>
                                            <div class="d-flex align-items-center mt-1 fs-6">
                                                <div class="text-muted me-2 fs-7">
                                                    {{ $version->created_at->format('d/m/Y H:i') }}
                                                </div>
                                                <div class="text-muted me-2 fs-7">•</div>
                                                <div class="text-muted me-2 fs-7">
                                                    {{ $version->author->name ?? 'Sistema' }}
                                                </div>
                                                @if($version->tipo_alteracao)
                                                    <div class="text-muted me-2 fs-7">•</div>
                                                    <div class="badge badge-light-{{ 
                                                        match($version->tipo_alteracao) {
                                                            'criacao' => 'success',
                                                            'revisao' => 'primary',
                                                            'emenda' => 'warning',
                                                            'correcao' => 'info',
                                                            'formatacao' => 'secondary',
                                                            default => 'secondary'
                                                        } 
                                                    }} fs-8">
                                                        {{ ucfirst($version->tipo_alteracao) }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        <div class="ms-auto">
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-light-primary btn-sm" 
                                                        onclick="viewVersion({{ $version->id }})"
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#modal_view_version">
                                                    <i class="ki-duotone ki-eye fs-3">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                        <span class="path3"></span>
                                                    </i>
                                                    Ver
                                                </button>
                                                
                                                @if(!$version->is_current && $projeto->podeEditarConteudo())
                                                <button type="button" class="btn btn-light-warning btn-sm"
                                                        onclick="restoreVersion({{ $version->id }})">
                                                    <i class="ki-duotone ki-arrow-up fs-3">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                    </i>
                                                    Restaurar
                                                </button>
                                                @endif
                                                
                                                @if($loop->index > 0)
                                                <button type="button" class="btn btn-light-info btn-sm"
                                                        onclick="compareVersions({{ $projeto->versions->sortByDesc('version_number')->values()[$loop->index - 1]->id }}, {{ $version->id }})">
                                                    <i class="ki-duotone ki-compare fs-3">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                    </i>
                                                    Comparar
                                                </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    
                                    @if($version->changelog)
                                    <div class="text-gray-800 fw-normal fs-6 mb-3">
                                        {{ $version->changelog }}
                                    </div>
                                    @endif
                                    
                                    @if($version->comentarios)
                                    <div class="text-gray-600 fs-7 mb-3">
                                        <strong>Comentários:</strong> {{ $version->comentarios }}
                                    </div>
                                    @endif
                                    
                                    <!-- Estatísticas da versão -->
                                    <div class="d-flex align-items-center text-gray-600 fs-7">
                                        @if($version->tamanho_bytes)
                                        <div class="me-5">
                                            <i class="ki-duotone ki-document fs-6 me-1">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            {{ number_format($version->tamanho_bytes / 1024, 1) }} KB
                                        </div>
                                        @endif
                                        
                                        @if($version->diff_data && isset($version->diff_data['stats']))
                                        <div class="me-5">
                                            <span class="text-success">+{{ $version->diff_data['stats']['additions'] ?? 0 }}</span>
                                            <span class="text-danger">-{{ $version->diff_data['stats']['deletions'] ?? 0 }}</span>
                                        </div>
                                        @endif
                                        
                                        <div class="me-5">
                                            <i class="ki-duotone ki-time fs-6 me-1">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            {{ $version->created_at->diffForHumans() }}
                                        </div>
                                        
                                        @if($version->is_published)
                                        <div class="me-5">
                                            <i class="ki-duotone ki-check-circle fs-6 me-1 text-success">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            Publicada
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-10">
                            <div class="text-gray-500 fs-6">
                                Nenhuma versão encontrada para este projeto.
                            </div>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Ver Versão -->
    <div class="modal fade" id="modal_view_version" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 id="version_modal_title">Visualizar Versão</h2>
                    <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                        <i class="ki-duotone ki-cross fs-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </div>
                </div>
                <div class="modal-body">
                    <div id="version_content" style="padding: 20px; line-height: 1.6;"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Comparar Versões -->
    <div class="modal fade" id="modal_compare_versions" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 id="compare_modal_title">Comparar Versões</h2>
                    <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                        <i class="ki-duotone ki-cross fs-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-6">
                            <h4 id="version_old_title">Versão Anterior</h4>
                            <div id="version_old_content" style="padding: 20px; line-height: 1.6; border-right: 1px solid #e5e5e5;"></div>
                        </div>
                        <div class="col-6">
                            <h4 id="version_new_title">Versão Atual</h4>
                            <div id="version_new_content" style="padding: 20px; line-height: 1.6;"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        let versions = @json($projeto->versions->keyBy('id'));
        
        function viewVersion(versionId) {
            const version = versions[versionId];
            if (!version) return;
            
            document.getElementById('version_modal_title').textContent = 
                `Versão ${version.version_number} - ${new Date(version.created_at).toLocaleDateString('pt-BR')}`;
            
            document.getElementById('version_content').innerHTML = version.conteudo || '<p class="text-muted">Sem conteúdo</p>';
        }
        
        function compareVersions(newVersionId, oldVersionId) {
            const newVersion = versions[newVersionId];
            const oldVersion = versions[oldVersionId];
            
            if (!newVersion || !oldVersion) return;
            
            document.getElementById('compare_modal_title').textContent = 
                `Comparar: Versão ${oldVersion.version_number} vs Versão ${newVersion.version_number}`;
            
            document.getElementById('version_old_title').textContent = 
                `Versão ${oldVersion.version_number} (${new Date(oldVersion.created_at).toLocaleDateString('pt-BR')})`;
            
            document.getElementById('version_new_title').textContent = 
                `Versão ${newVersion.version_number} (${new Date(newVersion.created_at).toLocaleDateString('pt-BR')})`;
            
            document.getElementById('version_old_content').innerHTML = 
                oldVersion.conteudo || '<p class="text-muted">Sem conteúdo</p>';
            
            document.getElementById('version_new_content').innerHTML = 
                newVersion.conteudo || '<p class="text-muted">Sem conteúdo</p>';
            
            // Mostrar modal
            const modal = new bootstrap.Modal(document.getElementById('modal_compare_versions'));
            modal.show();
        }
        
        function restoreVersion(versionId) {
            const version = versions[versionId];
            if (!version) return;
            
            if (!confirm(`Tem certeza que deseja restaurar a versão ${version.version_number}? Isso criará uma nova versão baseada no conteúdo selecionado.`)) {
                return;
            }
            
            fetch(`/projetos/{{ $projeto->id }}/salvar-conteudo`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    conteudo: version.conteudo,
                    changelog: `Restauração da versão ${version.version_number}`,
                    tipo_alteracao: 'revisao'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(`Versão ${version.version_number} restaurada com sucesso! Nova versão: ${data.version}`);
                    location.reload();
                } else {
                    alert('Erro ao restaurar versão: ' + data.message);
                }
            })
            .catch(error => {
                alert('Erro na conexão. Tente novamente.');
                console.error('Erro:', error);
            });
        }
        
        // Adicionar estilos para diff (se implementado no futuro)
        document.addEventListener('DOMContentLoaded', function() {
            const style = document.createElement('style');
            style.textContent = `
                .diff-added {
                    background-color: #d4edda;
                    color: #155724;
                }
                .diff-removed {
                    background-color: #f8d7da;
                    color: #721c24;
                    text-decoration: line-through;
                }
                .timeline-item:last-child .timeline-line {
                    display: none;
                }
            `;
            document.head.appendChild(style);
        });
    </script>
    @endpush
</x-layouts.app>