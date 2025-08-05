<!DOCTYPE html>
<html>
<head>
    <title>Debug Menu Expediente</title>
</head>
<body>
    <h1>Debug Menu - Usuário: {{ Auth::user()->name ?? 'Não logado' }}</h1>
    
    @if(Auth::check())
        <p><strong>Email:</strong> {{ Auth::user()->email }}</p>
        <p><strong>Roles:</strong> {{ Auth::user()->roles->pluck('name')->implode(', ') }}</p>
        
        <h2>Permissões de Módulos:</h2>
        <ul>
            <li>proposicoes: {{ \App\Models\ScreenPermission::userCanAccessModule('proposicoes') ? '✅ SIM' : '❌ NÃO' }}</li>
            <li>expediente: {{ \App\Models\ScreenPermission::userCanAccessModule('expediente') ? '✅ SIM' : '❌ NÃO' }}</li>
        </ul>
        
        <h2>Permissões de Rotas:</h2>
        <ul>
            <li>expediente.index: {{ \App\Models\ScreenPermission::userCanAccessRoute('expediente.index') ? '✅ SIM' : '❌ NÃO' }}</li>
            <li>proposicoes.legislativo.index: {{ \App\Models\ScreenPermission::userCanAccessRoute('proposicoes.legislativo.index') ? '✅ SIM' : '❌ NÃO' }}</li>
            <li>expediente.aguardando-pauta: {{ \App\Models\ScreenPermission::userCanAccessRoute('expediente.aguardando-pauta') ? '✅ SIM' : '❌ NÃO' }}</li>
            <li>expediente.relatorio: {{ \App\Models\ScreenPermission::userCanAccessRoute('expediente.relatorio') ? '✅ SIM' : '❌ NÃO' }}</li>
        </ul>
        
        <h2>Condições do Menu:</h2>
        <ul>
            <li>Mostrar Menu Proposições: {{ \App\Models\ScreenPermission::userCanAccessModule('proposicoes') ? '✅ SIM' : '❌ NÃO' }}</li>
            <li>Mostrar Submenu Expediente: {{ (\App\Models\ScreenPermission::userCanAccessModule('expediente') || \App\Models\ScreenPermission::userCanAccessRoute('expediente.index')) ? '✅ SIM' : '❌ NÃO' }}</li>
        </ul>
        
        <h2>HTML Simulado do Menu:</h2>
        <div style="border: 1px solid #ccc; padding: 10px; background: #f9f9f9;">
            @if(\App\Models\ScreenPermission::userCanAccessModule('proposicoes'))
                <strong>Menu Proposições</strong>
                <div style="margin-left: 20px;">
                    @if(\App\Models\ScreenPermission::userCanAccessModule('expediente') || \App\Models\ScreenPermission::userCanAccessRoute('expediente.index'))
                        <strong>📋 Submenu Expediente</strong>
                        <div style="margin-left: 20px;">
                            @if(\App\Models\ScreenPermission::userCanAccessRoute('expediente.index'))
                                <div>✅ Painel do Expediente</div>
                            @endif
                            @if(\App\Models\ScreenPermission::userCanAccessRoute('proposicoes.legislativo.index'))
                                <div>✅ Proposições Protocoladas</div>
                            @endif
                            @if(\App\Models\ScreenPermission::userCanAccessRoute('expediente.aguardando-pauta'))
                                <div>✅ Aguardando Pauta</div>
                            @endif
                            @if(\App\Models\ScreenPermission::userCanAccessRoute('expediente.relatorio'))
                                <div>✅ Relatório</div>
                            @endif
                        </div>
                    @else
                        <div style="color: red;">❌ Submenu Expediente (condições não atendidas)</div>
                    @endif
                </div>
            @else
                <div style="color: red;">❌ Menu Proposições (módulo não acessível)</div>
            @endif
        </div>
        
    @else
        <p style="color: red;">❌ Usuário não está logado</p>
    @endif
    
</body>
</html>