<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withProviders([
        App\Providers\OnlyOfficeServiceProvider::class,
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
            'block.protocolo.access' => \App\Http\Middleware\BlockProtocoloAccess::class,
            'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
            'prevent.back.history' => \App\Http\Middleware\PreventBackHistory::class,
            'role.permission' => \App\Http\Middleware\RolePermissionMiddleware::class,
        ]);
        
        // Aplica middleware para prevenir navegação com botão voltar em todas as rotas autenticadas
        $middleware->web([
            \App\Http\Middleware\PreventBackHistory::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
