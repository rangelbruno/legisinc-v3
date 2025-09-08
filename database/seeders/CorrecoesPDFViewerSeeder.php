<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CorrecoesPDFViewerSeeder extends Seeder
{
    /**
     * Aplica correções críticas no PDF Viewer para preservar funcionalidade
     */
    public function run()
    {
        $this->command->info('🔧 Aplicando Correções do PDF Viewer...');

        $this->corrigirPDFViewerBlade();
        $this->corrigirCaminhoPdfOficial();
        $this->sincronizarLogicaPDFRobusta();

        $this->command->info('✅ Correções do PDF Viewer aplicadas com sucesso!');
    }

    /**
     * Corrige o loading overlay que não desaparece no PDF Viewer
     */
    private function corrigirPDFViewerBlade()
    {
        $arquivoPath = resource_path('views/proposicoes/pdf-viewer.blade.php');
        
        if (!file_exists($arquivoPath)) {
            $this->command->warn('⚠️ Arquivo pdf-viewer.blade.php não encontrado');
            return;
        }

        $conteudo = file_get_contents($arquivoPath);

        // Buscar pela função logPDFLoad original e substituir pela versão corrigida
        $antigo = 'function logPDFLoad() {
    const loadTime = Date.now() - pdfLoadStartTime;
    console.log(\'✅ PDF VIEWER: PDF carregado com sucesso\', {
        proposicao_id: {{ $proposicao->id }},
        load_time_ms: loadTime,
        timestamp: new Date().toISOString()
    });

    // Atualizar interface
    document.getElementById(\'pdf-loading\').style.display = \'none\';
    document.getElementById(\'load-time\').textContent = loadTime + \'ms\';
    document.getElementById(\'pdf-status\').textContent = \'Carregado com sucesso\';
    document.getElementById(\'pdf-status\').className = \'text-success small\';';

        $novo = 'function logPDFLoad() {
    const loadTime = Date.now() - pdfLoadStartTime;
    console.log(\'✅ PDF VIEWER: PDF carregado com sucesso\', {
        proposicao_id: {{ $proposicao->id }},
        load_time_ms: loadTime,
        timestamp: new Date().toISOString()
    });

    // Atualizar interface - forçar ocultação do loading
    const loadingDiv = document.getElementById(\'pdf-loading\');
    if (loadingDiv) {
        loadingDiv.style.display = \'none\';
        loadingDiv.style.visibility = \'hidden\';
        loadingDiv.classList.add(\'d-none\');
    }
    document.getElementById(\'load-time\').textContent = loadTime + \'ms\';
    document.getElementById(\'pdf-status\').textContent = \'Carregado com sucesso\';
    document.getElementById(\'pdf-status\').className = \'text-success small\';';

        if (strpos($conteudo, $antigo) !== false) {
            $conteudo = str_replace($antigo, $novo, $conteudo);
            file_put_contents($arquivoPath, $conteudo);
            $this->command->info('  ✅ Correção do loading overlay aplicada');
        } else if (strpos($conteudo, 'loadingDiv.classList.add(\'d-none\')') !== false) {
            $this->command->info('  ✅ Correção do loading overlay já aplicada');
        } else {
            $this->command->warn('  ⚠️ Função logPDFLoad não encontrada para correção');
        }
    }

    /**
     * Corrige a lógica de seleção do PDF mais recente no ProposicaoController
     */
    private function corrigirCaminhoPdfOficial()
    {
        $controllerPath = app_path('Http/Controllers/ProposicaoController.php');
        
        if (!file_exists($controllerPath)) {
            $this->command->warn('⚠️ ProposicaoController não encontrado');
            return;
        }

        $conteudo = file_get_contents($controllerPath);

        // Verificar se a correção já foi aplicada
        if (strpos($conteudo, '!str_contains($file, \'_assinado_\') &&') !== false) {
            $this->command->info('  ✅ Correção do caminhoPdfOficial já aplicada');
            return;
        }

        // Buscar pela lógica de filtro OnlyOffice e corrigir
        $antigoFiltro = '// Filtrar arquivos OnlyOffice
        $onlyOfficeFiles = array_filter($files, function($file) {
            return str_contains($file, \'_onlyoffice_\') && str_ends_with($file, \'.pdf\');
        });';

        $novoFiltro = '// Filtrar arquivos OnlyOffice - excluir assinados e protocolados
        $onlyOfficeFiles = array_filter($files, function($file) {
            return str_contains($file, \'_onlyoffice_\') && 
                   str_ends_with($file, \'.pdf\') &&
                   !str_contains($file, \'_assinado_\') &&
                   !str_contains($file, \'_protocolado_\');
        });';

        if (strpos($conteudo, $antigoFiltro) !== false) {
            $conteudo = str_replace($antigoFiltro, $novoFiltro, $conteudo);
        }

        // Corrigir a lógica de ordenação por timestamp
        $antigoSort = '// Ordenar por timestamp no nome (mais recente primeiro)
            usort($onlyOfficeFiles, function($a, $b) {
                preg_match(\'/_(\d+)\.pdf$/\', $a, $matchesA);
                preg_match(\'/_(\d+)\.pdf$/\', $b, $matchesB);
                return ($matchesB[1] ?? 0) <=> ($matchesA[1] ?? 0);
            });';

        $novoSort = '// Ordenar por timestamp no nome (mais recente primeiro)
            usort($onlyOfficeFiles, function($a, $b) {
                preg_match(\'/_onlyoffice_(\d+)/\', $a, $matchesA);
                preg_match(\'/_onlyoffice_(\d+)/\', $b, $matchesB);
                $timestampA = intval($matchesA[1] ?? 0);
                $timestampB = intval($matchesB[1] ?? 0);
                return $timestampB <=> $timestampA; // Maior timestamp (mais recente) primeiro
            });';

        if (strpos($conteudo, $antigoSort) !== false) {
            $conteudo = str_replace($antigoSort, $novoSort, $conteudo);
        }

        // Melhorar os logs de debug
        $antigoLog = 'Log::info(\'DEBUG: Arquivo OnlyOffice mais recente selecionado\', [
                \'proposicao_id\' => $proposicao->id,
                \'arquivo_selecionado\' => $maisRecente,
                \'existe\' => Storage::exists($maisRecente)
            ]);';

        $novoLog = 'Log::info(\'DEBUG: Arquivo OnlyOffice mais recente selecionado\', [
                \'proposicao_id\' => $proposicao->id,
                \'arquivo_selecionado\' => $maisRecente,
                \'existe\' => Storage::exists($maisRecente),
                \'timestamp_extraido\' => preg_match(\'/_onlyoffice_(\d+)/\', $maisRecente, $matches) ? $matches[1] : \'não encontrado\'
            ]);';

        if (strpos($conteudo, $antigoLog) !== false) {
            $conteudo = str_replace($antigoLog, $novoLog, $conteudo);
        }

        file_put_contents($controllerPath, $conteudo);
        $this->command->info('  ✅ Correção do caminhoPdfOficial aplicada');
    }

    /**
     * Substitui caminhoPdfOficial por lógica robusta que busca PDFs por timestamp real
     */
    private function sincronizarLogicaPDFRobusta()
    {
        $controllerPath = app_path('Http/Controllers/ProposicaoController.php');
        
        if (!file_exists($controllerPath)) {
            $this->command->warn('⚠️ ProposicaoController não encontrado para sincronização');
            return;
        }

        $conteudo = file_get_contents($controllerPath);

        // Verificar se a correção já foi aplicada
        if (strpos($conteudo, 'encontrarPDFMaisRecenteRobusta') !== false) {
            $this->command->info('  ✅ Sincronização robusta já aplicada');
            return;
        }

        // Substituir chamada caminhoPdfOficial por nova lógica
        $antigoUso = '$relativePath = $this->caminhoPdfOficial($proposicao);';
        $novoUso = '$relativePath = $this->encontrarPDFMaisRecenteRobusta($proposicao);';

        if (strpos($conteudo, $antigoUso) !== false) {
            $conteudo = str_replace($antigoUso, $novoUso, $conteudo);
        }

        // Adicionar novo método encontrarPDFMaisRecenteRobusta antes do último }
        $novoMetodo = '
    /**
     * Método robusto que replica a lógica do ProposicaoAssinaturaController
     * Busca PDFs por timestamp real de modificação, não por nome
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
        $this->command->info('  ✅ Sincronização robusta de PDFs aplicada');
    }
}