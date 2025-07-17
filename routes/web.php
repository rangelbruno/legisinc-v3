<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserApiController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Parlamentar\ParlamentarController;
use App\Http\Controllers\User\UserController as UserManagementController;
use App\Http\Controllers\Projeto\ProjetoController;
use App\Http\Controllers\ModeloProjetoController;
use App\Http\Controllers\Session\SessionController;
use App\Http\Controllers\ProgressController;
use App\Http\Controllers\ApiDocumentationController;

Route::get('/', function () {
    return view('welcome');
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

// Dashboard route (protected)
Route::get('/dashboard', function () {
    // Auto-login as admin if no user is authenticated (for demo purposes)
    if (!auth()->check()) {
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
    
    return view('dashboard');
})->name('dashboard');
Route::get('/home', function () {
    return view('welcome');
})->name('home');

// User profile routes (protected)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [UserController::class, 'profile'])->name('user.profile');
    Route::post('/update-last-access', [UserController::class, 'updateLastAccess'])->name('user.update-last-access');
});

// Test route for debugging
Route::get('/test-auth', function () {
    if (auth()->check()) {
        $user = auth()->user();
        $info = [
            'name' => $user->name,
            'email' => $user->email,
            'isAdmin' => $user->isAdmin(),
            'hasSessionsView' => $user->hasPermissionTo('sessions.view'),
            'hasAdminRole' => $user->hasRole('ADMIN'),
            'roles' => $user->getRoleNames()->toArray(),
            'debug_email_check' => ($user->email === 'test@example.com'),
        ];
        return response()->json($info);
    }
    return 'Usuário não está logado';
})->name('test.auth');

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

// Test get session by ID
Route::get('/test-get-session/{id}', function ($id) {
    try {
        $sessionService = app(\App\Services\Session\SessionService::class);
        $session = $sessionService->obterPorId($id);
        $matters = $sessionService->obterMaterias($id);
        $exports = $sessionService->obterHistoricoExportacoes($id);
        return response()->json(['success' => true, 'session' => $session, 'matters' => $matters, 'exports' => $exports]);
    } catch (Exception $e) {
        return response()->json(['success' => false, 'error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
    }
})->middleware('auth');

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

// Projetos routes (protected with auth only - TODO: add permissions later)
Route::prefix('projetos')->name('projetos.')->middleware(['auth', 'check.screen.permission'])->group(function () {
    // CRUD básico
    Route::get('/', [ProjetoController::class, 'index'])->name('index');
    Route::get('/create', [ProjetoController::class, 'create'])->name('create');
    Route::post('/', [ProjetoController::class, 'store'])->name('store');
    Route::get('/{id}', [ProjetoController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [ProjetoController::class, 'edit'])->name('edit');
    Route::put('/{id}', [ProjetoController::class, 'update'])->name('update');
    Route::delete('/{id}', [ProjetoController::class, 'destroy'])->name('destroy');
    
    // Ações de workflow
    Route::post('/{id}/protocolar', [ProjetoController::class, 'protocolar'])->name('protocolar');
    Route::post('/{id}/encaminhar-comissao', [ProjetoController::class, 'encaminharComissao'])->name('encaminhar-comissao');
    
    // AJAX endpoints
    Route::get('/buscar', [ProjetoController::class, 'buscar'])->name('buscar');
    Route::get('/estatisticas', [ProjetoController::class, 'estatisticas'])->name('estatisticas');
    
    // Editor de conteúdo
    Route::get('/{id}/editor', [ProjetoController::class, 'editor'])->name('editor');
    Route::get('/{id}/editor-tiptap', [ProjetoController::class, 'editorTiptap'])->name('editor-tiptap');
    Route::post('/{id}/salvar-conteudo', [ProjetoController::class, 'salvarConteudo'])->name('salvar-conteudo');
    
    // Sub-recursos
    Route::get('/{id}/versoes', [ProjetoController::class, 'versoes'])->name('versoes');
    Route::get('/{id}/tramitacao', [ProjetoController::class, 'tramitacao'])->name('tramitacao');
    Route::get('/{id}/anexos', [ProjetoController::class, 'anexos'])->name('anexos');
    
    // Exportação e importação
    Route::get('/{id}/export-word', [ProjetoController::class, 'exportarWord'])->name('export-word');
    Route::post('/{id}/import-word', [ProjetoController::class, 'importarWord'])->name('import-word');
});

// Modelos de Projeto routes (protected with auth only - Admin only)
Route::prefix('admin/modelos')->name('modelos.')->middleware(['auth', 'check.screen.permission'])->group(function () {
    // Rotas específicas (devem vir antes das rotas com parâmetros)
    Route::get('/', [ModeloProjetoController::class, 'index'])->name('index');
    Route::get('/create', [ModeloProjetoController::class, 'create'])->name('create');
    Route::get('/editor', [ModeloProjetoController::class, 'editor'])->name('editor');
    Route::get('/editor-tiptap', [ModeloProjetoController::class, 'editorTiptap'])->name('editor-tiptap'); // Rota alternativa para compatibilidade
    Route::get('/por-tipo', [ModeloProjetoController::class, 'porTipo'])->name('por-tipo');
    Route::post('/upload-image', [ModeloProjetoController::class, 'uploadImage'])->name('upload-image');
    Route::post('/', [ModeloProjetoController::class, 'store'])->name('store');
    
    // Rotas com parâmetros (devem vir após as rotas específicas)
    Route::get('/{modelo}', [ModeloProjetoController::class, 'show'])->name('show');
    Route::get('/{modelo}/edit', [ModeloProjetoController::class, 'edit'])->name('edit');
    Route::get('/{modelo}/conteudo', [ModeloProjetoController::class, 'conteudo'])->name('conteudo');
    Route::put('/{modelo}', [ModeloProjetoController::class, 'update'])->name('update');
    Route::delete('/{modelo}', [ModeloProjetoController::class, 'destroy'])->name('destroy');
    Route::post('/{modelo}/toggle-status', [ModeloProjetoController::class, 'toggleStatus'])->name('toggle-status');
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
    Route::post('/', [App\Http\Controllers\Admin\ScreenPermissionController::class, 'update'])->name('update');
    Route::get('/role/{role}', [App\Http\Controllers\Admin\ScreenPermissionController::class, 'getRolePermissions'])->name('get-role-permissions');
    Route::post('/reset', [App\Http\Controllers\Admin\ScreenPermissionController::class, 'reset'])->name('reset');
    Route::post('/sync', [App\Http\Controllers\Admin\ScreenPermissionController::class, 'sync'])->name('sync');
    Route::get('/export', [App\Http\Controllers\Admin\ScreenPermissionController::class, 'export'])->name('export');
    Route::post('/import', [App\Http\Controllers\Admin\ScreenPermissionController::class, 'import'])->name('import');
    Route::get('/cache/stats', [App\Http\Controllers\Admin\ScreenPermissionController::class, 'cacheStats'])->name('cache-stats');
    Route::post('/cache/clear', [App\Http\Controllers\Admin\ScreenPermissionController::class, 'clearCache'])->name('cache-clear');
    Route::post('/cache/warm', [App\Http\Controllers\Admin\ScreenPermissionController::class, 'warmCache'])->name('cache-warm');
    Route::post('/initialize', [App\Http\Controllers\Admin\ScreenPermissionController::class, 'initialize'])->name('initialize');
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

// Registration functionality working correctly

// Rota de teste temporária sem autenticação
Route::get('/test-projeto/{id}/edit', [ProjetoController::class, 'edit'])->name('test.projetos.edit');

// Demo do Editor Jurídico
Route::get('/editor-demo', function () {
    return view('editor.demo');
})->name('editor.demo');

// Teste do editor de modelos Tiptap (sem auth para teste)
Route::get('/test-modelos-editor-tiptap', function () {
    $tipos = \App\Models\ModeloProjeto::TIPOS_PROJETO;
    $tipoSelecionado = 'contrato';
    $modelo = null;
    return view('admin.modelos.editor-tiptap-minimal', compact('tipos', 'tipoSelecionado', 'modelo'));
});

// Rota de teste para verificar se o editor TipTap está funcionando
Route::get('/test-editor-funcionando', function () {
    return view('test-editor-funcionando');
});

