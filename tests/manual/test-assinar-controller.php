<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Http\Kernel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Proposicao;
use App\Http\Controllers\ProposicaoAssinaturaController;

// Inicializar Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Kernel::class);

// Simular requisição
$request = Request::create('/proposicoes/1/assinar', 'GET');
$response = $kernel->handle($request);

// Configurar autenticação
$user = User::where('email', 'jessica@sistema.gov.br')->first();
Auth::login($user);

echo "=== TESTE DE CONTROLLER ASSINAR ===" . PHP_EOL;
echo "Usuário autenticado: " . Auth::user()->name . PHP_EOL;
echo "Role: " . Auth::user()->getRoleNames()->first() . PHP_EOL;

// Buscar proposição
$proposicao = Proposicao::find(1);
echo "Proposição ID: " . $proposicao->id . ", Status: " . $proposicao->status . PHP_EOL;

// Testar método diretamente
try {
    $controller = new ProposicaoAssinaturaController();
    
    echo "Tentando executar método assinar..." . PHP_EOL;
    
    // Verificar condições do controller antes de executar
    if (!in_array($proposicao->status, ['aprovado_assinatura', 'retornado_legislativo'])) {
        echo "❌ Status não permite assinatura: " . $proposicao->status . PHP_EOL;
    } else {
        echo "✅ Status permite assinatura: " . $proposicao->status . PHP_EOL;
        
        // Tentar executar o método
        $result = $controller->assinar($proposicao);
        
        if ($result instanceof \Illuminate\Http\Response) {
            echo "✅ Controller executou com sucesso!" . PHP_EOL;
            echo "Tipo de resposta: " . get_class($result) . PHP_EOL;
        } elseif ($result instanceof \Illuminate\View\View) {
            echo "✅ Controller retornou view: " . $result->getName() . PHP_EOL;
        } else {
            echo "⚠️ Controller retornou: " . get_class($result) . PHP_EOL;
        }
    }
    
} catch (Exception $e) {
    echo "❌ Erro ao executar controller: " . $e->getMessage() . PHP_EOL;
    echo "Arquivo: " . $e->getFile() . ":" . $e->getLine() . PHP_EOL;
}

$kernel->terminate($request, $response);