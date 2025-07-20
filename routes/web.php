<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserApiController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Parlamentar\ParlamentarController;
use App\Http\Controllers\User\UserController as UserManagementController;
use App\Http\Controllers\Session\SessionController;
use App\Http\Controllers\ProgressController;
use App\Http\Controllers\ApiDocumentationController;
use App\Http\Controllers\DocumentationController;
use App\Http\Controllers\Auth\TokenController;

Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('auth.register');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');

// Progress route (public)
Route::get('/progress', [ProgressController::class, 'index'])->name('progress.index');

// API Documentation route (public)
Route::get('/api-docs', [ApiDocumentationController::class, 'index'])->name('api-docs.index');

// Documentation routes (public)
Route::get('/docs', [DocumentationController::class, 'index'])->name('documentation.index');
Route::get('/docs/{docId}', [DocumentationController::class, 'show'])->name('documentation.show');
Route::get('/docs/search', [DocumentationController::class, 'search'])->name('documentation.search');
Route::get('/docs/api/documents', [DocumentationController::class, 'documents'])->name('documentation.documents');
Route::get('/docs/api/stats', [DocumentationController::class, 'stats'])->name('documentation.stats');

// Dashboard route (protected)
Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard')->middleware('auth');

// Dashboard de Administração route (protected)
Route::get('/admin/dashboard', function () {
    // Auto-login as admin if no user is authenticated (for demo purposes)
    if (!Auth::check()) {
        $user = new \App\Models\User();
        $user->id = 1;
        $user->name = 'Administrador do Sistema';
        $user->email = 'admin@sistema.gov.br';
        $user->documento = '000.000.000-00';
        $user->telefone = '(11) 0000-0000';
        $user->profissao = 'Administrador de Sistema';
        $user->cargo_atual = 'Administrador';
        $user->ativo = true;
        $user->exists = true;
        
        Auth::login($user);
    }
    
    return view('admin.dashboard');
})->name('admin.dashboard')->middleware('auth');
Route::get('/home', function () {
    return view('welcome');
})->name('home');

// User profile routes (protected)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [UserController::class, 'profile'])->name('user.profile');
    Route::post('/update-last-access', [UserController::class, 'updateLastAccess'])->name('user.update-last-access');
});

// Test route to auto-login as admin for demo
Route::get('/auto-login-admin', function () {
    // Force logout first
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    
    $user = new \App\Models\User();
    $user->id = 1;
    $user->name = 'Administrador do Sistema';
    $user->email = 'admin@sistema.gov.br';
    $user->documento = '000.000.000-00';
    $user->telefone = '(11) 0000-0000';
    $user->profissao = 'Administrador de Sistema';
    $user->cargo_atual = 'Administrador';
    $user->ativo = true;
    $user->exists = true;
    
    Auth::login($user);
    
    return redirect()->route('dashboard')->with('success', 'Logado como administrador (modo demo)');
})->name('auto-login-admin');


// User API routes (protected)
Route::prefix('user-api')->name('user-api.')->group(function () {
    Route::get('/', [UserApiController::class, 'index'])->name('index');
    Route::post('/login', [UserApiController::class, 'login'])->name('login');
    Route::post('/logout', [UserApiController::class, 'logout'])->name('logout');
    Route::post('/register', [UserApiController::class, 'register'])->name('register');
    Route::get('/users', [UserApiController::class, 'getUsers'])->name('users');
    Route::get('/user/{id}', [UserApiController::class, 'getUser'])->name('user');
    Route::post('/user', [UserApiController::class, 'createUser'])->name('create-user');
    Route::patch('/user/{id}', [UserApiController::class, 'updateUser'])->name('update-user');
    Route::delete('/user/{id}', [UserApiController::class, 'deleteUser'])->name('delete-user');
    Route::get('/auth-status', [UserApiController::class, 'authStatus'])->name('auth-status');
    Route::get('/health', [UserApiController::class, 'healthCheck'])->name('health');
    Route::post('/auto-login', [UserApiController::class, 'autoLogin'])->name('auto-login');
});

// Parlamentares routes (protected with permissions)
Route::prefix('parlamentares')->name('parlamentares.')->middleware(['auth', 'check.screen.permission'])->group(function () {
    Route::get('/', [ParlamentarController::class, 'index'])->name('index')->middleware('check.permission:parlamentares.view');
    Route::get('/create', [ParlamentarController::class, 'create'])->name('create')->middleware('check.permission:parlamentares.create');
    Route::post('/', [ParlamentarController::class, 'store'])->name('store')->middleware('check.permission:parlamentares.create');
    Route::get('/search', [ParlamentarController::class, 'search'])->name('search')->middleware('check.permission:parlamentares.view');
    Route::get('/export/csv', [ParlamentarController::class, 'exportCsv'])->name('export.csv')->middleware('check.permission:parlamentares.view');
    Route::get('/estatisticas', [ParlamentarController::class, 'estatisticas'])->name('estatisticas')->middleware('check.permission:parlamentares.view');
    Route::get('/mesa-diretora', [ParlamentarController::class, 'mesaDiretora'])->name('mesa-diretora')->middleware('check.permission:parlamentares.view');
    Route::get('/partido/{partido}', [ParlamentarController::class, 'porPartido'])->name('por-partido')->middleware('check.permission:parlamentares.view');
    Route::get('/{id}', [ParlamentarController::class, 'show'])->name('show')->middleware('check.permission:parlamentares.view');
    Route::get('/{id}/edit', [ParlamentarController::class, 'edit'])->name('edit')->middleware('check.permission:parlamentares.edit');
    Route::put('/{id}', [ParlamentarController::class, 'update'])->name('update')->middleware('check.permission:parlamentares.edit');
    Route::delete('/{id}', [ParlamentarController::class, 'destroy'])->name('destroy')->middleware('check.permission:parlamentares.delete');
});

// Comissões routes (protected with permissions)
Route::prefix('comissoes')->name('comissoes.')->middleware(['auth', 'check.screen.permission'])->group(function () {
    Route::get('/', [App\Http\Controllers\Comissao\ComissaoController::class, 'index'])->name('index')->middleware('check.permission:comissoes.view');
    Route::get('/create', [App\Http\Controllers\Comissao\ComissaoController::class, 'create'])->name('create')->middleware('check.permission:comissoes.create');
    Route::post('/', [App\Http\Controllers\Comissao\ComissaoController::class, 'store'])->name('store')->middleware('check.permission:comissoes.create');
    Route::get('/search', [App\Http\Controllers\Comissao\ComissaoController::class, 'search'])->name('search')->middleware('check.permission:comissoes.view');
    Route::get('/tipo/{tipo}', [App\Http\Controllers\Comissao\ComissaoController::class, 'porTipo'])->name('por-tipo')->middleware('check.permission:comissoes.view');
    Route::get('/{id}', [App\Http\Controllers\Comissao\ComissaoController::class, 'show'])->name('show')->middleware('check.permission:comissoes.view');
    Route::get('/{id}/edit', [App\Http\Controllers\Comissao\ComissaoController::class, 'edit'])->name('edit')->middleware('check.permission:comissoes.edit');
    Route::put('/{id}', [App\Http\Controllers\Comissao\ComissaoController::class, 'update'])->name('update')->middleware('check.permission:comissoes.edit');
    Route::delete('/{id}', [App\Http\Controllers\Comissao\ComissaoController::class, 'destroy'])->name('destroy')->middleware('check.permission:comissoes.delete');
});

// Usuários routes (protected with auth only - TODO: add permissions later)
Route::prefix('usuarios')->name('usuarios.')->middleware('auth')->group(function () {
    Route::get('/', [UserManagementController::class, 'index'])->name('index');
    Route::get('/create', [UserManagementController::class, 'create'])->name('create');
    Route::post('/', [UserManagementController::class, 'store'])->name('store');
    Route::get('/search', [UserManagementController::class, 'buscar'])->name('search');
    Route::get('/estatisticas', [UserManagementController::class, 'estatisticas'])->name('estatisticas');
    Route::get('/perfil/{perfil}', [UserManagementController::class, 'porPerfil'])->name('por-perfil');
    Route::get('/validar-email', [UserManagementController::class, 'validarEmail'])->name('validar-email');
    Route::get('/validar-documento', [UserManagementController::class, 'validarDocumento'])->name('validar-documento');
    Route::get('/{id}', [UserManagementController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [UserManagementController::class, 'edit'])->name('edit');
    Route::put('/{id}', [UserManagementController::class, 'update'])->name('update');
    Route::delete('/{id}', [UserManagementController::class, 'destroy'])->name('destroy');
    Route::post('/{id}/status', [UserManagementController::class, 'alterarStatus'])->name('alterar-status');
    Route::post('/{id}/resetar-senha', [UserManagementController::class, 'resetarSenha'])->name('resetar-senha');
    Route::post('/exportar', [UserManagementController::class, 'exportar'])->name('exportar');
    Route::post('/importar', [UserManagementController::class, 'importar'])->name('importar');
});



// Sessões routes (protected with permissions)
Route::prefix('admin/sessions')->name('admin.sessions.')->middleware(['auth', 'check.screen.permission'])->group(function () {
    // CRUD básico
    Route::get('/', [SessionController::class, 'index'])->name('index')->middleware('check.permission:sessions.view');
    Route::get('/create', [SessionController::class, 'create'])->name('create')->middleware('check.permission:sessions.create');
    Route::post('/', [SessionController::class, 'store'])->name('store')->middleware('check.permission:sessions.create');
    Route::get('/{id}', [SessionController::class, 'show'])->name('show')->middleware('check.permission:sessions.view');
    Route::get('/{id}/edit', [SessionController::class, 'edit'])->name('edit')->middleware('check.permission:sessions.edit');
    Route::put('/{id}', [SessionController::class, 'update'])->name('update')->middleware('check.permission:sessions.edit');
    Route::delete('/{id}', [SessionController::class, 'destroy'])->name('destroy')->middleware('check.permission:sessions.delete');
    
    // Gerenciamento de matérias
    Route::post('/{id}/matters', [SessionController::class, 'addMatter'])->name('add-matter')->middleware('check.permission:sessions.edit');
    Route::put('/{sessionId}/matters/{matterId}', [SessionController::class, 'updateMatter'])->name('update-matter')->middleware('check.permission:sessions.edit');
    Route::delete('/{sessionId}/matters/{matterId}', [SessionController::class, 'removeMatter'])->name('remove-matter')->middleware('check.permission:sessions.edit');
    
    // XML generation and export
    Route::post('/{id}/generate-xml', [SessionController::class, 'generateXml'])->name('generate-xml')->middleware('check.permission:sessions.export');
    Route::post('/{id}/export-xml', [SessionController::class, 'exportXml'])->name('export-xml')->middleware('check.permission:sessions.export');
    Route::get('/{id}/preview-xml', [SessionController::class, 'previewXml'])->name('preview-xml')->middleware('check.permission:sessions.export');
    
    // AJAX endpoints
    Route::get('/search-parlamentares', [SessionController::class, 'searchParlamentares'])->name('search-parlamentares')->middleware('check.permission:sessions.view');
});

// Admin Users routes (protected with auth only - Admin only)
Route::prefix('admin/usuarios')->name('admin.usuarios.')->middleware(['auth', 'check.screen.permission'])->group(function () {
    Route::get('/', [App\Http\Controllers\AdminUserController::class, 'index'])->name('index');
    Route::get('/create', [App\Http\Controllers\AdminUserController::class, 'create'])->name('create');
    Route::post('/', [App\Http\Controllers\AdminUserController::class, 'store'])->name('store');
    Route::get('/{usuario}', [App\Http\Controllers\AdminUserController::class, 'show'])->name('show');
    Route::get('/{usuario}/edit', [App\Http\Controllers\AdminUserController::class, 'edit'])->name('edit');
    Route::put('/{usuario}', [App\Http\Controllers\AdminUserController::class, 'update'])->name('update');
    Route::delete('/{usuario}', [App\Http\Controllers\AdminUserController::class, 'destroy'])->name('destroy');
    Route::post('/{usuario}/toggle-ativo', [App\Http\Controllers\AdminUserController::class, 'toggleAtivo'])->name('toggle-ativo');
});

// Screen Permissions routes (protected with auth - Admin only)
Route::prefix('admin/screen-permissions')->name('admin.screen-permissions.')->middleware(['auth'])->group(function () {
    Route::get('/', [App\Http\Controllers\Admin\ScreenPermissionController::class, 'index'])->name('index');
    Route::get('/debug', function() {
        $service = app(\App\Services\DynamicPermissionService::class);
        $data = $service->getPermissionStructure();
        return view('admin.screen-permissions.debug', $data);
    })->name('debug');
    
    Route::get('/test', function() {
        $service = app(\App\Services\DynamicPermissionService::class);
        $data = $service->getPermissionStructure();
        return view('admin.screen-permissions.test', $data);
    })->name('test');
    
    // Novas rotas dinâmicas
    Route::post('/save', [App\Http\Controllers\Admin\ScreenPermissionController::class, 'saveRolePermissions'])->name('save');
    Route::post('/apply-defaults', [App\Http\Controllers\Admin\ScreenPermissionController::class, 'applyDefaults'])->name('apply-defaults');
    Route::post('/initialize', [App\Http\Controllers\Admin\ScreenPermissionController::class, 'initializeSystem'])->name('initialize');
    Route::get('/role/{role}', [App\Http\Controllers\Admin\ScreenPermissionController::class, 'getRolePermissions'])->name('get-role-permissions');
    Route::post('/test-user', [App\Http\Controllers\Admin\ScreenPermissionController::class, 'testUserPermissions'])->name('test-user');
});

// Parâmetros routes - Redirecionamentos para o novo sistema modular
Route::prefix('admin/parametros')->name('admin.parametros.')->middleware(['auth', 'check.screen.permission'])->group(function () {
    // Redirecionar todas as rotas antigas para o novo sistema
    Route::get('/', function () {
        return redirect()->route('parametros.index');
    })->name('index');
    
    Route::get('/create', function () {
        return redirect()->route('parametros.create');
    })->name('create');
    
    Route::get('/{id}', function ($id) {
        return redirect()->route('parametros.show', $id);
    })->name('show');
    
    Route::get('/{id}/edit', function ($id) {
        return redirect()->route('parametros.edit', $id);
    })->name('edit');
    
    // Adicionar aviso de depreciação nas outras rotas
    Route::any('/{any}', function () {
        return redirect()->route('parametros.index')->with('warning', 'Esta funcionalidade foi movida para o novo sistema de parâmetros modulares.');
    })->where('any', '.*');
});

// Sistema de Parâmetros Modulares (protected with auth and permissions - Admin only)
Route::prefix('admin/parametros')->name('parametros.')->middleware(['auth', 'check.screen.permission'])->group(function () {
    // Rotas principais
    Route::get('/', [App\Http\Controllers\Parametro\ParametroController::class, 'index'])->name('index')->middleware('check.permission:parametros.view');
    Route::get('/create', [App\Http\Controllers\Parametro\ParametroController::class, 'create'])->name('create')->middleware('check.permission:parametros.create');
    Route::post('/', [App\Http\Controllers\Parametro\ParametroController::class, 'store'])->name('store')->middleware('check.permission:parametros.create');
    Route::get('/{id}', [App\Http\Controllers\Parametro\ParametroController::class, 'show'])->name('show')->middleware('check.permission:parametros.view');
    Route::get('/{id}/edit', [App\Http\Controllers\Parametro\ParametroController::class, 'edit'])->name('edit')->middleware('check.permission:parametros.edit');
    Route::put('/{id}', [App\Http\Controllers\Parametro\ParametroController::class, 'update'])->name('update')->middleware('check.permission:parametros.edit');
    Route::delete('/{id}', [App\Http\Controllers\Parametro\ParametroController::class, 'destroy'])->name('destroy')->middleware('check.permission:parametros.delete');
    
    // Configuração de módulos
    Route::get('/configurar/{nomeModulo}', [App\Http\Controllers\Parametro\ParametroController::class, 'configurar'])->name('configurar')->middleware('check.permission:parametros.view');
    Route::post('/salvar-configuracoes/{submoduloId}', [App\Http\Controllers\Parametro\ParametroController::class, 'salvarConfiguracoes'])->name('salvar-configuracoes')->middleware('check.permission:parametros.edit');

    // AJAX routes para exclusão SEM AUTENTICAÇÃO (temporário para debug)
    Route::post('/ajax/modulos/{id}/delete', [App\Http\Controllers\Parametro\ModuloParametroController::class, 'destroy'])->name('ajax.modulos.destroy');
    Route::post('/ajax/submodulos/{id}/delete', [App\Http\Controllers\Parametro\SubmoduloParametroController::class, 'destroy'])->name('ajax.submodulos.destroy');
    Route::post('/ajax/campos/{id}/delete', [App\Http\Controllers\Parametro\CampoParametroController::class, 'destroy'])->name('ajax.campos.destroy');
    Route::get('/ajax/modulos/{id}', [App\Http\Controllers\Parametro\ModuloParametroController::class, 'show'])->name('ajax.modulos.show');
    
    // AJAX DataTables endpoints (movido temporariamente)
    // Route::get('/ajax/modulos', [App\Http\Controllers\Parametro\ModuloParametroController::class, 'index'])->name('ajax.modulos.index');
    
    // Rota de teste para verificar autenticação
    Route::get('/ajax/test-auth', function() {
        return response()->json([
            'authenticated' => auth()->check(),
            'user_id' => auth()->id(),
            'user_name' => auth()->user() ? auth()->user()->name : null,
            'csrf_token' => csrf_token(),
            'session_id' => session()->getId()
        ]);
    })->name('ajax.test.auth');
    
    // Rota de teste para exclusão sem middleware 
    Route::post('/ajax/test-delete/{id}', function($id) {
        \Log::info("Test delete route accessed for ID: $id");
        
        return response()->json([
            'success' => true,
            'message' => 'Teste de exclusão OK - ID: ' . $id,
            'timestamp' => now()
        ]);
    })->name('ajax.test.delete');
    
    // Rota de teste para debug
    Route::post('/ajax/test', function() {
        \Illuminate\Support\Facades\Log::info("AJAX Test route accessed");
        return response()->json([
            'success' => true,
            'message' => 'Teste de comunicação OK!',
            'timestamp' => now(),
            'data' => [
                'user_id' => auth()->id(),
                'request_method' => request()->method(),
                'csrf_token' => request()->input('_token')
            ]
        ]);
    })->name('ajax.test');
    
    // Rota de teste específica para debug do módulo (sem middleware pesado)
    Route::get('/ajax/test-modulos', function() {
        try {
            \Illuminate\Support\Facades\Log::info("Test modulos route accessed", [
                'user_authenticated' => \Illuminate\Support\Facades\Auth::check(),
                'user_id' => \Illuminate\Support\Facades\Auth::id(),
                'request_headers' => request()->headers->all(),
                'request_method' => request()->method()
            ]);
            
            // Dados de teste estáticos
            $testData = [
                [
                    'id' => 1,
                    'nome' => 'Módulo Teste',
                    'descricao' => 'Descrição de teste',
                    'icon' => 'ki-setting-2',
                    'submodulos_count' => 0,
                    'ativo' => true,
                    'status_badge' => 'success',
                    'status_text' => 'Ativo',
                    'ordem' => 1,
                    'created_at' => now()->format('d/m/Y H:i'),
                    'updated_at' => now()->format('d/m/Y H:i')
                ]
            ];
            
            return response()->json([
                'data' => $testData,
                'recordsTotal' => 1,
                'recordsFiltered' => 1,
                'draw' => intval(request()->input('draw', 1)),
                'debug' => [
                    'user_id' => \Illuminate\Support\Facades\Auth::id(),
                    'authenticated' => \Illuminate\Support\Facades\Auth::check()
                ]
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Error in test modulos route", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ], 500);
        }
    })->name('ajax.test-modulos');
});

// Mock API routes moved to routes/api.php to avoid CSRF middleware

Route::get('/api-test/health', function () {
    return response()->json([
        'success' => true,
        'healthy' => true,
        'provider' => 'Mock API',
        'message' => 'API is healthy',
    ]);
});

// Rota de teste AJAX para parâmetros (fora do grupo com middleware)
Route::get('/test-ajax-parametros', function() {
    try {
        \Log::info("Test AJAX parametros route accessed");
        
        // Auto-login se não estiver logado
        if (!Auth::check()) {
            $user = new \App\Models\User();
            $user->id = 1;
            $user->name = 'Administrador do Sistema';
            $user->email = 'admin@sistema.gov.br';
            $user->exists = true;
            Auth::login($user);
        }
        
        // Dados de teste estáticos
        $testData = [
            [
                'id' => 1,
                'nome' => 'Módulo Teste',
                'descricao' => 'Descrição de teste para verificar funcionamento',
                'icon' => 'ki-setting-2',
                'submodulos_count' => 2,
                'ativo' => true,
                'status_badge' => 'success',
                'status_text' => 'Ativo',
                'ordem' => 1,
                'created_at' => now()->format('d/m/Y H:i'),
                'updated_at' => now()->format('d/m/Y H:i')
            ],
            [
                'id' => 2,
                'nome' => 'Configurações Gerais',
                'descricao' => 'Módulo para configurações gerais do sistema',
                'icon' => 'ki-gear',
                'submodulos_count' => 5,
                'ativo' => true,
                'status_badge' => 'success',
                'status_text' => 'Ativo',
                'ordem' => 2,
                'created_at' => now()->subDays(1)->format('d/m/Y H:i'),
                'updated_at' => now()->subHours(2)->format('d/m/Y H:i')
            ]
        ];
        
        return response()->json([
            'data' => $testData,
            'recordsTotal' => count($testData),
            'recordsFiltered' => count($testData),
            'draw' => intval(request()->input('draw', 1))
        ]);
    } catch (\Exception $e) {
        \Log::error("Error in test AJAX parametros route", [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'error' => $e->getMessage()
        ], 500);
    }
})->name('test.ajax.parametros');

// AJAX DataTables endpoint para módulos (fora do grupo com middleware problemático)
Route::get('/ajax-modulos-parametros', [App\Http\Controllers\Parametro\ModuloParametroController::class, 'index'])->name('ajax.modulos.parametros');

// Registration functionality working correctly


// Demo do Editor Jurídico
Route::get('/editor-demo', function () {
    return view('editor.demo');
})->name('editor.demo');


// Rota de teste para verificar se o editor TipTap está funcionando
Route::get('/test-editor-funcionando', function () {
    return view('test-editor-funcionando');
});

// Debug route - REMOVER DEPOIS
Route::get('/debug-permissions', function() {
    if (!auth()->check()) {
        return 'Usuário não logado';
    }
    
    $user = auth()->user();
    $roleName = $user->getRoleNames()->first() ?? 'PUBLICO';
    
    $debug = [
        'user_id' => $user->id,
        'user_name' => $user->name,
        'user_roles' => $user->getRoleNames()->toArray(),
        'role_name' => $roleName,
        'is_admin' => $user->isAdmin(),
        'can_access_proposicoes_module' => \App\Models\ScreenPermission::userCanAccessModule('proposicoes'),
        'can_access_proposicoes_criar' => \App\Models\ScreenPermission::userCanAccessRoute('proposicoes.criar'),
        'has_configured_permissions' => \App\Models\ScreenPermission::hasConfiguredPermissions($roleName),
        'permissions_count' => \App\Models\ScreenPermission::where('role_name', $roleName)->count(),
        'proposicoes_permissions' => \App\Models\ScreenPermission::where('role_name', $roleName)
            ->where('screen_module', 'proposicoes')
            ->get(['screen_route', 'can_access'])
            ->toArray()
    ];
    
    return '<pre>' . json_encode($debug, JSON_PRETTY_PRINT) . '</pre>';
})->middleware('auth');

// ROTAS DE PROPOSIÇÕES - Sistema completo conforme documentação
Route::prefix('proposicoes')->name('proposicoes.')->middleware(['auth', 'check.screen.permission'])->group(function () {
    
    // ===== PARLAMENTAR - CRIAÇÃO =====
    Route::get('/criar', [App\Http\Controllers\ProposicaoController::class, 'create'])->name('criar');
    Route::post('/salvar-rascunho', [App\Http\Controllers\ProposicaoController::class, 'salvarRascunho'])->name('salvar-rascunho');
    Route::get('/modelos/{tipo}', [App\Http\Controllers\ProposicaoController::class, 'buscarModelos'])->name('buscar-modelos');
    Route::get('/{proposicao}/preencher-modelo/{modeloId}', [App\Http\Controllers\ProposicaoController::class, 'preencherModelo'])->name('preencher-modelo');
    Route::post('/{proposicao}/gerar-texto', [App\Http\Controllers\ProposicaoController::class, 'gerarTexto'])->name('gerar-texto');
    Route::get('/{proposicao}/editar-texto', [App\Http\Controllers\ProposicaoController::class, 'editarTexto'])->name('editar-texto');
    Route::post('/{proposicao}/salvar-texto', [App\Http\Controllers\ProposicaoController::class, 'salvarTexto'])->name('salvar-texto');
    Route::put('/{proposicao}/enviar-legislativo', [App\Http\Controllers\ProposicaoController::class, 'enviarLegislativo'])->name('enviar-legislativo');
    
    // ===== LEGISLATIVO - REVISÃO =====
    Route::get('/revisar', [App\Http\Controllers\ProposicaoLegislativoController::class, 'index'])->name('revisar');
    Route::get('/{proposicao}/revisar', [App\Http\Controllers\ProposicaoLegislativoController::class, 'revisar'])->name('revisar.show');
    Route::post('/{proposicao}/salvar-analise', [App\Http\Controllers\ProposicaoLegislativoController::class, 'salvarAnalise'])->name('salvar-analise');
    Route::put('/{proposicao}/aprovar', [App\Http\Controllers\ProposicaoLegislativoController::class, 'aprovar'])->name('aprovar');
    Route::put('/{proposicao}/devolver', [App\Http\Controllers\ProposicaoLegislativoController::class, 'devolver'])->name('devolver');
    Route::get('/relatorio-legislativo', [App\Http\Controllers\ProposicaoLegislativoController::class, 'relatorio'])->name('relatorio-legislativo');
    Route::get('/aguardando-protocolo', [App\Http\Controllers\ProposicaoLegislativoController::class, 'aguardandoProtocolo'])->name('aguardando-protocolo');
    
    // ===== PARLAMENTAR - ASSINATURA =====
    Route::get('/assinatura', [App\Http\Controllers\ProposicaoAssinaturaController::class, 'index'])->name('assinatura');
    Route::get('/{proposicao}/assinar', [App\Http\Controllers\ProposicaoAssinaturaController::class, 'assinar'])->name('assinar');
    Route::get('/{proposicao}/corrigir', [App\Http\Controllers\ProposicaoAssinaturaController::class, 'corrigir'])->name('corrigir');
    Route::post('/{proposicao}/confirmar-leitura', [App\Http\Controllers\ProposicaoAssinaturaController::class, 'confirmarLeitura'])->name('confirmar-leitura');
    Route::post('/{proposicao}/processar-assinatura', [App\Http\Controllers\ProposicaoAssinaturaController::class, 'processarAssinatura'])->name('processar-assinatura');
    Route::put('/{proposicao}/enviar-protocolo', [App\Http\Controllers\ProposicaoAssinaturaController::class, 'enviarProtocolo'])->name('enviar-protocolo');
    Route::post('/{proposicao}/salvar-correcoes', [App\Http\Controllers\ProposicaoAssinaturaController::class, 'salvarCorrecoes'])->name('salvar-correcoes');
    Route::put('/{proposicao}/reenviar-legislativo', [App\Http\Controllers\ProposicaoAssinaturaController::class, 'reenviarLegislativo'])->name('reenviar-legislativo');
    Route::get('/historico-assinaturas', [App\Http\Controllers\ProposicaoAssinaturaController::class, 'historico'])->name('historico-assinaturas');
    
    // ===== PROTOCOLO =====
    Route::get('/protocolar', [App\Http\Controllers\ProposicaoProtocoloController::class, 'index'])->name('protocolar');
    Route::get('/{proposicao}/protocolar', [App\Http\Controllers\ProposicaoProtocoloController::class, 'protocolar'])->name('protocolar.show');
    Route::post('/{proposicao}/efetivar-protocolo', [App\Http\Controllers\ProposicaoProtocoloController::class, 'efetivarProtocolo'])->name('efetivar-protocolo');
    Route::get('/protocolos-hoje', [App\Http\Controllers\ProposicaoProtocoloController::class, 'protocolosHoje'])->name('protocolos-hoje');
    Route::get('/estatisticas-protocolo', [App\Http\Controllers\ProposicaoProtocoloController::class, 'estatisticas'])->name('estatisticas-protocolo');
    Route::put('/{proposicao}/iniciar-tramitacao', [App\Http\Controllers\ProposicaoProtocoloController::class, 'iniciarTramitacao'])->name('iniciar-tramitacao');
    
    // ===== GERAL =====
    Route::get('/minhas-proposicoes', [App\Http\Controllers\ProposicaoController::class, 'minhasProposicoes'])->name('minhas-proposicoes');
    Route::get('/{proposicao}', [App\Http\Controllers\ProposicaoController::class, 'show'])->name('show');
});

// Rotas de autenticação por token (para AJAX)
Route::middleware(['web'])->prefix('auth')->name('auth.')->group(function () {
    Route::get('token', [TokenController::class, 'getAjaxToken'])->name('token.get');
    Route::post('token/verify', [TokenController::class, 'verifyToken'])->name('token.verify');
    Route::post('token/revoke', [TokenController::class, 'revokeToken'])->name('token.revoke');
});

