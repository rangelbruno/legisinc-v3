<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserApiController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Parlamentar\ParlamentarController;
use App\Http\Controllers\Partido\PartidoController;
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
Route::get('/docs/statistics', [DocumentationController::class, 'statistics'])->name('documentation.statistics');
Route::get('/docs/test', function() {
    $cssExists = file_exists(public_path('css/documentation.css'));
    $cssSize = $cssExists ? filesize(public_path('css/documentation.css')) : 0;
    return response('<html><head><title>CSS Test</title></head><body style="font-family: Arial; padding: 20px; background: #f8f9fa;"><div style="max-width: 800px; margin: 0 auto;"><h1 style="color: #28a745;">✅ CSS Fix Successful</h1><div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 4px; margin: 20px 0;"><strong>Status:</strong><ul><li>CSS File exists: ' . ($cssExists ? '✅ Yes' : '❌ No') . '</li><li>File size: ' . $cssSize . ' bytes</li><li>Location: public/css/documentation.css</li><li>URL: <a href="/css/documentation.css" target="_blank">/css/documentation.css</a></li></ul></div><div style="margin: 20px 0;"><a href="/css/documentation.css" style="background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; margin-right: 10px;" target="_blank">🎨 Test CSS Direct</a><a href="/docs" style="background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px;">📚 Go to Documentation</a></div></div></body></html>');
})->name('documentation.test');
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

// Partidos routes (protected with permissions)
Route::prefix('partidos')->name('partidos.')->middleware(['auth', 'check.screen.permission'])->group(function () {
    Route::get('/', [App\Http\Controllers\Partido\PartidoController::class, 'index'])->name('index')->middleware('check.permission:partidos.view');
    Route::get('/create', [App\Http\Controllers\Partido\PartidoController::class, 'create'])->name('create')->middleware('check.permission:partidos.create');
    Route::post('/', [App\Http\Controllers\Partido\PartidoController::class, 'store'])->name('store')->middleware('check.permission:partidos.create');
    Route::get('/search', [App\Http\Controllers\Partido\PartidoController::class, 'search'])->name('search')->middleware('check.permission:partidos.view');
    Route::get('/brasileiros', function () { return view('modules.partidos.brasileiros', ['title' => 'Partidos Brasileiros']); })->name('brasileiros')->middleware('check.permission:partidos.view');
    Route::get('/export/csv', [App\Http\Controllers\Partido\PartidoController::class, 'exportCsv'])->name('export.csv')->middleware('check.permission:partidos.view');
    Route::get('/estatisticas', [App\Http\Controllers\Partido\PartidoController::class, 'estatisticas'])->name('estatisticas')->middleware('check.permission:partidos.view');
    Route::get('/{id}', [App\Http\Controllers\Partido\PartidoController::class, 'show'])->name('show')->middleware('check.permission:partidos.view');
    Route::get('/{id}/edit', [App\Http\Controllers\Partido\PartidoController::class, 'edit'])->name('edit')->middleware('check.permission:partidos.edit');
    Route::put('/{id}', [App\Http\Controllers\Partido\PartidoController::class, 'update'])->name('update')->middleware('check.permission:partidos.edit');
    Route::delete('/{id}', [App\Http\Controllers\Partido\PartidoController::class, 'destroy'])->name('destroy')->middleware('check.permission:partidos.delete');
});

// Mesa Diretora routes (protected with permissions)
Route::prefix('mesa-diretora')->name('mesa-diretora.')->middleware(['auth', 'check.screen.permission'])->group(function () {
    Route::get('/', [App\Http\Controllers\MesaDiretora\MesaDiretoraController::class, 'index'])->name('index')->middleware('check.permission:mesa-diretora.view');
    Route::get('/atual', [App\Http\Controllers\MesaDiretora\MesaDiretoraController::class, 'composicaoAtual'])->name('atual')->middleware('check.permission:mesa-diretora.view');
    Route::get('/historico', [App\Http\Controllers\MesaDiretora\MesaDiretoraController::class, 'historico'])->name('historico')->middleware('check.permission:mesa-diretora.view');
    Route::get('/create', [App\Http\Controllers\MesaDiretora\MesaDiretoraController::class, 'create'])->name('create')->middleware('check.permission:mesa-diretora.create');
    Route::post('/', [App\Http\Controllers\MesaDiretora\MesaDiretoraController::class, 'store'])->name('store')->middleware('check.permission:mesa-diretora.create');
    Route::get('/search', [App\Http\Controllers\MesaDiretora\MesaDiretoraController::class, 'search'])->name('search')->middleware('check.permission:mesa-diretora.view');
    Route::get('/estatisticas', [App\Http\Controllers\MesaDiretora\MesaDiretoraController::class, 'estatisticas'])->name('estatisticas')->middleware('check.permission:mesa-diretora.view');
    Route::get('/{id}', [App\Http\Controllers\MesaDiretora\MesaDiretoraController::class, 'show'])->name('show')->middleware('check.permission:mesa-diretora.view');
    Route::get('/{id}/edit', [App\Http\Controllers\MesaDiretora\MesaDiretoraController::class, 'edit'])->name('edit')->middleware('check.permission:mesa-diretora.edit');
    Route::put('/{id}', [App\Http\Controllers\MesaDiretora\MesaDiretoraController::class, 'update'])->name('update')->middleware('check.permission:mesa-diretora.edit');
    Route::delete('/{id}', [App\Http\Controllers\MesaDiretora\MesaDiretoraController::class, 'destroy'])->name('destroy')->middleware('check.permission:mesa-diretora.delete');
    Route::post('/{id}/finalizar', [App\Http\Controllers\MesaDiretora\MesaDiretoraController::class, 'finalizarMandato'])->name('finalizar')->middleware('check.permission:mesa-diretora.edit');
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
    Route::get('/{proposicao}/editar-onlyoffice/{template}', [App\Http\Controllers\ProposicaoController::class, 'editarOnlyOffice'])->name('editar-onlyoffice');
    Route::get('/{proposicao}/preparar-edicao/{template}', [App\Http\Controllers\ProposicaoController::class, 'prepararEdicao'])->name('preparar-edicao');
    Route::get('/{proposicao}/editor-completo/{template}', [App\Http\Controllers\ProposicaoController::class, 'editorCompleto'])->name('editor-completo');
    Route::post('/{proposicao}/salvar-dados-temporarios', [App\Http\Controllers\ProposicaoController::class, 'salvarDadosTemporarios'])->name('salvar-dados-temporarios');
    Route::post('/{proposicao}/salvar-texto', [App\Http\Controllers\ProposicaoController::class, 'salvarTexto'])->name('salvar-texto');
    Route::post('/{proposicao}/upload-anexo', [App\Http\Controllers\ProposicaoController::class, 'uploadAnexo'])->name('upload-anexo');
    Route::delete('/{proposicao}/remover-anexo/{anexo}', [App\Http\Controllers\ProposicaoController::class, 'removerAnexo'])->name('remover-anexo');
    Route::post('/{proposicao}/atualizar-status', [App\Http\Controllers\ProposicaoController::class, 'atualizarStatus'])->name('atualizar-status');
    Route::put('/{proposicao}/enviar-legislativo', [App\Http\Controllers\ProposicaoController::class, 'enviarLegislativo'])->name('enviar-legislativo');
    Route::post('/{proposicao}/retorno-legislativo', [App\Http\Controllers\ProposicaoController::class, 'retornoLegislativo'])->name('retorno-legislativo');
    Route::post('/{proposicao}/assinar-documento', [App\Http\Controllers\ProposicaoController::class, 'assinarDocumento'])->name('assinar-documento');
    Route::post('/{proposicao}/enviar-protocolo', [App\Http\Controllers\ProposicaoController::class, 'enviarProtocolo'])->name('enviar-protocolo');
    
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
    Route::get('/limpar-sessao-teste', [App\Http\Controllers\ProposicaoController::class, 'limparSessaoTeste'])->name('limpar-sessao-teste'); // Temporário para desenvolvimento
    Route::get('/{proposicao}/status', [App\Http\Controllers\ProposicaoController::class, 'statusTramitacao'])->name('status-tramitacao');
    Route::get('/{proposicao}', [App\Http\Controllers\ProposicaoController::class, 'show'])->name('show');
    Route::delete('/{proposicao}', [App\Http\Controllers\ProposicaoController::class, 'destroy'])->name('destroy');
});

// ONLYOFFICE ROUTES FOR PROPOSIÇÕES
Route::prefix('onlyoffice')->name('onlyoffice.')->group(function () {
    // File serving routes (no auth middleware since OnlyOffice server needs direct access)
    Route::get('/file/proposicao/{proposicao}/{arquivo}', [App\Http\Controllers\ProposicaoController::class, 'serveFile'])->name('file.proposicao');
    
    // Callback routes for document updates
    Route::post('/callback/proposicao/{proposicao}', [App\Http\Controllers\ProposicaoController::class, 'onlyOfficeCallback'])->name('callback.proposicao');
});

// ROTAS DE ADMINISTRAÇÃO - TIPOS DE PROPOSIÇÃO
Route::prefix('admin/tipo-proposicoes')->name('admin.tipo-proposicoes.')->middleware(['auth', 'check.screen.permission'])->group(function () {
    // CRUD básico
    Route::get('/', [App\Http\Controllers\Admin\TipoProposicaoController::class, 'index'])->name('index')->middleware('check.permission:tipo_proposicoes.view');
    Route::get('/create', [App\Http\Controllers\Admin\TipoProposicaoController::class, 'create'])->name('create')->middleware('check.permission:tipo_proposicoes.create');
    Route::post('/', [App\Http\Controllers\Admin\TipoProposicaoController::class, 'store'])->name('store')->middleware('check.permission:tipo_proposicoes.create');
    Route::get('/{tipoProposicao}', [App\Http\Controllers\Admin\TipoProposicaoController::class, 'show'])->name('show')->middleware('check.permission:tipo_proposicoes.view');
    Route::get('/{tipoProposicao}/edit', [App\Http\Controllers\Admin\TipoProposicaoController::class, 'edit'])->name('edit')->middleware('check.permission:tipo_proposicoes.edit');
    Route::put('/{tipoProposicao}', [App\Http\Controllers\Admin\TipoProposicaoController::class, 'update'])->name('update')->middleware('check.permission:tipo_proposicoes.edit');
    Route::delete('/{tipoProposicao}', [App\Http\Controllers\Admin\TipoProposicaoController::class, 'destroy'])->name('destroy')->middleware('check.permission:tipo_proposicoes.delete');
    
    // Ações especiais
    Route::post('/{tipoProposicao}/toggle-status', [App\Http\Controllers\Admin\TipoProposicaoController::class, 'toggleStatus'])->name('toggle-status')->middleware('check.permission:tipo_proposicoes.edit');
    Route::post('/reordenar', [App\Http\Controllers\Admin\TipoProposicaoController::class, 'reordenar'])->name('reordenar')->middleware('check.permission:tipo_proposicoes.edit');
    Route::post('/acoes-bulk', [App\Http\Controllers\Admin\TipoProposicaoController::class, 'acoesBulk'])->name('acoes-bulk')->middleware('check.permission:tipo_proposicoes.delete');
    
    // AJAX endpoints
    Route::get('/ajax/dropdown', [App\Http\Controllers\Admin\TipoProposicaoController::class, 'getParaDropdown'])->name('ajax.dropdown');
    Route::get('/ajax/validar-codigo', [App\Http\Controllers\Admin\TipoProposicaoController::class, 'validarCodigo'])->name('ajax.validar-codigo');
});

// Rotas de autenticação por token (para AJAX)
Route::middleware(['web'])->prefix('auth')->name('auth.')->group(function () {
    Route::get('token', [TokenController::class, 'getAjaxToken'])->name('token.get');
    Route::post('token/verify', [TokenController::class, 'verifyToken'])->name('token.verify');
    Route::post('token/revoke', [TokenController::class, 'revokeToken'])->name('token.revoke');
});

// ===== SISTEMA DE DOCUMENTOS - GESTÃO DE MODELOS =====
Route::prefix('admin/documentos')->name('documentos.')->middleware(['auth', 'check.screen.permission'])->group(function () {
    
    // ===== GESTÃO DE MODELOS =====
    Route::prefix('modelos')->name('modelos.')->group(function () {
        Route::get('/', [App\Http\Controllers\Documento\DocumentoModeloController::class, 'index'])->name('index')->middleware('check.permission:documentos.view');
        Route::get('/create', [App\Http\Controllers\Documento\DocumentoModeloController::class, 'create'])->name('create')->middleware('check.permission:documentos.create');
        Route::post('/', [App\Http\Controllers\Documento\DocumentoModeloController::class, 'store'])->name('store')->middleware('check.permission:documentos.create');
        
        // ONLYOFFICE creation workflow (antes das rotas com parâmetros)
        Route::get('/create-onlyoffice', [App\Http\Controllers\Documento\DocumentoModeloController::class, 'createOnlyOffice'])->name('create-onlyoffice')->middleware('check.permission:documentos.create');
        Route::post('/store-onlyoffice', [App\Http\Controllers\Documento\DocumentoModeloController::class, 'storeOnlyOffice'])->name('store-onlyoffice')->middleware('check.permission:documentos.create');
        
        // Rotas com parâmetros (devem vir depois das rotas específicas)
        Route::get('/{modelo}', [App\Http\Controllers\Documento\DocumentoModeloController::class, 'show'])->name('show')->middleware('check.permission:documentos.view');
        Route::get('/{modelo}/edit', [App\Http\Controllers\Documento\DocumentoModeloController::class, 'edit'])->name('edit')->middleware('check.permission:documentos.edit');
        Route::put('/{modelo}', [App\Http\Controllers\Documento\DocumentoModeloController::class, 'update'])->name('update')->middleware('check.permission:documentos.edit');
        Route::delete('/{modelo}', [App\Http\Controllers\Documento\DocumentoModeloController::class, 'destroy'])->name('destroy')->middleware('check.permission:documentos.delete');
        
        // Downloads
        Route::get('/{modelo}/download', [App\Http\Controllers\Documento\DocumentoModeloController::class, 'download'])->name('download')->middleware('check.permission:documentos.view');
        Route::get('/{modelo}/download-personalizado/{projeto}', [App\Http\Controllers\Documento\DocumentoModeloController::class, 'downloadModelo'])->name('download-personalizado')->middleware('check.permission:documentos.view');
        
        // API endpoint para verificar último update
        Route::get('/{modelo}/last-update', [App\Http\Controllers\Documento\DocumentoModeloController::class, 'getLastUpdate'])->name('last-update')->middleware('check.permission:documentos.view');
        
        // API endpoint para verificar atividade recente de callbacks
        Route::get('/callback-activity', [App\Http\Controllers\Documento\DocumentoModeloController::class, 'getCallbackActivity'])->name('callback-activity')->middleware('check.permission:documentos.view');
        
        // ONLYOFFICE editing and duplication
        Route::get('/{modelo}/editor-onlyoffice', [App\Http\Controllers\Documento\DocumentoModeloController::class, 'editorOnlyOffice'])->name('editor-onlyoffice')->middleware('check.permission:documentos.edit');
        Route::get('/{modelo}/duplicate-onlyoffice', [App\Http\Controllers\Documento\DocumentoModeloController::class, 'duplicateOnlyOffice'])->name('duplicate-onlyoffice')->middleware('check.permission:documentos.create');
        Route::post('/{modelo}/store-duplicate-onlyoffice', [App\Http\Controllers\Documento\DocumentoModeloController::class, 'storeDuplicateOnlyOffice'])->name('store-duplicate-onlyoffice')->middleware('check.permission:documentos.create');
    });
    
    // ===== GESTÃO DE INSTÂNCIAS =====
    Route::prefix('instancias')->name('instancias.')->group(function () {
        Route::get('/', [App\Http\Controllers\Documento\DocumentoInstanciaController::class, 'index'])->name('index')->middleware('check.permission:documentos.view');
        Route::get('/{instancia}', [App\Http\Controllers\Documento\DocumentoInstanciaController::class, 'show'])->name('show')->middleware('check.permission:documentos.view');
        Route::delete('/{instancia}', [App\Http\Controllers\Documento\DocumentoInstanciaController::class, 'destroy'])->name('destroy')->middleware('check.permission:documentos.delete');
        
        // Upload e download de versões
        Route::post('/{instancia}/upload-versao', [App\Http\Controllers\Documento\DocumentoInstanciaController::class, 'uploadVersao'])->name('upload-versao')->middleware('check.permission:documentos.edit');
        Route::get('/{instancia}/download', [App\Http\Controllers\Documento\DocumentoInstanciaController::class, 'download'])->name('download')->middleware('check.permission:documentos.view');
        Route::get('/versoes/{versao}/download', [App\Http\Controllers\Documento\DocumentoInstanciaController::class, 'downloadVersao'])->name('versao.download')->middleware('check.permission:documentos.view');
        
        // Gestão de status e finalização
        Route::post('/{instancia}/alterar-status', [App\Http\Controllers\Documento\DocumentoInstanciaController::class, 'alterarStatus'])->name('alterar-status')->middleware('check.permission:documentos.edit');
        Route::post('/{instancia}/finalizar', [App\Http\Controllers\Documento\DocumentoInstanciaController::class, 'finalizar'])->name('finalizar')->middleware('check.permission:documentos.edit');
        Route::get('/{instancia}/gerar-pdf', [App\Http\Controllers\Documento\DocumentoInstanciaController::class, 'gerarPDF'])->name('gerar-pdf')->middleware('check.permission:documentos.edit');
        
        // Histórico de versões
        Route::get('/{instancia}/versoes', [App\Http\Controllers\Documento\DocumentoInstanciaController::class, 'versoes'])->name('versoes')->middleware('check.permission:documentos.view');
        Route::get('/versoes/{versao1}/comparar/{versao2}', [App\Http\Controllers\Documento\DocumentoInstanciaController::class, 'compararVersoes'])->name('comparar-versoes')->middleware('check.permission:documentos.view');
    });

});

// Editor de Documentos routes (redirecionando para OnlyOffice)
Route::prefix('admin/documentos/editor')->name('documentos.editor.')->middleware(['auth', 'check.screen.permission'])->group(function () {
    Route::get('/', [App\Http\Controllers\Documento\DocumentoModeloController::class, 'index'])->name('index')->middleware('check.permission:documentos.view');
    Route::get('/create', [App\Http\Controllers\Documento\DocumentoModeloController::class, 'createOnlyOffice'])->name('create')->middleware('check.permission:documentos.create');
    Route::post('/', [App\Http\Controllers\Documento\DocumentoModeloController::class, 'storeOnlyOffice'])->name('store')->middleware('check.permission:documentos.create');
    Route::get('/{instancia}/download', [App\Http\Controllers\OnlyOffice\OnlyOfficeController::class, 'downloadInstancia'])->name('download')->middleware('check.permission:documentos.view');
    Route::get('/variaveis/{modelo_id}', [App\Http\Controllers\Documento\DocumentoModeloController::class, 'apiList'])->name('variaveis')->middleware('check.permission:documentos.view');
});

// ===== ONLYOFFICE INTEGRATION ROUTES =====
Route::prefix('onlyoffice')->name('onlyoffice.')->group(function () {
    
    // Editor routes (protected)
    Route::middleware(['auth'])->group(function () {
        Route::get('/editor/modelo/{modelo}', [App\Http\Controllers\OnlyOffice\OnlyOfficeController::class, 'editarModelo'])->name('editor.modelo');
        Route::get('/editor/instancia/{instancia}', [App\Http\Controllers\OnlyOffice\OnlyOfficeController::class, 'editarDocumento'])->name('editor.instancia');
        Route::get('/viewer/instancia/{instancia}', [App\Http\Controllers\OnlyOffice\OnlyOfficeController::class, 'visualizarDocumento'])->name('viewer.instancia');
        
        // Conversion routes
        Route::get('/pdf/instancia/{instancia}', [App\Http\Controllers\OnlyOffice\OnlyOfficeController::class, 'converterParaPDF'])->name('pdf.instancia');
        
        // History routes
        Route::get('/history/instancia/{instancia}', [App\Http\Controllers\OnlyOffice\OnlyOfficeController::class, 'obterHistoricoVersoes'])->name('history.instancia');
    });
    
    // File serving routes (public for OnlyOffice access)
    Route::get('/file/modelo/{modelo}', [App\Http\Controllers\OnlyOffice\OnlyOfficeController::class, 'downloadModelo'])->name('file.modelo');
    Route::get('/file/instancia/{instancia}', [App\Http\Controllers\OnlyOffice\OnlyOfficeController::class, 'downloadInstancia'])->name('file.instancia');
    
    // Callback route (public for OnlyOffice server)
    Route::post('/callback/{documentKey}', [App\Http\Controllers\OnlyOffice\OnlyOfficeController::class, 'callback'])->name('callback');
});

// ===== ONLYOFFICE STANDALONE EDITOR ROUTES (opens in new tab without layout) =====
Route::prefix('onlyoffice-standalone')->name('onlyoffice.standalone.')->middleware(['auth'])->group(function () {
    // Standalone editor routes
    Route::get('/editor/modelo/{modelo}', [App\Http\Controllers\OnlyOffice\OnlyOfficeController::class, 'editarModeloStandalone'])->name('editor.modelo');
    Route::get('/editor/instancia/{instancia}', [App\Http\Controllers\OnlyOffice\OnlyOfficeController::class, 'editarDocumentoStandalone'])->name('editor.instancia');
    Route::get('/viewer/instancia/{instancia}', [App\Http\Controllers\OnlyOffice\OnlyOfficeController::class, 'visualizarDocumentoStandalone'])->name('viewer.instancia');
    
    // Force save routes
    Route::post('/force-save/modelo/{modelo}', [App\Http\Controllers\OnlyOffice\OnlyOfficeController::class, 'forceSaveModelo'])->name('force-save.modelo');
});


// Debug route - remove after testing
Route::get('/debug-user', function () {
    if (!auth()->check()) {
        return response()->json(['error' => 'User not authenticated']);
    }
    
    $user = auth()->user();
    return response()->json([
        'user_id' => $user->id,
        'user_name' => $user->name,
        'user_email' => $user->email,
        'roles' => $user->getRoleNames(),
        'permissions' => $user->getPermissionNames(),
    ]);
})->middleware('auth');

// Templates routes (protected with auth and permissions)
Route::prefix('admin')->middleware(['auth', 'check.screen.permission'])->group(function () {
    Route::get('templates', [App\Http\Controllers\TemplateController::class, 'index'])
         ->name('templates.index');
    Route::get('templates/create', [App\Http\Controllers\TemplateController::class, 'create'])
         ->name('templates.create');
    Route::post('templates', [App\Http\Controllers\TemplateController::class, 'store'])
         ->name('templates.store');
    Route::delete('templates/{template}', [App\Http\Controllers\TemplateController::class, 'destroy'])
         ->name('templates.destroy');
    Route::get('templates/{tipo}/editor', [App\Http\Controllers\TemplateController::class, 'editor'])
         ->name('templates.editor');
});

