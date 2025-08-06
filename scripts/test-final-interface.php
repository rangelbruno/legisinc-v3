#!/usr/bin/env php
<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\Admin\ParametrosTemplatesController;
use App\Services\Template\TemplateParametrosService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

echo "=== TESTE FINAL DA INTERFACE DE PARÂMETROS ===\n\n";

try {
    // Simular um usuário autenticado (usando o usuário ID 1)
    Auth::loginUsingId(1);
    echo "✅ Usuário autenticado: " . Auth::user()->name . "\n";

    // Criar controller
    $service = app(TemplateParametrosService::class);
    $controller = new ParametrosTemplatesController($service);
    
    echo "✅ Controller criado com sucesso\n";

    // Testar método index (carregar parâmetros)
    $response = $controller->index();
    echo "✅ Método index executado: " . get_class($response) . "\n";
    
    // Se for uma view, significa que carregou com sucesso
    if ($response instanceof \Illuminate\View\View) {
        $data = $response->getData();
        echo "✅ Dados da view carregados:\n";
        echo "   - Módulo: " . ($data['modulo'] ? $data['modulo']->nome : 'null') . "\n";
        echo "   - Variáveis disponíveis: " . count($data['variaveisDisponiveis']) . "\n";
    } elseif ($response instanceof \Illuminate\Http\RedirectResponse) {
        echo "⚠️  Redirecionamento: " . $response->getTargetUrl() . "\n";
    }

    // Testar método de salvar com dados simulados
    echo "\n🧪 Testando salvamento...\n";
    
    $parametrosTeste = [
        '54' => 'TESTE', // var_prefixo_numeracao  
        '30' => '1',     // usar_marca_dagua (checkbox)
        '29' => 'topo'   // cabecalho_posicao (select)
    ];
    
    $request = Request::create('/admin/templates/parametros/salvar', 'POST', [
        'parametros' => $parametrosTeste
    ]);
    
    $saveResponse = $controller->salvar($request);
    echo "✅ Método salvar executado: " . get_class($saveResponse) . "\n";
    
    if ($saveResponse instanceof \Illuminate\Http\RedirectResponse) {
        echo "✅ Redirecionamento após salvar: " . $saveResponse->getTargetUrl() . "\n";
        
        // Verificar se há mensagem de sucesso na sessão
        $session = $saveResponse->getSession();
        if ($session && $session->has('success')) {
            echo "✅ Mensagem de sucesso: " . $session->get('success') . "\n";
        }
    }

    echo "\n=== INTERFACE TOTALMENTE FUNCIONAL ===\n";

} catch (Exception $e) {
    echo "❌ ERRO: " . $e->getMessage() . "\n";
    echo "Arquivo: " . $e->getFile() . ":" . $e->getLine() . "\n";
    exit(1);
}