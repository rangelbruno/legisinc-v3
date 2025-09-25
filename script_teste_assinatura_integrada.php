#!/usr/bin/env php
<?php
/**
 * Script para testar a nova funcionalidade de assinatura integrada no PDF
 */

echo "=== TESTE: Sistema de Assinatura Digital Integrada ===\n\n";

// Verificar arquivos criados
$arquivos = [
    'Serviço Principal' => './app/Services/PDFAssinaturaIntegradaService.php',
    'Controller Modificado' => './app/Http/Controllers/ProposicaoAssinaturaController.php'
];

echo "1. Verificando arquivos criados/modificados:\n";
foreach ($arquivos as $nome => $path) {
    if (file_exists($path)) {
        $size = filesize($path);
        echo "✓ $nome: $path ($size bytes)\n";
    } else {
        echo "✗ $nome: $path (não encontrado)\n";
    }
}

// Verificar se as modificações foram aplicadas
echo "\n2. Verificando modificações no controller:\n";

$controller = file_get_contents('./app/Http/Controllers/ProposicaoAssinaturaController.php');

$modificacoes = [
    'PDF integrado na visualização' => 'pdfIntegrado = $this->obterPDFComAssinaturaIntegrada',
    'Geração após assinatura' => 'gerarPDFComAssinaturaIntegrada($proposicao->fresh())',
    'Método obter PDF integrado' => 'private function obterPDFComAssinaturaIntegrada',
    'Buscar PDF existente' => 'buscarPDFIntegradoExistente',
    'Usar serviço de assinatura' => 'PDFAssinaturaIntegradaService'
];

foreach ($modificacoes as $desc => $codigo) {
    if (strpos($controller, $codigo) !== false) {
        echo "✓ $desc: Implementado\n";
    } else {
        echo "✗ $desc: Não encontrado\n";
    }
}

// Verificar estrutura do serviço
echo "\n3. Verificando estrutura do serviço:\n";

if (file_exists('./app/Services/PDFAssinaturaIntegradaService.php')) {
    $servico = file_get_contents('./app/Services/PDFAssinaturaIntegradaService.php');

    $metodos = [
        'Modificar PDF' => 'public function modificarPDFComAssinatura',
        'Obter PDF Original' => 'private function obterPDFOriginal',
        'Criar PDF Modificado' => 'private function criarPDFModificado',
        'Adicionar Assinatura' => 'private function adicionarElementosAssinatura',
        'Limpeza de arquivos' => 'public function limparPDFsAntigos',
        'Verificar possibilidade' => 'public function podeModificarPDF'
    ];

    foreach ($metodos as $desc => $metodo) {
        if (strpos($servico, $metodo) !== false) {
            echo "✓ $desc: $metodo\n";
        } else {
            echo "✗ $desc: $metodo (não encontrado)\n";
        }
    }
}

// Mostrar fluxo implementado
echo "\n4. FLUXO IMPLEMENTADO:\n\n";
echo "A) Quando o usuário acessa /proposicoes/{id}/pdf-original:\n";
echo "   1. Controller verifica se proposição tem assinatura digital\n";
echo "   2. Se SIM: Busca PDF com assinatura integrada existente\n";
echo "   3. Se não existe: Gera novo PDF modificado com assinatura\n";
echo "   4. Serve o PDF com assinatura integrada no documento\n";
echo "   5. Se NÃO: Usa fluxo normal (PDF OnlyOffice sem assinatura)\n\n";

echo "B) Quando o usuário assina digitalmente:\n";
echo "   1. Processa a assinatura (fluxo existente)\n";
echo "   2. NOVO: Gera automaticamente PDF com assinatura integrada\n";
echo "   3. PDF fica disponível para próximas visualizações\n\n";

echo "C) PDF com assinatura integrada contém:\n";
echo "   • Todas as páginas do PDF original preservadas\n";
echo "   • Nova página com informações da assinatura:\n";
echo "     - Nome do assinante\n";
echo "     - Data e hora da assinatura\n";
echo "     - Identificador único\n";
echo "     - Hash da assinatura\n";
echo "     - QR Code para verificação (placeholder)\n";
echo "     - Informações legais (Lei 14.063/2020)\n\n";

// Instruções de teste
echo "5. COMO TESTAR:\n\n";
echo "1. Acesse: /proposicoes/{id}/assinatura-digital\n";
echo "2. Assine uma proposição com o certificado digital\n";
echo "3. Após a assinatura, acesse: /proposicoes/{id}/pdf-original\n";
echo "4. Verifique se o PDF agora mostra:\n";
echo "   • Documento original nas primeiras páginas\n";
echo "   • Página adicional com informações da assinatura\n";
echo "   • Título 'ASSINATURA DIGITAL' em verde\n";
echo "   • Dados do assinante e timestamp\n\n";

echo "6. DIFERENÇAS DA IMPLEMENTAÇÃO ANTERIOR:\n\n";
echo "ANTES: Assinatura era aplicada 'por cima' do PDF via JavaScript\n";
echo "AGORA: Assinatura é integrada diretamente no documento PDF\n\n";
echo "VANTAGENS:\n";
echo "• PDF com assinatura faz parte do documento\n";
echo "• Não depende de JavaScript no frontend\n";
echo "• Assinatura persiste em downloads/impressões\n";
echo "• Melhor para auditoria e compliance\n";
echo "• PDF autocontido com todas as informações\n\n";

echo "ARQUIVOS DE LOG PARA MONITORAMENTO:\n";
echo "• storage/logs/laravel.log (busque por 'PDFAssinaturaIntegrada')\n\n";

echo "=== TESTE CONCLUÍDO ===\n";
echo "A implementação está pronta para ser testada em ambiente real.\n\n";