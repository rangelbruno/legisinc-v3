<?php

use App\Models\Proposicao;
use App\Http\Controllers\ProposicaoAssinaturaController;

// Carregar proposição 6
$proposicao = Proposicao::find(6);
if (!$proposicao) {
    echo 'Proposição 6 não encontrada' . PHP_EOL;
    exit(1);
}

echo 'Proposição encontrada: ' . $proposicao->id . PHP_EOL;
echo 'Status: ' . $proposicao->status . PHP_EOL;
echo 'Tem assinatura: ' . ($proposicao->assinatura_digital ? 'Sim' : 'Não') . PHP_EOL;

// Instanciar controller
$controller = new ProposicaoAssinaturaController();

try {
    // Regenerar PDF
    $controller->regenerarPDFAtualizado($proposicao);
    echo 'PDF regenerado com sucesso!' . PHP_EOL;
    
    if ($proposicao->arquivo_pdf_path) {
        echo 'Caminho: ' . $proposicao->arquivo_pdf_path . PHP_EOL;
    }
    
} catch (Exception $e) {
    echo 'Erro: ' . $e->getMessage() . PHP_EOL;
}