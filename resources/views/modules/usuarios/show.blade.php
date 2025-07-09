<x-layouts.app title="Detalhes do Usuário">
    <div class="d-flex flex-column flex-column-fluid">
        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                    <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                        Detalhes do Usuário
                    </h1>
                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-400 w-5px h-2px"></span>
                        </li>
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('usuarios.index') }}" class="text-muted text-hover-primary">Usuários</a>
                        </li>
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-400 w-5px h-2px"></span>
                        </li>
                        <li class="breadcrumb-item text-muted">Detalhes</li>
                    </ul>
                </div>
                <div class="d-flex align-items-center gap-2 gap-lg-3">
                    <a href="{{ route('usuarios.edit', $usuario->id) }}" class="btn btn-primary">
                        <i class="ki-duotone ki-pencil fs-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        Editar
                    </a>
                </div>
            </div>
        </div>

        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div id="kt_app_content_container" class="app-container container-xxl">
                <div class="d-flex flex-column flex-lg-row">
                    <div class="flex-column flex-lg-row-auto w-lg-250px w-xl-350px mb-10">
                        <div class="card mb-5 mb-xl-8">
                            <div class="card-body">
                                <div class="d-flex flex-center flex-column py-5">
                                    <div class="symbol symbol-100px symbol-circle mb-7">
                                        <div class="symbol-label fs-1 bg-light-primary text-primary">
                                            {{ $usuario->getAvatarAttribute($usuario->avatar) }}
                                        </div>
                                    </div>
                                    <a href="#" class="fs-3 text-gray-800 text-hover-primary fw-bold mb-3">{{ $usuario->name }}</a>
                                    <div class="mb-9">
                                        <div class="badge badge-lg badge-light-{{ $usuario->getCorPerfil() }} d-inline">{{ $usuario->getPerfilFormatado() }}</div>
                                    </div>
                                </div>
                                
                                <div class="d-flex flex-stack fs-4 py-3">
                                    <div class="fw-bold rotate collapsible" data-bs-toggle="collapse" href="#kt_user_view_details" role="button" aria-expanded="false" aria-controls="kt_user_view_details">
                                        Detalhes
                                        <span class="ms-2 rotate-180">
                                            <i class="ki-duotone ki-down fs-3"></i>
                                        </span>
                                    </div>
                                    <span data-bs-toggle="tooltip" data-bs-trigger="hover" title="Editar detalhes do usuário">
                                        <a href="{{ route('usuarios.edit', $usuario->id) }}" class="btn btn-sm btn-light-primary">
                                            <i class="ki-duotone ki-pencil fs-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                        </a>
                                    </span>
                                </div>
                                
                                <div class="separator"></div>
                                
                                <div id="kt_user_view_details" class="collapse show">
                                    <div class="pb-5 fs-6">
                                        <div class="fw-bold mt-5">Email</div>
                                        <div class="text-gray-600">{{ $usuario->email }}</div>
                                        
                                        @if($usuario->documento)
                                        <div class="fw-bold mt-5">Documento</div>
                                        <div class="text-gray-600">{{ $usuario->documento }}</div>
                                        @endif
                                        
                                        @if($usuario->telefone)
                                        <div class="fw-bold mt-5">Telefone</div>
                                        <div class="text-gray-600">{{ $usuario->telefone }}</div>
                                        @endif
                                        
                                        @if($usuario->data_nascimento)
                                        <div class="fw-bold mt-5">Data de Nascimento</div>
                                        <div class="text-gray-600">{{ $usuario->data_nascimento->format('d/m/Y') }}</div>
                                        @endif
                                        
                                        @if($usuario->profissao)
                                        <div class="fw-bold mt-5">Profissão</div>
                                        <div class="text-gray-600">{{ $usuario->profissao }}</div>
                                        @endif
                                        
                                        @if($usuario->cargo_atual)
                                        <div class="fw-bold mt-5">Cargo Atual</div>
                                        <div class="text-gray-600">{{ $usuario->cargo_atual }}</div>
                                        @endif
                                        
                                        @if($usuario->partido)
                                        <div class="fw-bold mt-5">Partido</div>
                                        <div class="text-gray-600">{{ $usuario->partido }}</div>
                                        @endif
                                        
                                        <div class="fw-bold mt-5">Status</div>
                                        <div class="text-gray-600">
                                            <span class="badge badge-light-{{ $usuario->ativo ? 'success' : 'danger' }}">
                                                {{ $usuario->ativo ? 'Ativo' : 'Inativo' }}
                                            </span>
                                        </div>
                                        
                                        <div class="fw-bold mt-5">Último Acesso</div>
                                        <div class="text-gray-600">{{ $usuario->ultimo_acesso ? $usuario->ultimo_acesso->format('d/m/Y H:i') : 'Nunca' }}</div>
                                        
                                        <div class="fw-bold mt-5">Criado em</div>
                                        <div class="text-gray-600">{{ $usuario->created_at->format('d/m/Y H:i') }}</div>
                                        
                                        <div class="fw-bold mt-5">Atualizado em</div>
                                        <div class="text-gray-600">{{ $usuario->updated_at->format('d/m/Y H:i') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex-lg-row-fluid ms-lg-15">
                        <ul class="nav nav-custom nav-tabs nav-line-tabs nav-line-tabs-2x border-0 fs-4 fw-semibold mb-8" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a class="nav-link text-active-primary pb-4 active" data-bs-toggle="tab" href="#kt_user_view_overview_tab" aria-selected="true" role="tab">Visão Geral</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link text-active-primary pb-4" data-bs-toggle="tab" href="#kt_user_view_permissions_tab" aria-selected="false" role="tab">Permissões</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link text-active-primary pb-4" data-bs-toggle="tab" href="#kt_user_view_logs_tab" aria-selected="false" role="tab">Logs</a>
                            </li>
                        </ul>
                        
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="kt_user_view_overview_tab" role="tabpanel">
                                <div class="card mb-5 mb-xl-10">
                                    <div class="card-header cursor-pointer">
                                        <div class="card-title m-0">
                                            <h3 class="fw-bold m-0">Informações do Usuário</h3>
                                        </div>
                                    </div>
                                    <div class="card-body p-9">
                                        <div class="row mb-7">
                                            <label class="col-lg-4 fw-semibold text-muted">Nome Completo</label>
                                            <div class="col-lg-8">
                                                <span class="fw-bold fs-6 text-gray-800">{{ $usuario->name }}</span>
                                            </div>
                                        </div>
                                        
                                        <div class="row mb-7">
                                            <label class="col-lg-4 fw-semibold text-muted">Email</label>
                                            <div class="col-lg-8 fv-row">
                                                <span class="fw-semibold text-gray-800 fs-6">{{ $usuario->email }}</span>
                                            </div>
                                        </div>
                                        
                                        @if($usuario->documento)
                                        <div class="row mb-7">
                                            <label class="col-lg-4 fw-semibold text-muted">Documento</label>
                                            <div class="col-lg-8 fv-row">
                                                <span class="fw-semibold text-gray-800 fs-6">{{ $usuario->documento }}</span>
                                            </div>
                                        </div>
                                        @endif
                                        
                                        @if($usuario->telefone)
                                        <div class="row mb-7">
                                            <label class="col-lg-4 fw-semibold text-muted">Telefone</label>
                                            <div class="col-lg-8 fv-row">
                                                <span class="fw-semibold text-gray-800 fs-6">{{ $usuario->telefone }}</span>
                                            </div>
                                        </div>
                                        @endif
                                        
                                        @if($usuario->data_nascimento)
                                        <div class="row mb-7">
                                            <label class="col-lg-4 fw-semibold text-muted">Data de Nascimento</label>
                                            <div class="col-lg-8 fv-row">
                                                <span class="fw-semibold text-gray-800 fs-6">{{ $usuario->data_nascimento->format('d/m/Y') }}</span>
                                            </div>
                                        </div>
                                        @endif
                                        
                                        @if($usuario->profissao)
                                        <div class="row mb-7">
                                            <label class="col-lg-4 fw-semibold text-muted">Profissão</label>
                                            <div class="col-lg-8 fv-row">
                                                <span class="fw-semibold text-gray-800 fs-6">{{ $usuario->profissao }}</span>
                                            </div>
                                        </div>
                                        @endif
                                        
                                        @if($usuario->cargo_atual)
                                        <div class="row mb-7">
                                            <label class="col-lg-4 fw-semibold text-muted">Cargo Atual</label>
                                            <div class="col-lg-8 fv-row">
                                                <span class="fw-semibold text-gray-800 fs-6">{{ $usuario->cargo_atual }}</span>
                                            </div>
                                        </div>
                                        @endif
                                        
                                        @if($usuario->partido)
                                        <div class="row mb-7">
                                            <label class="col-lg-4 fw-semibold text-muted">Partido</label>
                                            <div class="col-lg-8 fv-row">
                                                <span class="fw-semibold text-gray-800 fs-6">{{ $usuario->partido }}</span>
                                            </div>
                                        </div>
                                        @endif
                                        
                                        <div class="row mb-7">
                                            <label class="col-lg-4 fw-semibold text-muted">Status</label>
                                            <div class="col-lg-8 fv-row">
                                                <span class="badge badge-light-{{ $usuario->ativo ? 'success' : 'danger' }} fs-7 fw-bold">
                                                    {{ $usuario->ativo ? 'Ativo' : 'Inativo' }}
                                                </span>
                                            </div>
                                        </div>
                                        
                                        <div class="row mb-7">
                                            <label class="col-lg-4 fw-semibold text-muted">Último Acesso</label>
                                            <div class="col-lg-8 fv-row">
                                                <span class="fw-semibold text-gray-800 fs-6">{{ $usuario->ultimo_acesso ? $usuario->ultimo_acesso->format('d/m/Y H:i') : 'Nunca' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="tab-pane fade" id="kt_user_view_permissions_tab" role="tabpanel">
                                <div class="card mb-5 mb-xl-10">
                                    <div class="card-header cursor-pointer">
                                        <div class="card-title m-0">
                                            <h3 class="fw-bold m-0">Permissões do Usuário</h3>
                                        </div>
                                    </div>
                                    <div class="card-body p-9">
                                        <div class="row mb-7">
                                            <label class="col-lg-4 fw-semibold text-muted">Perfil</label>
                                            <div class="col-lg-8 fv-row">
                                                <span class="badge badge-light-{{ $usuario->getCorPerfil() }} fs-7 fw-bold">
                                                    {{ $usuario->getPerfilFormatado() }}
                                                </span>
                                            </div>
                                        </div>
                                        
                                        <div class="row mb-7">
                                            <label class="col-lg-4 fw-semibold text-muted">Nível Hierárquico</label>
                                            <div class="col-lg-8 fv-row">
                                                <span class="fw-semibold text-gray-800 fs-6">{{ $usuario->getNivelHierarquico() }}</span>
                                            </div>
                                        </div>
                                        
                                        <div class="row mb-7">
                                            <label class="col-lg-4 fw-semibold text-muted">Tipo de Acesso</label>
                                            <div class="col-lg-8 fv-row">
                                                <div class="d-flex flex-column">
                                                    @if($usuario->isAdmin())
                                                        <span class="badge badge-light-danger mb-2">Administrador</span>
                                                    @endif
                                                    @if($usuario->isParlamentar())
                                                        <span class="badge badge-light-success mb-2">Parlamentar</span>
                                                    @endif
                                                    @if($usuario->isLegislativo())
                                                        <span class="badge badge-light-primary mb-2">Legislativo</span>
                                                    @endif
                                                    @if($usuario->isRelator())
                                                        <span class="badge badge-light-warning mb-2">Relator</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="tab-pane fade" id="kt_user_view_logs_tab" role="tabpanel">
                                <div class="card mb-5 mb-xl-10">
                                    <div class="card-header cursor-pointer">
                                        <div class="card-title m-0">
                                            <h3 class="fw-bold m-0">Logs de Atividade</h3>
                                        </div>
                                    </div>
                                    <div class="card-body p-9">
                                        <div class="d-flex align-items-center justify-content-center h-200px">
                                            <div class="text-center">
                                                <i class="ki-duotone ki-file-deleted fs-5x text-muted mb-5">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                    <span class="path3"></span>
                                                </i>
                                                <div class="fw-semibold fs-6 text-gray-500">Nenhum log de atividade disponível</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>