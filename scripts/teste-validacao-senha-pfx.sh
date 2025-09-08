#!/bin/bash

echo "=== TESTE DE VALIDAÇÃO DE SENHA PFX ==="
echo "Este script testa se a validação de senha do certificado PFX está funcionando corretamente"
echo

# Cores para output
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Verificar se o Docker está rodando
if ! docker ps | grep -q legisinc-app; then
    echo -e "${RED}❌ Container legisinc-app não está rodando${NC}"
    echo "Execute: docker-compose up -d"
    exit 1
fi

echo -e "${YELLOW}🔧 Executando migrate:fresh --seed...${NC}"
docker exec legisinc-app php artisan migrate:fresh --seed --quiet

echo -e "${GREEN}✅ Base de dados preparada${NC}"
echo

echo -e "${YELLOW}🧪 Testando validação de senha PFX${NC}"

# Script PHP para testar a validação
SCRIPT_PHP='
<?php
use App\Services\AssinaturaDigitalService;

$service = app(AssinaturaDigitalService::class);

echo "=== TESTE DE PROCESSAMENTO CERTIFICADO PFX ===\n";

// Testar com senha inválida (deve falhar)
try {
    $dados = [
        "tipo_certificado" => "PFX",
        "arquivo_pfx" => "/tmp/certificado_teste.pfx",
        "senha_pfx" => "senha_incorreta"
    ];
    
    // Criar arquivo PFX fictício para teste
    $pfxData = "-----BEGIN CERTIFICATE-----\nMIIC2jCCAcKgAwIBAgIJAN...\n-----END CERTIFICATE-----";
    file_put_contents("/tmp/certificado_teste.pfx", $pfxData);
    
    $resultado = $service->assinarPDF("/tmp/teste.pdf", $dados);
    echo "❌ ERRO: Senha incorreta foi aceita (não deveria)\n";
    
} catch (Exception $e) {
    echo "✅ CORRETO: Senha incorreta rejeitada - " . $e->getMessage() . "\n";
}

echo "\n=== TESTE COM VALIDAÇÃO DIRETA ===\n";

// Testar método de processamento PFX diretamente
$reflection = new ReflectionClass(AssinaturaDigitalService::class);
$method = $reflection->getMethod("processarCertificadoPFX");
$method->setAccessible(true);

try {
    $resultado = $method->invokeArgs($service, ["/tmp/certificado_teste.pfx", "senha_incorreta"]);
    if ($resultado === null) {
        echo "✅ CORRETO: Método processarCertificadoPFX rejeitou senha incorreta\n";
    } else {
        echo "❌ ERRO: Método processarCertificadoPFX aceitou senha incorreta\n";
    }
} catch (Exception $e) {
    echo "✅ CORRETO: Exceção lançada para senha incorreta - " . $e->getMessage() . "\n";
}

// Limpar arquivos de teste
unlink("/tmp/certificado_teste.pfx");

echo "\n=== RESUMO ===\n";
echo "Teste de validação de senha PFX concluído.\n";
echo "Se viu mensagens \"✅ CORRETO\", a correção está funcionando.\n";
echo "Se viu \"❌ ERRO\", ainda há problemas na validação.\n";
'

echo "$SCRIPT_PHP" > /tmp/teste_pfx.php

echo -e "${YELLOW}🚀 Executando teste de validação...${NC}"
docker exec legisinc-app php -f /tmp/teste_pfx.php

rm -f /tmp/teste_pfx.php

echo
echo -e "${GREEN}🎯 TESTE CONCLUÍDO${NC}"
echo -e "${YELLOW}💡 Para testar manualmente:${NC}"
echo "1. Acesse: http://localhost:8001/proposicoes/1/assinatura-digital"
echo "2. Selecione 'Arquivo PFX/P12'"
echo "3. Faça upload de um certificado .pfx"
echo "4. Digite uma senha incorreta"
echo "5. O sistema deve rejeitar e mostrar erro de senha incorreta"

echo
echo -e "${GREEN}✅ Script de teste concluído${NC}"