<?php

// Teste de upload de certificado via API interna
require_once '/var/www/html/vendor/autoload.php';

$app = require_once '/var/www/html/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Parlamentar;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;

try {
    echo "🔍 Testando sistema de upload de certificados\n";
    echo "=" . str_repeat("=", 50) . "\n\n";
    
    // Buscar parlamentar Jessica
    $parlamentar = Parlamentar::whereHas('user', function($q) {
        $q->where('email', 'jessica@sistema.gov.br');
    })->first();
    
    if (!$parlamentar) {
        throw new Exception("Parlamentar Jessica não encontrado!");
    }
    
    echo "✓ Parlamentar encontrado: {$parlamentar->nome} (ID: {$parlamentar->id})\n";
    echo "✓ User ID: {$parlamentar->user_id}\n\n";
    
    // Simular autenticação como admin
    $admin = User::where('email', 'bruno@sistema.gov.br')->first();
    Auth::login($admin);
    
    echo "📤 Simulando upload de certificado...\n";
    
    // Criar arquivo temporário do certificado
    $certificadoPath = '/tmp/certificado_teste.pfx';
    if (!file_exists($certificadoPath)) {
        throw new Exception("Arquivo de certificado não encontrado!");
    }
    
    // Criar instância de UploadedFile
    $uploadedFile = new UploadedFile(
        $certificadoPath,
        'BRUNO_JOSE_PEREIRA_RANGEL.pfx',
        'application/x-pkcs12',
        null,
        true // isTest
    );
    
    // Criar request simulado
    $request = new \Illuminate\Http\Request();
    $request->files->set('certificado_digital', $uploadedFile);
    $request->merge([
        'certificado_senha' => '123Ligado',
        'salvar_senha_certificado' => '1'
    ]);
    
    // Instanciar controller
    $controller = app(\App\Http\Controllers\Parlamentar\ParlamentarController::class);
    
    // Usar reflection para acessar método privado
    $reflection = new \ReflectionClass($controller);
    $method = $reflection->getMethod('processarCertificadoDigital');
    $method->setAccessible(true);
    
    echo "🔄 Processando certificado...\n";
    
    try {
        $method->invoke($controller, $request, $parlamentar->id);
        echo "✅ Certificado processado com sucesso!\n";
    } catch (\Exception $e) {
        echo "⚠️  Erro ao processar (esperado se já configurado): " . $e->getMessage() . "\n";
    }
    
    // Verificar resultado
    $user = User::find($parlamentar->user_id);
    $user->refresh();
    
    echo "\n📊 Status do certificado:\n";
    echo "   Path: " . ($user->certificado_digital_path ?: 'Não configurado') . "\n";
    echo "   Nome: " . ($user->certificado_digital_nome ?: 'N/A') . "\n";
    echo "   CN: " . ($user->certificado_digital_cn ?: 'N/A') . "\n";
    echo "   Validade: " . ($user->certificado_digital_validade ?: 'N/A') . "\n";
    echo "   Ativo: " . ($user->certificado_digital_ativo ? 'Sim' : 'Não') . "\n";
    echo "   Senha salva: " . ($user->certificado_digital_senha_salva ? 'Sim' : 'Não') . "\n";
    
    if ($user->certificado_digital_path) {
        $fullPath = storage_path('app/private/' . $user->certificado_digital_path);
        echo "   Arquivo existe: " . (file_exists($fullPath) ? 'Sim' : 'Não') . "\n";
        if (file_exists($fullPath)) {
            echo "   Tamanho: " . filesize($fullPath) . " bytes\n";
            echo "   Permissões: " . substr(sprintf('%o', fileperms($fullPath)), -4) . "\n";
        }
    }
    
    echo "\n✅ Teste concluído!\n";
    
} catch (Exception $e) {
    echo "\n❌ ERRO: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}