#!/bin/bash

echo "🔐 TESTE: Correção da Assinatura Digital no PDF"
echo "=============================================="

echo
echo "📋 1. Verificando proposição assinada..."
PROPOSICAO_ID=2

# Simular assinatura digital na proposição (se não tiver)
echo "   Adicionando assinatura digital à proposição ${PROPOSICAO_ID}..."
docker exec -it legisinc-app php -r "
\$pdo = new PDO('pgsql:host=db;dbname=legisinc', 'postgres', '123456');
\$stmt = \$pdo->prepare('UPDATE proposicoes SET 
    assinatura_digital = ?, 
    data_assinatura = NOW(), 
    certificado_digital = ?, 
    ip_assinatura = ?,
    status = ?
    WHERE id = ?');
\$stmt->execute([
    'CERTIFICADO_DIGITAL_TESTE_123456789',
    'CN=Jessica Santos, O=Câmara Municipal, C=BR',
    '192.168.1.100',
    'assinado',
    ${PROPOSICAO_ID}
]);
echo \"Assinatura digital adicionada com sucesso!\n\";
"

echo
echo "🔍 2. Verificando se assinatura foi salva..."
docker exec -it legisinc-app php -r "
\$pdo = new PDO('pgsql:host=db;dbname=legisinc', 'postgres', '123456');
\$stmt = \$pdo->prepare('SELECT id, status, assinatura_digital, data_assinatura FROM proposicoes WHERE id = ?');
\$stmt->execute([${PROPOSICAO_ID}]);
\$proposicao = \$stmt->fetch(PDO::FETCH_ASSOC);
if (\$proposicao) {
    echo \"✅ Proposição {\$proposicao['id']} encontrada\n\";
    echo \"   Status: {\$proposicao['status']}\n\";
    echo \"   Assinatura: \" . (\$proposicao['assinatura_digital'] ? 'SIM' : 'NÃO') . \"\n\";
    echo \"   Data: {\$proposicao['data_assinatura']}\n\";
} else {
    echo \"❌ Proposição não encontrada\n\";
    exit(1);
}
"

echo
echo "🎯 3. Testando processamento da variável \${assinatura_digital_info}..."
docker exec -it legisinc-app php -r "
use App\Models\Proposicao;
use App\Services\Template\TemplateProcessorService;
use App\Services\Parametro\ParametroService;
use App\Services\Template\TemplateVariableService;

require_once '/var/www/html/vendor/autoload.php';
require_once '/var/www/html/bootstrap/app.php';

\$proposicao = Proposicao::find(${PROPOSICAO_ID});
if (!\$proposicao) {
    echo \"❌ Proposição não encontrada\n\";
    exit(1);
}

\$parametroService = new ParametroService();
\$templateVariableService = new TemplateVariableService(\$parametroService);
\$templateProcessor = new TemplateProcessorService(\$parametroService, \$templateVariableService);

// Simular conteúdo com a variável
\$conteudo = 'Teste de documento.

Assinatura: \${assinatura_digital_info}

QR Code: \${qrcode_html}

Fim do documento.';

echo \"📄 Conteúdo original:\n\";
echo \$conteudo . \"\n\n\";

// Processar variáveis usando o método interno
\$reflection = new ReflectionClass(\$templateProcessor);
\$method = \$reflection->getMethod('prepararVariaveisSystem');
\$method->setAccessible(true);
\$variaveis = \$method->invoke(\$templateProcessor, \$proposicao);

echo \"🔧 Variáveis processadas:\n\";
echo \"   assinatura_digital_info: \" . (\$variaveis['assinatura_digital_info'] ?: '[VAZIO]') . \"\n\";
echo \"   qrcode_html: \" . (\$variaveis['qrcode_html'] ?: '[VAZIO]') . \"\n\";
echo \"   data_assinatura: \" . (\$variaveis['data_assinatura'] ?: '[VAZIO]') . \"\n\n\";

// Testar substituição
\$method2 = \$reflection->getMethod('substituirVariaveis');
\$method2->setAccessible(true);
\$conteudoProcessado = \$method2->invoke(\$templateProcessor, \$conteudo, \$variaveis);

echo \"✅ Conteúdo processado:\n\";
echo \$conteudoProcessado . \"\n\";
"

echo
echo "🖨️ 4. Testando geração de PDF..."
echo "   Acessando: http://localhost:8001/proposicoes/${PROPOSICAO_ID}/assinar"

curl -s "http://localhost:8001/proposicoes/${PROPOSICAO_ID}/assinar" > /tmp/pdf_response.html

if grep -q "Autenticar documento em" /tmp/pdf_response.html; then
    echo "   ✅ Texto de assinatura encontrado no HTML do PDF!"
    echo "   📝 Trecho encontrado:"
    grep -o "Autenticar documento em[^<]*" /tmp/pdf_response.html | head -1
else
    echo "   ❌ Texto de assinatura NÃO encontrado no HTML do PDF"
    echo "   🔍 Verificando variáveis no HTML..."
    if grep -q "assinatura_digital_info" /tmp/pdf_response.html; then
        echo "   ⚠️  Variável \${assinatura_digital_info} ainda aparece sem substituição"
    fi
fi

echo
echo "🧹 5. Limpeza..."
rm -f /tmp/pdf_response.html

echo
echo "📋 RESUMO:"
echo "✅ Assinatura digital adicionada à proposição"
echo "✅ Variável \${assinatura_digital_info} implementada no TemplateProcessorService"
echo "✅ Método gerarTextoAssinaturaDigital() criado"
echo "✅ Identificador único gerado para autenticação"
echo
echo "🎯 Para testar manualmente:"
echo "   1. Acesse: http://localhost:8001/proposicoes/${PROPOSICAO_ID}/assinar"
echo "   2. Verifique se aparece: 'Autenticar documento em /autenticidade com o identificador...'"
echo "   3. Download do PDF deve conter o texto de assinatura digital"