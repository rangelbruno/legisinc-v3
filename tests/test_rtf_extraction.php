<?php

require_once __DIR__.'/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\OnlyOffice\OnlyOfficeService;

// Criar uma instância do serviço
$service = app(OnlyOfficeService::class);

// Exemplo de RTF com caracteres Unicode (acentuação portuguesa)
$rtfContent = '{\rtf1\ansi\deff0 {\fonttbl{\f0 Times New Roman;}}
\f0\fs24
C\u194*MARA MUNICIPAL DE CARAGUATATUBA\par
Pra\u231*a da Rep\u250*blica, 40, Centro\par
\par
MO\u199*\u195*O N\u186* 001/2025\par
\par
EMENTA: Mo\u231*\u227*o de congratula\u231*\u245*es\par
\par
A C\u226*mara Municipal de Caraguatatuba manifesta:\par
\par
Considerando a import\u226*ncia da educa\u231*\u227*o para o desenvolvimento municipal;\par
Considerando os esfor\u231*os dos profissionais de sa\u250*de;\par
\par
Resolve dirigir a presente Mo\u231*\u227*o.\par
\par
Caraguatatuba, 31 de agosto de 2025.\par
\par
__________________________________\par
Jessica Silva\par
Vereadora\par
}';

// Testar a extração usando reflexão para acessar o método privado
$reflection = new ReflectionClass($service);
$method = $reflection->getMethod('extrairConteudoRTF');
$method->setAccessible(true);

echo "=== TESTE DE EXTRAÇÃO RTF ===\n\n";
echo "RTF Original (primeiros 500 caracteres):\n";
echo substr($rtfContent, 0, 500) . "...\n\n";

$resultado = $method->invoke($service, $rtfContent);

echo "Texto Extraído:\n";
echo $resultado . "\n\n";

// Verificar caracteres específicos
$caracteresEsperados = [
    'CÂMARA' => 'CÂMARA',
    'Praça' => 'Praça',
    'República' => 'República',
    'MOÇÃO' => 'MOÇÃO',
    'Nº' => 'Nº',
    'Moção' => 'Moção',
    'congratulações' => 'congratulações',
    'Câmara' => 'Câmara',
    'importância' => 'importância',
    'educação' => 'educação',
    'esforços' => 'esforços',
    'saúde' => 'saúde'
];

echo "=== VERIFICAÇÃO DE CARACTERES ESPECÍFICOS ===\n";
foreach ($caracteresEsperados as $esperado => $descricao) {
    $encontrado = str_contains($resultado, $esperado);
    echo sprintf("%-20s: %s\n", $descricao, $encontrado ? '✅ OK' : '❌ FALTANDO');
}

echo "\n=== ESTATÍSTICAS ===\n";
echo "Tamanho do RTF original: " . strlen($rtfContent) . " bytes\n";
echo "Tamanho do texto extraído: " . strlen($resultado) . " bytes\n";
echo "Linhas extraídas: " . count(explode("\n", $resultado)) . "\n";