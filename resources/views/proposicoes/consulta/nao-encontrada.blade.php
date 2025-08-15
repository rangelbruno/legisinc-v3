<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proposição Não Encontrada - Consulta Pública</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
        }
        .error-container {
            text-align: center;
            background: white;
            border-radius: 10px;
            padding: 3rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        .error-icon {
            font-size: 4rem;
            color: #dc3545;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="error-container">
                    <div class="error-icon">❌</div>
                    <h1 class="text-danger mb-4">Proposição Não Encontrada</h1>
                    <p class="mb-4">
                        A proposição solicitada não foi encontrada no sistema ou pode ter sido removida.
                    </p>
                    <div class="alert alert-warning">
                        <strong>Possíveis causas:</strong>
                        <ul class="text-start mt-2 mb-0">
                            <li>O ID da proposição está incorreto</li>
                            <li>A proposição foi removida do sistema</li>
                            <li>O QR Code pode estar danificado</li>
                        </ul>
                    </div>
                    <p class="text-muted">
                        <small>Se você possui o documento físico, verifique se o QR Code está legível e tente novamente.</small>
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>