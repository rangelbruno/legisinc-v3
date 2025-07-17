<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentação - LegisInc</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        
        .empty-container {
            background: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
            max-width: 600px;
        }
        
        .empty-icon {
            font-size: 4rem;
            margin-bottom: 20px;
        }
        
        .empty-title {
            color: #666;
            font-size: 2rem;
            margin-bottom: 20px;
        }
        
        .empty-message {
            color: #999;
            font-size: 1.1rem;
            line-height: 1.6;
        }
    </style>
</head>
<body>
    <div class="empty-container">
        <div class="empty-icon">📄</div>
        <h1 class="empty-title">Nenhum Documento Encontrado</h1>
        <p class="empty-message">
            Não há documentos disponíveis na pasta <code>/docs</code>.<br>
            Adicione arquivos .md à pasta para começar a usar a documentação.
        </p>
    </div>
</body>
</html> 