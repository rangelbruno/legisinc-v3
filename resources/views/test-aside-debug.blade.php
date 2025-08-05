<!DOCTYPE html>
<html>
<head>
    <title>Test Aside Debug</title>
</head>
<body>
    <h1>Debug Aside Template</h1>
    
    <h2>User Info:</h2>
    <p>User: {{ Auth::user()->name ?? 'NOT_LOGGED' }}</p>
    <p>Roles: {{ Auth::user()->roles->pluck('name')->implode(',') ?? 'NO_ROLES' }}</p>
    
    <h2>Menu Structure Test:</h2>
    
    <!-- Test if proposicoes module works -->
    @if(\App\Models\ScreenPermission::userCanAccessModule('proposicoes'))
        <div style="border: 2px solid green; padding: 10px; margin: 10px;">
            <strong>✅ PROPOSIÇÕES MODULE ACCESSIBLE</strong>
            
            <!-- Test expediente submenu condition -->
            @if(\App\Models\ScreenPermission::userCanAccessModule('expediente') || \App\Models\ScreenPermission::userCanAccessRoute('expediente.index'))
                <div style="border: 2px solid blue; padding: 10px; margin: 10px;">
                    <strong>✅ EXPEDIENTE SUBMENU SHOULD APPEAR</strong>
                    
                    <div style="margin-left: 20px;">
                        <h3>📋 Expediente Menu Items:</h3>
                        
                        @if(\App\Models\ScreenPermission::userCanAccessRoute('expediente.index'))
                            <p>✅ Painel do Expediente</p>
                        @else
                            <p>❌ Painel do Expediente</p>
                        @endif
                        
                        @if(\App\Models\ScreenPermission::userCanAccessRoute('proposicoes.legislativo.index'))
                            <p>✅ Proposições Protocoladas</p>
                        @else
                            <p>❌ Proposições Protocoladas</p>
                        @endif
                        
                        @if(\App\Models\ScreenPermission::userCanAccessRoute('expediente.aguardando-pauta'))
                            <p>✅ Aguardando Pauta</p>
                        @else
                            <p>❌ Aguardando Pauta</p>
                        @endif
                        
                        @if(\App\Models\ScreenPermission::userCanAccessRoute('expediente.relatorio'))
                            <p>✅ Relatório</p>
                        @else
                            <p>❌ Relatório</p>
                        @endif
                    </div>
                </div>
            @else
                <div style="border: 2px solid red; padding: 10px; margin: 10px;">
                    <strong>❌ EXPEDIENTE SUBMENU CONDITIONS NOT MET</strong>
                    <p>Module expediente: {{ \App\Models\ScreenPermission::userCanAccessModule('expediente') ? 'TRUE' : 'FALSE' }}</p>
                    <p>Route expediente.index: {{ \App\Models\ScreenPermission::userCanAccessRoute('expediente.index') ? 'TRUE' : 'FALSE' }}</p>
                </div>
            @endif
        </div>
    @else
        <div style="border: 2px solid red; padding: 10px; margin: 10px;">
            <strong>❌ PROPOSIÇÕES MODULE NOT ACCESSIBLE</strong>
        </div>
    @endif
    
    <h2>File Info:</h2>
    <p>Current time: {{ now() }}</p>
    <p>Template compiled: {{ date('Y-m-d H:i:s') }}</p>
    
</body>
</html>