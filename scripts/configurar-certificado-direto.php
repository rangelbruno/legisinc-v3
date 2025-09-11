<?php

// Configuração direta de certificado - solução definitiva
require_once '/var/www/html/vendor/autoload.php';

$app = require_once '/var/www/html/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Helpers\CertificadoHelper;

try {
    echo "🔐 CONFIGURAÇÃO DIRETA DE CERTIFICADO DIGITAL\n";
    echo "=" . str_repeat("=", 50) . "\n\n";
    
    // Configurar para Jessica
    $jessica = User::where('email', 'jessica@sistema.gov.br')->first();
    
    if (!$jessica) {
        throw new Exception("Usuário Jessica não encontrado!");
    }
    
    echo "📌 Usuário: {$jessica->name} (ID: {$jessica->id})\n";
    echo "📧 Email: {$jessica->email}\n\n";
    
    // Configurar certificado
    $certificadoPath = '/tmp/certificado_teste.pfx';
    $senha = '123Ligado';
    
    if (!file_exists($certificadoPath)) {
        echo "⚠️  Certificado não encontrado em /tmp. Copiando...\n";
        // Comando será executado externamente
        throw new Exception("Execute primeiro: docker cp \"BRUNO JOSE PEREIRA RANGEL_31748726854.pfx\" legisinc-app:/tmp/certificado_teste.pfx");
    }
    
    echo "📋 Configurando certificado...\n";
    
    $resultado = CertificadoHelper::configurarCertificadoPadrao($jessica, $certificadoPath, $senha);
    
    if ($resultado) {
        echo "✅ Certificado configurado com sucesso!\n\n";
        
        // Verificar status
        $status = CertificadoHelper::getStatus($jessica);
        
        echo "📊 STATUS ATUAL:\n";
        echo "   ✓ Configurado: " . ($status['configurado'] ? 'SIM' : 'NÃO') . "\n";
        echo "   ✓ Arquivo existe: " . ($status['existe'] ? 'SIM' : 'NÃO') . "\n";
        echo "   ✓ Ativo: " . ($status['ativo'] ? 'SIM' : 'NÃO') . "\n";
        echo "   ✓ Válido: " . ($status['valido'] ? 'SIM' : 'NÃO') . "\n";
        echo "   ✓ Senha salva: " . ($status['senha_salva'] ? 'SIM' : 'NÃO') . "\n";
        echo "   ✓ CN: " . ($status['cn'] ?: 'N/A') . "\n";
        echo "   ✓ Validade: " . ($status['validade'] ?: 'N/A') . "\n";
        echo "   ✓ Arquivo: " . ($status['nome_arquivo'] ?: 'N/A') . "\n";
        
        // Verificar no banco
        $jessica->refresh();
        
        echo "\n📄 DADOS NO BANCO:\n";
        echo "   • Path: " . $jessica->certificado_digital_path . "\n";
        echo "   • Nome: " . $jessica->certificado_digital_nome . "\n";
        echo "   • CN: " . $jessica->certificado_digital_cn . "\n";
        echo "   • Validade: " . $jessica->certificado_digital_validade . "\n";
        echo "   • Ativo: " . ($jessica->certificado_digital_ativo ? 'SIM' : 'NÃO') . "\n";
        echo "   • Senha Salva: " . ($jessica->certificado_digital_senha_salva ? 'SIM' : 'NÃO') . "\n";
        
        // Verificar arquivo físico
        $caminhoCompleto = CertificadoHelper::getCaminhoCompleto($jessica);
        if ($caminhoCompleto && file_exists($caminhoCompleto)) {
            echo "\n📁 ARQUIVO FÍSICO:\n";
            echo "   • Caminho: " . $caminhoCompleto . "\n";
            echo "   • Tamanho: " . filesize($caminhoCompleto) . " bytes\n";
            echo "   • Permissões: " . substr(sprintf('%o', fileperms($caminhoCompleto)), -4) . "\n";
            echo "   • Proprietário: " . posix_getpwuid(fileowner($caminhoCompleto))['name'] . "\n";
        }
        
        echo "\n" . str_repeat("=", 50) . "\n";
        echo "🎉 CERTIFICADO CONFIGURADO E FUNCIONANDO!\n";
        echo str_repeat("=", 50) . "\n";
        
    } else {
        echo "❌ Erro ao configurar certificado\n";
    }
    
} catch (Exception $e) {
    echo "\n❌ ERRO: " . $e->getMessage() . "\n";
    echo "\nPara corrigir, execute:\n";
    echo "1. docker cp \"BRUNO JOSE PEREIRA RANGEL_31748726854.pfx\" legisinc-app:/tmp/certificado_teste.pfx\n";
    echo "2. docker exec legisinc-app php /var/www/html/scripts/configurar-certificado-direto.php\n";
    exit(1);
}