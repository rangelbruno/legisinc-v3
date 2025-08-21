#!/bin/bash

echo "🔐 TESTE SIMPLES: Verificar Assinatura Digital"
echo "=============================================="

echo
echo "📋 1. Adicionando assinatura à proposição 2..."
docker exec legisinc-app php -r "
\$pdo = new PDO('pgsql:host=db;dbname=legisinc', 'postgres', '123456');
\$stmt = \$pdo->prepare('UPDATE proposicoes SET 
    assinatura_digital = ?, 
    data_assinatura = NOW(), 
    certificado_digital = ?, 
    ip_assinatura = ?,
    status = ?
    WHERE id = ?');
\$result = \$stmt->execute([
    'CERTIFICADO_DIGITAL_TESTE_123456789',
    'CN=Jessica Santos, O=Câmara Municipal, C=BR',
    '192.168.1.100',
    'assinado',
    2
]);
echo \$result ? 'Sucesso' : 'Erro';
"

echo
echo "🔍 2. Verificando dados da proposição..."
docker exec legisinc-app php -r "
\$pdo = new PDO('pgsql:host=db;dbname=legisinc', 'postgres', '123456');
\$stmt = \$pdo->prepare('SELECT id, status, assinatura_digital IS NOT NULL as tem_assinatura, data_assinatura FROM proposicoes WHERE id = 2');
\$stmt->execute();
\$row = \$stmt->fetch(PDO::FETCH_ASSOC);
if (\$row) {
    echo \"ID: {\$row['id']}, Status: {\$row['status']}, Assinatura: \" . (\$row['tem_assinatura'] ? 'SIM' : 'NÃO') . \", Data: {\$row['data_assinatura']}\";
}
"

echo
echo
echo "🎯 3. Testando processamento direto da variável..."
docker exec legisinc-app php -r "
require_once '/var/www/html/bootstrap/app.php';

// Carregar proposição
\$proposicao = App\Models\Proposicao::find(2);
if (!\$proposicao) {
    echo 'Proposição não encontrada';
    exit(1);
}

// Instanciar serviços
\$parametroService = new App\Services\Parametro\ParametroService();
\$templateVariableService = new App\Services\Template\TemplateVariableService(\$parametroService);
\$templateProcessor = new App\Services\Template\TemplateProcessorService(\$parametroService, \$templateVariableService);

// Usar reflexão para acessar método privado
\$reflection = new ReflectionClass(\$templateProcessor);
\$method = \$reflection->getMethod('gerarTextoAssinaturaDigital');
\$method->setAccessible(true);
\$textoAssinatura = \$method->invoke(\$templateProcessor, \$proposicao);

echo \"Texto da assinatura gerado: \" . \$textoAssinatura;
"

echo
echo
echo "✅ TESTE CONCLUÍDO"
echo "Para verificar o resultado, acesse: http://localhost:8001/proposicoes/2/assinar"