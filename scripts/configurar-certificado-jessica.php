<?php

// Script para configurar certificado digital da Jessica
// Uso: docker exec legisinc-app php /var/www/html/scripts/configurar-certificado-jessica.php

require_once '/var/www/html/vendor/autoload.php';

$app = require_once '/var/www/html/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

try {
    echo "🔐 Configurando certificado digital para Jessica Santos\n";
    echo "=" . str_repeat("=", 50) . "\n\n";
    
    // Buscar usuário Jessica
    $jessica = User::where('email', 'jessica@sistema.gov.br')->first();
    
    if (!$jessica) {
        throw new Exception("Usuário Jessica não encontrado!");
    }
    
    echo "✓ Usuário encontrado: {$jessica->name} (ID: {$jessica->id})\n";
    
    // Configurações do certificado
    $certificadoOrigem = '/tmp/certificado_teste.pfx';
    $senha = '123Ligado';
    
    // Verificar se o arquivo existe
    if (!file_exists($certificadoOrigem)) {
        throw new Exception("Arquivo de certificado não encontrado em: {$certificadoOrigem}");
    }
    
    echo "✓ Arquivo de certificado encontrado\n";
    
    // Criar diretórios necessários
    $dirPrivate = storage_path('app/private/certificados-digitais');
    if (!is_dir($dirPrivate)) {
        mkdir($dirPrivate, 0755, true);
        echo "✓ Diretório criado: {$dirPrivate}\n";
    }
    
    // Validar certificado
    echo "\n📋 Validando certificado...\n";
    
    $tempFile = tempnam(sys_get_temp_dir(), 'cert_');
    file_put_contents($tempFile, file_get_contents($certificadoOrigem));
    
    $command = sprintf(
        'openssl pkcs12 -legacy -in %s -passin pass:%s -noout 2>&1',
        escapeshellarg($tempFile),
        escapeshellarg($senha)
    );
    
    exec($command, $output, $returnCode);
    
    if ($returnCode !== 0) {
        unlink($tempFile);
        throw new Exception("Certificado inválido ou senha incorreta: " . implode("\n", $output));
    }
    
    echo "✓ Certificado válido!\n";
    
    // Extrair informações do certificado
    $commandInfo = sprintf(
        'openssl pkcs12 -legacy -in %s -passin pass:%s -nokeys -clcerts 2>/dev/null | openssl x509 -noout -subject -enddate 2>&1',
        escapeshellarg($tempFile),
        escapeshellarg($senha)
    );
    
    exec($commandInfo, $infoOutput);
    
    $cn = null;
    $validade = null;
    
    foreach ($infoOutput as $line) {
        if (strpos($line, 'subject=') !== false) {
            if (preg_match('/CN\s*=\s*([^,\/]+)/i', $line, $matches)) {
                $cn = trim($matches[1]);
            }
        }
        if (strpos($line, 'notAfter=') !== false) {
            $dateStr = str_replace('notAfter=', '', $line);
            $validade = date('Y-m-d H:i:s', strtotime($dateStr));
        }
    }
    
    echo "✓ CN do certificado: {$cn}\n";
    echo "✓ Válido até: {$validade}\n";
    
    unlink($tempFile);
    
    // Remover certificado anterior se existir
    if ($jessica->certificado_digital_path && Storage::exists('private/' . $jessica->certificado_digital_path)) {
        Storage::delete('private/' . $jessica->certificado_digital_path);
        echo "✓ Certificado anterior removido\n";
    }
    
    // Copiar certificado para local definitivo
    $nomeArquivo = 'certificado_' . $jessica->id . '_' . time() . '.pfx';
    $caminhoRelativo = 'certificados-digitais/' . $nomeArquivo;
    $caminhoCompleto = $dirPrivate . '/' . $nomeArquivo;
    
    if (!copy($certificadoOrigem, $caminhoCompleto)) {
        throw new Exception("Erro ao copiar certificado para: {$caminhoCompleto}");
    }
    
    // Ajustar permissões
    chmod($caminhoCompleto, 0600);
    chown($caminhoCompleto, 'www-data');
    chgrp($caminhoCompleto, 'www-data');
    
    echo "✓ Certificado salvo em: {$caminhoRelativo}\n";
    
    // Atualizar dados no banco
    $jessica->certificado_digital_path = $caminhoRelativo;
    $jessica->certificado_digital_nome = 'BRUNO JOSE PEREIRA RANGEL_31748726854.pfx';
    $jessica->certificado_digital_upload_em = now();
    $jessica->certificado_digital_validade = $validade;
    $jessica->certificado_digital_cn = $cn;
    $jessica->certificado_digital_ativo = true;
    
    // Salvar senha criptografada
    $jessica->certificado_digital_senha = encrypt($senha);
    $jessica->certificado_digital_senha_salva = true;
    
    $jessica->save();
    
    echo "\n✅ Certificado configurado com sucesso!\n";
    echo "=" . str_repeat("=", 50) . "\n";
    echo "📊 Resumo da configuração:\n";
    echo "   Usuário: {$jessica->name}\n";
    echo "   Email: {$jessica->email}\n";
    echo "   CN: {$cn}\n";
    echo "   Válido até: {$validade}\n";
    echo "   Arquivo: {$caminhoRelativo}\n";
    echo "   Senha salva: Sim (criptografada)\n";
    echo "=" . str_repeat("=", 50) . "\n";
    
    // Log da operação
    Log::info('Certificado digital configurado via script', [
        'user_id' => $jessica->id,
        'email' => $jessica->email,
        'cn' => $cn,
        'validade' => $validade,
        'arquivo' => $caminhoRelativo
    ]);
    
} catch (Exception $e) {
    echo "\n❌ ERRO: " . $e->getMessage() . "\n";
    exit(1);
}