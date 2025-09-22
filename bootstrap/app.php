<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withProviders([
        App\Providers\OnlyOfficeServiceProvider::class,
        App\Providers\MonitoringServiceProvider::class,
    ])
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'check.permission' => \App\Http\Middleware\CheckPermission::class,
            'check.screen.permission' => \App\Http\Middleware\CheckScreenPermission::class,
            'auth.token' => \App\Http\Middleware\AuthenticateToken::class,
            'check.proposicao.permission' => \App\Http\Middleware\CheckProposicaoPermission::class,
            'check.parlamentar.access' => \App\Http\Middleware\CheckParlamentarAccess::class,
            'check.parlamentar.ativo' => \App\Http\Middleware\CheckParlamentarAtivo::class,
            'block.protocolo.access' => \App\Http\Middleware\BlockProtocoloAccess::class,
            'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
            'prevent.back.history' => \App\Http\Middleware\PreventBackHistory::class,
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'role.permission' => \App\Http\Middleware\RolePermissionMiddleware::class,
            'check.assinatura.permission' => \App\Http\Middleware\CheckAssinaturaPermission::class,
            'debug.logger' => \App\Http\Middleware\DebugActionLogger::class,
            'database.debug' => \App\Http\Middleware\DatabaseDebugMiddleware::class,
            'database.activity' => \App\Http\Middleware\DatabaseActivityLogger::class,
            'request.tracing' => \App\Http\Middleware\RequestTracing::class,
        ]);

        // Aplica middleware para prevenir navegação com botão voltar em todas as rotas autenticadas
        $middleware->web([
            \App\Http\Middleware\PreventBackHistory::class,
            \App\Http\Middleware\DebugActionLogger::class,
            \App\Http\Middleware\DatabaseDebugMiddleware::class,
            \App\Http\Middleware\DatabaseActivityLogger::class,
            \App\Http\Middleware\RequestTracing::class,
        ]);

        // Remover middleware de sessão das rotas API (especialmente callbacks OnlyOffice)
        $middleware->api([]);  // Usar só throttle padrão sem session middleware
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
