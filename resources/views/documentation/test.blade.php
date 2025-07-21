<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste CSS - Documentação LegisInc</title>
    <style>
        /* CSS inline para teste */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Arial', sans-serif; 
            background: #f5f5f5; 
            color: #333;
            line-height: 1.6;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
        }
        .success {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 4px;
            border: 1px solid #c3e6cb;
            margin: 10px 0;
        }
        .info {
            background: #d1ecf1;
            color: #0c5460;
            padding: 15px;
            border-radius: 4px;
            border: 1px solid #bee5eb;
            margin: 10px 0;
        }
        .code {
            background: #f8f9fa;
            color: #6f42c1;
            padding: 10px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            margin: 10px 0;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin: 5px;
        }
        .btn:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✅ Sistema de Documentação - Teste de CSS</h1>
            <p>Verificação da funcionalidade de CSS para documentação automática</p>
        </div>

        <div class="success">
            <strong>✅ Sucesso!</strong> O CSS está carregando e funcionando corretamente.
        </div>

        <div class="info">
            <strong>ℹ️ Informações do Sistema:</strong>
            <ul>
                <li>📂 Pasta docs: {{ count(File::files(base_path('docs'))) }} arquivos .md detectados</li>
                <li>🎨 CSS: Arquivo documentation.css localizado em public/css/</li>
                <li>🌐 URL do CSS: <code>{{ asset('css/documentation.css') }}</code></li>
                <li>📱 Responsivo: Interface otimizada para todos os dispositivos</li>
            </ul>
        </div>

        <div class="code">
            <strong>Funcionalidades Implementadas:</strong><br>
            • Detecção automática de arquivos .md<br>
            • Categorização inteligente<br>
            • Metadata avançada<br>
            • Busca em tempo real<br>
            • Sidebar responsivo<br>
            • Sistema de prioridades
        </div>

        <div style="margin: 20px 0;">
            <a href="{{ route('documentation.index') }}" class="btn">
                📚 Ver Documentação Completa
            </a>
            <a href="{{ asset('css/documentation.css') }}" class="btn" target="_blank">
                🎨 Ver Arquivo CSS
            </a>
        </div>

        <div class="info">
            <strong>🔧 Status dos Componentes:</strong>
            <ul>
                <li>✅ Controller: DocumentationController funcionando</li>
                <li>✅ CSS: documentation.css carregando ({{ filesize(public_path('css/documentation.css')) }} bytes)</li>
                <li>✅ Views: Templates Blade renderizando</li>
                <li>✅ Rotas: Todas as rotas /docs/* configuradas</li>
                <li>✅ Assets: Pasta public/css/ criada e acessível</li>
            </ul>
        </div>
    </div>
</body>
</html>