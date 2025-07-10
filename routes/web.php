<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserApiController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Parlamentar\ParlamentarController;
use App\Http\Controllers\User\UserController as UserManagementController;
use App\Http\Controllers\Projeto\ProjetoController;
use App\Http\Controllers\ModeloProjetoController;

Route::get('/', function () {
    return view('welcome');
});

// Authentication routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('auth.login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('auth.register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');

// Dashboard route (protected)
Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard')->middleware('auth');
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
        return 'Usuário logado: ' . auth()->user()->name . ' - ' . auth()->user()->email;
    }
    return 'Usuário não está logado';
})->name('test.auth');

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
Route::prefix('parlamentares')->name('parlamentares.')->middleware('auth')->group(function () {
    Route::get('/', [ParlamentarController::class, 'index'])->name('index')->middleware('check.permission:parlamentares.view');
    Route::get('/create', [ParlamentarController::class, 'create'])->name('create')->middleware('check.permission:parlamentares.create');
    Route::post('/', [ParlamentarController::class, 'store'])->name('store')->middleware('check.permission:parlamentares.create');
    Route::get('/search', [ParlamentarController::class, 'search'])->name('search')->middleware('check.permission:parlamentares.view');
    Route::get('/mesa-diretora', [ParlamentarController::class, 'mesaDiretora'])->name('mesa-diretora')->middleware('check.permission:parlamentares.view');
    Route::get('/partido/{partido}', [ParlamentarController::class, 'porPartido'])->name('por-partido')->middleware('check.permission:parlamentares.view');
    Route::get('/{id}', [ParlamentarController::class, 'show'])->name('show')->middleware('check.permission:parlamentares.view');
    Route::get('/{id}/edit', [ParlamentarController::class, 'edit'])->name('edit')->middleware('check.permission:parlamentares.edit');
    Route::put('/{id}', [ParlamentarController::class, 'update'])->name('update')->middleware('check.permission:parlamentares.edit');
    Route::delete('/{id}', [ParlamentarController::class, 'destroy'])->name('destroy')->middleware('check.permission:parlamentares.delete');
});

// Comissões routes (protected with permissions)
Route::prefix('comissoes')->name('comissoes.')->middleware('auth')->group(function () {
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
Route::prefix('projetos')->name('projetos.')->middleware('auth')->group(function () {
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
    Route::post('/{id}/salvar-conteudo', [ProjetoController::class, 'salvarConteudo'])->name('salvar-conteudo');
    
    // Sub-recursos
    Route::get('/{id}/versoes', [ProjetoController::class, 'versoes'])->name('versoes');
    Route::get('/{id}/tramitacao', [ProjetoController::class, 'tramitacao'])->name('tramitacao');
    Route::get('/{id}/anexos', [ProjetoController::class, 'anexos'])->name('anexos');
});

// Modelos de Projeto routes (protected with auth only - Admin only)
Route::prefix('admin/modelos')->name('modelos.')->middleware('auth')->group(function () {
    Route::get('/', [ModeloProjetoController::class, 'index'])->name('index');
    Route::get('/create', [ModeloProjetoController::class, 'create'])->name('create');
    Route::get('/editor', [ModeloProjetoController::class, 'editor'])->name('editor');
    Route::post('/', [ModeloProjetoController::class, 'store'])->name('store');
    Route::get('/{modelo}', [ModeloProjetoController::class, 'show'])->name('show');
    Route::get('/{modelo}/edit', [ModeloProjetoController::class, 'edit'])->name('edit');
    Route::put('/{modelo}', [ModeloProjetoController::class, 'update'])->name('update');
    Route::delete('/{modelo}', [ModeloProjetoController::class, 'destroy'])->name('destroy');
    Route::post('/{modelo}/toggle-status', [ModeloProjetoController::class, 'toggleStatus'])->name('toggle-status');
    
    // AJAX endpoints
    Route::get('/por-tipo', [ModeloProjetoController::class, 'porTipo'])->name('por-tipo');
    Route::get('/{modelo}/conteudo', [ModeloProjetoController::class, 'conteudo'])->name('conteudo');
    Route::post('/upload-image', [ModeloProjetoController::class, 'uploadImage'])->name('upload-image');
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