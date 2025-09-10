<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Illuminate\Foundation\Application;

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🧪 TESTE DE EXCLUSÃO DE ARQUIVOS - PERMISSÕES\n";
echo "=============================================\n\n";

// Arquivo de teste que vamos tentar excluir
$arquivoTeste = '/var/www/html/storage/app/private/proposicoes/proposicao_1_1757079284.rtf';

echo "📁 Arquivo para teste: $arquivoTeste\n";

// Verificar se arquivo existe
if (file_exists($arquivoTeste)) {
    echo "✅ Arquivo existe\n";
    
    // Verificar permissões
    $perms = fileperms($arquivoTeste);
    echo "🔒 Permissões atuais: " . decoct($perms & 0777) . "\n";
    
    // Verificar proprietário
    $owner = posix_getpwuid(fileowner($arquivoTeste));
    echo "👤 Proprietário: " . $owner['name'] . "\n";
    
    // Verificar se é gravável
    if (is_writable($arquivoTeste)) {
        echo "✅ Arquivo é gravável\n";
    } else {
        echo "❌ Arquivo NÃO é gravável\n";
    }
    
    // Verificar se diretório pai é gravável
    $diretorioPai = dirname($arquivoTeste);
    if (is_writable($diretorioPai)) {
        echo "✅ Diretório pai é gravável\n";
    } else {
        echo "❌ Diretório pai NÃO é gravável\n";
    }
    
    echo "\n🗑️ Tentando excluir arquivo...\n";
    
    // Tentar excluir
    try {
        if (unlink($arquivoTeste)) {
            echo "✅ Arquivo excluído com sucesso!\n";
        } else {
            echo "❌ Falha ao excluir arquivo (unlink retornou false)\n";
        }
    } catch (Exception $e) {
        echo "❌ Erro ao excluir arquivo: " . $e->getMessage() . "\n";
    }
    
} else {
    echo "❌ Arquivo não existe\n";
}

echo "\n📊 RESUMO DO TESTE:\n";
echo "===================\n";

if (!file_exists($arquivoTeste)) {
    echo "✅ Teste bem-sucedido: Arquivo não existe mais (foi excluído)\n";
} else {
    echo "❌ Teste falhou: Arquivo ainda existe\n";
}

echo "\n✅ Teste concluído!\n";