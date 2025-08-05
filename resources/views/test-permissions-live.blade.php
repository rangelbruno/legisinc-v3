<!DOCTYPE html>
<html>
<head>
    <title>Test Permissions Live</title>
    <meta http-equiv="refresh" content="5">
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        .debug { background: #f0f0f0; padding: 10px; margin: 10px 0; }
    </style>
</head>
<body>
    <h1>Live Permissions Test - Auto Refresh</h1>
    
    @if(Auth::check())
        <div class="debug">
            <h2>User Info:</h2>
            <p><strong>Name:</strong> {{ Auth::user()->name }}</p>
            <p><strong>Email:</strong> {{ Auth::user()->email }}</p>
            <p><strong>Roles:</strong> {{ Auth::user()->roles->pluck('name')->implode(', ') }}</p>
            <p><strong>Time:</strong> {{ now() }}</p>
        </div>
        
        <div class="debug">
            <h2>Module Permissions:</h2>
            <p>proposicoes: <span class="{{ \App\Models\ScreenPermission::userCanAccessModule('proposicoes') ? 'success' : 'error' }}">{{ \App\Models\ScreenPermission::userCanAccessModule('proposicoes') ? 'TRUE' : 'FALSE' }}</span></p>
            <p>expediente: <span class="{{ \App\Models\ScreenPermission::userCanAccessModule('expediente') ? 'success' : 'error' }}">{{ \App\Models\ScreenPermission::userCanAccessModule('expediente') ? 'TRUE' : 'FALSE' }}</span></p>
        </div>
        
        <div class="debug">
            <h2>Route Permissions:</h2>
            <p>expediente.index: <span class="{{ \App\Models\ScreenPermission::userCanAccessRoute('expediente.index') ? 'success' : 'error' }}">{{ \App\Models\ScreenPermission::userCanAccessRoute('expediente.index') ? 'TRUE' : 'FALSE' }}</span></p>
            <p>proposicoes.legislativo.index: <span class="{{ \App\Models\ScreenPermission::userCanAccessRoute('proposicoes.legislativo.index') ? 'success' : 'error' }}">{{ \App\Models\ScreenPermission::userCanAccessRoute('proposicoes.legislativo.index') ? 'TRUE' : 'FALSE' }}</span></p>
        </div>
        
        <div class="debug">
            <h2>Menu Conditions:</h2>
            <p>Show Proposições Menu: <span class="{{ \App\Models\ScreenPermission::userCanAccessModule('proposicoes') ? 'success' : 'error' }}">{{ \App\Models\ScreenPermission::userCanAccessModule('proposicoes') ? 'TRUE' : 'FALSE' }}</span></p>
            <p>Show Expediente Submenu: <span class="{{ (\App\Models\ScreenPermission::userCanAccessModule('expediente') || \App\Models\ScreenPermission::userCanAccessRoute('expediente.index')) ? 'success' : 'error' }}">{{ (\App\Models\ScreenPermission::userCanAccessModule('expediente') || \App\Models\ScreenPermission::userCanAccessRoute('expediente.index')) ? 'TRUE' : 'FALSE' }}</span></p>
        </div>
        
        <div class="debug">
            <h2>Expected Menu Structure:</h2>
            @if(\App\Models\ScreenPermission::userCanAccessModule('proposicoes'))
                <p>✅ <strong>Menu Proposições</strong></p>
                <div style="margin-left: 20px;">
                    @if(\App\Models\ScreenPermission::userCanAccessModule('expediente') || \App\Models\ScreenPermission::userCanAccessRoute('expediente.index'))
                        <p>✅ <strong>Submenu Expediente</strong></p>
                        <div style="margin-left: 20px;">
                            @if(\App\Models\ScreenPermission::userCanAccessRoute('expediente.index'))
                                <p>✅ Painel do Expediente</p>
                            @endif
                            @if(\App\Models\ScreenPermission::userCanAccessRoute('proposicoes.legislativo.index'))
                                <p>✅ Proposições Protocoladas</p>
                            @endif
                            @if(\App\Models\ScreenPermission::userCanAccessRoute('expediente.aguardando-pauta'))
                                <p>✅ Aguardando Pauta</p>
                            @endif
                            @if(\App\Models\ScreenPermission::userCanAccessRoute('expediente.relatorio'))
                                <p>✅ Relatório</p>
                            @endif
                        </div>
                    @else
                        <p>❌ Submenu Expediente (conditions not met)</p>
                    @endif
                </div>
            @else
                <p>❌ Menu Proposições (not accessible)</p>
            @endif
        </div>
        
    @else
        <p class="error">User not logged in</p>
    @endif
    
</body>
</html>