<?php

use App\Http\Controllers\Admin\MonitoringController;
use App\Http\Controllers\ApiDocumentationController;
use App\Http\Controllers\Auth\TokenController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DebugController;
use App\Http\Controllers\DocumentationController;
use App\Http\Controllers\Parlamentar\ParlamentarController;
use App\Http\Controllers\ProgressController;
use App\Http\Controllers\Session\SessionController;
use App\Http\Controllers\User\UserController as UserManagementController;
use App\Http\Controllers\UserApiController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

// Public routes for signature validation
Route::get('/conferir_assinatura', [App\Http\Controllers\AssinaturaValidacaoController::class, 'mostrarFormulario'])
    ->name('validacao.assinatura.formulario');
Route::post('/conferir_assinatura', [App\Http\Controllers\AssinaturaValidacaoController::class, 'validarAssinatura'])
    ->name('validacao.assinatura.validar');
Route::get('/certificado_validacao/{codigo}', [App\Http\Controllers\AssinaturaValidacaoController::class, 'certificadoValidacao'])
    ->name('validacao.assinatura.certificado');
Route::get('/qr_validacao/{codigo}', [App\Http\Controllers\AssinaturaValidacaoController::class, 'qrCodeValidacao'])
    ->name('validacao.assinatura.qr');

// Authentication routes - com middleware guest para prevenir acesso quando autenticado
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('auth.register');
    Route::post('/register', [AuthController::class, 'register'])->name('register');
});

// Logout routes - GET para redirecionamento de sessão expirada, POST para logout normal
Route::get('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect('/login')->with('info', 'Sua sessão expirou. Por favor, faça login novamente.');
})->name('logout.get');
Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout')->middleware('auth');

// Progress route (public)
Route::get('/progress', [ProgressController::class, 'index'])->name('progress.index');

// API Documentation route (public)
Route::get('/api-docs', [ApiDocumentationController::class, 'index'])->name('api-docs.index');

// Documentation routes (public)
Route::get('/docs', [DocumentationController::class, 'index'])->name('documentation.index');
Route::get('/docs/{docId}', [DocumentationController::class, 'show'])->name('documentation.show');
Route::get('/docs/search', [DocumentationController::class, 'search'])->name('documentation.search');
Route::get('/docs/statistics', [DocumentationController::class, 'statistics'])->name('documentation.statistics');

// Processo Completo Analysis routes
Route::get('/tests/processo-completo', [\App\Http\Controllers\ProcessoCompletoController::class, 'index'])->name('tests.processo-completo');

// API routes for processo completo testing
Route::prefix('api')->group(function () {
    Route::get('/templates/check', [\App\Http\Controllers\ProcessoCompletoController::class, 'checkTemplates']);
    Route::post('/proposicoes/create-test', [\App\Http\Controllers\ProcessoCompletoController::class, 'createTestProposicao']);
    Route::post('/proposicoes/{proposicao}/simulate-edit', [\App\Http\Controllers\ProcessoCompletoController::class, 'simulateEdit']);
    Route::post('/proposicoes/{proposicao}/enviar-legislativo', [\App\Http\Controllers\ProcessoCompletoController::class, 'enviarLegislativo']);
    Route::post('/proposicoes/{proposicao}/simulate-legislativo-edit', [\App\Http\Controllers\ProcessoCompletoController::class, 'simulateLegislativoEdit']);
    Route::post('/proposicoes/{proposicao}/retornar-parlamentar', [\App\Http\Controllers\ProcessoCompletoController::class, 'retornarParlamentar']);
    Route::post('/proposicoes/{proposicao}/simulate-assinatura', [\App\Http\Controllers\ProcessoCompletoController::class, 'simulateAssinatura']);
    Route::post('/proposicoes/{proposicao}/simulate-protocolo', [\App\Http\Controllers\ProcessoCompletoController::class, 'simulateProtocolo']);
    Route::get('/proposicoes/{proposicao}/status', [\App\Http\Controllers\ProcessoCompletoController::class, 'getProposicaoStatus']);
    Route::delete('/tests/limpar-dados', [\App\Http\Controllers\ProcessoCompletoController::class, 'limparDadosTeste']);
});
Route::get('/docs/test', function () {
    $cssExists = file_exists(public_path('css/documentation.css'));
    $cssSize = $cssExists ? filesize(public_path('css/documentation.css')) : 0;

    return response('<html><head><title>CSS Test</title></head><body style="font-family: Arial; padding: 20px; background: #f8f9fa;"><div style="max-width: 800px; margin: 0 auto;"><h1 style="color: #28a745;">✅ CSS Fix Successful</h1><div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 4px; margin: 20px 0;"><strong>Status:</strong><ul><li>CSS File exists: '.($cssExists ? '✅ Yes' : '❌ No').'</li><li>File size: '.$cssSize.' bytes</li><li>Location: public/css/documentation.css</li><li>URL: <a href="/css/documentation.css" target="_blank">/css/documentation.css</a></li></ul></div><div style="margin: 20px 0;"><a href="/css/documentation.css" style="background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; margin-right: 10px;" target="_blank">🎨 Test CSS Direct</a><a href="/docs" style="background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px;">📚 Go to Documentation</a></div></div></body></html>');
})->name('documentation.test');
Route::get('/docs/api/documents', [DocumentationController::class, 'documents'])->name('documentation.documents');
Route::get('/docs/api/stats', [DocumentationController::class, 'stats'])->name('documentation.stats');

// Dashboard route (protected)
Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard')->middleware('auth');

// Dashboard de Administração route (protected)
Route::get('/admin/dashboard', function () {
    // Auto-login as admin if no user is authenticated (for demo purposes)
    if (! Auth::check()) {
        $user = new \App\Models\User;
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

// System Configuration routes (protected)
Route::prefix('admin/system-configuration')->name('admin.system-configuration.')->middleware(['auth', 'check.screen.permission'])->group(function () {
    Route::get('/', [App\Http\Controllers\Admin\SystemConfigurationController::class, 'index'])
        ->name('index')->middleware('check.permission:admin.view');
    Route::post('/', [App\Http\Controllers\Admin\SystemConfigurationController::class, 'update'])
        ->name('update')->middleware('check.permission:admin.manage');
    Route::get('/test-debug-logger', [App\Http\Controllers\Admin\SystemConfigurationController::class, 'testDebugLogger'])
        ->name('test-debug-logger')->middleware('check.permission:admin.view');
    Route::post('/clear-cache', [App\Http\Controllers\Admin\SystemConfigurationController::class, 'clearCache'])
        ->name('clear-cache')->middleware('check.permission:admin.manage');
    
    // Database Debug routes
    Route::post('/database/start-capture', [App\Http\Controllers\Admin\SystemConfigurationController::class, 'startDatabaseCapture'])
        ->name('database.start-capture')->middleware('check.permission:admin.manage');
    Route::post('/database/stop-capture', [App\Http\Controllers\Admin\SystemConfigurationController::class, 'stopDatabaseCapture'])
        ->name('database.stop-capture')->middleware('check.permission:admin.manage');
    Route::get('/database/queries', [App\Http\Controllers\Admin\SystemConfigurationController::class, 'getDatabaseQueries'])
        ->name('database.queries')->middleware('check.permission:admin.view');
    Route::get('/database/stats', [App\Http\Controllers\Admin\SystemConfigurationController::class, 'getDatabaseStats'])
        ->name('database.stats')->middleware('check.permission:admin.view');
});

Route::get('/home', function () {
    return view('welcome');
})->name('home');

// User profile routes (protected)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [UserController::class, 'profile'])->name('user.profile');
    Route::post('/update-last-access', [UserController::class, 'updateLastAccess'])->name('user.update-last-access');
});

// Certificado Digital routes (protected)
Route::prefix('certificado-digital')->name('certificado-digital.')->middleware('auth')->group(function () {
    Route::get('/', [App\Http\Controllers\CertificadoDigitalController::class, 'index'])->name('index');
    Route::post('/upload', [App\Http\Controllers\CertificadoDigitalController::class, 'upload'])->name('upload');
    Route::delete('/remover', [App\Http\Controllers\CertificadoDigitalController::class, 'remover'])->name('remover');
    Route::post('/toggle', [App\Http\Controllers\CertificadoDigitalController::class, 'toggleAtivo'])->name('toggle');
    Route::post('/testar', [App\Http\Controllers\CertificadoDigitalController::class, 'testar'])->name('testar');
});

// Test route to auto-login as admin for demo
Route::get('/auto-login-admin', function () {
    // Force logout first
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    $user = new \App\Models\User;
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
    Route::post('/{id}/remover-certificado', [ParlamentarController::class, 'removerCertificado'])->name('remover-certificado')->middleware('check.permission:parlamentares.edit');
});

// Partidos routes (protected with permissions)
Route::prefix('partidos')->name('partidos.')->middleware(['auth', 'check.screen.permission'])->group(function () {
    Route::get('/', [App\Http\Controllers\Partido\PartidoController::class, 'index'])->name('index')->middleware('check.permission:partidos.view');
    Route::get('/create', [App\Http\Controllers\Partido\PartidoController::class, 'create'])->name('create')->middleware('check.permission:partidos.create');
    Route::post('/', [App\Http\Controllers\Partido\PartidoController::class, 'store'])->name('store')->middleware('check.permission:partidos.create');
    Route::get('/search', [App\Http\Controllers\Partido\PartidoController::class, 'search'])->name('search')->middleware('check.permission:partidos.view');
    Route::get('/brasileiros', function () {
        return view('modules.partidos.brasileiros', ['title' => 'Partidos Brasileiros']);
    })->name('brasileiros')->middleware('check.permission:partidos.view');
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

// System Diagram routes (protected with auth - Admin only)
Route::prefix('admin/system-diagram')->name('admin.system-diagram.')->middleware(['auth', 'check.screen.permission'])->group(function () {
    Route::get('/', [App\Http\Controllers\Admin\SystemDiagramController::class, 'index'])->name('index');
    Route::post('/export', [App\Http\Controllers\Admin\SystemDiagramController::class, 'export'])->name('export');

    // API routes for detailed lists
    Route::get('/api/controllers', [App\Http\Controllers\Admin\SystemDiagramController::class, 'getControllers'])->name('api.controllers');
    Route::get('/api/views', [App\Http\Controllers\Admin\SystemDiagramController::class, 'getViews'])->name('api.views');
    Route::get('/api/routes', [App\Http\Controllers\Admin\SystemDiagramController::class, 'getRoutes'])->name('api.routes');
    Route::get('/api/services', [App\Http\Controllers\Admin\SystemDiagramController::class, 'getServices'])->name('api.services');
    Route::get('/api/diagrams', [App\Http\Controllers\Admin\SystemDiagramController::class, 'getDiagrams'])->name('api.diagrams');
    Route::get('/api/categories', [App\Http\Controllers\Admin\SystemDiagramController::class, 'getCategories'])->name('api.categories');
});

// Admin Architecture routes (protected with auth - Admin only)
Route::prefix('admin/arquitetura')->name('admin.arquitetura.')->middleware(['auth'])->group(function () {
    Route::get('/', [App\Http\Controllers\Admin\ArquiteturaController::class, 'index'])->name('index');
    Route::get('/api/containers', [App\Http\Controllers\Admin\ArquiteturaController::class, 'statusContainers'])->name('api.containers');
    Route::get('/api/servicos', [App\Http\Controllers\Admin\ArquiteturaController::class, 'statusServicos'])->name('api.servicos');
});

// Migration Preparation routes (protected with auth - Admin only)
Route::prefix('admin/migration-preparation')->name('admin.migration-preparation.')->middleware(['auth'])->group(function () {
    Route::get('/', [App\Http\Controllers\Admin\MigrationPreparationController::class, 'index'])->name('index');
    Route::get('/api/endpoints', [App\Http\Controllers\Admin\MigrationPreparationController::class, 'generateEndpointsJson'])->name('api.endpoints');
    Route::get('/api/database', [App\Http\Controllers\Admin\MigrationPreparationController::class, 'generateDatabaseStructureJson'])->name('api.database');
    Route::get('/api/models', [App\Http\Controllers\Admin\MigrationPreparationController::class, 'generateModelsJson'])->name('api.models');
    Route::get('/api/integrations', [App\Http\Controllers\Admin\MigrationPreparationController::class, 'generateIntegrationsJson'])->name('api.integrations');
    Route::get('/api/complete', [App\Http\Controllers\Admin\MigrationPreparationController::class, 'generateCompleteJson'])->name('api.complete');
});

// Admin Monitoring routes (protected with auth - Admin only)
Route::prefix('admin/monitoramento')->name('admin.monitoramento.')->middleware(['auth'])->group(function () {
    Route::get('/', [App\Http\Controllers\Admin\MonitoramentoController::class, 'index'])->name('index');
    Route::get('/api/status', [App\Http\Controllers\Admin\MonitoramentoController::class, 'statusServicos'])->name('status');
    Route::get('/api/metricas', [App\Http\Controllers\Admin\MonitoramentoController::class, 'metricas'])->name('metricas');
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

// Admin Documentation routes (protected with auth - Admin only)
// DEPRECATED: Redirected to centralized system diagram page
Route::prefix('admin/docs')->name('admin.docs.')->middleware(['auth', 'check.screen.permission'])->group(function () {
    Route::get('/fluxo-proposicoes', function () {
        return redirect()->route('admin.system-diagram.index')->with('info', 'Os fluxos de proposições foram centralizados na página de Arquitetura do Sistema.');
    })->name('fluxo-proposicoes');

    Route::get('/fluxo-documentos', function () {
        return redirect()->route('admin.system-diagram.index')->with('info', 'Os fluxos de documentos foram centralizados na página de Arquitetura do Sistema.');
    })->name('fluxo-documentos');
});

// Screen Permissions routes (protected with auth - Admin only)
Route::prefix('admin/screen-permissions')->name('admin.screen-permissions.')->middleware(['auth'])->group(function () {
    Route::get('/', [App\Http\Controllers\Admin\ScreenPermissionController::class, 'index'])->name('index');
    Route::get('/debug', function () {
        $service = app(\App\Services\DynamicPermissionService::class);
        $data = $service->getPermissionStructure();

        return view('admin.screen-permissions.debug', $data);
    })->name('debug');

    Route::get('/test', function () {
        $service = app(\App\Services\DynamicPermissionService::class);
        $data = $service->getPermissionStructure();

        return view('admin.screen-permissions.test', $data);
    })->name('test');

    // Novas rotas dinâmicas
    Route::post('/save', [App\Http\Controllers\Admin\ScreenPermissionController::class, 'saveRolePermissions'])->name('save');
    Route::post('/apply-defaults', [App\Http\Controllers\Admin\ScreenPermissionController::class, 'applyDefaults'])->name('apply-defaults');
    Route::post('/apply-default', [App\Http\Controllers\Admin\ScreenPermissionController::class, 'applyDefault'])->name('apply-default');
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

    // Legacy catch-all route removed to prevent conflicts with specific routes
});

// Sistema de Parâmetros Modulares (protected with auth and permissions - Admin only)
Route::prefix('admin/parametros')->name('parametros.')->middleware(['auth', 'check.screen.permission'])->group(function () {
    // Rotas principais
    Route::get('/', [App\Http\Controllers\Parametro\ParametroController::class, 'index'])->name('index')->middleware('check.permission:parametros.view');
    Route::get('/create', [App\Http\Controllers\Parametro\ParametroController::class, 'create'])->name('create')->middleware('check.permission:parametros.create');
    Route::post('/', [App\Http\Controllers\Parametro\ParametroController::class, 'store'])->name('store')->middleware('check.permission:parametros.create');

    // Rota específica para configuração de IA (nova arquitetura com múltiplas APIs)
    Route::get('/configurar-ia', function () {
        \Log::info('Redirecionando para configurações de IA', ['user' => auth()->id()]);

        return redirect()->route('admin.ai-configurations.index');
    })->name('configurar-ia')->middleware('check.permission:parametros.view');

    Route::get('/{id}', [App\Http\Controllers\Parametro\ParametroController::class, 'show'])->name('show')->middleware('check.permission:parametros.view')->where('id', '[0-9]+');
    Route::get('/{id}/edit', [App\Http\Controllers\Parametro\ParametroController::class, 'edit'])->name('edit')->middleware('check.permission:parametros.edit')->where('id', '[0-9]+');
    Route::put('/{id}', [App\Http\Controllers\Parametro\ParametroController::class, 'update'])->name('update')->middleware('check.permission:parametros.edit')->where('id', '[0-9]+');
    Route::delete('/{id}', [App\Http\Controllers\Parametro\ParametroController::class, 'destroy'])->name('destroy')->middleware('check.permission:parametros.delete')->where('id', '[0-9]+');

    // Página de teste isolada
    Route::get('/test-page', function () {
        return view('test-page');
    })->name('test.page')->middleware('auth');

    // Rota de teste para debug do configurar-ia
    Route::get('/debug-configurar-ia', function () {
        return response()->json([
            'route_exists' => Route::has('parametros.configurar-ia'),
            'route_url' => route('parametros.configurar-ia'),
            'timestamp' => now(),
            'user_authenticated' => auth()->check(),
            'user_email' => auth()->check() ? auth()->user()->email : null,
        ]);
    })->name('debug.configurar-ia');

    // Configuração de módulos
    Route::get('/configurar/{nomeModulo}', [App\Http\Controllers\Parametro\ParametroController::class, 'configurar'])->name('configurar')->middleware('check.permission:parametros.view');

    Route::post('/salvar-configuracoes/{submoduloId}', [App\Http\Controllers\Parametro\ParametroController::class, 'salvarConfiguracoes'])->name('salvar-configuracoes')->middleware('check.permission:parametros.edit');

    // Rotas específicas para configurações de IA
    Route::prefix('ia')->name('ia.')->group(function () {
        Route::get('/config', [App\Http\Controllers\AI\AIConfigController::class, 'index'])->name('config')->middleware('check.permission:parametros.view');
        Route::post('/providers/{providerId}/config', [App\Http\Controllers\AI\AIConfigController::class, 'saveProviderConfig'])->name('providers.config')->middleware('check.permission:parametros.edit');
        Route::post('/providers/{providerId}/activate', [App\Http\Controllers\AI\AIConfigController::class, 'activateProvider'])->name('providers.activate')->middleware('check.permission:parametros.edit');
        Route::get('/providers/{providerId}/config', [App\Http\Controllers\AI\AIConfigController::class, 'getProviderConfig'])->name('providers.get-config')->middleware('check.permission:parametros.view');
        Route::post('/providers/{providerId}/test', [App\Http\Controllers\AI\AIConfigController::class, 'testProviderConnection'])->name('providers.test')->middleware('check.permission:parametros.view');
        Route::post('/providers/{providerId}/generate-sample', [App\Http\Controllers\AI\AIConfigController::class, 'generateSampleData'])->name('providers.generate-sample')->middleware('check.permission:parametros.edit');
        Route::post('/usage', [App\Http\Controllers\AI\AIConfigController::class, 'recordTokenUsage'])->name('usage')->middleware('check.permission:parametros.edit');
    });

    // Rota específica para configurar Dados Gerais
    Route::get('/dados-gerais', function () {
        return app(App\Http\Controllers\Parametro\ParametroController::class)->configurar('Dados Gerais');
    })->name('dados-gerais')->middleware('check.permission:parametros.view');

    // Rota de teste para debug
    Route::get('/test-templates-debug', function () {
        $parametroService = app(App\Services\Parametro\ParametroService::class);
        $modulo = $parametroService->obterModulos()->where('nome', 'Templates')->first();
        $submodulos = $parametroService->obterSubmodulos($modulo->id);

        return response()->json([
            'modulo' => $modulo->nome,
            'total_submodulos' => $submodulos->count(),
            'submodulos' => $submodulos->map(function ($sub) {
                return [
                    'id' => $sub->id,
                    'nome' => $sub->nome,
                    'ativo' => $sub->ativo,
                ];
            }),
        ]);
    });

    // AJAX routes para exclusão SEM AUTENTICAÇÃO (temporário para debug)
    Route::post('/ajax/modulos/{id}/delete', [App\Http\Controllers\Parametro\ModuloParametroController::class, 'destroy'])->name('ajax.modulos.destroy');
    Route::post('/ajax/submodulos/{id}/delete', [App\Http\Controllers\Parametro\SubmoduloParametroController::class, 'destroy'])->name('ajax.submodulos.destroy');
    Route::post('/ajax/campos/{id}/delete', [App\Http\Controllers\Parametro\CampoParametroController::class, 'destroy'])->name('ajax.campos.destroy');
    Route::get('/ajax/modulos/{id}', [App\Http\Controllers\Parametro\ModuloParametroController::class, 'show'])->name('ajax.modulos.show');

    // AJAX DataTables endpoints (movido temporariamente)
    // Route::get('/ajax/modulos', [App\Http\Controllers\Parametro\ModuloParametroController::class, 'index'])->name('ajax.modulos.index');

    // Rota de teste para verificar autenticação
    Route::get('/ajax/test-auth', function () {
        return response()->json([
            'authenticated' => auth()->check(),
            'user_id' => auth()->id(),
            'user_name' => auth()->user() ? auth()->user()->name : null,
            'csrf_token' => csrf_token(),
            'session_id' => session()->getId(),
        ]);
    })->name('ajax.test.auth');

    // Rota de teste para exclusão sem middleware
    Route::post('/ajax/test-delete/{id}', function ($id) {
        \Log::info("Test delete route accessed for ID: $id");

        return response()->json([
            'success' => true,
            'message' => 'Teste de exclusão OK - ID: '.$id,
            'timestamp' => now(),
        ]);
    })->name('ajax.test.delete');

    // Rota de teste para debug
    Route::post('/ajax/test', function () {
        \Illuminate\Support\Facades\Log::info('AJAX Test route accessed');

        return response()->json([
            'success' => true,
            'message' => 'Teste de comunicação OK!',
            'timestamp' => now(),
            'data' => [
                'user_id' => auth()->id(),
                'request_method' => request()->method(),
                'csrf_token' => request()->input('_token'),
            ],
        ]);
    })->name('ajax.test');

    // Rota de teste específica para debug do módulo (sem middleware pesado)
    Route::get('/ajax/test-modulos', function () {
        try {
            \Illuminate\Support\Facades\Log::info('Test modulos route accessed', [
                'user_authenticated' => \Illuminate\Support\Facades\Auth::check(),
                'user_id' => \Illuminate\Support\Facades\Auth::id(),
                'request_headers' => request()->headers->all(),
                'request_method' => request()->method(),
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
                    'updated_at' => now()->format('d/m/Y H:i'),
                ],
            ];

            return response()->json([
                'data' => $testData,
                'recordsTotal' => 1,
                'recordsFiltered' => 1,
                'draw' => intval(request()->input('draw', 1)),
                'debug' => [
                    'user_id' => \Illuminate\Support\Facades\Auth::id(),
                    'authenticated' => \Illuminate\Support\Facades\Auth::check(),
                ],
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error in test modulos route', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
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
Route::get('/test-ajax-parametros', function () {
    try {
        \Log::info('Test AJAX parametros route accessed');

        // Auto-login se não estiver logado
        if (! Auth::check()) {
            $user = new \App\Models\User;
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
                'updated_at' => now()->format('d/m/Y H:i'),
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
                'updated_at' => now()->subHours(2)->format('d/m/Y H:i'),
            ],
        ];

        return response()->json([
            'data' => $testData,
            'recordsTotal' => count($testData),
            'recordsFiltered' => count($testData),
            'draw' => intval(request()->input('draw', 1)),
        ]);
    } catch (\Exception $e) {
        \Log::error('Error in test AJAX parametros route', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);

        return response()->json([
            'error' => $e->getMessage(),
        ], 500);
    }
})->name('test.ajax.parametros');

// AJAX DataTables endpoint para módulos (fora do grupo com middleware problemático)
Route::get('/ajax-modulos-parametros', [App\Http\Controllers\Parametro\ModuloParametroController::class, 'index'])->name('ajax.modulos.parametros');

// Rotas de templates com auto-login (sem prefix admin)
Route::get('/parametros-templates-debug', function () {
    // Auto-login se não estiver logado
    if (! Auth::check()) {
        $user = new \App\Models\User;
        $user->id = 5;
        $user->name = 'Bruno Administrador';
        $user->email = 'bruno@sistema.gov.br';
        $user->exists = true;
        Auth::login($user);
    }

    return '<h1>🎯 DEBUG: Rota funcionando!</h1><p>User: '.auth()->user()->email.'</p><p>Time: '.now().'</p><p><a href="'.route('parametros.index').'">Voltar para Parâmetros</a></p>';
})->name('parametros.templates.debug');

Route::get('/parametros-templates-cabecalho', function () {
    // Auto-login se não estiver logado
    if (! Auth::check()) {
        $user = new \App\Models\User;
        $user->id = 5;
        $user->name = 'Bruno Administrador';
        $user->email = 'bruno@sistema.gov.br';
        $user->exists = true;
        Auth::login($user);
    }

    return app(App\Http\Controllers\TemplateHeaderController::class)->index();
})->name('parametros.templates.cabecalho');

Route::post('/parametros-templates-cabecalho', function () {
    // Auto-login se não estiver logado
    if (! Auth::check()) {
        $user = new \App\Models\User;
        $user->id = 5;
        $user->name = 'Bruno Administrador';
        $user->email = 'bruno@sistema.gov.br';
        $user->exists = true;
        Auth::login($user);
    }

    return app(App\Http\Controllers\TemplateHeaderController::class)->store(request());
})->name('parametros.templates.cabecalho.store');

Route::get('/parametros-templates-marca-dagua', function () {
    // Auto-login se não estiver logado
    if (! Auth::check()) {
        $user = new \App\Models\User;
        $user->id = 5;
        $user->name = 'Bruno Administrador';
        $user->email = 'bruno@sistema.gov.br';
        $user->exists = true;
        Auth::login($user);
    }

    return app(App\Http\Controllers\TemplateWatermarkController::class)->index();
})->name('parametros.templates.marca-dagua');

Route::post('/parametros-templates-marca-dagua', function () {
    // Auto-login se não estiver logado
    if (! Auth::check()) {
        $user = new \App\Models\User;
        $user->id = 5;
        $user->name = 'Bruno Administrador';
        $user->email = 'bruno@sistema.gov.br';
        $user->exists = true;
        Auth::login($user);
    }

    return app(App\Http\Controllers\TemplateWatermarkController::class)->store(request());
})->name('parametros.templates.marca-dagua.store');

Route::get('/test-criar-docx', [App\Http\Controllers\ProposicaoController::class, 'criarArquivoTesteDOCX'])->name('test.criar.docx');

// Rotas do template padrão removidas - sistema usa apenas templates específicos por tipo de proposição
Route::get('/parametros-templates-rodape', function () {
    // Auto-login se não estiver logado
    if (! Auth::check()) {
        $user = new \App\Models\User;
        $user->id = 5;
        $user->name = 'Bruno Administrador';
        $user->email = 'bruno@sistema.gov.br';
        $user->exists = true;
        Auth::login($user);
    }

    return app(App\Http\Controllers\TemplateFooterController::class)->index();
})->name('parametros.templates.rodape');
Route::post('/parametros-templates-rodape', function () {
    // Auto-login se não estiver logado
    if (! Auth::check()) {
        $user = new \App\Models\User;
        $user->id = 5;
        $user->name = 'Bruno Administrador';
        $user->email = 'bruno@sistema.gov.br';
        $user->exists = true;
        Auth::login($user);
    }

    return app(App\Http\Controllers\TemplateFooterController::class)->store(request());
})->name('parametros.templates.rodape.store');

// Rotas para configuração de Assinatura e QR Code
Route::get('/parametros-templates-assinatura-qrcode', function () {
    // Auto-login se não estiver logado
    if (! Auth::check()) {
        $user = new \App\Models\User;
        $user->id = 5;
        $user->name = 'Bruno Administrador';
        $user->email = 'bruno@sistema.gov.br';
        $user->exists = true;
        Auth::login($user);
    }

    return app(App\Http\Controllers\TemplateAssinaturaQRController::class)->index();
})->name('parametros.templates.assinatura-qrcode');

Route::post('/parametros-templates-assinatura-qrcode', function () {
    // Auto-login se não estiver logado
    if (! Auth::check()) {
        $user = new \App\Models\User;
        $user->id = 5;
        $user->name = 'Bruno Administrador';
        $user->email = 'bruno@sistema.gov.br';
        $user->exists = true;
        Auth::login($user);
    }

    return app(App\Http\Controllers\TemplateAssinaturaQRController::class)->store(request());
})->name('parametros.templates.assinatura-qrcode.store');

// Rotas para Dados Gerais da Câmara
Route::get('/parametros-dados-gerais-camara', function () {
    // Auto-login se não estiver logado
    if (! Auth::check()) {
        $user = new \App\Models\User;
        $user->id = 5;
        $user->name = 'Bruno Administrador';
        $user->email = 'bruno@sistema.gov.br';
        $user->exists = true;
        Auth::login($user);
    }

    return app(App\Http\Controllers\DadosGeraisCamaraController::class)->index();
})->name('parametros.dados-gerais-camara');

Route::post('/parametros-dados-gerais-camara', function () {
    // Auto-login se não estiver logado
    if (! Auth::check()) {
        $user = new \App\Models\User;
        $user->id = 5;
        $user->name = 'Bruno Administrador';
        $user->email = 'bruno@sistema.gov.br';
        $user->exists = true;
        Auth::login($user);
    }

    return app(App\Http\Controllers\DadosGeraisCamaraController::class)->store(request());
})->name('parametros.dados-gerais-camara.store');

// Rotas para Configurações do Editor
Route::get('/parametros-editor-config', function () {
    // Auto-login se não estiver logado
    if (! Auth::check()) {
        $user = new \App\Models\User;
        $user->id = 5;
        $user->name = 'Bruno Administrador';
        $user->email = 'bruno@sistema.gov.br';
        $user->exists = true;
        Auth::login($user);
    }

    return app(App\Http\Controllers\EditorConfigController::class)->index();
})->name('parametros.editor.config');

Route::post('/parametros-editor-config', function () {
    // Auto-login se não estiver logado
    if (! Auth::check()) {
        $user = new \App\Models\User;
        $user->id = 5;
        $user->name = 'Bruno Administrador';
        $user->email = 'bruno@sistema.gov.br';
        $user->exists = true;
        Auth::login($user);
    }

    return app(App\Http\Controllers\EditorConfigController::class)->store(request());
})->name('parametros.editor.config.store');

// Test route para debug
Route::get('/test-debug', function () {
    return response()->json(['message' => 'Route working', 'time' => now()]);
});

Route::get('/test-controller', function () {
    try {
        \Log::info('🧪 Test route chamado ['.now().']');
        $controller = app(App\Http\Controllers\DadosGeraisCamaraController::class);
        \Log::info('🧪 Controller instantiated');
        $response = $controller->index();
        \Log::info('✅ Controller funcionou via test route');

        return $response;
    } catch (\Exception $e) {
        \Log::error('❌ Erro no test route', [
            'error' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile(),
            'trace' => $e->getTraceAsString(),
        ]);

        return response('Error: '.$e->getMessage().' at line '.$e->getLine(), 500);
    }
});

// Rotas para Variáveis Dinâmicas
Route::get('/parametros-variaveis-dinamicas', function () {
    // Auto-login se não estiver logado
    if (! Auth::check()) {
        $user = new \App\Models\User;
        $user->id = 5;
        $user->name = 'Bruno Administrador';
        $user->email = 'bruno@sistema.gov.br';
        $user->exists = true;
        Auth::login($user);
    }

    return app(App\Http\Controllers\VariaveisDinamicasController::class)->index();
})->name('parametros.variaveis-dinamicas');

Route::post('/parametros-variaveis-dinamicas', function () {
    // Auto-login se não estiver logado
    if (! Auth::check()) {
        $user = new \App\Models\User;
        $user->id = 5;
        $user->name = 'Bruno Administrador';
        $user->email = 'bruno@sistema.gov.br';
        $user->exists = true;
        Auth::login($user);
    }

    return app(App\Http\Controllers\VariaveisDinamicasController::class)->store(request());
})->name('parametros.variaveis-dinamicas.store');

// API Routes movidas para routes/api.php para evitar problemas de CSRF

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
Route::get('/debug-permissions', function () {
    if (! auth()->check()) {
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
            ->toArray(),
    ];

    return '<pre>'.json_encode($debug, JSON_PRETTY_PRINT).'</pre>';
})->middleware('auth');

// ROTAS DE PROPOSIÇÕES - Sistema completo conforme documentação
Route::prefix('proposicoes')->name('proposicoes.')->middleware(['auth', 'check.screen.permission'])->group(function () {

    // ===== LISTAGEM DE TIPOS DE PROPOSIÇÃO =====
    Route::get('/criar', function () {
        return view('proposicoes.criar');
    })->name('criar')->middleware('check.parlamentar.ativo');

    // ===== PARLAMENTAR - CRIAÇÃO (FORMULÁRIO DETALHADO) =====
    Route::get('/create', [App\Http\Controllers\ProposicaoController::class, 'createModern'])->name('create')->middleware('check.parlamentar.ativo');

    // ===== INTERFACE ORIGINAL (BACKUP) =====
    Route::get('/criar-original', [App\Http\Controllers\ProposicaoController::class, 'create'])->name('criar-original')->middleware('check.parlamentar.ativo');

    // ===== NOVA INTERFACE VUE.JS (STANDALONE) =====
    Route::get('/criar-vue', function () {
        return view('proposicoes.create-vue');
    })->name('criar-vue')->middleware('check.parlamentar.ativo');
    Route::post('/salvar-rascunho', [App\Http\Controllers\ProposicaoController::class, 'salvarRascunho'])->name('salvar-rascunho')->middleware('check.parlamentar.ativo');
    Route::get('/tipos', [App\Http\Controllers\ProposicaoController::class, 'getTiposProposicao'])->name('tipos')->middleware('check.parlamentar.ativo');
    Route::post('/gerar-texto-ia', [App\Http\Controllers\ProposicaoController::class, 'gerarTextoIA'])->name('gerar-texto-ia')->middleware('check.parlamentar.ativo');
    Route::get('/modelos/{tipo}', [App\Http\Controllers\ProposicaoController::class, 'buscarModelos'])->name('buscar-modelos')->middleware('check.parlamentar.ativo');
    Route::get('/{proposicao}/preencher-modelo/{modeloId}', [App\Http\Controllers\ProposicaoController::class, 'preencherModelo'])->name('preencher-modelo')->middleware('check.parlamentar.ativo');
    Route::get('/{proposicao}/processar-texto-e-redirecionar/{modeloId}', [App\Http\Controllers\ProposicaoController::class, 'processarTextoERedirecionar'])->name('processar-texto-e-redirecionar')->middleware('check.parlamentar.ativo');

    // Validação ABNT para proposições
    Route::prefix('abnt')->name('abnt.')->group(function () {
        Route::post('/validar', [App\Http\Controllers\Template\ABNTValidationController::class, 'validarDocumento'])->name('validar');
        Route::post('/corrigir', [App\Http\Controllers\Template\ABNTValidationController::class, 'aplicarCorrecoes'])->name('corrigir');
        Route::get('/estatisticas', [App\Http\Controllers\Template\ABNTValidationController::class, 'obterEstatisticasTemplate'])->name('estatisticas');
        Route::post('/relatorio', [App\Http\Controllers\Template\ABNTValidationController::class, 'gerarRelatorioDetalhado'])->name('relatorio');
        Route::get('/painel', [App\Http\Controllers\Template\ABNTValidationController::class, 'exibirPagina'])->name('painel');
    });
    Route::post('/{proposicao}/gerar-texto', [App\Http\Controllers\ProposicaoController::class, 'gerarTexto'])->name('gerar-texto')->middleware('check.parlamentar.ativo');
    Route::get('/{proposicao}/editar-texto', [App\Http\Controllers\ProposicaoController::class, 'editarTexto'])->name('editar-texto')->middleware('check.parlamentar.ativo');
    Route::get('/{proposicao}/editar-onlyoffice/{template}', [App\Http\Controllers\ProposicaoController::class, 'editarOnlyOffice'])->name('editar-onlyoffice')->middleware('check.parlamentar.ativo');
    Route::get('/{proposicao}/preparar-edicao/{template}', [App\Http\Controllers\ProposicaoController::class, 'prepararEdicao'])->name('preparar-edicao')->middleware('check.parlamentar.ativo');
    Route::get('/{proposicao}/editor-completo/{template}', [App\Http\Controllers\ProposicaoController::class, 'editorCompleto'])->name('editor-completo')->middleware('check.parlamentar.ativo');

    // Nova Arquitetura - Template Instance routes
    Route::post('/{proposicao}/selecionar-template', [App\Http\Controllers\ProposicaoController::class, 'selecionarTemplate'])->name('selecionar-template')->middleware('check.parlamentar.ativo');
    Route::post('/{proposicao}/processar-template', [App\Http\Controllers\ProposicaoController::class, 'processarTemplateNovaArquitetura'])->name('processar-template')->middleware('check.parlamentar.ativo');
    Route::get('/{proposicao}/nova-arquitetura/{instance}', [App\Http\Controllers\ProposicaoController::class, 'editarOnlyOfficeNovaArquitetura'])->name('editar-onlyoffice-nova-arquitetura')->middleware('check.parlamentar.ativo');
    Route::get('/instance/{instance}/serve', [App\Http\Controllers\ProposicaoController::class, 'serveInstance'])->name('serve-instance')->middleware('check.parlamentar.ativo');
    Route::post('/{instance}/finalizar-edicao', [App\Http\Controllers\ProposicaoController::class, 'finalizarEdicaoInstance'])->name('finalizar-edicao-instance')->middleware('check.parlamentar.ativo');
    Route::post('/{proposicao}/salvar-dados-temporarios', [App\Http\Controllers\ProposicaoController::class, 'salvarDadosTemporarios'])->name('salvar-dados-temporarios')->middleware('check.parlamentar.ativo');
    Route::post('/{proposicao}/salvar-texto', [App\Http\Controllers\ProposicaoController::class, 'salvarTexto'])->name('salvar-texto')->middleware('check.parlamentar.ativo');
    Route::post('/{proposicao}/upload-anexo', [App\Http\Controllers\ProposicaoController::class, 'uploadAnexo'])->name('upload-anexo')->middleware('check.parlamentar.ativo');
    Route::delete('/{proposicao}/remover-anexo/{anexo}', [App\Http\Controllers\ProposicaoController::class, 'removerAnexo'])->name('remover-anexo')->middleware('check.parlamentar.ativo');
    Route::post('/{proposicao}/atualizar-status', [App\Http\Controllers\ProposicaoController::class, 'atualizarStatus'])->name('atualizar-status');
    Route::put('/{proposicao}/enviar-legislativo', [App\Http\Controllers\ProposicaoController::class, 'enviarLegislativo'])->name('enviar-legislativo');
    Route::post('/{proposicao}/retorno-legislativo', [App\Http\Controllers\ProposicaoController::class, 'retornoLegislativo'])->name('retorno-legislativo');
    Route::post('/{proposicao}/assinar-documento', [App\Http\Controllers\ProposicaoController::class, 'assinarDocumento'])->name('assinar-documento')->middleware('check.parlamentar.ativo');
    Route::post('/{proposicao}/enviar-protocolo', [App\Http\Controllers\ProposicaoController::class, 'enviarProtocolo'])->name('enviar-protocolo')->middleware('check.parlamentar.ativo');

    // ===== LEGISLATIVO - REVISÃO =====
    Route::get('/legislativo', [App\Http\Controllers\ProposicaoLegislativoController::class, 'index'])->name('legislativo.index')->middleware('check.proposicao.permission');
    Route::get('/{proposicao}/legislativo/editar', [App\Http\Controllers\ProposicaoLegislativoController::class, 'editar'])->name('legislativo.editar')->middleware('check.proposicao.permission');
    Route::put('/{proposicao}/legislativo/salvar-edicao', [App\Http\Controllers\ProposicaoLegislativoController::class, 'salvarEdicao'])->name('legislativo.salvar-edicao')->middleware('check.proposicao.permission');
    Route::put('/{proposicao}/legislativo/enviar-parlamentar', [App\Http\Controllers\ProposicaoLegislativoController::class, 'enviarParaParlamentar'])->name('legislativo.enviar-parlamentar')->middleware('check.proposicao.permission');

    // OnlyOffice para Legislativo
    Route::get('/{proposicao}/onlyoffice/editor', [App\Http\Controllers\OnlyOfficeController::class, 'editorLegislativo'])->name('onlyoffice.editor')->middleware('role.permission:onlyoffice.editor.review');

    // OnlyOffice para Parlamentares
    Route::get('/{proposicao}/onlyoffice/editor-parlamentar', [App\Http\Controllers\OnlyOfficeController::class, 'editorParlamentar'])->name('onlyoffice.editor-parlamentar')->middleware('role.permission:onlyoffice.editor.own');

    Route::post('/{proposicao}/onlyoffice/callback/{documentKey}', [App\Http\Controllers\OnlyOfficeController::class, 'callback'])->name('onlyoffice.callback');
    Route::get('/revisar', [App\Http\Controllers\ProposicaoLegislativoController::class, 'index'])->name('revisar');
    Route::get('/{proposicao}/revisar', [App\Http\Controllers\ProposicaoLegislativoController::class, 'revisar'])->name('revisar.show');
    Route::post('/{proposicao}/salvar-analise', [App\Http\Controllers\ProposicaoLegislativoController::class, 'salvarAnalise'])->name('salvar-analise');
    Route::put('/{proposicao}/aprovar', [App\Http\Controllers\ProposicaoLegislativoController::class, 'aprovar'])->name('aprovar');
    Route::put('/{proposicao}/devolver', [App\Http\Controllers\ProposicaoLegislativoController::class, 'devolver'])->name('devolver');
    Route::get('/relatorio-legislativo', [App\Http\Controllers\ProposicaoLegislativoController::class, 'relatorio'])->name('relatorio-legislativo');
    Route::get('/relatorio-legislativo/dados', [App\Http\Controllers\ProposicaoLegislativoController::class, 'dadosRelatorio'])->name('relatorio-legislativo.dados');
    Route::get('/relatorio-legislativo/pdf', [App\Http\Controllers\ProposicaoLegislativoController::class, 'relatorioPdf'])->name('relatorio-legislativo.pdf');
    Route::get('/relatorio-legislativo/excel', [App\Http\Controllers\ProposicaoLegislativoController::class, 'relatorioExcel'])->name('relatorio-legislativo.excel');
    Route::get('/aguardando-protocolo', [App\Http\Controllers\ProposicaoLegislativoController::class, 'aguardandoProtocolo'])->name('aguardando-protocolo')->middleware('block.protocolo.access');

    // ===== PARLAMENTAR - ASSINATURA =====
    Route::get('/assinatura', [App\Http\Controllers\ProposicaoAssinaturaController::class, 'index'])->name('assinatura')->middleware('role.permission:proposicoes.view.own');
    Route::get('/{proposicao}/assinar', [App\Http\Controllers\ProposicaoAssinaturaController::class, 'assinar'])->name('assinar')->middleware('role.permission:proposicoes.assinar');
    Route::get('/{proposicao}/corrigir', [App\Http\Controllers\ProposicaoAssinaturaController::class, 'corrigir'])->name('corrigir')->middleware('role.permission:proposicoes.corrigir');
    Route::post('/{proposicao}/confirmar-leitura', [App\Http\Controllers\ProposicaoAssinaturaController::class, 'confirmarLeitura'])->name('confirmar-leitura')->middleware('role.permission:proposicoes.assinar');
    Route::post('/{proposicao}/processar-assinatura', [App\Http\Controllers\ProposicaoAssinaturaController::class, 'processarAssinatura'])->name('processar-assinatura')->middleware('role.permission:proposicoes.assinar');
    Route::put('/{proposicao}/enviar-protocolo', [App\Http\Controllers\ProposicaoAssinaturaController::class, 'enviarProtocolo'])->name('assinatura.enviar-protocolo')->middleware('role.permission:proposicoes.assinar');
    Route::post('/{proposicao}/salvar-correcoes', [App\Http\Controllers\ProposicaoAssinaturaController::class, 'salvarCorrecoes'])->name('salvar-correcoes')->middleware('role.permission:proposicoes.corrigir');
    Route::put('/{proposicao}/reenviar-legislativo', [App\Http\Controllers\ProposicaoAssinaturaController::class, 'reenviarLegislativo'])->name('reenviar-legislativo')->middleware('role.permission:proposicoes.corrigir');
    Route::put('/{proposicao}/devolver-legislativo', [App\Http\Controllers\ProposicaoAssinaturaController::class, 'devolverLegislativo'])->name('devolver-legislativo')->middleware('role.permission:proposicoes.corrigir');
    Route::get('/historico-assinaturas', [App\Http\Controllers\ProposicaoAssinaturaController::class, 'historico'])->name('historico-assinaturas')->middleware('role.permission:proposicoes.view.own');

    // ===== NOVAS ROTAS PARA PDF VUE.JS =====
    Route::post('/{proposicao}/salvar-pdf', [App\Http\Controllers\ProposicaoAssinaturaController::class, 'salvarPDFVue'])->name('salvar-pdf')->middleware('role.permission:proposicoes.assinar');
    Route::get('/{proposicao}/dados-template', [App\Http\Controllers\ProposicaoAssinaturaController::class, 'obterDadosTemplate'])->name('dados-template')->middleware('role.permission:proposicoes.view.own');
    Route::post('/{proposicao}/processar-assinatura-vue', [App\Http\Controllers\ProposicaoAssinaturaController::class, 'processarAssinaturaVue'])->name('processar-assinatura-vue')->middleware('role.permission:proposicoes.assinar');
    Route::get('/{proposicao}/verificar-assinatura', [App\Http\Controllers\ProposicaoAssinaturaController::class, 'verificarAssinatura'])->name('verificar-assinatura')->middleware('role.permission:proposicoes.view.own');
    Route::get('/{proposicao}/conteudo-onlyoffice', [App\Http\Controllers\ProposicaoAssinaturaController::class, 'obterConteudoOnlyOffice'])->name('conteudo-onlyoffice')->middleware('role.permission:proposicoes.view.own');
    Route::post('/{proposicao}/assinatura-digital/processar', [App\Http\Controllers\ProposicaoAssinaturaController::class, 'processarAssinaturaDigital'])->name('processar-assinatura-digital')->middleware('role.permission:proposicoes.assinar');

    Route::get('/{proposicao}/pdf-original', [App\Http\Controllers\ProposicaoAssinaturaController::class, 'visualizarPDFOriginal'])->name('pdf-original')->middleware('role.permission:proposicoes.view.own');
    Route::delete('/{proposicao}/excluir-documento', [App\Http\Controllers\ProposicaoAssinaturaController::class, 'excluirDocumento'])->name('excluir-documento')->middleware('role.permission:proposicoes.assinar');

    // Voltar proposição para parlamentar (do legislativo)
    Route::put('/{proposicao}/voltar-parlamentar', [App\Http\Controllers\ProposicaoController::class, 'voltarParaParlamentar'])->name('voltar-parlamentar');

    // Aprovar edições do legislativo (parlamentar)
    Route::post('/{proposicao}/aprovar-edicoes-legislativo', [App\Http\Controllers\ProposicaoController::class, 'aprovarEdicoesLegislativo'])->name('aprovar-edicoes-legislativo')->middleware('check.parlamentar.ativo');

    // ===== PROTOCOLO =====
    Route::get('/protocolar', [App\Http\Controllers\ProposicaoProtocoloController::class, 'index'])->name('protocolar');
    Route::get('/{proposicao}/protocolar', [App\Http\Controllers\ProposicaoProtocoloController::class, 'protocolar'])->name('protocolar.show');
    Route::post('/{proposicao}/efetivar-protocolo', [App\Http\Controllers\ProposicaoProtocoloController::class, 'efetivarProtocolo'])->name('efetivar-protocolo');
    Route::post('/{proposicao}/atribuir-numero-protocolo', [App\Http\Controllers\ProposicaoProtocoloController::class, 'atribuirNumeroProtocolo'])->name('atribuir-numero-protocolo');
    Route::get('/protocolos-hoje', [App\Http\Controllers\ProposicaoProtocoloController::class, 'protocolosHoje'])->name('protocolos-hoje');
    Route::get('/estatisticas-protocolo', [App\Http\Controllers\ProposicaoProtocoloController::class, 'estatisticas'])->name('estatisticas-protocolo');
    Route::put('/{proposicao}/iniciar-tramitacao', [App\Http\Controllers\ProposicaoProtocoloController::class, 'iniciarTramitacao'])->name('iniciar-tramitacao');

    // ===== GERAL =====
    Route::get('/minhas-proposicoes', [App\Http\Controllers\ProposicaoController::class, 'minhasProposicoes'])->name('minhas-proposicoes')->middleware('check.parlamentar.ativo');
    Route::get('/limpar-sessao-teste', [App\Http\Controllers\ProposicaoController::class, 'limparSessaoTeste'])->name('limpar-sessao-teste'); // Temporário para desenvolvimento
    Route::get('/{proposicao}/status', [App\Http\Controllers\ProposicaoController::class, 'statusTramitacao'])->name('status-tramitacao');
    Route::get('/notificacoes', [App\Http\Controllers\ProposicaoController::class, 'buscarNotificacoes'])->name('notificacoes');
    Route::get('/{proposicao}/pdf-debug', [App\Http\Controllers\ProposicaoController::class, 'debugPDF'])->name('debug-pdf');
    Route::get('/{proposicao}/pdf-viewer', [App\Http\Controllers\ProposicaoController::class, 'viewPDFWithDebug'])->name('pdf-viewer');
    Route::get('/{proposicao}/pdf', [App\Http\Controllers\ProposicaoController::class, 'servePDF'])->name('serve-pdf');

    // ===== ANEXOS =====
    Route::get('/{proposicao}/anexo/{anexoIndex}/download', [App\Http\Controllers\AnexoController::class, 'download'])->name('anexo.download');
    Route::get('/{proposicao}/anexo/{anexoIndex}/view', [App\Http\Controllers\AnexoController::class, 'view'])->name('anexo.view');

    // ===== HISTÓRICO DE ALTERAÇÕES =====
    Route::get('/{proposicao}/historico', [App\Http\Controllers\ProposicaoHistoricoController::class, 'index'])->name('historico.index');
    Route::get('/{proposicao}/historico/view', [App\Http\Controllers\ProposicaoHistoricoController::class, 'webView'])->name('historico.view');
    Route::get('/{proposicao}/historico/{historico}', [App\Http\Controllers\ProposicaoHistoricoController::class, 'show'])->name('historico.show');

    // ===== VISUALIZAÇÃO VUE.JS (NOVA) =====
    Route::get('/{proposicao}/vue', function ($proposicaoId) {
        $proposicao = \App\Models\Proposicao::findOrFail($proposicaoId);

        return view('proposicoes.show-vue', compact('proposicao'));
    })->name('show-vue');

    // ===== ATUALIZAÇÃO DE STATUS =====
    Route::patch('/{proposicao}/status', [App\Http\Controllers\ProposicaoController::class, 'updateStatus'])->name('update-status');

    // ===== DADOS FRESCOS PARA VUE.JS =====
    Route::get('/{proposicao}/dados-frescos', [App\Http\Controllers\ProposicaoController::class, 'getDadosFrescos'])->name('dados-frescos');

    Route::get('/{proposicao}', [App\Http\Controllers\ProposicaoController::class, 'show'])->name('show');
    Route::delete('/{proposicao}', [App\Http\Controllers\ProposicaoController::class, 'destroy'])->name('destroy');

    // Rota para exportar PDF do OnlyOffice (método tradicional)
    Route::post('/{proposicao}/onlyoffice/exportar-pdf', [App\Http\Controllers\OnlyOfficeController::class, 'exportarPDF'])
        ->name('onlyoffice.exportar-pdf')
        ->middleware(['auth']);

    // Rota para exportar PDF diretamente para S3 (método eficiente)
    Route::post('/{proposicao}/onlyoffice/exportar-pdf-s3', [App\Http\Controllers\OnlyOfficeController::class, 'exportarPDFParaS3'])
        ->name('onlyoffice.exportar-pdf-s3')
        ->middleware(['auth']);

    // Rota para verificar última exportação S3
    Route::get('/{proposicao}/onlyoffice/verificar-exportacao-s3', [App\Http\Controllers\OnlyOfficeController::class, 'verificarUltimaExportacaoS3'])
        ->name('onlyoffice.verificar-exportacao-s3')
        ->middleware(['auth']);

    // Rota para exportação automática S3 durante aprovação (server-side only)
    Route::post('/{proposicao}/onlyoffice/exportar-pdf-s3-automatico', [App\Http\Controllers\OnlyOfficeController::class, 'exportarPDFParaS3Automatico'])
        ->name('onlyoffice.exportar-pdf-s3-automatico')
        ->middleware(['auth']);

    // Rota para interceptar PDF do evento onDownloadAs
    Route::post('/onlyoffice/interceptar-pdf-download', [App\Http\Controllers\OnlyOfficeController::class, 'interceptarPDFOnDownloadAs'])
        ->name('onlyoffice.interceptar-pdf-download');
});

// ONLYOFFICE ROUTES FOR PROPOSIÇÕES (sem autenticação)
Route::prefix('onlyoffice')->name('onlyoffice.')->group(function () {
    // File serving routes (no auth middleware since OnlyOffice server needs direct access)
    Route::get('/file/proposicao/{proposicao}/{arquivo}', [App\Http\Controllers\ProposicaoController::class, 'serveFile'])->name('file.proposicao');

    // Callback routes for document updates
    Route::post('/callback/proposicao/{proposicao}', [App\Http\Controllers\ProposicaoController::class, 'onlyOfficeCallback'])->name('callback.proposicao');
});

// IMAGE UPLOAD ROUTES
Route::prefix('images')->name('images.')->middleware('auth')->group(function () {
    Route::post('/upload/template', [App\Http\Controllers\ImageUploadController::class, 'uploadTemplateImage'])->name('upload.template');
    Route::post('/upload/cabecalho', [App\Http\Controllers\ImageUploadController::class, 'uploadCabecalhoTemplate'])->name('upload.cabecalho');
    Route::post('/upload/marca-dagua', [App\Http\Controllers\ImageUploadController::class, 'uploadMarcaDagua'])->name('upload.marca-dagua');
    Route::post('/upload/rodape', [App\Http\Controllers\ImageUploadController::class, 'uploadRodape'])->name('upload.rodape');
    Route::post('/upload/proposicao/{proposicao}', [App\Http\Controllers\ImageUploadController::class, 'uploadProposicaoImage'])->name('upload.proposicao');
    Route::post('/upload/multiple', [App\Http\Controllers\ImageUploadController::class, 'uploadMultiple'])->name('upload.multiple');
});

// OnlyOffice download route for Legislativo (sem autenticação para acesso do servidor OnlyOffice)
Route::get('/proposicoes/{id}/onlyoffice/download', [App\Http\Controllers\OnlyOfficeController::class, 'downloadById'])->name('proposicoes.onlyoffice.download');
Route::get('/proposicoes/{proposicao}/onlyoffice/debug', [App\Http\Controllers\OnlyOfficeController::class, 'debugDownload'])->name('proposicoes.onlyoffice.debug');
Route::get('/test-debug', function() {
    return response()->json(['status' => 'OK', 'time' => now()]);
})->name('test.debug');

Route::get('/test-rtf/{id}', function($id) {
    $rtfContent = '{\rtf1\ansi\ansicpg1252\deff0\nouicompat\deflang1046{\fonttbl{\f0\fnil\fcharset0 Times New Roman;}}
{\*\generator Riched20 10.0.19041}\viewkind4\uc1 
\pard\sa200\sl276\slmult1\qc\f0\fs24\b TESTE RTF DIRETO\b0\par
\ql ID: ' . $id . '\par
Data: ' . now()->format('d/m/Y H:i:s') . '\par
Status: Funcionando sem dependências\par
}';
    
    $tempFile = tempnam(sys_get_temp_dir(), 'test_direct_') . '.rtf';
    file_put_contents($tempFile, $rtfContent);
    
    return response()->download($tempFile, "test_direct_{$id}.rtf", [
        'Content-Type' => 'application/rtf'
    ])->deleteFileAfterSend(true);
})->name('test.rtf');
Route::get('/proposicoes/{proposicao}/onlyoffice/status', [App\Http\Controllers\OnlyOfficeController::class, 'getUpdateStatus'])->name('proposicoes.onlyoffice.status');

// ROTAS DO EXPEDIENTE
Route::prefix('expediente')->name('expediente.')->middleware(['auth', 'check.screen.permission'])->group(function () {
    // Painel principal do expediente
    Route::get('/', [App\Http\Controllers\ExpedienteController::class, 'index'])->name('index');

    // Visualização de proposições
    Route::get('/proposicoes/{proposicao}', [App\Http\Controllers\ExpedienteController::class, 'show'])->name('show');

    // Classificação de proposições
    Route::post('/proposicoes/{proposicao}/classificar', [App\Http\Controllers\ExpedienteController::class, 'classificar'])->name('classificar');
    Route::get('/reclassificar-todas', [App\Http\Controllers\ExpedienteController::class, 'reclassificarTodas'])->name('reclassificar');

    // Envio para votação
    Route::post('/proposicoes/{proposicao}/enviar-votacao', [App\Http\Controllers\ExpedienteController::class, 'enviarParaVotacao'])->name('enviar-votacao');

    // Rotas específicas do menu
    Route::get('/aguardando-pauta', [App\Http\Controllers\ExpedienteController::class, 'aguardandoPauta'])->name('aguardando-pauta');
    Route::get('/relatorio', [App\Http\Controllers\ExpedienteController::class, 'relatorio'])->name('relatorio');
});

// GERADOR DE MÓDULOS (ADMIN)
Route::prefix('admin/module-generator')->name('admin.module-generator.')->middleware(['auth', 'check.screen.permission'])->group(function () {
    Route::get('/', [App\Http\Controllers\Admin\ModuleGeneratorController::class, 'index'])->name('index');
    Route::get('/create', [App\Http\Controllers\Admin\ModuleGeneratorController::class, 'create'])->name('create');
    Route::post('/', [App\Http\Controllers\Admin\ModuleGeneratorController::class, 'store'])->name('store');
    Route::get('/{generatedModule}', [App\Http\Controllers\Admin\ModuleGeneratorController::class, 'show'])->name('show');
    Route::get('/{generatedModule}/edit', [App\Http\Controllers\Admin\ModuleGeneratorController::class, 'edit'])->name('edit');
    Route::put('/{generatedModule}', [App\Http\Controllers\Admin\ModuleGeneratorController::class, 'update'])->name('update');
    Route::delete('/{generatedModule}', [App\Http\Controllers\Admin\ModuleGeneratorController::class, 'destroy'])->name('destroy');
    Route::post('/{generatedModule}/generate', [App\Http\Controllers\Admin\ModuleGeneratorController::class, 'generate'])->name('generate');
    Route::get('/{generatedModule}/preview', [App\Http\Controllers\Admin\ModuleGeneratorController::class, 'preview'])->name('preview');
    Route::get('/api/table-structure', [App\Http\Controllers\Admin\ModuleGeneratorController::class, 'getTableStructure'])->name('table-structure');
});

// ROTAS DE ADMINISTRAÇÃO - TEMPLATES
Route::prefix('admin/templates')->name('admin.templates.')->middleware(['auth', 'check.screen.permission'])->group(function () {
    // Template Universal (Nova funcionalidade)
    Route::get('/universal', [App\Http\Controllers\Admin\TemplateUniversalController::class, 'index'])->name('universal');
    Route::get('/universal/editor/{template?}', [App\Http\Controllers\Admin\TemplateUniversalController::class, 'editor'])->name('universal.editor');
    Route::post('/universal', [App\Http\Controllers\Admin\TemplateUniversalController::class, 'store'])->name('universal.store');
    Route::post('/universal/{template}/set-default', [App\Http\Controllers\Admin\TemplateUniversalController::class, 'setDefault'])->name('universal.set-default');
    Route::post('/universal/{template}/aplicar/{tipo}', [App\Http\Controllers\Admin\TemplateUniversalController::class, 'aplicarParaTipo'])->name('universal.aplicar-tipo');

    // Templates de Relatório
    Route::get('/relatorio-pdf', [App\Http\Controllers\Admin\TemplateRelatorioController::class, 'editarTemplatePdf'])->name('relatorio-pdf');
    Route::post('/relatorio-pdf/salvar', [App\Http\Controllers\Admin\TemplateRelatorioController::class, 'salvarTemplatePdf'])->name('salvar-pdf');
    Route::post('/relatorio-pdf/preview', [App\Http\Controllers\Admin\TemplateRelatorioController::class, 'previewTemplate'])->name('preview');
    Route::get('/backups', [App\Http\Controllers\Admin\TemplateRelatorioController::class, 'listarBackups'])->name('listar-backups');
    Route::post('/backup/restaurar', [App\Http\Controllers\Admin\TemplateRelatorioController::class, 'restaurarBackup'])->name('restaurar-backup');
    Route::post('/resetar', [App\Http\Controllers\Admin\TemplateRelatorioController::class, 'resetarTemplate'])->name('resetar');
});

// ROTAS DO PARECER JURÍDICO
Route::prefix('parecer-juridico')->name('parecer-juridico.')->middleware(['auth', 'check.screen.permission'])->group(function () {
    // Listar proposições para parecer jurídico
    Route::get('/', [App\Http\Controllers\ParecerJuridicoController::class, 'index'])->name('index');

    // Meus pareceres
    Route::get('/meus-pareceres', [App\Http\Controllers\ParecerJuridicoController::class, 'meusPareceres'])->name('meus-pareceres');

    // Emitir parecer
    Route::get('/proposicoes/{proposicao}/parecer', [App\Http\Controllers\ParecerJuridicoController::class, 'create'])->name('create');
    Route::post('/proposicoes/{proposicao}/parecer', [App\Http\Controllers\ParecerJuridicoController::class, 'store'])->name('store');

    // Visualizar e editar parecer
    Route::get('/pareceres/{parecerJuridico}', [App\Http\Controllers\ParecerJuridicoController::class, 'show'])->name('show');
    Route::get('/pareceres/{parecerJuridico}/edit', [App\Http\Controllers\ParecerJuridicoController::class, 'edit'])->name('edit');
    Route::put('/pareceres/{parecerJuridico}', [App\Http\Controllers\ParecerJuridicoController::class, 'update'])->name('update');

    // Gerar PDF
    Route::get('/pareceres/{parecerJuridico}/pdf', [App\Http\Controllers\ParecerJuridicoController::class, 'generatePDF'])->name('pdf');
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
    Route::get('/ajax/buscar-sugestoes', [App\Http\Controllers\Admin\TipoProposicaoController::class, 'buscarSugestoes'])->name('ajax.buscar-sugestoes');

    // ===== RELATÓRIOS DE AUDITORIA =====
    Route::get('/auditoria/relatorio', [App\Http\Controllers\ProposicaoHistoricoController::class, 'relatorioAuditoria'])->name('auditoria.relatorio');
});

// Rotas de autenticação por token (para AJAX)
Route::middleware(['web'])->prefix('auth')->name('auth.')->group(function () {
    Route::get('token', [TokenController::class, 'getAjaxToken'])->name('token.get');
    Route::post('token/verify', [TokenController::class, 'verifyToken'])->name('token.verify');
    Route::post('token/revoke', [TokenController::class, 'revokeToken'])->name('token.revoke');
});

// ===== SISTEMA DE DOCUMENTOS - GESTÃO DE MODELOS =====
Route::prefix('admin/documentos')->name('documentos.')->middleware(['auth', 'check.screen.permission'])->group(function () {

    // ===== GESTÃO DE TEMPLATES PADRÃO =====
    Route::prefix('templates')->name('templates.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\DocumentoTemplateController::class, 'index'])->name('index')->middleware('check.permission:documentos.view');
        Route::get('/create', [App\Http\Controllers\Admin\DocumentoTemplateController::class, 'create'])->name('create')->middleware('check.permission:documentos.create');
        Route::post('/', [App\Http\Controllers\Admin\DocumentoTemplateController::class, 'store'])->name('store')->middleware('check.permission:documentos.create');
        Route::get('/{template}/edit', [App\Http\Controllers\Admin\DocumentoTemplateController::class, 'edit'])->name('edit')->middleware('check.permission:documentos.edit');
        Route::put('/{template}', [App\Http\Controllers\Admin\DocumentoTemplateController::class, 'update'])->name('update')->middleware('check.permission:documentos.edit');
        Route::delete('/{template}', [App\Http\Controllers\Admin\DocumentoTemplateController::class, 'destroy'])->name('destroy')->middleware('check.permission:documentos.delete');
        Route::post('/reordenar', [App\Http\Controllers\Admin\DocumentoTemplateController::class, 'reordenar'])->name('reordenar')->middleware('check.permission:documentos.edit');
        Route::post('/resetar-padrao', [App\Http\Controllers\Admin\DocumentoTemplateController::class, 'resetarPadrao'])->name('resetar-padrao')->middleware('check.permission:documentos.create');
    });

    // ===== GESTÃO DE MODELOS =====
    Route::prefix('modelos')->name('modelos.')->group(function () {
        Route::get('/', [App\Http\Controllers\Documento\DocumentoModeloController::class, 'index'])->name('index')->middleware('check.permission:documentos.view');
        Route::get('/create', [App\Http\Controllers\Documento\DocumentoModeloController::class, 'create'])->name('create')->middleware('check.permission:documentos.create');
        Route::post('/', [App\Http\Controllers\Documento\DocumentoModeloController::class, 'store'])->name('store')->middleware('check.permission:documentos.create');
        Route::get('/create-onlyoffice', [App\Http\Controllers\Documento\DocumentoModeloController::class, 'createOnlyOffice'])->name('create-onlyoffice')->middleware('check.permission:documentos.create');
        Route::post('/store-onlyoffice', [App\Http\Controllers\Documento\DocumentoModeloController::class, 'storeOnlyOffice'])->name('store-onlyoffice')->middleware('check.permission:documentos.create');
        Route::get('/{modelo}', [App\Http\Controllers\Documento\DocumentoModeloController::class, 'show'])->name('show')->middleware('check.permission:documentos.view');
        Route::get('/{modelo}/edit', [App\Http\Controllers\Documento\DocumentoModeloController::class, 'edit'])->name('edit')->middleware('check.permission:documentos.edit');
        Route::put('/{modelo}', [App\Http\Controllers\Documento\DocumentoModeloController::class, 'update'])->name('update')->middleware('check.permission:documentos.edit');
        Route::delete('/{modelo}', [App\Http\Controllers\Documento\DocumentoModeloController::class, 'destroy'])->name('destroy')->middleware('check.permission:documentos.delete');
        Route::get('/{modelo}/download', [App\Http\Controllers\Documento\DocumentoModeloController::class, 'download'])->name('download')->middleware('check.permission:documentos.view');
        Route::get('/{modelo}/editor-onlyoffice', [App\Http\Controllers\Documento\DocumentoModeloController::class, 'editorOnlyOffice'])->name('editor-onlyoffice')->middleware('check.permission:documentos.edit');
        Route::get('/{modelo}/last-update', [App\Http\Controllers\Documento\DocumentoModeloController::class, 'lastUpdate'])->name('last-update')->middleware('check.permission:documentos.view');
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
    if (! auth()->check()) {
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
    // Guia de Desenvolvimento
    Route::get('guia-desenvolvimento', [App\Http\Controllers\Admin\GuiaDesenvolvimentoController::class, 'index'])
        ->name('admin.guia-desenvolvimento.index');
    Route::get('guia-desenvolvimento/biblioteca-digital', [App\Http\Controllers\Admin\GuiaDesenvolvimentoController::class, 'bibliotecaDigital'])
        ->name('admin.guia-desenvolvimento.biblioteca-digital');

    // Documentação Técnica
    Route::prefix('technical-doc')->name('admin.technical-doc.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\TechnicalDocController::class, 'index'])
            ->name('index')->middleware('check.permission:admin.view');
        Route::get('/{module}', [App\Http\Controllers\Admin\TechnicalDocController::class, 'module'])
            ->name('module')->middleware('check.permission:admin.view');
    });
    Route::post('guia-desenvolvimento/gerar', [App\Http\Controllers\Admin\GuiaDesenvolvimentoController::class, 'gerarExemplo'])
        ->name('admin.guia-desenvolvimento.gerar');
    Route::post('guia-desenvolvimento/consultar-docs', [App\Http\Controllers\Admin\GuiaDesenvolvimentoController::class, 'consultarDocs'])
        ->name('admin.guia-desenvolvimento.consultar-docs');

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
    Route::post('templates/{tipo}/salvar', [App\Http\Controllers\TemplateController::class, 'salvarTemplate'])
        ->name('templates.salvar');
    Route::post('templates/regenerar-todos', [App\Http\Controllers\TemplateController::class, 'regenerarTodos'])
        ->name('templates.regenerar-todos');
    Route::get('templates/status', [App\Http\Controllers\TemplateController::class, 'status'])
        ->name('templates.status');
    Route::get('templates/{tipo}/preview', [App\Http\Controllers\TemplateController::class, 'previewTemplate'])
        ->name('templates.preview');

    // Rotas para Padrões Legais
    Route::post('templates/{tipo}/gerar-padroes-legais', [App\Http\Controllers\TemplateController::class, 'gerarComPadroesLegais'])
        ->name('templates.gerar-padroes-legais');

});

// 🔄 ROTAS DE SISTEMA DE WORKFLOWS MODULARES (apenas middleware auth)
Route::prefix('admin/workflows')->name('admin.workflows.')->middleware('auth')->group(function () {
    Route::get('/', [App\Http\Controllers\Admin\WorkflowController::class, 'index'])
        ->name('index');
    Route::get('/create', [App\Http\Controllers\Admin\WorkflowController::class, 'create'])
        ->name('create');
    Route::post('/', [App\Http\Controllers\Admin\WorkflowController::class, 'store'])
        ->name('store');
    Route::get('/{workflow}', [App\Http\Controllers\Admin\WorkflowController::class, 'show'])
        ->name('show');
    Route::get('/{workflow}/edit', [App\Http\Controllers\Admin\WorkflowController::class, 'edit'])
        ->name('edit');
    Route::put('/{workflow}', [App\Http\Controllers\Admin\WorkflowController::class, 'update'])
        ->name('update');
    Route::delete('/{workflow}', [App\Http\Controllers\Admin\WorkflowController::class, 'destroy'])
        ->name('destroy');
    
    // Ações especiais
    Route::patch('/{workflow}/toggle', [App\Http\Controllers\Admin\WorkflowController::class, 'toggle'])
        ->name('toggle');
    Route::patch('/{workflow}/set-default', [App\Http\Controllers\Admin\WorkflowController::class, 'setDefault'])
        ->name('set-default');
    Route::post('/{workflow}/duplicate', [App\Http\Controllers\Admin\WorkflowController::class, 'duplicate'])
        ->name('duplicate');
    
    // Designer visual
    Route::get('/designer/new', [App\Http\Controllers\Admin\WorkflowController::class, 'designer'])
        ->name('designer.new');
    Route::get('/{workflow}/designer', [App\Http\Controllers\Admin\WorkflowController::class, 'designer'])
        ->name('designer.edit');
    Route::get('/{workflow}/designer-data', [App\Http\Controllers\Admin\WorkflowController::class, 'designerData'])
        ->name('designer.data');
});

Route::prefix('admin')->middleware(['auth', 'check.screen.permission'])->group(function () {
    Route::get('templates/{tipo}/validar', [App\Http\Controllers\TemplateController::class, 'validarTemplate'])
        ->name('templates.validar');

    // Parâmetros de Protocolo
    Route::prefix('parametros/protocolo')->name('admin.parametros.protocolo.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\ParametroProtocoloController::class, 'index'])->name('index');
        Route::put('/update', [App\Http\Controllers\Admin\ParametroProtocoloController::class, 'update'])->name('update');
        Route::post('/testar', [App\Http\Controllers\Admin\ParametroProtocoloController::class, 'testarFormato'])->name('testar');
        Route::get('/restaurar', [App\Http\Controllers\Admin\ParametroProtocoloController::class, 'restaurarPadroes'])->name('restaurar');
    });

    // Nova Arquitetura - DocumentoTemplate routes
    Route::prefix('documento-templates')->name('documento-templates.')->group(function () {
        Route::get('/', [App\Http\Controllers\DocumentoTemplateController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\DocumentoTemplateController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\DocumentoTemplateController::class, 'store'])->name('store');
        Route::get('/{documentoTemplate}', [App\Http\Controllers\DocumentoTemplateController::class, 'show'])->name('show');
        Route::get('/{documentoTemplate}/edit', [App\Http\Controllers\DocumentoTemplateController::class, 'edit'])->name('edit');
        Route::put('/{documentoTemplate}', [App\Http\Controllers\DocumentoTemplateController::class, 'update'])->name('update');
        Route::delete('/{documentoTemplate}', [App\Http\Controllers\DocumentoTemplateController::class, 'destroy'])->name('destroy');
        Route::get('/{documentoTemplate}/download', [App\Http\Controllers\DocumentoTemplateController::class, 'download'])->name('download');
        Route::get('/{documentoTemplate}/preview', [App\Http\Controllers\DocumentoTemplateController::class, 'preview'])->name('preview');
        Route::get('/{documentoTemplate}/serve', [App\Http\Controllers\DocumentoTemplateController::class, 'serve'])->name('serve');
        Route::patch('/{documentoTemplate}/toggle-status', [App\Http\Controllers\DocumentoTemplateController::class, 'toggleStatus'])->name('toggle-status');
        Route::post('/{documentoTemplate}/duplicate', [App\Http\Controllers\DocumentoTemplateController::class, 'duplicate'])->name('duplicate');
    });

    // AI Configurations routes
    Route::prefix('ai-configurations')->name('admin.ai-configurations.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\AIConfigurationController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Admin\AIConfigurationController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Admin\AIConfigurationController::class, 'store'])->name('store');

        // AJAX routes (must come BEFORE parameterized routes)
        Route::post('/test-data', [App\Http\Controllers\Admin\AIConfigurationController::class, 'testConnectionData'])->name('test-data');
        Route::post('/test-all', [App\Http\Controllers\Admin\AIConfigurationController::class, 'testAllConnections'])->name('test-all');
        Route::get('/provider-models', [App\Http\Controllers\Admin\AIConfigurationController::class, 'getProviderModels'])->name('provider-models');
        Route::post('/reorder', [App\Http\Controllers\Admin\AIConfigurationController::class, 'reorder'])->name('reorder');
        Route::get('/usage-stats', [App\Http\Controllers\Admin\AIConfigurationController::class, 'getUsageStats'])->name('usage-stats');

        // Parameterized routes (must come AFTER specific routes)
        Route::get('/{aiConfiguration}', [App\Http\Controllers\Admin\AIConfigurationController::class, 'show'])->name('show');
        Route::get('/{aiConfiguration}/edit', [App\Http\Controllers\Admin\AIConfigurationController::class, 'edit'])->name('edit');
        Route::put('/{aiConfiguration}', [App\Http\Controllers\Admin\AIConfigurationController::class, 'update'])->name('update');
        Route::delete('/{aiConfiguration}', [App\Http\Controllers\Admin\AIConfigurationController::class, 'destroy'])->name('destroy');
        Route::post('/{aiConfiguration}/test', [App\Http\Controllers\Admin\AIConfigurationController::class, 'testConnection'])->name('test');
        Route::post('/{aiConfiguration}/toggle-active', [App\Http\Controllers\Admin\AIConfigurationController::class, 'toggleActive'])->name('toggle-active');
        Route::post('/{aiConfiguration}/reset-usage', [App\Http\Controllers\Admin\AIConfigurationController::class, 'resetDailyUsage'])->name('reset-usage');
    });

    // Database Administration routes
    Route::get('/database', [App\Http\Controllers\Admin\AdminDatabaseController::class, 'index'])
        ->name('admin.database.index')
        ->middleware('check.screen.permission');
    Route::get('/database/table/{table}', [App\Http\Controllers\Admin\AdminDatabaseController::class, 'showTable'])
        ->name('admin.database.table')
        ->middleware('check.screen.permission');

    // System Diagnostic routes
    Route::get('system-diagnostic', [App\Http\Controllers\Admin\SystemDiagnosticController::class, 'index'])
        ->name('admin.system-diagnostic.index');
    Route::get('system-diagnostic/database', [App\Http\Controllers\Admin\SystemDiagnosticController::class, 'database'])
        ->name('admin.system-diagnostic.database');
    Route::get('system-diagnostic/database/table/{table}', [App\Http\Controllers\Admin\SystemDiagnosticController::class, 'tableRecords'])
        ->name('admin.system-diagnostic.table');
    Route::post('system-diagnostic/fix-permissions', [App\Http\Controllers\Admin\SystemDiagnosticController::class, 'fixPermissions'])
        ->name('admin.system-diagnostic.fix-permissions');

    // PyHanko Fluxo routes
    Route::get('pyhanko-fluxo', [App\Http\Controllers\Admin\PyHankoFluxoController::class, 'index'])
        ->name('admin.pyhanko-fluxo.index');
    Route::post('pyhanko-fluxo/testar-status', [App\Http\Controllers\Admin\PyHankoFluxoController::class, 'testarStatus'])
        ->name('admin.pyhanko-fluxo.testar-status');
    Route::post('pyhanko-fluxo/executar-teste', [App\Http\Controllers\Admin\PyHankoFluxoController::class, 'executarTeste'])
        ->name('admin.pyhanko-fluxo.executar-teste');
});

// Test Module routes (protected with auth)
Route::prefix('tests')->name('tests.')->middleware('auth')->group(function () {
    Route::get('/', [App\Http\Controllers\TestController::class, 'index'])->name('index');

    // User Tests
    Route::get('/users', [App\Http\Controllers\TestController::class, 'usersIndex'])->name('users');
    Route::post('/create-users', [App\Http\Controllers\TestController::class, 'createTestUsers'])->name('create-users');
    Route::get('/list-users', [App\Http\Controllers\TestController::class, 'listTestUsers'])->name('list-users');
    Route::delete('/clear-users', [App\Http\Controllers\TestController::class, 'clearTestUsers'])->name('clear-users');

    // Process Tests
    Route::get('/processes', [App\Http\Controllers\TestController::class, 'processesIndex'])->name('processes');
    Route::post('/run-process-tests', [App\Http\Controllers\TestController::class, 'runProcessTests'])->name('run-process-tests');
    Route::post('/run-pest-tests', [App\Http\Controllers\TestController::class, 'runPestTests'])->name('run-pest-tests');

    // Visualization HTML Files
    Route::get('/processes/index.html', [App\Http\Controllers\TestController::class, 'visualizationCenter'])->name('processes.visualization-center');
    Route::get('/processes/fluxo-visualizer.html', [App\Http\Controllers\TestController::class, 'fluxoVisualizer'])->name('processes.fluxo-visualizer');
    Route::get('/processes/fluxo-dashboard.html', [App\Http\Controllers\TestController::class, 'fluxoDashboard'])->name('processes.fluxo-dashboard');
    Route::get('/processes/network-flow.html', [App\Http\Controllers\TestController::class, 'networkFlow'])->name('processes.network-flow');
    Route::get('/processes/animated-flow.html', [App\Http\Controllers\TestController::class, 'animatedFlow'])->name('processes.animated-flow');

    // Interactive Process Test Routes
    Route::get('/get-tipos-proposicao', [App\Http\Controllers\TestController::class, 'getTiposProposicao'])->name('get-tipos-proposicao');
    Route::get('/get-templates/{tipoId}', [App\Http\Controllers\TestController::class, 'getTemplates'])->name('get-templates');
    Route::post('/create-proposicao', [App\Http\Controllers\TestController::class, 'createProposicaoTest'])->name('create-proposicao');
    Route::post('/tramitar/{id}/enviar-legislativo', [App\Http\Controllers\TestController::class, 'enviarLegislativo'])->name('tramitar.enviar-legislativo');
    Route::post('/tramitar/{id}/analisar-legislativo', [App\Http\Controllers\TestController::class, 'analisarLegislativo'])->name('tramitar.analisar-legislativo');
    Route::post('/tramitar/{id}/converter-pdf', [App\Http\Controllers\TestController::class, 'converterPDF'])->name('tramitar.converter-pdf');
    Route::post('/tramitar/{id}/assinar', [App\Http\Controllers\TestController::class, 'assinarDocumento'])->name('tramitar.assinar');
    Route::post('/tramitar/{id}/protocolizar', [App\Http\Controllers\TestController::class, 'protocolizar'])->name('tramitar.protocolizar');
    Route::post('/tramitar/{id}/enviar-expediente', [App\Http\Controllers\TestController::class, 'enviarExpediente'])->name('tramitar.enviar-expediente');
    Route::post('/tramitar/{id}/emitir-parecer', [App\Http\Controllers\TestController::class, 'emitirParecer'])->name('tramitar.emitir-parecer');

    // API Tests
    Route::get('/api', [App\Http\Controllers\TestController::class, 'apiIndex'])->name('api');

    // Database Tests
    Route::get('/database', [App\Http\Controllers\TestController::class, 'databaseIndex'])->name('database');

    // Performance Tests
    Route::get('/performance', [App\Http\Controllers\TestController::class, 'performanceIndex'])->name('performance');

    // Security Tests
    Route::get('/security', [App\Http\Controllers\TestController::class, 'securityIndex'])->name('security');
});

// API Routes
Route::prefix('api')->name('api.')->middleware(['auth'])->group(function () {
    // CEP search API
    Route::get('/cep/{cep}', [App\Http\Controllers\Api\CepController::class, 'buscar'])->name('cep.buscar');
});

// Menu Debug Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/test-menu', [App\Http\Controllers\TestMenuController::class, 'testExpedienteMenu'])->name('test-menu');
    Route::get('/debug-menu', function () {
        return view('debug-menu');
    })->name('debug-menu');
    Route::get('/test-permissions-live', function () {
        return view('test-permissions-live');
    })->name('test-permissions-live');
    Route::get('/test-aside-debug', function () {
        return view('test-aside-debug');
    })->name('test-aside-debug');
});

// Consulta pública de proposições (acesso sem autenticação)
Route::get('/consulta/proposicao/{id}', [\App\Http\Controllers\ProposicaoController::class, 'consultaPublica'])
    ->name('proposicoes.consulta.publica')
    ->where('id', '[0-9]+');

// Download do PDF para consulta pública
Route::get('/consulta/proposicao/{id}/pdf', [\App\Http\Controllers\ProposicaoController::class, 'consultaPublicaPdf'])
    ->name('proposicoes.consulta.pdf')
    ->where('id', '[0-9]+');

// ===== ASSINATURA DIGITAL ===== (FORA do grupo proposicoes para evitar conflito de middlewares)
Route::prefix('proposicoes/{proposicao}/assinatura-digital')->name('proposicoes.assinatura-digital.')->middleware(['auth', 'check.assinatura.permission'])->group(function () {
    Route::get('/', [App\Http\Controllers\AssinaturaDigitalController::class, 'mostrarFormulario'])->name('formulario');
    Route::get('/dados', [App\Http\Controllers\AssinaturaDigitalController::class, 'dados'])->name('dados');
    Route::get('/pdf', [App\Http\Controllers\AssinaturaDigitalController::class, 'servirPDFParaAssinatura'])->name('pdf');
    Route::post('/processar', [App\Http\Controllers\AssinaturaDigitalController::class, 'processarAssinatura'])->name('processar');
    Route::get('/visualizar', [App\Http\Controllers\AssinaturaDigitalController::class, 'visualizarPDFAssinado'])->name('visualizar');
    Route::get('/download', [App\Http\Controllers\AssinaturaDigitalController::class, 'downloadPDFAssinado'])->name('download');
    Route::get('/status', [App\Http\Controllers\AssinaturaDigitalController::class, 'verificarStatus'])->name('status');
});

// Rota de teste JSON para debug
Route::post('/teste-json', [App\Http\Controllers\AssinaturaDigitalController::class, 'testeJson'])->middleware('auth');

// Rota pública para verificação de assinatura PAdES
Route::get('/proposicoes/{proposicao}/verificar-assinatura/{uuid?}', [App\Http\Controllers\AssinaturaDigitalController::class, 'verificarAssinaturaPublica'])
    ->name('proposicoes.verificar.assinatura');

// Debug S3 status (temporário)
Route::get('/debug/proposicoes/{proposicao}/s3-status', [App\Http\Controllers\AssinaturaDigitalController::class, 'debugS3Status'])->middleware('auth');

// Fix proposição 4 S3 (temporário)
Route::post('/debug/proposicoes/{proposicao}/fix-s3', [App\Http\Controllers\AssinaturaDigitalController::class, 'fixProposicao4S3'])->middleware('auth');
Route::get('/debug/proposicoes/{proposicao}/fix-s3', [App\Http\Controllers\AssinaturaDigitalController::class, 'fixProposicao4S3'])->middleware('auth');

// Fix proposição S3 automático (qualquer proposição)
Route::get('/debug/proposicoes/{proposicao}/fix-s3-auto', [App\Http\Controllers\AssinaturaDigitalController::class, 'fixProposicaoS3Auto'])->middleware('auth');

// ===== ROTA TEMPORÁRIA PARA PDF DE PROPOSIÇÕES PROTOCOLADAS (SEM AUTENTICAÇÃO) =====
Route::get('/proposicoes/{proposicao}/pdf-publico', [\App\Http\Controllers\ProposicaoController::class, 'servePDFPublico'])
    ->name('proposicoes.pdf.publico')
    ->where('proposicao', '[0-9]+');

// ===== ROTA PARA URLs TEMPORÁRIAS DE PDF COM TOKEN =====
Route::get('/pdf-temp/{token}', [\App\Http\Controllers\ProposicaoController::class, 'servePDFTemporary'])
    ->name('proposicoes.pdf.temporary')
    ->where('token', '[a-zA-Z0-9]{64}');

// ===== DEBUG LOGGER ROUTES =====
Route::middleware(['auth'])->prefix('debug')->name('debug.')->group(function () {
    Route::post('/start', [DebugController::class, 'start'])->name('start');
    Route::post('/stop', [DebugController::class, 'stop'])->name('stop');
    Route::get('/status', [DebugController::class, 'status'])->name('status');
    Route::get('/logs', [DebugController::class, 'getLogs'])->name('logs');
    Route::post('/export', [DebugController::class, 'exportLogs'])->name('export');
    Route::delete('/cleanup', [DebugController::class, 'cleanup'])->name('cleanup');
    
    // Database debug routes
    Route::get('/database/queries', [DebugController::class, 'getDatabaseQueries'])->name('database.queries');
    Route::get('/database/stats', [DebugController::class, 'getDatabaseStats'])->name('database.stats');
});

// Rota pública para servir arquivos de debug exportados
Route::get('/debug/exports/{filename}', function ($filename) {
    $path = storage_path("app/debug_exports/{$filename}");
    
    if (!file_exists($path)) {
        abort(404);
    }
    
    return response()->file($path, [
        'Content-Type' => 'text/plain',
        'Content-Disposition' => 'attachment; filename="' . $filename . '"'
    ]);
})->name('debug.download');

// Rota de teste para o debug logger
Route::middleware(['web', 'auth'])->get('/test-debug', function () {
    return view('test-debug');
})->name('test.debug.logger');

// Rotas de Logs do Fluxo de Documentos - Admin apenas
Route::middleware(['web', 'auth'])->prefix('admin/document-workflow-logs')->name('admin.document-workflow-logs.')->group(function () {
    Route::get('/', [App\Http\Controllers\Admin\DocumentWorkflowLogController::class, 'index'])->name('index');
    Route::get('/export', [App\Http\Controllers\Admin\DocumentWorkflowLogController::class, 'export'])->name('export');
    Route::get('/export-json', [App\Http\Controllers\Admin\DocumentWorkflowLogController::class, 'exportJson'])->name('export-json');
    Route::delete('/delete', [App\Http\Controllers\Admin\DocumentWorkflowLogController::class, 'deleteLogs'])->name('delete');
    Route::get('/{proposicao}', [App\Http\Controllers\Admin\DocumentWorkflowLogController::class, 'show'])->name('show');
});

// Rotas de Monitoramento - Admin apenas
Route::middleware(['web', 'auth'])->prefix('admin/monitoring')->name('admin.monitoring.')->group(function () {
    Route::get('/', [MonitoringController::class, 'index'])->name('index');
    Route::get('/database', [MonitoringController::class, 'database'])->name('database');
    Route::get('/performance', [MonitoringController::class, 'performance'])->name('performance');
    Route::get('/logs', [MonitoringController::class, 'logs'])->name('logs');
    Route::delete('/logs/clear', [MonitoringController::class, 'clearLogs'])->name('logs.clear');
    Route::get('/alerts', [MonitoringController::class, 'alerts'])->name('alerts');

    // Database Activity Routes
    Route::get('/database-activity', [App\Http\Controllers\Admin\DatabaseActivityController::class, 'index'])->name('database-activity');
    Route::get('/database-activity/recent', [App\Http\Controllers\Admin\DatabaseActivityController::class, 'getRecentActivity'])->name('database-activity.recent');
    Route::get('/database-activity/table-stats', [App\Http\Controllers\Admin\DatabaseActivityController::class, 'getTableStats'])->name('database-activity.table-stats');
    Route::get('/database-activity/realtime-metrics', [App\Http\Controllers\Admin\DatabaseActivityController::class, 'getRealTimeMetrics'])->name('database-activity.realtime-metrics');
    Route::get('/database-activity/filter', [App\Http\Controllers\Admin\DatabaseActivityController::class, 'filterActivities'])->name('database-activity.filter');
    Route::get('/database-activity/active-tables', [App\Http\Controllers\Admin\DatabaseActivityController::class, 'getActiveTables'])->name('database-activity.active-tables');
    Route::get('/database-activity/filter-options', [App\Http\Controllers\Admin\DatabaseActivityController::class, 'getFilterOptions'])->name('database-activity.filter-options');

    // Detailed Analysis Routes
    Route::get('/database-activity/detailed', function() { return view('admin.monitoring.database-activity-detailed'); })->name('database-activity.detailed');
    Route::get('/database-activity/record-flow', [App\Http\Controllers\Admin\DatabaseActivityController::class, 'getRecordFlow'])->name('database-activity.record-flow');
    Route::get('/database-activity/column-analysis', [App\Http\Controllers\Admin\DatabaseActivityController::class, 'getColumnAnalysis'])->name('database-activity.column-analysis');
    Route::get('/database-activity/table-records', [App\Http\Controllers\Admin\DatabaseActivityController::class, 'getTableRecords'])->name('database-activity.table-records');
    Route::get('/database-activity/export', [App\Http\Controllers\Admin\DatabaseActivityController::class, 'exportActivities'])->name('database-activity.export');

    // API endpoints para AJAX
    Route::get('/api/database-stats', [MonitoringController::class, 'apiDatabaseStats'])->name('api.database-stats');
});

// Health check público (para monitoring externo)
Route::get('/health', [MonitoringController::class, 'health'])->name('monitoring.health');

// Teste temporário sem auth
Route::get('/monitoring-test', [MonitoringController::class, 'database'])->name('monitoring.test');

// Server-Sent Events for real-time monitoring
Route::get('/admin/monitoring/stream', [MonitoringController::class, 'stream'])
    ->name('admin.monitoring.stream')
    ->middleware('auth');

// Include debug routes
require __DIR__.'/debug.php';
