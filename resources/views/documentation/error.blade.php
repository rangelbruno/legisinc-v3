<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erro - Documentação</title>
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
        
        .error-container {
            background: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
            max-width: 600px;
        }
        
        .error-icon {
            font-size: 4rem;
            margin-bottom: 20px;
        }
        
        .error-title {
            color: #e74c3c;
            font-size: 2rem;
            margin-bottom: 20px;
        }
        
        .error-message {
            color: #666;
            font-size: 1.1rem;
            margin-bottom: 30px;
            line-height: 1.6;
        }
        
        .back-button {
            background: #3498db;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            display: inline-block;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.3s;
        }
        
        .back-button:hover {
            background: #2980b9;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">❌</div>
        <h1 class="error-title">Erro na Documentação</h1>
        <p class="error-message">{{ $error ?? 'Ocorreu um erro ao carregar a documentação.' }}</p>
        <a href="{{ url('/') }}" class="back-button">Voltar ao Início</a>
    </div>
</body>
</html> 