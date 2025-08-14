#!/bin/bash

echo "=== Teste de Número de Protocolo ==="
echo ""

# Testar conexão com banco
echo "1. Verificando proposições existentes..."
docker exec -it legisinc-app php -r "
    require_once '/var/www/html/vendor/autoload.php';
    \$app = require_once '/var/www/html/bootstrap/app.php';
    \$kernel = \$app->make(Illuminate\Contracts\Http\Kernel::class);
    \$response = \$kernel->handle(
        \$request = Illuminate\Http\Request::capture()
    );
    
    use App\Models\Proposicao;
    use App\Services\Template\TemplateProcessorService;
    
    echo \"\\n=== PROPOSIÇÕES SEM NÚMERO DE PROTOCOLO ===\\n\";
    \$proposicoesSemNumero = Proposicao::whereNull('numero_protocolo')
        ->orWhere('numero_protocolo', '')
        ->limit(3)
        ->get(['id', 'tipo', 'ementa', 'numero_protocolo', 'status']);
    
    foreach (\$proposicoesSemNumero as \$prop) {
        echo \"ID: {\$prop->id} | Tipo: {\$prop->tipo} | Status: {\$prop->status}\\n\";
        echo \"  Número Protocolo: \" . (\$prop->numero_protocolo ?: 'VAZIO') . \"\\n\";
        
        // Testar o que será exibido no template
        \$templateProcessor = app(TemplateProcessorService::class);
        \$reflection = new ReflectionClass(\$templateProcessor);
        \$method = \$reflection->getMethod('gerarNumeroProposicao');
        \$method->setAccessible(true);
        \$numeroGerado = \$method->invoke(\$templateProcessor, \$prop);
        echo \"  Número no Template: {\$numeroGerado}\\n\\n\";
    }
    
    echo \"\\n=== PROPOSIÇÕES COM NÚMERO DE PROTOCOLO ===\\n\";
    \$proposicoesComNumero = Proposicao::whereNotNull('numero_protocolo')
        ->where('numero_protocolo', '!=', '')
        ->limit(3)
        ->get(['id', 'tipo', 'ementa', 'numero_protocolo', 'status']);
    
    foreach (\$proposicoesComNumero as \$prop) {
        echo \"ID: {\$prop->id} | Tipo: {\$prop->tipo} | Status: {\$prop->status}\\n\";
        echo \"  Número Protocolo: {\$prop->numero_protocolo}\\n\";
        
        // Testar o que será exibido no template
        \$templateProcessor = app(TemplateProcessorService::class);
        \$reflection = new ReflectionClass(\$templateProcessor);
        \$method = \$reflection->getMethod('gerarNumeroProposicao');
        \$method->setAccessible(true);
        \$numeroGerado = \$method->invoke(\$templateProcessor, \$prop);
        echo \"  Número no Template: {\$numeroGerado}\\n\\n\";
    }
"

echo ""
echo "2. Testando substituição de variáveis no template..."
docker exec -it legisinc-app php -r "
    require_once '/var/www/html/vendor/autoload.php';
    \$app = require_once '/var/www/html/bootstrap/app.php';
    \$kernel = \$app->make(Illuminate\Contracts\Http\Kernel::class);
    \$response = \$kernel->handle(
        \$request = Illuminate\Http\Request::capture()
    );
    
    use App\Models\Proposicao;
    use App\Services\Template\TemplateProcessorService;
    
    echo \"\\n=== TESTE DE SUBSTITUIÇÃO DE VARIÁVEIS ===\\n\";
    
    // Criar proposição teste sem número
    \$propSemNumero = new Proposicao();
    \$propSemNumero->id = 999;
    \$propSemNumero->tipo = 'Moção';
    \$propSemNumero->ementa = 'Teste sem protocolo';
    \$propSemNumero->numero_protocolo = null;
    
    // Criar proposição teste com número
    \$propComNumero = new Proposicao();
    \$propComNumero->id = 1000;
    \$propComNumero->tipo = 'Moção';
    \$propComNumero->ementa = 'Teste com protocolo';
    \$propComNumero->numero_protocolo = '0042/2025';
    
    \$templateProcessor = app(TemplateProcessorService::class);
    \$reflection = new ReflectionClass(\$templateProcessor);
    \$method = \$reflection->getMethod('gerarNumeroProposicao');
    \$method->setAccessible(true);
    
    echo \"Proposição SEM número de protocolo:\\n\";
    echo \"  ID: 999\\n\";
    echo \"  Número gerado: \" . \$method->invoke(\$templateProcessor, \$propSemNumero) . \"\\n\\n\";
    
    echo \"Proposição COM número de protocolo:\\n\";
    echo \"  ID: 1000\\n\";
    echo \"  Número protocolo: 0042/2025\\n\";
    echo \"  Número gerado: \" . \$method->invoke(\$templateProcessor, \$propComNumero) . \"\\n\\n\";
    
    echo \"✅ Se aparecer [AGUARDANDO PROTOCOLO] para proposições sem número, está funcionando corretamente!\\n\";
    echo \"✅ Se aparecer o número do protocolo para proposições com número, está funcionando corretamente!\\n\";
"

echo ""
echo "=== Teste Concluído ==="