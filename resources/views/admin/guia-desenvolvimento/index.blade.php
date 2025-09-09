@extends('components.layouts.app')

@section('title', 'Guia de Desenvolvimento - Sistema Parlamentar')

@section('content')
<div class="d-flex flex-column flex-column-fluid">
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    🚀 Guia de Desenvolvimento
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('admin.dashboard') }}" class="text-muted text-hover-primary">Admin</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Guia de Desenvolvimento</li>
                </ul>
            </div>
        </div>
    </div>
    <!--end::Toolbar-->

    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">
            
            <!--begin::Alert-->
            <div class="alert alert-primary d-flex align-items-center p-5 mb-10">
                <i class="ki-duotone ki-shield-tick fs-2hx text-primary me-4">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
                <div class="d-flex flex-column">
                    <h4 class="mb-1 text-primary">Bem-vindo ao Guia de Desenvolvimento!</h4>
                    <span>Este guia interativo ajudará você a criar novos módulos seguindo as melhores práticas do sistema Legisinc.</span>
                </div>
            </div>
            <!--end::Alert-->

            <!--begin::Row-->
            <div class="row g-5 g-xl-10">
                <!--begin::Col-->
                <div class="col-xl-4">
                    <!--begin::Card-->
                    <div class="card card-flush h-xl-100">
                        <div class="card-header pt-7">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold text-gray-800">📁 Estrutura do Projeto</span>
                                <span class="text-gray-400 mt-1 fw-semibold fs-6">Organização de pastas</span>
                            </h3>
                        </div>
                        <div class="card-body">
                            <div style="font-family: monospace; line-height: 1.8;">
                                <div class="mb-2">
                                    <strong class="text-primary">📁 app/</strong>
                                    <div class="ms-4">
                                        <div>📁 Http/Controllers/</div>
                                        <div>📁 Models/</div>
                                        <div>📁 Services/</div>
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <strong class="text-primary">📁 resources/</strong>
                                    <div class="ms-4">
                                        <div>📁 views/</div>
                                        <div>📁 js/components/</div>
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <strong class="text-primary">📁 routes/</strong>
                                    <div class="ms-4">
                                        <div>📄 web.php</div>
                                        <div>📄 api.php</div>
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <strong class="text-primary">📁 database/</strong>
                                    <div class="ms-4">
                                        <div>📁 migrations/</div>
                                        <div>📁 seeders/</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end::Card-->
                </div>
                <!--end::Col-->

                <!--begin::Col-->
                <div class="col-xl-8">
                    <!--begin::Card-->
                    <div class="card card-flush h-xl-100">
                        <div class="card-header pt-7">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold text-gray-800">🔨 Gerador de Código</span>
                                <span class="text-gray-400 mt-1 fw-semibold fs-6">Crie seu módulo personalizado</span>
                            </h3>
                        </div>
                        <div class="card-body">
                            <div style="background: #f9f9f9; padding: 30px; border-radius: 10px;">
                                <form id="module-generator-form">
                                    <div class="row mb-5">
                                        <div class="col-md-6">
                                            <label class="form-label required">Nome do Módulo</label>
                                            <input type="text" class="form-control" id="nome_modulo" 
                                                   placeholder="Ex: produto, categoria, cliente" required>
                                            <div class="form-text">Use singular, minúsculo, sem espaços</div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label required">Tipo de Módulo</label>
                                            <select class="form-select" id="tipo_modulo" required>
                                                <option value="">Selecione...</option>
                                                <option value="crud">CRUD Completo</option>
                                                <option value="simples">Página Simples</option>
                                                <option value="vue">Módulo com Vue.js</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-5">
                                        <label class="form-label">Funcionalidades Extras</label>
                                        <div class="d-flex flex-wrap">
                                            <div class="form-check form-check-custom form-check-solid me-5 mb-3">
                                                <input class="form-check-input" type="checkbox" value="search" id="feat_search">
                                                <label class="form-check-label" for="feat_search">
                                                    Busca/Filtros
                                                </label>
                                            </div>
                                            <div class="form-check form-check-custom form-check-solid me-5 mb-3">
                                                <input class="form-check-input" type="checkbox" value="pagination" id="feat_pagination">
                                                <label class="form-check-label" for="feat_pagination">
                                                    Paginação
                                                </label>
                                            </div>
                                            <div class="form-check form-check-custom form-check-solid me-5 mb-3">
                                                <input class="form-check-input" type="checkbox" value="export" id="feat_export">
                                                <label class="form-check-label" for="feat_export">
                                                    Exportar Excel/PDF
                                                </label>
                                            </div>
                                            <div class="form-check form-check-custom form-check-solid me-5 mb-3">
                                                <input class="form-check-input" type="checkbox" value="permissions" id="feat_permissions">
                                                <label class="form-check-label" for="feat_permissions">
                                                    Controle de Permissões
                                                </label>
                                            </div>
                                            <div class="form-check form-check-custom form-check-solid me-5 mb-3">
                                                <input class="form-check-input" type="checkbox" value="api" id="feat_api">
                                                <label class="form-check-label" for="feat_api">
                                                    API REST
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="d-flex justify-content-end">
                                        <button type="button" class="btn btn-primary" id="generate-btn">
                                            <i class="ki-duotone ki-code fs-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                                <span class="path3"></span>
                                                <span class="path4"></span>
                                            </i>
                                            Gerar Código
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!--end::Card-->
                </div>
                <!--end::Col-->
            </div>
            <!--end::Row-->

            <!--begin::Generated Code Section-->
            <div id="generated-code-section" class="mt-10" style="display: none;">
                <!--begin::Card-->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">📝 Código Gerado</h3>
                        <div class="card-toolbar">
                            <ul class="nav nav-tabs nav-line-tabs nav-stretch fs-6 border-0">
                                <li class="nav-item">
                                    <a class="nav-link active" data-bs-toggle="tab" href="#tab_controller">Controller</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#tab_model">Model</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#tab_migration">Migration</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#tab_view">View</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#tab_routes">Routes</a>
                                </li>
                                <li class="nav-item" id="vue-tab" style="display: none;">
                                    <a class="nav-link" data-bs-toggle="tab" href="#tab_vue">Vue Component</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="tab_controller">
                                <div style="background: #1e1e2e; color: #e1e1e1; border-radius: 8px; padding: 20px; position: relative;">
                                    <button class="btn btn-sm btn-secondary position-absolute" style="top: 10px; right: 10px;" onclick="copyToClipboard('controller-code')">📋 Copiar</button>
                                    <pre id="controller-code" style="margin: 0; white-space: pre-wrap;"></pre>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="tab_model">
                                <div style="background: #1e1e2e; color: #e1e1e1; border-radius: 8px; padding: 20px; position: relative;">
                                    <button class="btn btn-sm btn-secondary position-absolute" style="top: 10px; right: 10px;" onclick="copyToClipboard('model-code')">📋 Copiar</button>
                                    <pre id="model-code" style="margin: 0; white-space: pre-wrap;"></pre>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="tab_migration">
                                <div style="background: #1e1e2e; color: #e1e1e1; border-radius: 8px; padding: 20px; position: relative;">
                                    <button class="btn btn-sm btn-secondary position-absolute" style="top: 10px; right: 10px;" onclick="copyToClipboard('migration-code')">📋 Copiar</button>
                                    <pre id="migration-code" style="margin: 0; white-space: pre-wrap;"></pre>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="tab_view">
                                <div style="background: #1e1e2e; color: #e1e1e1; border-radius: 8px; padding: 20px; position: relative;">
                                    <button class="btn btn-sm btn-secondary position-absolute" style="top: 10px; right: 10px;" onclick="copyToClipboard('view-code')">📋 Copiar</button>
                                    <pre id="view-code" style="margin: 0; white-space: pre-wrap;"></pre>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="tab_routes">
                                <div style="background: #1e1e2e; color: #e1e1e1; border-radius: 8px; padding: 20px; position: relative;">
                                    <button class="btn btn-sm btn-secondary position-absolute" style="top: 10px; right: 10px;" onclick="copyToClipboard('routes-code')">📋 Copiar</button>
                                    <pre id="routes-code" style="margin: 0; white-space: pre-wrap;"></pre>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="tab_vue">
                                <div style="background: #1e1e2e; color: #e1e1e1; border-radius: 8px; padding: 20px; position: relative;">
                                    <button class="btn btn-sm btn-secondary position-absolute" style="top: 10px; right: 10px;" onclick="copyToClipboard('vue-code')">📋 Copiar</button>
                                    <pre id="vue-code" style="margin: 0; white-space: pre-wrap;"></pre>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end::Card-->
            </div>
            <!--end::Generated Code Section-->

            <!--begin::Steps Guide-->
            <div class="mt-10">
                <!--begin::Card-->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">📚 Guia Passo a Passo</h3>
                    </div>
                    <div class="card-body">
                        <div class="accordion" id="accordionSteps">
                            <!--begin::Step 1-->
                            <div class="accordion-item mb-3">
                                <h2 class="accordion-header" id="step1">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" 
                                            data-bs-target="#collapse1" aria-expanded="true">
                                        <span class="badge badge-primary me-3">1</span>
                                        <div>
                                            <div class="fw-bold">Criar o Controller</div>
                                            <div class="text-muted fs-7">Defina a lógica de negócio</div>
                                        </div>
                                    </button>
                                </h2>
                                <div id="collapse1" class="accordion-collapse collapse show" data-bs-parent="#accordionSteps">
                                    <div class="accordion-body">
                                        <p>Controllers processam requisições e retornam respostas. Localize em <code>app/Http/Controllers/</code></p>
                                        
                                        <div class="alert alert-info">
                                            <strong>Dica:</strong> Use Resource Controllers para CRUDs: <code>php artisan make:controller NomeController --resource</code>
                                        </div>

                                        <h5>Organização:</h5>
                                        <ul>
                                            <li><code>Admin/</code> - Controllers administrativos</li>
                                            <li><code>Api/</code> - Controllers para API REST</li>
                                            <li><code>Parlamentar/</code> - Área dos parlamentares</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <!--end::Step 1-->

                            <!--begin::Step 2-->
                            <div class="accordion-item mb-3">
                                <h2 class="accordion-header" id="step2">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
                                            data-bs-target="#collapse2">
                                        <span class="badge badge-success me-3">2</span>
                                        <div>
                                            <div class="fw-bold">Criar o Model</div>
                                            <div class="text-muted fs-7">Defina a estrutura de dados</div>
                                        </div>
                                    </button>
                                </h2>
                                <div id="collapse2" class="accordion-collapse collapse" data-bs-parent="#accordionSteps">
                                    <div class="accordion-body">
                                        <p>Models representam tabelas do banco. Crie em <code>app/Models/</code></p>
                                        
                                        <h5>Convenções:</h5>
                                        <ul>
                                            <li>Nome no singular (User, Proposicao)</li>
                                            <li>Use <code>$fillable</code> para campos</li>
                                            <li>Defina <code>$casts</code> para tipos</li>
                                            <li>Implemente relacionamentos</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <!--end::Step 2-->

                            <!--begin::Step 3-->
                            <div class="accordion-item mb-3">
                                <h2 class="accordion-header" id="step3">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
                                            data-bs-target="#collapse3">
                                        <span class="badge badge-info me-3">3</span>
                                        <div>
                                            <div class="fw-bold">Criar a Migration</div>
                                            <div class="text-muted fs-7">Estruture o banco de dados</div>
                                        </div>
                                    </button>
                                </h2>
                                <div id="collapse3" class="accordion-collapse collapse" data-bs-parent="#accordionSteps">
                                    <div class="accordion-body">
                                        <p>Migrations definem tabelas. Crie em <code>database/migrations/</code></p>
                                        
                                        <h5>Comando:</h5>
                                        <code>php artisan make:migration create_nome_tabela_table</code>
                                        
                                        <h5 class="mt-3">Boas Práticas:</h5>
                                        <ul>
                                            <li>Adicione índices para busca</li>
                                            <li>Use foreign keys</li>
                                            <li>Defina valores default</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <!--end::Step 3-->

                            <!--begin::Step 4-->
                            <div class="accordion-item mb-3">
                                <h2 class="accordion-header" id="step4">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
                                            data-bs-target="#collapse4">
                                        <span class="badge badge-warning me-3">4</span>
                                        <div>
                                            <div class="fw-bold">Criar as Views</div>
                                            <div class="text-muted fs-7">Interface com Blade ou Vue.js</div>
                                        </div>
                                    </button>
                                </h2>
                                <div id="collapse4" class="accordion-collapse collapse" data-bs-parent="#accordionSteps">
                                    <div class="accordion-body">
                                        <p>Views definem a interface. Crie em <code>resources/views/nome-modulo/</code></p>
                                        
                                        <h5>Blade Template:</h5>
                                        <code>@@extends('components.layouts.app')</code>
                                        
                                        <h5 class="mt-3">Vue.js:</h5>
                                        <p>Crie componentes em <code>resources/js/components/</code></p>
                                    </div>
                                </div>
                            </div>
                            <!--end::Step 4-->

                            <!--begin::Step 5-->
                            <div class="accordion-item mb-3">
                                <h2 class="accordion-header" id="step5">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
                                            data-bs-target="#collapse5">
                                        <span class="badge badge-danger me-3">5</span>
                                        <div>
                                            <div class="fw-bold">Configurar Rotas</div>
                                            <div class="text-muted fs-7">Defina os endpoints</div>
                                        </div>
                                    </button>
                                </h2>
                                <div id="collapse5" class="accordion-collapse collapse" data-bs-parent="#accordionSteps">
                                    <div class="accordion-body">
                                        <p>Configure em <code>routes/web.php</code> ou <code>routes/api.php</code></p>
                                        
                                        <h5>Web Routes:</h5>
                                        <code>Route::resource('modulo', ModuloController::class);</code>
                                        
                                        <h5 class="mt-3">API Routes:</h5>
                                        <code>Route::apiResource('modulo', ModuloController::class);</code>
                                    </div>
                                </div>
                            </div>
                            <!--end::Step 5-->

                            <!--begin::Step 6-->
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="step6">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
                                            data-bs-target="#collapse6">
                                        <span class="badge badge-dark me-3">6</span>
                                        <div>
                                            <div class="fw-bold">Adicionar ao Menu</div>
                                            <div class="text-muted fs-7">Integre na navegação</div>
                                        </div>
                                    </button>
                                </h2>
                                <div id="collapse6" class="accordion-collapse collapse" data-bs-parent="#accordionSteps">
                                    <div class="accordion-body">
                                        <p>Adicione em <code>resources/views/components/layouts/aside.blade.php</code></p>
                                        
                                        <h5>Permissões:</h5>
                                        <code>@@can('modulo.visualizar') ... @@endcan</code>
                                    </div>
                                </div>
                            </div>
                            <!--end::Step 6-->
                        </div>
                    </div>
                </div>
                <!--end::Card-->
            </div>
            <!--end::Steps Guide-->

            <!--begin::Best Practices-->
            <div class="row g-5 g-xl-10 mt-5">
                <div class="col-xl-6">
                    <!--begin::Card-->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">✅ Boas Práticas</h3>
                        </div>
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-4">
                                <div class="symbol symbol-40px me-4">
                                    <div class="symbol-label bg-light-success">
                                        <i class="ki-duotone ki-check-circle fs-2 text-success">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </div>
                                </div>
                                <div>
                                    <div class="fw-bold">Nomenclatura Consistente</div>
                                    <div class="text-muted fs-7">PascalCase para Controllers</div>
                                </div>
                            </div>
                            
                            <div class="d-flex align-items-center mb-4">
                                <div class="symbol symbol-40px me-4">
                                    <div class="symbol-label bg-light-success">
                                        <i class="ki-duotone ki-shield-tick fs-2 text-success">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </div>
                                </div>
                                <div>
                                    <div class="fw-bold">Validação de Dados</div>
                                    <div class="text-muted fs-7">Use Form Requests</div>
                                </div>
                            </div>
                            
                            <div class="d-flex align-items-center mb-4">
                                <div class="symbol symbol-40px me-4">
                                    <div class="symbol-label bg-light-success">
                                        <i class="ki-duotone ki-lock fs-2 text-success">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </div>
                                </div>
                                <div>
                                    <div class="fw-bold">Segurança</div>
                                    <div class="text-muted fs-7">CSRF protection</div>
                                </div>
                            </div>
                            
                            <div class="d-flex align-items-center">
                                <div class="symbol symbol-40px me-4">
                                    <div class="symbol-label bg-light-success">
                                        <i class="ki-duotone ki-rocket fs-2 text-success">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </div>
                                </div>
                                <div>
                                    <div class="fw-bold">Performance</div>
                                    <div class="text-muted fs-7">Eager loading</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end::Card-->
                </div>
                
                <div class="col-xl-6">
                    <!--begin::Card-->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">🛠️ Ferramentas Úteis</h3>
                        </div>
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-4">
                                <div class="symbol symbol-40px me-4">
                                    <div class="symbol-label bg-light-primary">
                                        <i class="ki-duotone ki-code fs-2 text-primary">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                            <span class="path4"></span>
                                        </i>
                                    </div>
                                </div>
                                <div>
                                    <div class="fw-bold">Laravel Artisan</div>
                                    <div class="text-muted fs-7">CLI para tarefas</div>
                                </div>
                            </div>
                            
                            <div class="d-flex align-items-center mb-4">
                                <div class="symbol symbol-40px me-4">
                                    <div class="symbol-label bg-light-primary">
                                        <i class="ki-duotone ki-abstract-26 fs-2 text-primary">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </div>
                                </div>
                                <div>
                                    <div class="fw-bold">Vue DevTools</div>
                                    <div class="text-muted fs-7">Debug de componentes</div>
                                </div>
                            </div>
                            
                            <div class="d-flex align-items-center mb-4">
                                <div class="symbol symbol-40px me-4">
                                    <div class="symbol-label bg-light-primary">
                                        <i class="ki-duotone ki-data fs-2 text-primary">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                            <span class="path4"></span>
                                            <span class="path5"></span>
                                        </i>
                                    </div>
                                </div>
                                <div>
                                    <div class="fw-bold">Laravel Debugbar</div>
                                    <div class="text-muted fs-7">Análise de queries</div>
                                </div>
                            </div>
                            
                            <div class="d-flex align-items-center">
                                <div class="symbol symbol-40px me-4">
                                    <div class="symbol-label bg-light-primary">
                                        <i class="ki-duotone ki-chart-line-up fs-2 text-primary">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </div>
                                </div>
                                <div>
                                    <div class="fw-bold">Laravel Telescope</div>
                                    <div class="text-muted fs-7">Monitoramento</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end::Card-->
                </div>
            </div>
            <!--end::Best Practices-->

            <!--begin::Case Study-->
            <div class="mt-10">
                <!--begin::Card-->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">📚 Caso Prático: Módulo Biblioteca Digital</h3>
                        <div class="card-toolbar">
                            <span class="badge badge-light-success">Exemplo Completo</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info d-flex align-items-center p-5 mb-8">
                            <i class="ki-duotone ki-information-5 fs-2hx text-info me-4">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                            </i>
                            <div>
                                <h5 class="mb-1">Exemplo Real do Sistema</h5>
                                <span>Veja como seria desenvolvido o módulo <strong>Biblioteca Digital</strong> seguindo todas as melhores práticas do sistema Legisinc.</span>
                            </div>
                        </div>

                        <div class="row g-5">
                            <div class="col-xl-6">
                                <h4 class="mb-5">📋 Especificações do Módulo</h4>
                                <div class="d-flex align-items-center mb-4">
                                    <div class="symbol symbol-40px me-4">
                                        <div class="symbol-label bg-light-primary">
                                            <i class="ki-duotone ki-abstract-26 fs-2 text-primary">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="fw-bold">Funcionalidades</div>
                                        <div class="text-muted fs-7">Acervo digital, busca avançada, digitalização</div>
                                    </div>
                                </div>
                                
                                <div class="d-flex align-items-center mb-4">
                                    <div class="symbol symbol-40px me-4">
                                        <div class="symbol-label bg-light-success">
                                            <i class="ki-duotone ki-shield-tick fs-2 text-success">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="fw-bold">Controle de Acesso</div>
                                        <div class="text-muted fs-7">6 níveis diferentes de permissão</div>
                                    </div>
                                </div>
                                
                                <div class="d-flex align-items-center mb-4">
                                    <div class="symbol symbol-40px me-4">
                                        <div class="symbol-label bg-light-warning">
                                            <i class="ki-duotone ki-abstract-26 fs-2 text-warning">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="fw-bold">Tecnologias</div>
                                        <div class="text-muted fs-7">Laravel + Vue.js + OCR + Search</div>
                                    </div>
                                </div>
                                
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-40px me-4">
                                        <div class="symbol-label bg-light-info">
                                            <i class="ki-duotone ki-chart-line-up fs-2 text-info">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="fw-bold">Complexidade</div>
                                        <div class="text-muted fs-7">Alta - 15+ arquivos, múltiplos services</div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-xl-6">
                                <h4 class="mb-5">🏗️ Estrutura Implementada</h4>
                                <div style="font-family: monospace; background: #f8f9fa; padding: 20px; border-radius: 8px; line-height: 1.6;">
                                    <div class="text-primary fw-bold">📁 Controllers (3)</div>
                                    <div class="ms-3 mb-2">
                                        <div>├── BibliotecaController.php</div>
                                        <div>├── BibliotecaApiController.php</div>
                                        <div>└── BibliotecaAdminController.php</div>
                                    </div>
                                    
                                    <div class="text-success fw-bold">📁 Models (4)</div>
                                    <div class="ms-3 mb-2">
                                        <div>├── BibliotecaItem.php</div>
                                        <div>├── BibliotecaCategoria.php</div>
                                        <div>├── BibliotecaMetadata.php</div>
                                        <div>└── BibliotecaAcesso.php</div>
                                    </div>
                                    
                                    <div class="text-warning fw-bold">📁 Views (8+)</div>
                                    <div class="ms-3 mb-2">
                                        <div>├── index.blade.php</div>
                                        <div>├── show.blade.php</div>
                                        <div>├── admin/dashboard.blade.php</div>
                                        <div>└── components/*.blade.php</div>
                                    </div>
                                    
                                    <div class="text-info fw-bold">📁 Vue Components (4)</div>
                                    <div class="ms-3">
                                        <div>├── BibliotecaSearch.vue</div>
                                        <div>├── BibliotecaViewer.vue</div>
                                        <div>├── BibliotecaUpload.vue</div>
                                        <div>└── BibliotecaMetadata.vue</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="separator my-8"></div>

                        <div class="text-center">
                            <h4 class="mb-5">📖 Guia Completo Disponível</h4>
                            <p class="text-muted fs-6 mb-8">
                                Este exemplo inclui regras de negócio detalhadas, estrutura de permissões, 
                                código completo dos controllers, models, views e components Vue.js, 
                                além de jobs para processamento em background.
                            </p>
                            <a href="{{ route('admin.guia-desenvolvimento.biblioteca-digital') }}" 
                               class="btn btn-lg btn-primary me-3" target="_blank">
                                <i class="ki-duotone ki-document fs-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                Ver Guia Completo
                            </a>
                            <span class="text-muted fs-7">
                                <i class="ki-duotone ki-information fs-5 me-1">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                                +200 linhas de código com exemplos práticos
                            </span>
                        </div>
                    </div>
                </div>
                <!--end::Card-->
            </div>
            <!--end::Case Study-->

        </div>
    </div>
    <!--end::Content-->
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const generateBtn = document.getElementById('generate-btn');
    if (generateBtn) {
        generateBtn.addEventListener('click', function() {
            const form = document.getElementById('module-generator-form');
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }
            
            const nomeModulo = document.getElementById('nome_modulo').value;
            const tipoModulo = document.getElementById('tipo_modulo').value;
            
            const funcionalidades = [];
            document.querySelectorAll('input[type="checkbox"]:checked').forEach(cb => {
                funcionalidades.push(cb.value);
            });
            
            // Loading state
            generateBtn.disabled = true;
            generateBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Gerando...';
            
            // Make request
            fetch('{{ route("admin.guia-desenvolvimento.gerar") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    nome_modulo: nomeModulo,
                    tipo: tipoModulo,
                    funcionalidades: funcionalidades
                })
            })
            .then(response => response.json())
            .then(data => {
                // Update code blocks
                document.getElementById('controller-code').textContent = data.controller;
                document.getElementById('model-code').textContent = data.model;
                document.getElementById('migration-code').textContent = data.migration;
                document.getElementById('view-code').textContent = data.view;
                document.getElementById('routes-code').textContent = data.routes;
                
                if (tipoModulo === 'vue' && data.vue_component) {
                    document.getElementById('vue-code').textContent = data.vue_component;
                    document.getElementById('vue-tab').style.display = 'block';
                } else {
                    document.getElementById('vue-tab').style.display = 'none';
                }
                
                // Show generated code section
                document.getElementById('generated-code-section').style.display = 'block';
                
                // Scroll to generated code
                document.getElementById('generated-code-section').scrollIntoView({ behavior: 'smooth' });
                
                // Reset button
                generateBtn.disabled = false;
                generateBtn.innerHTML = '<i class="ki-duotone ki-code fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i> Gerar Código';
                
                // Success message
                if (window.toastr) {
                    toastr.success('Código gerado com sucesso!');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                // Reset button
                generateBtn.disabled = false;
                generateBtn.innerHTML = '<i class="ki-duotone ki-code fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i> Gerar Código';
                
                // Error message
                if (window.toastr) {
                    toastr.error('Erro ao gerar o código');
                } else {
                    alert('Erro ao gerar o código');
                }
            });
        });
    }
});

function copyToClipboard(elementId) {
    const code = document.getElementById(elementId).textContent;
    navigator.clipboard.writeText(code).then(() => {
        if (window.toastr) {
            toastr.success('Código copiado!');
        } else {
            alert('Código copiado!');
        }
    }).catch(err => {
        console.error('Erro ao copiar:', err);
    });
}
</script>
@endsection