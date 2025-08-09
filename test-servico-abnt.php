<?php

// Script de teste para serviço ABNT
require_once __DIR__ . '/vendor/autoload.php';

use App\Services\Template\TemplatePadraoABNTService;
use App\Services\Template\ABNTValidationService;
use App\Services\Parametro\ParametroService;
use App\Services\TemplateVariablesService;

// Criar container de dependências mock
class MockParametroService
{
    public function obterValor($modulo, $submodulo, $campo)
    {
        $valores = [
            'Dados Gerais|Informações da Câmara|nome_camara' => 'CÂMARA MUNICIPAL DE SÃO PAULO',
            'Dados Gerais|Informações da Câmara|municipio' => 'São Paulo',
            'Dados Gerais|Informações da Câmara|endereco_camara' => 'Viaduto Jacareí, 100 - Bela Vista - São Paulo/SP',
            'Dados Gerais|Legislatura|legislatura_atual' => '2021-2024',
            'Dados Gerais|Legislatura|sessao_legislativa' => '2025',
            'Templates|Cabeçalho|cabecalho_imagem' => ''
        ];
        
        $key = "$modulo|$submodulo|$campo";
        return $valores[$key] ?? null;
    }
}

echo "Iniciando teste do serviço ABNT...\n";

// Dados da proposição de teste
$dadosProposicao = [
    'tipo' => 'mocao',
    'ementa' => 'Moção de aplausos para Bruno José Pereira Rangel',
    'conteudo' => '**MUNICÍPIO DE [Nome do Município]**

**PODER LEGISLATIVO**

**CÂMARA MUNICIPAL**

**MOÇÃO Nº [Número da Moção], DE [Data]**

**MOÇÃO DE APLAUSOS**

O(A) Vereador(a) [Nome do Vereador(a) proponente], membro desta Egrégia Câmara Municipal, vem, respeitosamente, à presença dos Nobres Pares, apresentar a seguinte MOÇÃO DE APLAUSOS:

**Considerando** a importância da valorização dos cidadãos que se destacam por suas contribuições à comunidade;

**Considerando** os relevantes serviços prestados por Bruno José Pereira Rangel ao município de [Nome do Município];

**Considerando** [inserir aqui os feitos de Bruno José Pereira Rangel que justificam a moção, sendo detalhado e específico];

**Resolve esta Egrégia Câmara Municipal:**

**Art. 1º** Aplaudir e homenagear o cidadão Bruno José Pereira Rangel pelos relevantes serviços prestados ao município de [Nome do Município], conforme descrito nos considerandos desta Moção.

**Art. 2º** Determinar que cópia desta Moção seja encaminhada a Bruno José Pereira Rangel.

**Art. 3º** Esta Moção entra em vigor na data de sua aprovação.',
    'numero' => '0010',
    'status' => 'em_edicao',
    'created_at' => new DateTime(),
    'autor_nome' => 'Jessica Santos',
    'nome_parlamentar' => 'Jessica Santos',
    'cargo_parlamentar' => 'Vereador(a)',
    'email_parlamentar' => 'jessica@sistema.gov.br',
    'partido_parlamentar' => ''
];

try {
    // Instanciar serviços
    $parametroService = new MockParametroService();
    $templateVariablesService = new TemplateVariablesService();
    $abntValidationService = new ABNTValidationService();
    $templatePadraoService = new TemplatePadraoABNTService(
        $templateVariablesService,
        $parametroService,
        $abntValidationService
    );

    echo "Serviços instanciados com sucesso.\n";

    // Gerar documento
    echo "Gerando documento ABNT...\n";
    $resultado = $templatePadraoService->gerarDocumento($dadosProposicao);

    if ($resultado['success']) {
        echo "✅ Documento ABNT gerado com sucesso!\n";
        echo "Score ABNT: " . ($resultado['validacao_abnt']['score_geral']['percentual'] ?? 'N/A') . "%\n";
        echo "Status: " . ($resultado['validacao_abnt']['score_geral']['status'] ?? 'N/A') . "\n";
        
        // Salvar resultado
        $resultadoPath = __DIR__ . '/test-servico-abnt-resultado.html';
        file_put_contents($resultadoPath, $resultado['documento_html']);
        echo "Documento salvo em: $resultadoPath\n";
        
        // Mostrar preview do conteúdo
        echo "\nPreview do conteúdo gerado:\n";
        echo str_repeat('-', 50) . "\n";
        
        // Extrair texto limpo para preview
        $textoLimpo = strip_tags($resultado['documento_html']);
        $preview = substr($textoLimpo, 0, 500);
        echo $preview . "\n";
        
        if (strlen($textoLimpo) > 500) {
            echo "[... continua ...]\n";
        }
        
        echo str_repeat('-', 50) . "\n";
        
    } else {
        echo "❌ Erro ao gerar documento ABNT: " . $resultado['message'] . "\n";
    }
    
    echo "\nTeste do serviço ABNT concluído!\n";
    
} catch (Exception $e) {
    echo "❌ Erro no teste: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}