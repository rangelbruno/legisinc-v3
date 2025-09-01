<?php

require_once __DIR__.'/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\OnlyOffice\OnlyOfficeService;

echo "=== TESTE COMPLETO DE PROCESSAMENTO RTF ===\n\n";

// Simular documento RTF salvo pelo OnlyOffice com caracteres Unicode
$rtfOnlyOffice = '{\rtf1\ansi\ansicpg1252\deff0\nouicompat\deflang1046{\fonttbl{\f0\fnil\fcharset0 Calibri;}{\f1\fnil\fcharset0 Times New Roman;}}
{\*\generator Riched20 10.0.19041}\viewkind4\uc1 
\pard\sa200\sl276\slmult1\f0\fs22\lang22 C\u194*MARA MUNICIPAL DE CARAGUATATUBA\par
Pra\u231*a da Rep\u250*blica, 40, Centro\par
(12) 3882-5588\par
www.camaracaraguatatuba.sp.gov.br\par
\par
\b MO\u199*\u195*O N\u186* 001/2025\b0\par
\par
\b EMENTA:\b0  Mo\u231*\u227*o de congratula\u231*\u245*es aos profissionais da educa\u231*\u227*o municipal pelos excelentes resultados alcan\u231*ados no ano letivo.\par
\par
A C\u226*mara Municipal de Caraguatatuba, por meio de seus representantes legais, manifesta:\par
\par
\b Considerando\b0  a import\u226*ncia da educa\u231*\u227*o para o desenvolvimento municipal e a forma\u231*\u227*o de cidad\u227*os conscientes;\par
\par
\b Considerando\b0  os esfor\u231*os incans\u225*veis dos profissionais de educa\u231*\u227*o durante todo o ano letivo;\par
\par
\b Considerando\b0  os resultados positivos alcan\u231*ados pelos alunos da rede municipal em avalia\u231*\u245*es estaduais e nacionais;\par
\par
\b Resolve\b0  dirigir a presente Mo\u231*\u227*o de congratula\u231*\u245*es aos profissionais da educa\u231*\u227*o municipal, reconhecendo seu trabalho dedicado e comprometido com a excel\u234*ncia educacional.\par
\par
Plen\u225*rio da C\u226*mara Municipal de Caraguatatuba, 31 de agosto de 2025.\par
\par
__________________________________\par
\b Jessica Silva\b0\par
Vereadora\par
\par
__________________________________\par
\b Jo\u227*o Santos\b0\par
Vereador\f1\par
}';

// Criar instância do serviço
$service = app(OnlyOfficeService::class);

// Usar reflexão para acessar o método privado
$reflection = new ReflectionClass($service);
$method = $reflection->getMethod('extrairConteudoRTF');
$method->setAccessible(true);

echo "1. TESTANDO EXTRAÇÃO DE CONTEÚDO RTF\n";
echo str_repeat('-', 50) . "\n";

$conteudoExtraido = $method->invoke($service, $rtfOnlyOffice);

echo "✅ Conteúdo extraído com sucesso!\n";
echo "Tamanho: " . strlen($conteudoExtraido) . " caracteres\n";
echo "Linhas: " . count(explode("\n", $conteudoExtraido)) . "\n\n";

echo "2. VERIFICAÇÃO DE CARACTERES ESPECIAIS\n";
echo str_repeat('-', 50) . "\n";

$verificacoes = [
    'CÂMARA' => 'CÂMARA em maiúsculas',
    'Praça' => 'Praça com cedilha',
    'República' => 'República com acento',
    'MOÇÃO' => 'MOÇÃO em maiúsculas',
    'Nº' => 'Número ordinal',
    'Moção' => 'Moção com cedilha',
    'congratulações' => 'congratulações com cedilha e til',
    'educação' => 'educação com cedilha e til',
    'importância' => 'importância com circunflexo',
    'esforços' => 'esforços com cedilha',
    'incansáveis' => 'incansáveis com acento',
    'avaliações' => 'avaliações com cedilha e til',
    'excelência' => 'excelência com circunflexo',
    'Plenário' => 'Plenário com acento',
    'Câmara' => 'Câmara com circunflexo',
    'João' => 'João com til'
];

$todosOk = true;
foreach ($verificacoes as $buscar => $descricao) {
    $encontrado = str_contains($conteudoExtraido, $buscar);
    echo sprintf("%-25s: %s\n", $descricao, $encontrado ? '✅ OK' : '❌ FALTANDO');
    if (!$encontrado) {
        $todosOk = false;
    }
}

echo "\n3. PREVIEW DO CONTEÚDO EXTRAÍDO\n";
echo str_repeat('-', 50) . "\n";
echo substr($conteudoExtraido, 0, 500) . "...\n\n";

echo "4. RESULTADO FINAL\n";
echo str_repeat('-', 50) . "\n";
if ($todosOk) {
    echo "✅ SUCESSO: Todos os caracteres especiais foram extraídos corretamente!\n";
    echo "A solução está funcionando perfeitamente para processar RTF com caracteres Unicode.\n";
} else {
    echo "⚠️ ATENÇÃO: Alguns caracteres especiais não foram encontrados.\n";
    echo "Verifique o processamento de caracteres Unicode.\n";
}

echo "\n=== FIM DO TESTE ===\n";