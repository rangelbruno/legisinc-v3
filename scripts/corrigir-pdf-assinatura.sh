#!/bin/bash

echo "🔧 CORREÇÃO: PDF de Assinatura - Eliminar Execução Duplicada"
echo "============================================================="

echo ""
echo "📋 PROBLEMA IDENTIFICADO:"
echo "-------------------------"
echo "• Método obterConteudoOnlyOffice() executa 2 vezes consecutivas"
echo "• Isso pode estar causando conflito na geração do PDF"
echo "• PDF pode estar sendo gerado com conteúdo incorreto"

echo ""
echo "🔧 IMPLEMENTANDO CORREÇÃO:"
echo "-------------------------"

# Backup do arquivo original
echo "1. Fazendo backup do controller original..."
cp /home/bruno/legisinc/app/Http/Controllers/ProposicaoAssinaturaController.php \
   /home/bruno/legisinc/app/Http/Controllers/ProposicaoAssinaturaController.php.backup.$(date +%s)

echo "   ✅ Backup criado"

# Criar arquivo de patch para corrigir a execução duplicada
cat > /tmp/pdf_assinatura_fix.patch << 'EOF'
--- ProposicaoAssinaturaController.php.orig
+++ ProposicaoAssinaturaController.php
@@ -435,10 +435,15 @@
      */
     private function criarPDFDoArquivoMaisRecente(string $caminhoPdfAbsoluto, Proposicao $proposicao): void
     {
+        static $processingLock = [];
+        
         try {
+            // Evitar execução duplicada/concorrente
+            $lockKey = "pdf_generation_{$proposicao->id}";
+            if (isset($processingLock[$lockKey])) {
+                error_log("PDF Assinatura: Execução duplicada detectada e prevenida para proposição {$proposicao->id}");
+                return;
+            }
+            $processingLock[$lockKey] = true;
+            
             // ESTRATÉGIA MELHORADA: Buscar SEMPRE o arquivo mais recente
             $arquivoMaisRecente = $this->encontrarArquivoMaisRecente($proposicao);
             
@@ -447,8 +452,10 @@
                 
                 // Usar o arquivo encontrado
                 $arquivoEncontrado = $arquivoMaisRecente['path'];
+                error_log("PDF Assinatura: Usando arquivo: {$arquivoEncontrado}");
                 error_log("PDF Assinatura: Tamanho do arquivo: " . filesize($arquivoEncontrado) . " bytes");
                 
                 // Extrair conteúdo do arquivo mais recente
                 if (str_contains($arquivoEncontrado, '.docx')) {
                     try {
@@ -457,6 +464,7 @@
                         if (!empty($conteudo) && strlen($conteudo) > 50) {
                             error_log("PDF Assinatura: Primeiros 200 chars: " . substr($conteudo, 0, 200));
                             
+                            // Gerar PDF com o conteúdo extraído
                             $this->gerarPDFComConteudo($caminhoPdfAbsoluto, $conteudo, $proposicao);
                             error_log("PDF Assinatura: PDF gerado com sucesso usando arquivo mais recente");
                             return;
@@ -479,6 +487,10 @@
         } catch (\Exception $e) {
             error_log("PDF Assinatura: Erro na criação do PDF: " . $e->getMessage());
             $this->gerarPDFFallback($caminhoPdfAbsoluto, $proposicao);
+        } finally {
+            // Liberar lock
+            $lockKey = "pdf_generation_{$proposicao->id}";
+            unset($processingLock[$lockKey]);
         }
     }
EOF

echo ""
echo "2. Aplicando correção para eliminar execução duplicada..."

# Verificar se o patch pode ser aplicado
echo "   Verificando estrutura do arquivo..."

if grep -q "criarPDFDoArquivoMaisRecente" /home/bruno/legisinc/app/Http/Controllers/ProposicaoAssinaturaController.php; then
    echo "   ✅ Método encontrado"
    
    # Aplicar correção manual (já que patch pode não estar disponível)
    echo "   Aplicando correção manual..."
    
    # Criar arquivo temporário com a correção
    cat > /tmp/fix_controller.php << 'EOF'
<?php

// Corrigir método criarPDFDoArquivoMaisRecente para evitar execução duplicada
$file = '/home/bruno/legisinc/app/Http/Controllers/ProposicaoAssinaturaController.php';
$content = file_get_contents($file);

// Adicionar controle de lock para evitar execução duplicada
$search = 'private function criarPDFDoArquivoMaisRecente(string $caminhoPdfAbsoluto, Proposicao $proposicao): void
    {
        try {';

$replace = 'private function criarPDFDoArquivoMaisRecente(string $caminhoPdfAbsoluto, Proposicao $proposicao): void
    {
        static $processingLock = [];
        
        try {
            // Evitar execução duplicada/concorrente
            $lockKey = "pdf_generation_{$proposicao->id}";
            if (isset($processingLock[$lockKey])) {
                error_log("PDF Assinatura: Execução duplicada detectada e prevenida para proposição {$proposicao->id}");
                return;
            }
            $processingLock[$lockKey] = true;';

if (strpos($content, $search) !== false) {
    $content = str_replace($search, $replace, $content);
    echo "✅ Correção 1/3: Lock de execução duplicada aplicado\n";
} else {
    echo "⚠️  Correção 1/3: Padrão não encontrado (pode já estar aplicado)\n";
}

// Adicionar log melhorado
$search2 = '// Usar o arquivo encontrado
                $arquivoEncontrado = $arquivoMaisRecente[\'path\'];
                error_log("PDF Assinatura: Tamanho do arquivo: " . filesize($arquivoEncontrado) . " bytes");';

$replace2 = '// Usar o arquivo encontrado
                $arquivoEncontrado = $arquivoMaisRecente[\'path\'];
                error_log("PDF Assinatura: Usando arquivo: {$arquivoEncontrado}");
                error_log("PDF Assinatura: Tamanho do arquivo: " . filesize($arquivoEncontrado) . " bytes");';

if (strpos($content, $search2) !== false) {
    $content = str_replace($search2, $replace2, $content);
    echo "✅ Correção 2/3: Log melhorado aplicado\n";
} else {
    echo "⚠️  Correção 2/3: Padrão não encontrado (pode já estar aplicado)\n";
}

// Adicionar finally para liberar lock
$search3 = '} catch (\Exception $e) {
            error_log("PDF Assinatura: Erro na criação do PDF: " . $e->getMessage());
            $this->gerarPDFFallback($caminhoPdfAbsoluto, $proposicao);
        }
    }';

$replace3 = '} catch (\Exception $e) {
            error_log("PDF Assinatura: Erro na criação do PDF: " . $e->getMessage());
            $this->gerarPDFFallback($caminhoPdfAbsoluto, $proposicao);
        } finally {
            // Liberar lock
            $lockKey = "pdf_generation_{$proposicao->id}";
            if (isset($processingLock[$lockKey])) {
                unset($processingLock[$lockKey]);
            }
        }
    }';

if (strpos($content, $search3) !== false) {
    $content = str_replace($search3, $replace3, $content);
    echo "✅ Correção 3/3: Finally block para liberar lock aplicado\n";
} else {
    echo "⚠️  Correção 3/3: Padrão não encontrado (pode já estar aplicado)\n";
}

// Salvar arquivo corrigido
file_put_contents($file, $content);
echo "✅ Arquivo salvo com correções\n";
EOF

    # Executar correção
    php /tmp/fix_controller.php
    
    echo "   ✅ Correção aplicada"
else
    echo "   ❌ Método não encontrado no arquivo"
fi

echo ""
echo "3. Verificando se correção foi aplicada..."

if grep -q "processingLock\|Execução duplicada detectada" /home/bruno/legisinc/app/Http/Controllers/ProposicaoAssinaturaController.php; then
    echo "   ✅ Correção foi aplicada com sucesso"
else
    echo "   ❌ Correção NÃO foi aplicada"
fi

echo ""
echo "4. Limpando arquivos temporários..."
rm -f /tmp/pdf_assinatura_fix.patch
rm -f /tmp/fix_controller.php
echo "   ✅ Limpeza concluída"

echo ""
echo "🎯 CORREÇÃO CONCLUÍDA!"
echo "====================="
echo ""
echo "✅ Adicionado controle de lock para evitar execução duplicada"
echo "✅ Melhorados logs para debug"
echo "✅ Adicionado finally block para garantir limpeza"
echo ""
echo "📋 TESTE AGORA:"
echo "--------------"
echo "1. Execute: docker-compose up -d (se não estiver rodando)"
echo "2. Acesse: http://localhost:8001/login"
echo "3. Login: jessica@sistema.gov.br / 123456"
echo "4. Vá para: http://localhost:8001/proposicoes/8/assinar"
echo "5. Verifique se o PDF agora mostra o conteúdo correto"
echo ""
echo "💡 LOGS PARA ACOMPANHAR:"
echo "-----------------------"
echo "tail -f /home/bruno/legisinc/storage/logs/laravel.log | grep 'PDF Assinatura'"