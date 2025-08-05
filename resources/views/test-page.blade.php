<!DOCTYPE html>
<html>
<head>
    <title>Teste Simples</title>
    <style>
        body { font-family: Arial; padding: 20px; }
        .btn { padding: 10px 20px; margin: 10px; text-decoration: none; display: inline-block; }
        .red { background: red; color: white; }
        .blue { background: blue; color: white; }
    </style>
</head>
<body>
    <h1>🧪 Página de Teste Simples</h1>
    <p>Usuário logado: {{ auth()->check() ? auth()->user()->email : 'Não logado' }}</p>
    
    <div>
        <a href="{{ route('parametros.templates.debug') }}" class="btn red">
            🔧 DEBUG
        </a>
        
        <a href="{{ route('parametros.templates.cabecalho') }}" class="btn blue">
            🎯 TEMPLATES
        </a>
        
        <a href="{{ route('parametros.index') }}" class="btn" style="background:green;color:white;">
            ← Voltar para Parâmetros
        </a>
    </div>
    
    <hr>
    <h3>URLs Testadas:</h3>
    <ul>
        <li>Debug: {{ route('parametros.templates.debug') }}</li>
        <li>Templates: {{ route('parametros.templates.cabecalho') }}</li>
        <li>Parâmetros: {{ route('parametros.index') }}</li>
    </ul>
    
    <script>
        console.log('✅ JavaScript funcionando na página teste');
        
        // Test onclick
        function testClick() {
            alert('🎯 JavaScript funcionando!');
        }
    </script>
    
    <button onclick="testClick()" style="padding:10px;background:orange;color:white;border:none;">
        🧪 Testar JavaScript
    </button>
</body>
</html>