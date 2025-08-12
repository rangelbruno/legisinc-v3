<?php

use App\Http\Controllers\DadosGeraisCamaraController;
use Illuminate\Http\Request;

echo "🧪 Testando salvamento direto via controller...\n";

// Simular uma request
$request = new Request();
$request->merge([
    'nome_camara' => 'TESTE DIRETO CONTROLLER',
    'sigla_camara' => 'TDC', 
    'cnpj' => '99.888.777/0001-88',
    'save_tab' => 'identificacao'
]);

$request->setMethod('POST');

// Simular token CSRF
$request->headers->set('X-Requested-With', 'XMLHttpRequest');

try {
    $controller = app(DadosGeraisCamaraController::class);
    $response = $controller->store($request);
    
    echo "📡 Status da resposta: " . $response->getStatusCode() . "\n";
    echo "📨 Conteúdo da resposta: " . $response->getContent() . "\n";
    
    // Verificar se foi salvo no banco
    $valorSalvo = DB::table('parametros_valores')
        ->join('parametros_campos', 'parametros_valores.campo_id', '=', 'parametros_campos.id')
        ->where('parametros_campos.nome', 'nome_camara')
        ->whereNull('parametros_valores.valido_ate')
        ->orderBy('parametros_valores.created_at', 'desc')
        ->value('parametros_valores.valor');
        
    echo "🗄️ Valor no banco: " . $valorSalvo . "\n";
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
    echo "📚 Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "✅ Teste concluído!\n";