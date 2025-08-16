<?php

require '/var/www/html/vendor/autoload.php';

$app = require_once '/var/www/html/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

use App\Models\Proposicao;
use App\Models\User;
use App\Http\Controllers\ProposicaoAssinaturaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

echo "🌐 TESTE: Página de Assinatura Web\n";
echo "==================================\n\n";

// Buscar a proposição 1
$proposicao = Proposicao::find(1);

if (!$proposicao) {
    echo "❌ Proposição 1 não encontrada!\n";
    exit;
}

echo "📋 Proposição encontrada:\n";
echo "   ID: {$proposicao->id}\n";
echo "   Status: {$proposicao->status}\n";
echo "   Arquivo: {$proposicao->arquivo_path}\n\n";

// Simular login do usuário autor (parlamentar)
$autor = User::find($proposicao->autor_id);
if ($autor) {
    Auth::login($autor);
    echo "👤 Logado como: {$autor->name} (ID: {$autor->id})\n\n";
}

// Simular requisição HTTP
$request = new Request();
$request->setMethod('GET');

try {
    $controller = new ProposicaoAssinaturaController();
    
    echo "🔧 Testando método assinar()...\n";
    $response = $controller->assinar($proposicao);
    
    if ($response instanceof \Illuminate\View\View) {
        echo "✅ View de assinatura carregada com sucesso!\n";
        
        // Verificar se PDF foi regenerado
        $proposicao->refresh();
        if ($proposicao->arquivo_pdf_path) {
            $pdfPath = storage_path('app/' . $proposicao->arquivo_pdf_path);
            echo "📄 PDF disponível: {$pdfPath}\n";
            echo "📊 Tamanho: " . (file_exists($pdfPath) ? filesize($pdfPath) . " bytes" : "Arquivo não encontrado") . "\n";
            
            // Verificar logs mais recentes
            echo "\n📝 Logs mais recentes:\n";
            $logs = shell_exec('tail -3 /var/log/php/php_errors.log | grep "PDF Assinatura"');
            if ($logs) {
                echo $logs;
            } else {
                echo "   Nenhum log novo encontrado\n";
            }
        }
        
    } else {
        echo "❌ Resposta não é uma View: " . get_class($response) . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
    echo "📍 Arquivo: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "\n✅ Teste concluído!\n";