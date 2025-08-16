#!/bin/bash

echo "=== Teste Direto de Geração de PDF ==="

# Criar um comando artisan temporário para testar a geração de PDF
docker exec -it legisinc-app php -r "
use App\Models\Proposicao;
use App\Http\Controllers\ProposicaoAssinaturaController;

// Carregar proposição 6
\$proposicao = Proposicao::find(6);
if (!\$proposicao) {
    echo 'Proposição 6 não encontrada\n';
    exit(1);
}

echo 'Proposição encontrada: ' . \$proposicao->id . '\n';
echo 'Status: ' . \$proposicao->status . '\n';
echo 'Tem assinatura digital: ' . (\$proposicao->assinatura_digital ? 'Sim' : 'Não') . '\n';

// Instanciar controller
\$controller = new ProposicaoAssinaturaController();

try {
    // Forçar regeneração do PDF
    \$reflection = new ReflectionClass(\$controller);
    \$method = \$reflection->getMethod('gerarPDFParaAssinatura');
    \$method->setAccessible(true);
    \$method->invoke(\$controller, \$proposicao);
    
    echo 'PDF regenerado com sucesso!\n';
    echo 'Caminho: ' . \$proposicao->arquivo_pdf_path . '\n';
    
} catch (Exception \$e) {
    echo 'Erro ao gerar PDF: ' . \$e->getMessage() . '\n';
}
"

# Verificar se o PDF foi criado
if [ -f "/home/bruno/legisinc/storage/app/proposicoes/pdfs/6/proposicao_6.pdf" ]; then
    echo "✅ PDF gerado com sucesso"
    
    # Verificar tamanho
    echo "Tamanho: $(ls -lh /home/bruno/legisinc/storage/app/proposicoes/pdfs/6/proposicao_6.pdf | awk '{print $5}')"
    
    # Se temos pdftotext, verificar conteúdo
    if command -v pdftotext >/dev/null 2>&1; then
        echo ""
        echo "=== Verificando conteúdo do PDF ==="
        pdftotext "/home/bruno/legisinc/storage/app/proposicoes/pdfs/6/proposicao_6.pdf" /tmp/pdf_content.txt
        
        if grep -q "ASSINATURA DIGITAL" /tmp/pdf_content.txt; then
            echo "❌ ERRO: PDF contém seções de assinatura (não deveria para status aprovado_assinatura)"
        else
            echo "✅ SUCESSO: PDF não contém seções de assinatura desnecessárias"
        fi
        
        echo ""
        echo "=== Primeiras 15 linhas do PDF ==="
        head -15 /tmp/pdf_content.txt
        
        rm -f /tmp/pdf_content.txt
    fi
else
    echo "❌ PDF não foi gerado"
fi

echo ""
echo "=== Teste Concluído ==="