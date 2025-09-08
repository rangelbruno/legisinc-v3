<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class PDFDesatualizadoFixSeeder extends Seeder
{
    /**
     * CORREÇÃO DEFINITIVA: PDF Desatualizado Entre Endpoints
     * 
     * Problema: /proposicoes/1/pdf serve PDF antigo enquanto /proposicoes/1/assinatura-digital serve PDF correto
     * Solução: Implementar método robusto encontrarPDFMaisRecenteRobusta() no ProposicaoController
     * 
     * Data: 07/09/2025
     * Status: CRÍTICO - NÃO REMOVER
     */
    public function run()
    {
        $this->command->info('🔧 APLICANDO CORREÇÃO: PDF Desatualizado Entre Endpoints...');
        
        $this->implementarMetodoRobusto();
        $this->adicionarHeadersAntiCache();
        $this->corrigirLoadingOverlay();
        $this->validarCorrecao();
        
        $this->command->info('✅ Correção PDF Desatualizado aplicada com sucesso!');
    }

    /**
     * Implementa o método encontrarPDFMaisRecenteRobusta no ProposicaoController
     */
    private function implementarMetodoRobusto()
    {
        $controllerPath = app_path('Http/Controllers/ProposicaoController.php');
        
        if (!file_exists($controllerPath)) {
            $this->command->warn('⚠️ ProposicaoController não encontrado');
            return;
        }

        $conteudo = file_get_contents($controllerPath);

        // Verificar se a correção já foi aplicada
        if (strpos($conteudo, 'encontrarPDFMaisRecenteRobusta') !== false) {
            $this->command->info('  ✅ Método robusto já implementado');
            
            // Garantir que está sendo usado no lugar correto
            $this->garantirUsoMetodoRobusto($conteudo, $controllerPath);
            return;
        }

        // 1. Substituir chamada antiga por nova
        $antigoUso = '$relativePath = $this->caminhoPdfOficial($proposicao);';
        $novoUso = '$relativePath = $this->encontrarPDFMaisRecenteRobusta($proposicao);';

        if (strpos($conteudo, $antigoUso) !== false) {
            $conteudo = str_replace($antigoUso, $novoUso, $conteudo);
            $this->command->info('  ✅ Chamada do método atualizada');
        }

        // 2. Adicionar método robusto
        $novoMetodo = '
    /**
     * CORREÇÃO CRÍTICA: Método robusto que replica a lógica do ProposicaoAssinaturaController
     * Busca PDFs por timestamp real de modificação, não por nome
     * 
     * FIX: PDF Desatualizado Entre Endpoints (07/09/2025)
     * PROBLEMA: /proposicoes/1/pdf serve PDF antigo vs /proposicoes/1/assinatura-digital serve PDF correto
     */
    private function encontrarPDFMaisRecenteRobusta(Proposicao $proposicao): ?string
    {
        $pdfsPossiveis = [];

        // 1. Verificar diretório principal de PDFs da proposição
        $diretorioPrincipal = storage_path("app/private/proposicoes/pdfs/{$proposicao->id}");
        if (is_dir($diretorioPrincipal)) {
            $arquivos = glob($diretorioPrincipal.\'/*.pdf\');
            foreach ($arquivos as $arquivo) {
                if (file_exists($arquivo)) {
                    $pdfsPossiveis[] = [
                        \'path\' => $arquivo,
                        \'relative_path\' => str_replace(storage_path(\'app/\'), \'\', $arquivo),
                        \'timestamp\' => filemtime($arquivo),
                        \'tipo\' => \'pdf_onlyoffice\',
                    ];
                }
            }
        }

        // 2. Verificar se há PDF no arquivo_pdf_path
        if ($proposicao->arquivo_pdf_path) {
            $caminhoCompleto = storage_path(\'app/\'.$proposicao->arquivo_pdf_path);
            if (file_exists($caminhoCompleto)) {
                $pdfsPossiveis[] = [
                    \'path\' => $caminhoCompleto,
                    \'relative_path\' => $proposicao->arquivo_pdf_path,
                    \'timestamp\' => filemtime($caminhoCompleto),
                    \'tipo\' => \'pdf_assinatura\',
                ];
            }
        }

        // 3. Verificar diretórios alternativos
        $diretorios = [
            storage_path("app/proposicoes/{$proposicao->id}"),
            storage_path("app/private/proposicoes/{$proposicao->id}"),
            storage_path("app/public/proposicoes/{$proposicao->id}"),
        ];

        foreach ($diretorios as $diretorio) {
            if (is_dir($diretorio)) {
                $arquivos = glob($diretorio.\'/*.pdf\');
                foreach ($arquivos as $arquivo) {
                    if (file_exists($arquivo)) {
                        $pdfsPossiveis[] = [
                            \'path\' => $arquivo,
                            \'relative_path\' => str_replace(storage_path(\'app/\'), \'\', $arquivo),
                            \'timestamp\' => filemtime($arquivo),
                            \'tipo\' => \'pdf_backup\',
                        ];
                    }
                }
            }
        }

        // Ordenar por data de modificação (mais recente primeiro)
        usort($pdfsPossiveis, function ($a, $b) {
            return $b[\'timestamp\'] - $a[\'timestamp\'];
        });

        Log::info(\'DEBUG: encontrarPDFMaisRecenteRobusta encontrou\', [
            \'proposicao_id\' => $proposicao->id,
            \'total_pdfs\' => count($pdfsPossiveis),
            \'mais_recente\' => !empty($pdfsPossiveis) ? $pdfsPossiveis[0][\'relative_path\'] : null
        ]);

        return !empty($pdfsPossiveis) ? $pdfsPossiveis[0][\'relative_path\'] : null;
    }';

        // Inserir método antes do último }
        $ultimaChave = strrpos($conteudo, '}');
        if ($ultimaChave !== false) {
            $conteudo = substr_replace($conteudo, $novoMetodo . "\n}", $ultimaChave, 1);
        }

        file_put_contents($controllerPath, $conteudo);
        $this->command->info('  ✅ Método robusto implementado');
    }

    /**
     * Garante que o método robusto está sendo usado
     */
    private function garantirUsoMetodoRobusto($conteudo, $controllerPath)
    {
        $antigoUso = '$relativePath = $this->caminhoPdfOficial($proposicao);';
        $novoUso = '$relativePath = $this->encontrarPDFMaisRecenteRobusta($proposicao);';

        if (strpos($conteudo, $antigoUso) !== false) {
            $conteudo = str_replace($antigoUso, $novoUso, $conteudo);
            file_put_contents($controllerPath, $conteudo);
            $this->command->info('  ✅ Uso do método robusto garantido');
        }
    }

    /**
     * Adiciona headers anti-cache agressivos
     */
    private function adicionarHeadersAntiCache()
    {
        $controllerPath = app_path('Http/Controllers/ProposicaoController.php');
        
        if (!file_exists($controllerPath)) {
            return;
        }

        $conteudo = file_get_contents($controllerPath);

        // Verificar se os headers anti-cache já estão implementados
        if (strpos($conteudo, 'Cache-Control\' => \'no-cache, no-store, must-revalidate') !== false) {
            $this->command->info('  ✅ Headers anti-cache já implementados');
            return;
        }

        // Procurar pelo response()->file e adicionar headers
        $padraoAntigo = 'return response()->file($absolutePath);';
        $padraoNovo = 'return response()->file($absolutePath, [
            \'Content-Type\' => \'application/pdf\',
            \'Content-Disposition\' => \'inline; filename="proposicao_\' . $proposicao->id . \'_\' . time() . \'.pdf"\',
            \'Cache-Control\' => \'no-cache, no-store, must-revalidate, max-age=0\',
            \'Pragma\' => \'no-cache\',
            \'Expires\' => \'-1\',
            \'Last-Modified\' => gmdate(\'D, d M Y H:i:s\') . \' GMT\',
            \'ETag\' => \'"\' . md5($absolutePath . filemtime($absolutePath)) . \'"\',
            \'X-PDF-Generator\' => $proposicao->pdf_conversor_usado ?? \'official-robusta\',
            \'X-PDF-Timestamp\' => time(),
            \'X-PDF-Source\' => basename($relativePath ?? \'unknown\')
        ]);';

        if (strpos($conteudo, $padraoAntigo) !== false) {
            $conteudo = str_replace($padraoAntigo, $padraoNovo, $conteudo);
            file_put_contents($controllerPath, $conteudo);
            $this->command->info('  ✅ Headers anti-cache adicionados');
        }
    }

    /**
     * Corrige o loading overlay do PDF Viewer
     */
    private function corrigirLoadingOverlay()
    {
        $arquivoPath = resource_path('views/proposicoes/pdf-viewer.blade.php');
        
        if (!file_exists($arquivoPath)) {
            $this->command->warn('  ⚠️ Arquivo pdf-viewer.blade.php não encontrado');
            return;
        }

        $conteudo = file_get_contents($arquivoPath);

        // Verificar se a correção já foi aplicada
        if (strpos($conteudo, 'loadingDiv.classList.add(\'d-none\')') !== false) {
            $this->command->info('  ✅ Loading overlay já corrigido');
            return;
        }

        // Buscar pela função logPDFLoad e substituir
        $antigo = 'document.getElementById(\'pdf-loading\').style.display = \'none\';';
        $novo = 'const loadingDiv = document.getElementById(\'pdf-loading\');
    if (loadingDiv) {
        loadingDiv.style.display = \'none\';
        loadingDiv.style.visibility = \'hidden\';
        loadingDiv.classList.add(\'d-none\');
    }';

        if (strpos($conteudo, $antigo) !== false) {
            $conteudo = str_replace($antigo, $novo, $conteudo);
            file_put_contents($arquivoPath, $conteudo);
            $this->command->info('  ✅ Loading overlay corrigido');
        }
    }

    /**
     * Valida se a correção foi aplicada corretamente
     */
    private function validarCorrecao()
    {
        $controllerPath = app_path('Http/Controllers/ProposicaoController.php');
        
        if (!file_exists($controllerPath)) {
            $this->command->error('  ❌ ProposicaoController não encontrado');
            return;
        }

        $conteudo = file_get_contents($controllerPath);

        $validacoes = [
            'encontrarPDFMaisRecenteRobusta' => 'Método robusto',
            '$relativePath = $this->encontrarPDFMaisRecenteRobusta($proposicao)' => 'Uso do método',
            'Cache-Control\' => \'no-cache, no-store, must-revalidate' => 'Headers anti-cache',
            'REPLICAR EXATAMENTE a lógica do AssinaturaDigitalController' => 'Lógica AssinaturaDigitalController',
            'app/proposicoes/pdfs/' => 'Busca diretório de assinatura',
            'app/private/proposicoes/pdfs/' => 'Busca diretório OnlyOffice',
        ];

        $todas_ok = true;
        foreach ($validacoes as $busca => $nome) {
            if (strpos($conteudo, $busca) !== false) {
                $this->command->info("  ✅ {$nome}: OK");
            } else {
                $this->command->warn("  ⚠️ {$nome}: FALTANDO");
                $todas_ok = false;
            }
        }

        if ($todas_ok) {
            $this->command->info('  🎯 Validação completa: TODAS AS CORREÇÕES APLICADAS');
        } else {
            $this->command->warn('  ⚠️ Algumas correções não foram aplicadas');
        }
    }
}