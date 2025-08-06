#!/usr/bin/env php
<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\Template\TemplateParametrosService;
use App\Models\Proposicao;
use App\Models\User;

echo "\n=== TESTE DO SISTEMA DE PARÂMETROS DE TEMPLATES ===\n\n";

// Instanciar o serviço
$service = app(TemplateParametrosService::class);

// 1. Obter parâmetros disponíveis
echo "1. Parâmetros do módulo Templates:\n";
$parametros = $service->obterParametrosTemplates();
foreach ($parametros as $chave => $valor) {
    echo "   - $chave: " . (is_string($valor) ? substr($valor, 0, 50) : json_encode($valor)) . "\n";
}

// 2. Testar variáveis disponíveis
echo "\n2. Variáveis disponíveis para substituição:\n";
$variaveis = $service->obterVariaveisDisponiveis();
$contador = 0;
foreach ($variaveis as $var => $desc) {
    echo "   - $var: $desc\n";
    $contador++;
    if ($contador >= 10) {
        echo "   ... e mais " . (count($variaveis) - 10) . " variáveis\n";
        break;
    }
}

// 3. Criar dados de teste
echo "\n3. Criando dados de teste...\n";

// Simular uma proposição
$proposicao = new Proposicao();
$proposicao->id = 999;
$proposicao->numero = 'TEST-001/2025';
$proposicao->tipo = 'Projeto de Lei';
$proposicao->ementa = 'Dispõe sobre o teste do sistema de parâmetros de templates';
$proposicao->conteudo = 'Este é o conteúdo de teste da proposição.';
$proposicao->ano = 2025;
$proposicao->created_at = now();

// Simular um autor
$autor = new User();
$autor->name = 'João da Silva';
$autor->cargo = 'Vereador';
$autor->partido = 'PSDB';

// 4. Testar substituição de variáveis
echo "\n4. Testando substituição de variáveis:\n\n";

$templateTeste = '
${nome_camara}
${endereco_camara}
${telefone_camara} | ${website_camara}

PROPOSIÇÃO Nº ${numero_proposicao}

Tipo: ${tipo_proposicao}
Ementa: ${ementa}
Autor: ${autor_nome} - ${autor_cargo}/${autor_partido}
Data: ${data_atual}
Mês: ${mes_extenso}

${texto}

${assinatura_padrao}

${rodape}
';

echo "Template Original:\n";
echo "================\n";
echo $templateTeste;

// Processar template
$textoProcessado = $service->processarTemplate($templateTeste, [
    'proposicao' => $proposicao,
    'autor' => $autor,
    'variaveis' => [
        'campo_customizado' => 'Valor customizado de teste'
    ]
]);

echo "\n\nTexto Processado:\n";
echo "================\n";
echo $textoProcessado;

// 5. Limpar cache
echo "\n\n5. Limpando cache de parâmetros...\n";
$service->limparCache();
echo "   Cache limpo com sucesso!\n";

echo "\n=== TESTE CONCLUÍDO COM SUCESSO ===\n\n";