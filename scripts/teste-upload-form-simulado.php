<?php

// Simular upload via formulário web
require_once '/var/www/html/vendor/autoload.php';

$app = require_once '/var/www/html/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Parlamentar;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

try {
    echo "🔍 Simulando upload de certificado via formulário\n";
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
    echo "✓ Autenticado como: {$admin->name}\n\n";
    
    // Copiar certificado para local temporário do PHP
    $certificadoOrigem = '/tmp/certificado_teste.pfx';
    if (!file_exists($certificadoOrigem)) {
        throw new Exception("Arquivo de certificado não encontrado!");
    }
    
    // Criar arquivo temporário no diretório de upload do PHP
    $tempPath = sys_get_temp_dir() . '/upload_' . uniqid() . '.pfx';
    copy($certificadoOrigem, $tempPath);
    
    echo "📤 Criando UploadedFile simulado...\n";
    echo "   Arquivo temporário: {$tempPath}\n";
    echo "   Tamanho: " . filesize($tempPath) . " bytes\n\n";
    
    // Criar instância de UploadedFile
    $uploadedFile = new UploadedFile(
        $tempPath,
        'BRUNO_JOSE_PEREIRA_RANGEL.pfx',
        'application/x-pkcs12',
        null,
        false // não é teste, é upload real
    );
    
    // Criar request simulado
    $request = Request::create(
        '/parlamentares/' . $parlamentar->id,
        'PUT',
        [
            'nome' => $parlamentar->nome,
            'partido' => $parlamentar->partido,
            'cargo' => $parlamentar->cargo,
            'status' => 'ativo',
            'certificado_senha' => '123Ligado',
            'salvar_senha_certificado' => '1'
        ],
        [], // cookies
        [
            'certificado_digital' => $uploadedFile
        ] // files
    );
    
    echo "🔄 Chamando método update do controller...\n";
    
    // Instanciar controller
    $controller = app(\App\Http\Controllers\Parlamentar\ParlamentarController::class);
    
    try {
        // Chamar método update
        $response = $controller->update($request, $parlamentar->id);
        
        echo "✅ Método update executado!\n";
        echo "   Response status: " . get_class($response) . "\n";
        
        if (method_exists($response, 'getStatusCode')) {
            echo "   HTTP Status: " . $response->getStatusCode() . "\n";
        }
        
    } catch (\Exception $e) {
        echo "⚠️  Erro no update: " . $e->getMessage() . "\n";
        echo "   Arquivo: " . $e->getFile() . ":" . $e->getLine() . "\n";
    }
    
    // Verificar resultado
    $user = User::find($parlamentar->user_id);
    $user->refresh();
    
    echo "\n📊 Status do certificado após update:\n";
    echo "   Path: " . ($user->certificado_digital_path ?: 'NÃO CONFIGURADO') . "\n";
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
        }
    }
    
    // Limpar arquivo temporário
    @unlink($tempPath);
    
    echo "\n✅ Teste concluído!\n";
    
} catch (Exception $e) {
    echo "\n❌ ERRO: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}